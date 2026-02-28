<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Luigel\Paymongo\Facades\Paymongo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // ✅ Add this import

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
        $payment = Payment::with('student.user')->findOrFail($id);
        return view('admin.payments.partials.view', compact('payment'));
    }

    /**
     * Initiate payment with PayMongo
     */

public function initiate(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:100',
    ]);

    $student = $request->user()->student;

    // Convert to float
    $amountPesos = floatval($request->amount);

    // Check PayMongo max (₱100,000)
    if ($amountPesos > 100000) {
        return response()->json([
            'success' => false,
            'message' => 'Amount exceeds PayMongo maximum of ₱100,000'
        ]);
    }

    // Convert to centavos as integer
    $amountCentavos = (int) round($amountPesos * 100);

    // Create pending payment in DB
    $payment = Payment::create([
        'student_id' => $student->id,
        'total_amount' => $amountPesos,
        'status' => 'pending',
        'payment_method' => 'gcash',
        'reference_no' => 'PAY-' . strtoupper(Str::random(10)),
    ]);

    try {
    $source = Paymongo::source()->create([
    'type' => 'gcash',
    'amount' => $amountPesos, // ✅ PESOS ONLY
    'currency' => 'PHP',
    'redirect' => [
        'success' => 'https://daniele-cosmic-vapidly.ngrok-free.dev/payment/success',
        'failed' => 'https://daniele-cosmic-vapidly.ngrok-free.dev/payment/failed',
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
    'payment_date' => now(), // ✅ CORRECT COLUMN
    'paymongo_payment_intent_id' => $paymentResponse->id,
]);
                Transaction::create([
                    'payment_id' => $payment->id,
                    'transaction_id' => $paymentResponse->id,
                    'amount' => $payment->total_amount,
                    'status' => 'completed',
                    'payment_method' => 'gcash',
                ]);

            } catch (\Exception $e) {
                Log::error('PayMongo webhook error', [
                    'error' => $e->getMessage(),
                    'payment_id' => $payment->id
                ]);
                $payment->update(['status' => 'failed']);
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
            ->with('transaction')
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
        'payment_date' => now(), // ✅ USE CORRECT COLUMN
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

    return response()->json([
        'success' => true
    ]);
}

}