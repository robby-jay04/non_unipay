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
use Illuminate\Support\Facades\DB;
use App\Models\Fee;
use App\Models\ExamPeriod;

class PaymentController extends Controller
{
    protected $clearanceService;

    public function __construct(ClearanceService $clearanceService)
    {
        $this->clearanceService = $clearanceService;
    }

    public function index(Request $request)
    {
        $query = Payment::with(['student.user', 'fees', 'semester', 'schoolYear', 'examPeriod'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(10);

        if ($request->expectsJson()) {
            return response()->json([
                'rows'       => view('admin.payments.partials.payments_rows', compact('payments'))->render(),
                'pagination' => view('admin.payments.partials.payments_pagination', compact('payments'))->render(),
            ]);
        }

        return view('admin.payments', compact('payments'));
    }

 public function show(Payment $payment)
{
    $payment->load(['student.user', 'fees', 'semester', 'schoolYear', 'examPeriod']);
    return view('admin.payments.partials.view', compact('payment'));
}

    public function initiate(Request $request)
    {
        $request->validate([
            'amount'    => 'required|numeric|min:100',
            'fee_ids'   => 'required|array',
            'fee_ids.*' => 'exists:fees,id',
        ]);

        $student      = $request->user()->student;
        $selectedFees = Fee::whereIn('id', $request->fee_ids)->get();

        // ── Per-fee already-paid amounts from confirmed payments ──────────────
        $paidPerFee = DB::table('fee_payment')
            ->join('payments', 'payments.id', '=', 'fee_payment.payment_id')
            ->where('payments.student_id', $student->id)
            ->where('payments.status', 'paid')
            ->whereIn('fee_payment.fee_id', $request->fee_ids)
            ->groupBy('fee_payment.fee_id')
            ->select('fee_payment.fee_id', DB::raw('SUM(fee_payment.amount) as total_paid'))
            ->pluck('total_paid', 'fee_id')
            ->map(fn($v) => (float) $v);

        // ── Expected total = sum of remaining balances per fee ────────────────
        $calculatedTotal = $selectedFees->sum(function ($fee) use ($paidPerFee) {
            $alreadyPaid = $paidPerFee[$fee->id] ?? 0;
            return max((float) $fee->amount - $alreadyPaid, 0);
        });

        if (abs($calculatedTotal - (float) $request->amount) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Total amount mismatch',
            ], 400);
        }

        $amountPesos = floatval($request->amount);

        if ($amountPesos > 100000) {
            return response()->json([
                'success' => false,
                'message' => 'Amount exceeds PayMongo maximum of ₱100,000',
            ]);
        }

        $firstFee     = $selectedFees->first();
        $semesterId   = $firstFee->semester_id
            ?? optional(\App\Models\Semester::where('is_current', true)->first())->id;
        $schoolYearId = $firstFee->school_year_id
            ?? optional(\App\Models\SchoolYear::where('is_current', true)->first())->id;

        $currentExamPeriod = null;
        if ($semesterId) {
            $currentExamPeriod = ExamPeriod::where('semester_id', $semesterId)
                                ->where('is_current', true)
                                ->first();
        }

        Log::info('Initiate payment debug', [
            'student_id'     => $student->id,
            'semester_id'    => $semesterId,
            'school_year_id' => $schoolYearId,
            'fee_ids'        => $request->fee_ids,
            'paid_per_fee'   => $paidPerFee,
            'calculated_total' => $calculatedTotal,
        ]);

        if ($semesterId && $schoolYearId) {
            // Remaining balance = all fees total minus all confirmed payments for this semester
            $semesterTotal = Fee::where('semester_id', $semesterId)
                ->where('school_year_id', $schoolYearId)
                ->sum('amount');

            $totalPaid = DB::table('fee_payment')
                ->join('payments', 'payments.id', '=', 'fee_payment.payment_id')
                ->where('payments.student_id', $student->id)
                ->where('payments.semester_id', $semesterId)
                ->where('payments.school_year_id', $schoolYearId)
                ->where('payments.status', 'paid')
                ->sum('fee_payment.amount'); // ← use pivot amount, not payment total

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

            // Clean up stale pending payments older than 60 minutes
            Payment::where('student_id', $student->id)
                ->where('semester_id', $semesterId)
                ->where('school_year_id', $schoolYearId)
                ->whereIn('status', ['pending', 'processing'])
                ->where('created_at', '<', now()->subMinutes(60))
                ->update(['status' => 'failed']);
        }

        // Block if any selected fee is already in a recent pending payment
        $existingPending = Payment::where('student_id', $student->id)
            ->where('status', 'pending')
            ->where('created_at', '>', now()->subMinutes(30))
            ->whereHas('fees', function ($query) use ($request) {
                $query->whereIn('fees.id', $request->fee_ids);
            })
            ->exists();

        if ($existingPending) {
            return response()->json([
                'success' => false,
                'message' => 'One or more selected fees are already pending payment. Please wait for the previous payment to complete.',
            ], 400);
        }

        // Create payment record
        $payment = Payment::create([
            'student_id'     => $student->id,
            'total_amount'   => $amountPesos,
            'status'         => 'pending',
            'payment_method' => 'gcash',
            'reference_no'   => 'PAY-' . strtoupper(Str::random(10)),
            'semester_id'    => $semesterId,
            'school_year_id' => $schoolYearId,
            'exam_period_id' => $currentExamPeriod ? $currentExamPeriod->id : null,
        ]);

        // ── Attach fees with REMAINING amount per fee (not full amount) ───────
        foreach ($selectedFees as $fee) {
            $alreadyPaid = $paidPerFee[$fee->id] ?? 0;
            $amountDue   = max((float) $fee->amount - $alreadyPaid, 0);
            $payment->fees()->attach($fee->id, ['amount' => $amountDue]);
        }

        try {
            $source = Paymongo::source()->create([
                'type'     => 'gcash',
                'amount'   => $amountPesos,
                'currency' => 'PHP',
                'redirect' => [
                    'success' => route('payment.success', ['payment_id' => $payment->id]),
                    'failed'  => route('payment.failed',  ['payment_id' => $payment->id]),
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

    public function status($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['status' => 'not_found'], 404);
        }

        $user = auth()->user();
        if ($payment->student_id !== $user->student->id && !$user->is_admin) {
            return response()->json(['status' => 'unauthorized'], 403);
        }

        return response()->json([
            'status'       => $payment->status,
            'payment_id'   => $payment->id,
            'reference_no' => $payment->reference_no,
        ]);
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

    public function success(Request $request)
    {
        Log::info('Payment success redirect params', $request->query());

        $payment = null;

        if ($request->filled('payment_id')) {
            $payment = Payment::find($request->query('payment_id'));
        }

        if (!$payment) {
            $sourceId = $request->query('source_id')
                     ?? $request->query('id')
                     ?? $request->query('source');

            if ($sourceId) {
                $payment = Payment::where('paymongo_source_id', $sourceId)->latest()->first();
            }
        }

        if (!$payment && auth()->check()) {
            $student = auth()->user()->student ?? null;
            if ($student) {
                $payment = Payment::where('student_id', $student->id)
                    ->whereIn('status', ['paid', 'pending'])
                    ->latest()
                    ->first();
            }
        }

        $displayDate = null;
        if ($payment) {
            $displayDate = $payment->payment_date
                ? \Carbon\Carbon::parse($payment->payment_date)->format('d F Y h:i A')
                : \Carbon\Carbon::parse($payment->created_at)->format('d F Y h:i A');
        } else {
            $displayDate = now()->format('d F Y h:i A');
        }

        return view('payments.success', [
            'message'   => 'Payment completed successfully!',
            'reference' => $payment?->reference_no ?? 'N/A',
            'amount'    => $payment ? 'PHP ' . number_format($payment->total_amount, 2) : 'PHP 0.00',
            'date'      => $displayDate,
        ]);
    }

    public function failed()
    {
        return view('payments.failed', [
            'message' => 'Payment failed. Please try again.',
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
                    'fees' => function ($query) {
                        $query->withPivot('amount'); // ← load what was actually paid per fee
                    },
                    'fees.semester',
                    'fees.schoolYear',
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // ── Expose pivot amount as pivot_amount on each fee ───────────────
            $payments->each(function ($payment) {
                $payment->fees->each(function ($fee) {
                    $fee->pivot_amount = $fee->pivot->amount ?? null;
                });
            });

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

    public function verify(Payment $payment)
{
    try {
        $payment->load('student.user');

        if ($payment->status === 'paid') {
            return response()->json(['success' => true, 'message' => 'Already verified']);
        }

        $payment->update([
            'status'       => 'paid',
            'payment_date' => now(),
        ]);

        try {
            $this->clearanceService->updateClearance($payment->student_id);
        } catch (\Exception $e) {
            Log::error('Clearance update failed on verify: ' . $e->getMessage());
        }

        try {
            Notification::create([
                'user_id' => $payment->student->user_id,
                'type'    => 'payment_success',
                'title'   => 'Payment Approved',
                'message' => 'Your payment of ₱' . number_format($payment->total_amount, 2) . ' has been approved.',
                'data'    => json_encode([
                    'payment_id' => $payment->id,
                    'amount'     => $payment->total_amount,
                    'reference'  => $payment->reference_no,
                ]),
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Notification failed on verify: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('Payment verify 500: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function reject(Payment $payment)
{
    try {
        $payment->load('student.user');

        if ($payment->status === 'failed') {
            return response()->json(['success' => true, 'message' => 'Already rejected']);
        }

        $payment->update(['status' => 'failed']);

        try {
            $this->clearanceService->updateClearance($payment->student_id);
        } catch (\Exception $e) {
            Log::error('Clearance update failed on reject: ' . $e->getMessage());
        }

        try {
            Notification::create([
                'user_id' => $payment->student->user_id,
                'type'    => 'payment_failed',
                'title'   => 'Payment Rejected',
                'message' => 'Your payment of ₱' . number_format($payment->total_amount, 2) . ' was rejected. Please contact support.',
                'data'    => json_encode([
                    'payment_id' => $payment->id,
                    'amount'     => $payment->total_amount,
                    'reference'  => $payment->reference_no,
                ]),
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Notification failed on reject: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        Log::error('Payment reject 500: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}