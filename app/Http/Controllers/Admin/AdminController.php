<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Exports\PaymentExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request; // Added import for Request

class AdminController extends Controller
{
 

public function payments(Request $request)
{
    $query = Payment::with(['student.user', 'transaction'])->orderBy('created_at', 'desc');

    // ✅ Only filter if status is explicitly provided and not empty
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $payments = $query->paginate(10);

    // AJAX request - return only table rows and pagination
    if ($request->ajax()) {
        return view('admin.payments.partials.ajax_response', compact('payments'))->render();
    }

    // Normal request - return full page
    return view('admin.payments', compact('payments'));
}

public function students(Request $request)
{
    $search = $request->query('search');

    // Get required amount from current semester/school year fees
    $currentSemester  = \App\Models\Semester::where('is_current', true)->first();
    $currentSchoolYear = \App\Models\SchoolYear::where('is_current', true)->first();

    $requiredAmount = ($currentSemester && $currentSchoolYear)
        ? \App\Models\Fee::where('semester_id', $currentSemester->id)
                         ->where('school_year_id', $currentSchoolYear->id)
                         ->sum('amount')
        : 0;

    $students = \App\Models\Student::with(['user', 'payments'])
        ->when($search, function($query, $search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('student_no', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends(request()->query());

    foreach ($students as $student) {
        // If no fees set yet, never show as cleared
        if ($requiredAmount <= 0) {
            $student->clearance_status = 'not cleared';
            continue;
        }

        $totalPaid = $student->payments
            ->where('status', 'paid')
            ->sum('total_amount');

        $student->clearance_status =
            $totalPaid >= $requiredAmount ? 'cleared' : 'not cleared';
    }

    return view('admin.students', compact('students', 'search'));
}
    public function reports()
    {
        $payments = Payment::with('student.user')
            ->latest()
            ->take(20)
            ->get();

        return view('admin.reports', compact('payments'));
    }

       public function exportPayments()
    {
        // Get filters from query string if needed
        $filters = request()->only(['status', 'student_id', 'date_from', 'date_to']);

        return Excel::download(new PaymentExport($filters), 'payments.xlsx');
    }
    
 public function studentJson($id)
{
    $student = \App\Models\Student::with(['user', 'payments'])->findOrFail($id);

    // Live clearance calculation using current semester/school year
    $currentSemester   = \App\Models\Semester::where('is_current', true)->first();
    $currentSchoolYear = \App\Models\SchoolYear::where('is_current', true)->first();

    $requiredAmount = ($currentSemester && $currentSchoolYear)
        ? \App\Models\Fee::where('semester_id', $currentSemester->id)
                         ->where('school_year_id', $currentSchoolYear->id)
                         ->sum('amount')
        : 0;

    $totalPaid = 0;
    if ($requiredAmount > 0 && $currentSemester && $currentSchoolYear) {
        $fees = \App\Models\Fee::where('semester_id', $currentSemester->id)
                               ->where('school_year_id', $currentSchoolYear->id)
                               ->get();
        foreach ($fees as $fee) {
            $totalPaid += $fee->payments()
                ->where('student_id', $student->id)
                ->where('status', 'paid')
                ->sum('payments.total_amount');
        }
    }

    $clearanceStatus = ($requiredAmount > 0 && $totalPaid >= $requiredAmount)
        ? 'cleared'
        : 'not cleared';

    return response()->json([
        'student_no'       => $student->student_no,
        'name'             => $student->user->name,
        'email'            => $student->user->email,
        'course'           => $student->course,
        'year_level'       => $student->year_level,
        'is_confirmed'     => (bool) $student->is_confirmed,
        'clearance_status' => $clearanceStatus,
         'contact'          => $student->contact,
         'profile_picture' => $student->profile_picture
    ? asset('storage/' . $student->profile_picture)
    : null,
    ]);
}



public function confirmStudent(Student $student)
{
    $student->is_confirmed = true;
    $student->save();

    return back()->with('success', 'Student confirmed successfully.');
}
public function destroy(Student $student)
{
    // Delete linked user first
    $student->user()->delete();

    // Delete student record
    $student->delete();

    return redirect()->route('admin.students')
        ->with('success', 'Student deleted successfully.');
}
 public function newStudentsCount()
{
    $count = \App\Models\Student::where('is_confirmed', false)->count();
    return response()->json(['count' => $count]);
}

}
