<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Exports\PaymentExport;
use App\Services\ClearanceService;
use App\Services\AuditLogger;      // ✅ added
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Mail\StudentVerified;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\StudentDeclined;
use App\Mail\StudentDeleted;

class AdminController extends Controller
{
    protected $clearanceService;
    protected $auditLogger;          // ✅ added

    public function __construct(ClearanceService $clearanceService, AuditLogger $auditLogger)
    {
        $this->clearanceService = $clearanceService;
        $this->auditLogger      = $auditLogger;
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
            'profile_picture' => $student->profile_picture ?: null,
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

    // ✅ Confirm student with audit log
    public function confirmStudent(Student $student)
    {
        $oldConfirmed = $student->is_confirmed;
        $student->is_confirmed = true;
        $student->save();

        // Audit log for the confirmation action
        $this->auditLogger->log(
            actionType: 'admin.student.confirm',
            module: 'Students',
            description: "Admin confirmed student #{$student->student_no} ({$student->user->name})",
            oldValue: ['is_confirmed' => $oldConfirmed],
            newValue: ['is_confirmed' => true],
            entity: $student,
            severity: 'low'
        );

        try {
            Log::info('Sending email to: ' . $student->user->email);
            Mail::to($student->user->email)->send(new StudentVerified($student));
            Log::info('Email sent successfully');
        } catch (\Exception $e) {
            Log::error('Mail failed: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Student confirmed successfully.']);
    }

    // ✅ Delete student with audit log (includes reason)
    public function destroy(Student $student)
    {
        $reason       = request('reason') ?: 'No reason provided.';
        $studentName  = $student->user->name;
        $studentNo    = $student->student_no;
        $studentEmail = $student->user->email;

        // Audit log before deletion (capture the full record)
        $this->auditLogger->log(
            actionType: 'admin.student.delete',
            module: 'Students',
            description: "Admin deleted student #{$studentNo} ({$studentName}). Reason: {$reason}",
            oldValue: $student->toArray(),
            newValue: null,
            entity: $student,
            severity: 'medium'
        );

        // Send email
        try {
            Mail::to($studentEmail)->send(new StudentDeleted($studentName, $studentNo, $reason));
            Log::info('Delete email sent to: ' . $studentEmail);
        } catch (\Exception $e) {
            Log::error('Delete mail failed: ' . $e->getMessage());
        }

        $student->user()->delete();
        $student->delete();

        return response()->json(['success' => true, 'message' => 'Student deleted successfully.']);
    }

    // ✅ Decline student with audit log (includes reason)
    public function declineStudent(Student $student)
    {
        $reason      = request('reason') ?: 'No reason provided.';
        $email       = $student->user->email;
        $studentCopy = clone $student;
        $studentCopy->setRelation('user', $student->user);

        // Audit log before deletion (capture the reason and the record)
        $this->auditLogger->log(
            actionType: 'admin.student.decline',
            module: 'Students',
            description: "Admin declined student #{$student->student_no} ({$student->user->name}). Reason: {$reason}",
            oldValue: $student->toArray(),
            newValue: null,
            entity: $student,
            severity: 'medium'
        );

        // Send email
        try {
            Mail::to($email)->send(new StudentDeclined($studentCopy, $reason));
            Log::info('Decline email sent to: ' . $email);
        } catch (\Exception $e) {
            Log::error('Decline mail failed: ' . $e->getMessage());
        }

        $student->user()->delete();
        $student->delete();

        return response()->json(['success' => true, 'message' => 'Student declined and removed.']);
    }

    public function newStudentsCount(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('admin.dashboard');
        }
        $count = Student::where('is_confirmed', false)->count();
        return response()->json(['count' => $count]);
    }

    public function pendingPaymentsCount(Request $request)
    {
        if (!$request->ajax() && !$request->wantsJson()) {
            return redirect()->route('admin.dashboard');
        }
        $count = Payment::whereIn('status', ['pending', 'processing'])->count();
        return response()->json(['count' => $count]);
    }
}