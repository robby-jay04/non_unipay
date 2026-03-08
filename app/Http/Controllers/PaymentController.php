<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Notification; // <-- ADD THIS
use Barryvdh\DomPDF\Facade\Pdf;
use Luigel\Paymongo\Facades\Paymongo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Fee;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('student.user')->orderBy('id', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(10);

        if ($request->ajax()) {
            return view('admin.payments', compact('payments'))->render();
        }

        return view('admin.payments', compact('payments'));
    }

    public function show($id)
    {
        $payment = Payment::with(['student.user', 'fees'])->findOrFail($id);
        return view('admin.payments.partials.view', compact('payment'));
    }

    /**
     * Initiate payment with PayMongo
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'amount'   => 'required|numeric|min:100',
            'fee_ids'  => 'required|array',
            'fee_ids.*' => 'exists:fees,id',
        ]);

        $student = $request->user()->student;

        // Verify total matches selected fees
        $selectedFees = Fee::whereIn('id', $request->fee_ids)->get();
        $calculatedTotal = $selectedFees->sum('amount');
        if (abs($calculatedTotal - $request->amount) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Total amount mismatch'
            ], 400);
        }

        // PayMongo max check
        $amountPesos = floatval($request->amount);
        if ($amountPesos > 100000) {
            return response()->json([
                'success' => false,
                'message' => 'Amount exceeds PayMongo maximum of ₱100,000'
            ]);
        }

        // Create pending payment in DB
        $payment = Payment::create([
            'student_id'   => $student->id,
            'total_amount' => $amountPesos,
            'status'       => 'pending',
            'payment_method' => 'gcash',
            'reference_no' => 'PAY-' . strtoupper(Str::random(10)),
        ]);

        // Attach selected fees
        foreach ($selectedFees as $fee) {
            $payment->fees()->attach($fee->id, ['amount' => $fee->amount]);
        }

        try {
            $source = Paymongo::source()->create([
                'type' => 'gcash',
                'amount' => $amountPesos,
                'currency' => 'PHP',
                'redirect' => [
                    'success' => route('payment.success'),
                    'failed'  => route('payment.failed'),
                ],
                'billing' => [
                    'name' => $student->user->name,
                    'email' => $student->user->email,
                    'phone' => $student->user->phone ?? '09000000000',
                ],
            ]);

            // Save PayMongo source ID
            $payment->update([
                'paymongo_source_id' => $source->id,
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $source->getRedirect()['checkout_url'],
                'reference_no' => $payment->reference_no,
                'payment_id' => $payment->id,
            ]);

        } catch (\Luigel\Paymongo\Exceptions\BadRequestException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Webhook — updates payment automatically
    public function webhook(Request $request)
    {
        $payload = $request->all();

        // Log for debugging
        Log::info('PayMongo webhook received', ['payload' => $payload]);

        // Only act on chargeable sources
        if (isset($payload['data']['attributes']['type']) &&
            $payload['data']['attributes']['type'] === 'source.chargeable') {

            $sourceId = $payload['data']['attributes']['data']['id'];
            $payment = Payment::where('paymongo_source_id', $sourceId)->first();

            if ($payment && $payment->status === 'pending') {
                try {
                    $paymentResponse = Paymongo::payment()->create([
                        'amount' => $payment->total_amount * 100,
                        'currency' => 'PHP',
                        'source' => [
                            'id' => $sourceId,
                            'type' => 'source'
                        ],
                        'description' => 'School Fee Payment - ' . $payment->reference_no,
                    ]);

                    // Update payment to paid
                    $payment->update([
                        'status' => 'paid',
                        'payment_date' => now(),
                        'paymongo_payment_intent_id' => $paymentResponse->id,
                    ]);

                    Transaction::create([
                        'payment_id' => $payment->id,
                        'transaction_id' => $paymentResponse->id,
                        'amount' => $payment->total_amount,
                        'status' => 'completed',
                        'payment_method' => 'gcash',
                    ]);

                    // Create success notification
                    Notification::create([
                        'user_id' => $payment->student->user_id,
                        'type' => 'payment_success',
                        'message' => 'Your payment of ₱' . number_format($payment->total_amount, 2) . ' has been approved.',
                        'data' => [
                            'payment_id' => $payment->id,
                            'amount' => $payment->total_amount,
                            'reference' => $payment->reference_no,
                        ],
                    ]);

                } catch (\Exception $e) {
                    Log::error('PayMongo webhook error', [
                        'error' => $e->getMessage(),
                        'payment_id' => $payment->id
                    ]);
                    $payment->update(['status' => 'failed']);

                    // Create failure notification
                    Notification::create([
                        'user_id' => $payment->student->user_id,
                        'type' => 'payment_failed',
                        'message' => 'Your payment of ₱' . number_format($payment->total_amount, 2) . ' failed. Please try again.',
                        'data' => [
                            'payment_id' => $payment->id,
                            'amount' => $payment->total_amount,
                            'reference' => $payment->reference_no,
                        ],
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    // Success & Failed routes
    public function success()
    {
        return view('payments.success', [
            'message' => 'Payment completed successfully!'
        ]);
    }

    public function failed()
    {
        return view('payments.failed', [
            'message' => 'Payment failed. Please try again.'
        ]);
    }

    /**
     * Check payment status
     */
    public function checkStatus($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        if ($payment->paymongo_source_id) {
            try {
                $source = Paymongo::source()->find($payment->paymongo_source_id);

                if ($source->getStatus() === 'chargeable') {
                    // Source is ready to be charged (user completed GCash)
                    $payment->update(['status' => 'processing']);
                } elseif ($source->getStatus() === 'cancelled' || $source->getStatus() === 'expired') {
                    $payment->update(['status' => 'failed']);
                }
            } catch (\Exception $e) {
                Log::error('Status check error', [
                    'error' => $e->getMessage(),
                    'payment_id' => $paymentId
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'payment' => $payment,
        ]);
    }

    public function downloadReceipt($id)
    {
        $payment = Payment::with(['student.user', 'transaction'])->findOrFail($id);

        if ($payment->status !== 'paid') {
            return response()->json(['message' => 'Payment not completed'], 400);
        }

        $pdf = PDF::loadView('receipts.payment', compact('payment'));
        return $pdf->download('receipt-' . $payment->id . '.pdf');
    }

    public function history(Request $request)
    {
        $student = $request->user()->student;

        $payments = Payment::where('student_id', $student->id)
            ->with(['transaction', 'fees'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments,
            'total_paid' => $payments->where('status', 'paid')->sum('total_amount'),
        ]);
    }

    public function verify($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ]);
        }

        $payment->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        // Create success notification for manual verification
        Notification::create([
            'user_id' => $payment->student->user_id,
            'type' => 'payment_success',
            'message' => 'Your payment of ₱' . number_format($payment->total_amount, 2) . ' has been approved.',
            'data' => [
                'payment_id' => $payment->id,
                'amount' => $payment->total_amount,
                'reference' => $payment->reference_no,
            ],
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function reject($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ]);
        }

        $payment->status = 'failed';
        $payment->save();

        // Create failure notification for manual rejection
        Notification::create([
            'user_id' => $payment->student->user_id,
            'type' => 'payment_failed',
            'message' => 'Your payment of ₱' . number_format($payment->total_amount, 2) . ' was rejected. Please contact support.',
            'data' => [
                'payment_id' => $payment->id,
                'amount' => $payment->total_amount,
                'reference' => $payment->reference_no,
            ],
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}