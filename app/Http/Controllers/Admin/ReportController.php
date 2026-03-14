<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentExport;
use App\Models\Student;
use App\Models;
use App\Models\Clearance;
use App\Models\Semester;
use App\Models\Fee;
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

   public function clearances(Request $request)
{
    $currentSemester = Semester::where('is_current', true)->first();
    $currentSchoolYear = $currentSemester?->schoolYear;

    // Base query for all students (with relationships)
    $allStudentsQuery = Student::with(['user', 'payments']);

    // Apply search if provided
    if ($request->filled('search')) {
        $search = $request->search;
        $allStudentsQuery->where(function ($q) use ($search) {
            $q->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%");
            })->orWhere('student_no', 'like', "%{$search}%");
        });
    }

    $allStudents = $allStudentsQuery->get();
    $totalStudents = $allStudents->count();

    // Determine which students are cleared
    $clearedIds = [];
    foreach ($allStudents as $student) {
        $requiredAmount = Fee::where('school_year', $currentSchoolYear?->name)
            ->where('semester', $currentSemester?->name)
            ->where(function ($q) use ($student) {
                $q->where('course', $student->course)
                  ->orWhereNull('course');
            })
            ->sum('amount');

        $totalPaid = $student->payments
            ->where('status', 'paid')
            ->sum('total_amount');

        if ($totalPaid >= $requiredAmount) {
            $clearedIds[] = $student->id;
        }
    }

    // Paginate the cleared students (10 per page)
    $clearedStudents = Student::with(['user', 'payments'])
        ->whereIn('id', $clearedIds)
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->appends($request->query()); // keep search/filter parameters

    $pendingCount = $totalStudents - $clearedStudents->total(); // use total() for paginator

    return view('admin.reports.clearances', [
        'clearances'      => $clearedStudents,   // now a paginator instance
        'pendingCount'    => $pendingCount,
        'currentSemester' => $currentSemester,
    ]);
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