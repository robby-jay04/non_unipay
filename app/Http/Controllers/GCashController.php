<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GCashService;
use App\Services\ClearanceService;
use App\Models\Payment;
use App\Models\Transaction;

class GCashController extends Controller
{
    protected $gcashService;
    protected $clearanceService;

    public function __construct(GCashService $gcashService, ClearanceService $clearanceService)
    {
        $this->gcashService = $gcashService;
        $this->clearanceService = $clearanceService;
    }

    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        // Create payment record
        $payment = Payment::create([
            'student_id' => $student->id,
            'total_amount' => $validated['amount'],
            'status' => 'pending',
        ]);

        // Generate reference number
        $referenceNo = 'NUP-' . time() . '-' . $student->id;

        try {
            // Call GCash API
            $gcashResponse = $this->gcashService->createPayment(
                $validated['amount'],
                $referenceNo
            );

            // Store transaction
            Transaction::create([
                'payment_id' => $payment->id,
                'reference_no' => $referenceNo,
                'gateway_response' => $gcashResponse,
                'status' => 'initiated',
            ]);

            return response()->json([
                'payment_url' => $gcashResponse['payment_url'] ?? '#',
                'reference_no' => $referenceNo,
                'payment_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed']);

            return response()->json([
                'message' => 'Payment initiation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function paymentCallback(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $referenceNo = $request->input('reference_no');

        try {
            // Verify payment with GCash
            $verification = $this->gcashService->verifyPayment($transactionId);

            $transaction = Transaction::where('reference_no', $referenceNo)->firstOrFail();
            $payment = $transaction->payment;

            if ($verification['status'] === 'success') {
                // Update payment status
                $payment->update([
                    'status' => 'paid',
                    'payment_date' => now(),
                ]);

                $transaction->update([
                    'status' => 'completed',
                    'gateway_response' => $verification,
                ]);

                // Update clearance status
                $this->clearanceService->updateClearance($payment->student_id);

                return response()->json([
                    'message' => 'Payment successful',
                    'payment_id' => $payment->id,
                ]);
            }

            // Payment failed
            $payment->update(['status' => 'failed']);
            $transaction->update(['status' => 'failed']);

            return response()->json(['message' => 'Payment verification failed'], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payment callback error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkStatus($referenceNo)
    {
        $transaction = Transaction::where('reference_no', $referenceNo)
            ->with('payment')
            ->firstOrFail();

        return response()->json([
            'reference_no' => $transaction->reference_no,
            'status' => $transaction->status,
            'payment_status' => $transaction->payment->status,
            'amount' => $transaction->payment->total_amount,
        ]);
    }
}