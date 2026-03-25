<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Exports\PaymentExport;
use App\Services\ClearanceService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $clearanceService;

    public function __construct(ClearanceService $clearanceService)
    {
        $this->clearanceService = $clearanceService;
    }

    public function payments(Request $request)
    {
        $query = Payment::with(['student.user', 'transaction'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(10);

        if ($request->ajax()) {
            return view('admin.payments.partials.ajax_response', compact('payments'))->render();
        }

        return view('admin.payments', compact('payments'));
    }

   public function students(Request $request)
{
    $query = Student::with(['user', 'payments']);

    // Apply search filter (name, email, student_no)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('user', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('student_no', 'like', "%{$search}%");
        });
    }

    // Apply course filter
    if ($request->filled('course')) {
        $query->where('course', $request->course);
    }

    // Order and paginate (preserve query parameters)
    $students = $query->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->appends($request->only(['search', 'course']));

    // Get distinct courses for the dropdown
    $courses = Student::distinct()->pluck('course')->filter()->values();

    return view('admin.students', compact('students', 'courses'));
}

    public function studentJson(Student $student)
    {
        $student->load('user');

        return response()->json([
            'student_no'       => $student->student_no,
            'name'             => $student->user->name,
            'email'            => $student->user->email,
            'course'           => $student->course,
            'year_level'       => $student->year_level,
            'contact'          => $student->contact,
            'is_confirmed'     => (bool) $student->is_confirmed,
            'clearance_status' => $student->clearance_status, // ✅ reads from synced column
            'profile_picture'  => $student->profile_picture
                                    ? asset('storage/' . $student->profile_picture)
                                    : null,
        ]);
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
        $filters = request()->only(['status', 'student_id', 'date_from', 'date_to']);
        return Excel::download(new PaymentExport($filters), 'payments.xlsx');
    }

    public function confirmStudent(Student $student)
    {
        $student->is_confirmed = true;
        $student->save();

        return back()->with('success', 'Student confirmed successfully.');
    }

    public function destroy(Student $student)
    {
        $student->user()->delete();
        $student->delete();

        return redirect()->route('admin.students')
            ->with('success', 'Student deleted successfully.');
    }

    public function newStudentsCount()
    {
        $count = Student::where('is_confirmed', false)->count();
        return response()->json(['count' => $count]);
    }
}