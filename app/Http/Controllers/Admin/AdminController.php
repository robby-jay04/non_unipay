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
    public function show(Payment $payment)
{
    $payment->load(['student.user', 'fees', 'semester', 'schoolYear', 'examPeriod']);
    return view('admin.payments.partials.payment_details', compact('payment'));
}

    public function payments(Request $request)
{
    $query = Payment::with(['student.user', 'fees', 'semester', 'schoolYear', 'examPeriod'])
                    ->orderBy('created_at', 'desc');

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('student.user', function ($q2) use ($search) {
                $q2->where('name', 'like', '%' . $search . '%');
            })->orWhere('reference_no', 'like', '%' . $search . '%');
        });
    }

    $payments = $query->paginate(10)->withQueryString();

    // Check for ajax=1 param OR standard AJAX headers
    if ($request->filled('ajax') || $request->ajax() || $request->wantsJson()) {
        return response()->json([
            'rows'       => view('admin.payments.partials.payments_rows', compact('payments'))->render(),
            'pagination' => view('admin.payments.partials.payments_pagination', compact('payments'))->render(),
        ]);
    }

    return view('admin.payments', compact('payments'));
}

    public function students(Request $request)
    {
        $query = Student::with(['user', 'payments']);

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

        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }

        if ($request->filled('clearance_status')) {
            $query->where('clearance_status', $request->clearance_status);
        }

        $students = $query->orderBy('created_at', 'desc')
                          ->paginate(10)
                          ->appends($request->only(['search', 'course', 'year_level', 'clearance_status']));

        $courses          = Student::distinct()->pluck('course')->filter()->values();
        $yearLevels       = Student::distinct()->pluck('year_level')->filter()->sort()->values();
        $clearanceStatuses = ['cleared', 'not_cleared'];

        return view('admin.students', compact('students', 'courses', 'yearLevels', 'clearanceStatuses'));
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
            'clearance_status' => $student->clearance_status,
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

    public function pendingPaymentsCount()
    {
        $count = Payment::whereIn('status', ['pending', 'processing'])->count();
        return response()->json(['count' => $count]);
    }
}