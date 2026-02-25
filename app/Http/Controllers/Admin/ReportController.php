<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentExport;

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

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $payments = $query->get();

        return view('admin.reports.payments', compact('payments'));
    }

    public function exportPDF(Request $request)
    {
        $payments = Payment::with('student.user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->get();

        $pdf = PDF::loadView('admin.reports.pdf', compact('payments'));
        return $pdf->download('payment-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new PaymentExport($request->all()),
            'payments-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function clearanceReport()
    {
        $clearances = \App\Models\Clearance::with('student.user')
            ->latest()
            ->get();

        return view('admin.reports.clearances', compact('clearances'));
    }
}