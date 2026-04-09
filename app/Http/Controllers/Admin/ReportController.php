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
use App\Models\Semester;
use App\Models\Fee;;
use Carbon\Carbon;
use Maatwebsite\Excel\Excel as ExcelWriter;


class ReportController extends Controller
{


public function index()
{
    // Existing payments query
    $payments = Payment::with('student.user')
        ->whereNotNull('payment_date')
        ->orderBy('payment_date', 'asc')
        ->paginate(10);

    // ─── Statistics for charts ─────────────────────────────────
    // 1. Clearance status counts (cleared vs pending)
    $clearedCount = Student::where('clearance_status', 'cleared')->count();
    $pendingCount = Student::where('clearance_status', 'pending')->count();

    // 2. Payment status distribution (paid, pending, failed)
    $paidCount = Payment::where('status', 'paid')->count();
    $pendingPaymentCount = Payment::where('status', 'pending')->count();
    $failedCount = Payment::where('status', 'failed')->count();

    // 3. Monthly payment totals (last 12 months)
    $monthlyData = Payment::where('status', 'paid')
        ->whereNotNull('payment_date')
        ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(total_amount) as total')
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->limit(12)
        ->get();

    $months = $monthlyData->pluck('month');
    $totals = $monthlyData->pluck('total');

    // Pass all to view
    return view('admin.reports', compact(
        'payments',
        'clearedCount',
        'pendingCount',
        'paidCount',
        'pendingPaymentCount',
        'failedCount',
        'months',
        'totals'
    ));
}

    public function paymentReport(Request $request)
    {
        $query = Payment::with('student.user')
            ->whereNotNull('payment_date')
            ->orderBy('payment_date', 'asc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from_date) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        $payments = $query->get();

        return view('admin.reports.payments', compact('payments'));
    }

   public function exportExcel(Request $request)
{
    $filters = array_merge($request->all(), ['sort_by_payment_date' => true]);

    return Excel::download(
        new PaymentExport($filters),
        'payments-' . now()->format('Y-m-d') . '.xlsx',
        ExcelWriter::XLSX   // ← add this
    );
}

public function downloadExcel()
{
    $filters = ['sort_by_payment_date' => true];

    return Excel::download(
        new PaymentExport($filters),
        'payments-' . now()->format('Y-m-d') . '.xlsx',
        ExcelWriter::XLSX   // ← add this too
    );
}
    public function downloadPdf(Request $request)
    {
        $query = Payment::with('student.user')
            ->whereNotNull('payment_date')
            ->orderBy('payment_date', 'asc');

        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->get();

        $groupedPayments = $payments->groupBy(function ($payment) {
            return $payment->payment_date->format('Y-m-d');
        })->sortKeys();

        $pdf = Pdf::loadView('admin.reports.payments_pdf', compact('payments', 'groupedPayments'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('payment-report-' . now()->format('Y-m-d') . '.pdf');
    }

   

   public function clearances(Request $request)
{
    $search = $request->get('search');
    $course = $request->get('course');
    $yearLevel = $request->get('year_level');

    $query = Student::with('user')
        ->where('clearance_status', 'cleared');

    if ($course) {
        $query->where('course', $course);
    }

    if ($yearLevel) {
        $query->where('year_level', $yearLevel);
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('student_no', 'like', "%{$search}%")
              ->orWhereHas('user', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%");
              });
        });
    }

    $clearances = $query->orderBy('student_no')->paginate(20);
    $currentSemester = Semester::where('is_current', true)->with('schoolYear')->first();

    // Check if it's an AJAX request
    if ($request->ajax()) {
        return response()->json([
            'rows' => view('admin.reports.partials.clearance_rows', compact('clearances', 'currentSemester'))->render(),
            'pagination' => $clearances->appends($request->only(['search', 'course', 'year_level']))->links('pagination::no-summary')->render(),
            'totalCleared' => $clearances->total()
        ]);
    }

    $pendingCount = Student::where('clearance_status', 'pending')->count();
    $courses = Student::distinct()->pluck('course')->filter()->values();
    $yearLevels = Student::distinct()->pluck('year_level')->filter()->sort()->values();

    return view('admin.reports.clearances', compact('clearances', 'pendingCount', 'currentSemester', 'courses', 'yearLevels'));
}
   public function clearancesPdf(Request $request)
{
    $search = $request->get('search');
    $course = $request->get('course');
    $yearLevel = $request->get('year_level'); // new

    $query = Student::with('user')
        ->where('clearance_status', 'cleared');

    if ($course) {
        $query->where('course', $course);
    }

    if ($yearLevel) {
        $query->where('year_level', $yearLevel);
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('student_no', 'like', "%{$search}%")
              ->orWhereHas('user', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%");
              });
        });
    }

    $clearances = $query->orderBy('student_no')->get();

    $currentSemester = Semester::where('is_current', true)->with('schoolYear')->first();

    $data = [
        'clearances'      => $clearances,
        'currentSemester' => $currentSemester,
        'search'          => $search,
        'course'          => $course,
        'year_level'      => $yearLevel,
        'generated_at'    => now()->format('F d, Y H:i'),
    ];

    $pdf = Pdf::loadView('admin.reports.clearances_pdf', $data);
    return $pdf->download('clearance_report_' . now()->format('Ymd_His') . '.pdf');
}
}