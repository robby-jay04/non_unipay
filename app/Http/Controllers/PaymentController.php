<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
  public function index(Request $request)
{
    $query = Payment::with('student.user')->orderBy('id', 'desc');

    // Apply status filter if provided
    if ($request->status) {
        $query->where('status', $request->status);
    }

    $payments = $query->paginate(10);

    // If AJAX request, return only partial rows
    if ($request->ajax()) {
        return view('admin.payments.partials.payments_rows', compact('payments'))->render();
    }

    // Normal page load
    return view('admin.payments.index', compact('payments'));
}




    public function show($id)
{
    $payment = Payment::with('student.user')->findOrFail($id);
    
    // Return a partial view to load in the modal
    return view('admin.payments.partials.view', compact('payment'));
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
            'payments' => $payments,
            'total_paid' => $payments->where('status', 'paid')->sum('total_amount'),
        ]);
    }
}
