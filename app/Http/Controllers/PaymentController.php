<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Notification;
use App\Services\ClearanceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Luigel\Paymongo\Facades\Paymongo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Fee;
use App\Models\ExamPeriod; // <-- Added for exam period retrieval

class PaymentController extends Controller
{
    protected $clearanceService;

    public function __construct(ClearanceService $clearanceService)
    {
        $this->clearanceService = $clearanceService;
    }

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
    $payment = Payment::with(['student.user', 'fees', 'semester', 'schoolYear', 'examPeriod'])
              ->findOrFail($id);

    return view('admin.payments.partials.view', compact('payment'));
}

    public function initiate(Request $request)
    {
        $request->validate([
            'amount'    => 'required|numeric|min:100',
            'fee_ids'   => 'required|array',
            'fee_ids.*' => 'exists:fees,id',
        ]);

        $student = $request->user()->student;

        $selectedFees    = Fee::whereIn('id', $request->fee_ids)->get();
        $calculatedTotal = $selectedFees->sum('amount');

        if (abs($calculatedTotal - $request->amount) > 0.01) {
            return response()->json(['success' => false, 'message' => 'Total amount mismatch'], 400);
        }

        $amountPesos = floatval($request->amount);
        if ($amountPesos > 100000) {
            return response()->json(['success' => false, 'message' => 'Amount exceeds PayMongo maximum of ₱100,000']);
        }

        $firstFee     = $selectedFees->first();
        $semesterId   = $firstFee->semester_id
            ?? optional(\App\Models\Semester::where('is_current', true)->first())->id;
        $schoolYearId = $firstFee->school_year_id
            ?? optional(\App\Models\SchoolYear::where('is_current', true)->first())->id;

        // --- NEW: Get the current exam period for this semester (if any) ---
        $currentExamPeriod = null;
        if ($semesterId) {
            $currentExamPeriod = ExamPeriod::where('semester_id', $semesterId)
                                ->where('is_current', true)
                                ->first();
        }
        // --- End of new code ---

        Log::info('Initiate payment debug', [
            'student_id'     => $student->id,
            'semester_id'    => $semesterId,
            'school_year_id' => $schoolYearId,
            'fee_ids'        => $request->fee_ids,
        ]);

        if ($semesterId && $schoolYearId) {
            $semesterTotal = Fee::where('semester_id', $semesterId)
                ->where('school_year_id', $schoolYearId)
                ->sum('amount');

            $totalPaid = Payment::where('student_id', $student->id)
                ->where('semester_id', $semesterId)
                ->where('school_year_id', $schoolYearId)
                ->where('status', 'paid')
                ->sum('total_amount');

            $remainingBalance = $semesterTotal - $totalPaid;

            Log::info('Payment balance check', [
                'semester_total'    => $semesterTotal,
                'total_paid'        => $totalPaid,
                'remaining_balance' => $remainingBalance,
                'requested_amount'  => $amountPesos,
            ]);

            if ($remainingBalance <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already fully paid for this semester.',
                ], 400);
            }

            if ($amountPesos > $remainingBalance + 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment of ₱' . number_format($amountPesos, 2) .
                                 ' exceeds your remaining balance of ₱' . number_format($remainingBalance, 2) . '.',
                ], 400);
            }

            Payment::where('student_id', $student->id)
                ->where('semester_id', $semesterId)
                ->where('school_year_id', $schoolYearId)
                ->whereIn('status', ['pending', 'processing'])
                ->where('created_at', '<', now()->subMinutes(30))
                ->update(['status' => 'cancelled']);
        }

        $payment = Payment::create([
            'student_id'     => $student->id,
            'total_amount'   => $amountPesos,
            'status'         => 'pending',
            'payment_method' => 'gcash',
            'reference_no'   => 'PAY-' . strtoupper(Str::random(10)),
            'semester_id'    => $semesterId,
            'school_year_id' => $schoolYearId,
            'exam_period_id' => $currentExamPeriod ? $currentExamPeriod->id : null, // <-- Store the exam period
        ]);

        foreach ($selectedFees as $fee) {
            $payment->fees()->attach($fee->id, ['amount' => $fee->amount]);
        }

        try {
            $source = Paymongo::source()->create([
                'type'     => 'gcash',
                'amount'   => $amountPesos,
                'currency' => 'PHP',
                'redirect' => [
                    'success' => route('payment.success'),
                    'failed'  => route('payment.failed'),
                ],
                'billing' => [
                    'name'  => $student->user->name,
                    'email' => $student->user->email,
                    'phone' => $student->user->phone ?? '09000000000',
                ],
            ]);

            $payment->update(['paymongo_source_id' => $source->id]);

            return response()->json([
                'success'      => true,
                'payment_url'  => $source->getRedirect()['checkout_url'],
                'reference_no' => $payment->reference_no,
                'payment_id'   => $payment->id,
            ]);

        } catch (\Luigel\Paymongo\Exceptions\BadRequestException $e) {
            $payment->delete();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->all();

        Log::info('PayMongo webhook received', ['payload' => $payload]);

        if (isset($payload['data']['attributes']['type']) &&
            $payload['data']['attributes']['type'] === 'source.chargeable') {

            $sourceId = $payload['data']['attributes']['data']['id'];
            $payment  = Payment::where('paymongo_source_id', $sourceId)->first();

            if ($payment && $payment->status === 'pending') {
                try {
                    $paymentResponse = Paymongo::payment()->create([
                        'amount'   => $payment->total_amount * 100,
                        'currency' => 'PHP',
                        'source'   => [
                            'id'   => $sourceId,
                            'type' => 'source',
                        ],
                        'description' => 'School Fee Payment - ' . $payment->reference_no,
                    ]);

                    $payment->update([
                        'status'                     => 'paid',
                        'payment_date'               => now(),
                        'paymongo_payment_intent_id' => $paymentResponse->id,
                    ]);

                    // ✅ Sync clearance status
                    $this->clearanceService->updateClearance($payment->student_id);

                    Transaction::create([
                        'payment_id'     => $payment->id,
                        'transaction_id' => $paymentResponse->id,
                        'amount'         => $payment->total_amount,
                        'status'         => 'completed',
                        'payment_method' => 'gcash',
                    ]);

                    Notification::create([
                        'user_id' => $payment->student->user_id,
                        'type'    => 'payment_success',
                        'message' => 'Your payment of ₱' . number_format($payment->total_amount, 2) . ' has been approved.',
                        'data'    => [
                            'payment_id' => $payment->id,
                            'amount'     => $payment->total_amount,
                            'reference'  => $payment->reference_no,
                        ],
                    ]);

                } catch (\Exception $e) {
                    Log::error('PayMongo webhook error', [
                        'error'      => $e->getMessage(),
                        'payment_id' => $payment->id,
                    ]);

                    $payment->update(['status' => 'failed']);

                    // ✅ Re-check clearance in case this was the payment that cleared them
                    $this->clearanceService->updateClearance($payment->student_id);

                    Notification::create([
                        'user_id' => $payment->student->user_id,
                        'type'    => 'payment_failed',
                        'message' => 'Your payment of ₱' . number_format($payment->total_amount, 2) . ' failed. Please try again.',
                        'data'    => [
                            'payment_id' => $payment->id,
                            'amount'     => $payment->total_amount,
                            'reference'  => $payment->reference_no,
                        ],
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function success()
    {
        return view('payments.success', [
            'message' => 'Payment completed successfully!',
        ]);
    }

    public function failed()
    {
        return view('payments.failed', [
            'message' => 'Payment failed. Please try again.',
        ]);
    }

    public function checkStatus($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        if ($payment->paymongo_source_id) {
            try {
                $source = Paymongo::source()->find($payment->paymongo_source_id);

                if ($source->getStatus() === 'chargeable') {
                    $payment->update(['status' => 'processing']);
                } elseif (in_array($source->getStatus(), ['cancelled', 'expired'])) {
                    $payment->update(['status' => 'failed']);
                }
            } catch (\Exception $e) {
                Log::error('Status check error', [
                    'error'      => $e->getMessage(),
                    'payment_id' => $paymentId,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status'  => $payment->status,
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

        if (!$student) {
            return response()->json([
                'success'  => false,
                'message'  => 'Student profile not found',
                'payments' => [],
            ], 404);
        }

        try {
            $payments = Payment::where('student_id', $student->id)
                ->with([
                    'transaction',
                    'fees',
                    'fees.semester',
                    'fees.schoolYear',
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success'    => true,
                'payments'   => $payments,
                'total_paid' => $payments->where('status', 'paid')->sum('total_amount'),
            ]);

        } catch (\Exception $e) {
            Log::error('Payment history error: ' . $e->getMessage());
            return response()->json([
                'success'  => false,
                'message'  => $e->getMessage(),
                'payments' => [],
            ], 500);
        }
    }

    public function verify($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found']);
        }

        $payment->update([
            'status'       => 'paid',
            'payment_date' => now(),
        ]);

        // ✅ Sync clearance status
        $this->clearanceService->updateClearance($payment->student_id);

        Notification::create([
            'user_id' => $payment->student->user_id,
            'type'    => 'payment_success',
            'message' => 'Your payment of ₱' . number_format($payment->total_amount, 2) . ' has been approved.',
            'data'    => [
                'payment_id' => $payment->id,
                'amount'     => $payment->total_amount,
                'reference'  => $payment->reference_no,
            ],
        ]);

        return response()->json(['success' => true]);
    }

    public function reject($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found']);
        }

        $payment->status = 'failed';
        $payment->save();

        // ✅ Re-check clearance in case rejection affects cleared status
        $this->clearanceService->updateClearance($payment->student_id);

        Notification::create([
            'user_id' => $payment->student->user_id,
            'type'    => 'payment_failed',
            'message' => 'Your payment of ₱' . number_format($payment->total_amount, 2) . ' was rejected. Please contact support.',
            'data'    => [
                'payment_id' => $payment->id,
                'amount'     => $payment->total_amount,
                'reference'  => $payment->reference_no,
            ],
        ]);

        return response()->json(['success' => true]);
    }
}