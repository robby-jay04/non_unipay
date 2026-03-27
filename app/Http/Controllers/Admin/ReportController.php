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
use App\Models\Fee;

class ReportController extends Controller
{
    public function index()
    {
        $payments = Payment::with('student.user')
            ->whereNotNull('payment_date')
            ->orderBy('payment_date', 'asc')
            ->paginate(10);

        return view('admin.reports', compact('payments'));
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
            'payments-' . now()->format('Y-m-d') . '.xlsx'
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

    public function downloadExcel()
    {
        $filters = ['sort_by_payment_date' => true];

        return Excel::download(
            new PaymentExport($filters),
            'payments-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

   public function clearances(Request $request)
{
    $search = $request->get('search');
    $course = $request->get('course');  // new filter

    $query = Student::with('user')
        ->where('clearance_status', 'cleared');

    if ($course) {
        $query->where('course', $course);
    }

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('student_no', 'like', "%{$search}%")
              ->orWhereHas('user', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%");
              });
        });
    }

    $clearances = $query->orderBy('student_no')->paginate(20)->appends(request()->only(['search', 'course']));
    $pendingCount = Student::where('clearance_status', 'pending')->count();
    $currentSemester = Semester::where('is_current', true)->with('schoolYear')->first();

    // List of available courses (you may fetch from DB)
    $courses = ['BSIT', 'BEED', 'BSED', 'BSCRIM', 'BSOA', 'BSPOLSCI'];

    return view('admin.reports.clearances', compact('clearances', 'pendingCount', 'currentSemester', 'courses'));
}
    public function clearancesPdf(Request $request)
{
    $search = $request->get('search');
    $course = $request->get('course');

    $query = Student::with('user')
        ->where('clearance_status', 'cleared');

    if ($course) {
        $query->where('course', $course);
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
        'generated_at'    => now()->format('F d, Y H:i'),
    ];

    $pdf = Pdf::loadView('admin.reports.clearances_pdf', $data);
    return $pdf->download('clearance_report_' . now()->format('Ymd_His') . '.pdf');
}
}