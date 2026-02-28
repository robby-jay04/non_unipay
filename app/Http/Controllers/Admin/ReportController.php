<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentExport;
use App\Models\Student;
use App\Models\Clearance;

class ReportController extends Controller
{
    public function index()
    {
        $payments = Payment::with('student.user')
            ->latest()
            ->paginate(10);

        return view('admin.reports', compact('payments'));
    }

    public function paymentReport(Request $request)
    {
        $query = Payment::with('student.user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $payments = $query->get();

        return view('admin.reports.payments', compact('payments'));
    }

public function exportPDF()
{
    $pdf = PDF::loadHTML('<h1>PDF WORKING BOSS</h1>');
    return $pdf->download('test.pdf');
}
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PaymentExport($request->all()),
            'payments-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function clearances()
{
    $students = Student::with(['user', 'payments'])->get();

    $clearances = $students->filter(function ($student) {

        $requiredAmount = \App\Models\Fee::where('school_year', $student->school_year)
            ->where('semester', $student->semester)
            ->sum('amount');

        $totalPaid = $student->payments
            ->where('status', 'paid')
            ->sum('total_amount');

        return $totalPaid >= $requiredAmount;
    });

    return view('admin.reports.clearances', compact('clearances'));
}

 public function downloadPdf()
{
    $payments = Payment::with('student.user')->get();

    $pdf = Pdf::loadView('admin.reports.payments_pdf', compact('payments'));

    return $pdf->download('payment-report-' . now()->format('Y-m-d') . '.pdf');
}

    public function downloadExcel()
    {
        return Excel::download(new PaymentExport, 'payments.xlsx');
    }
}