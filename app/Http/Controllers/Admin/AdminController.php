<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Exports\PaymentsExport;
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

    $students = \App\Models\Student::with('user')
        ->when($search, function($query, $search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('student_no', 'like', "%{$search}%");
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends(request()->query()); // preserve search query on pagination links

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

        return Excel::download(new PaymentsExport($filters), 'payments.xlsx');
    }public function studentJson($id)
{
    $student = \App\Models\Student::with('user')->findOrFail($id);

    return response()->json([
        'student_no' => $student->student_no,
        'name' => $student->user->name,
        'email' => $student->user->email,
        'course' => $student->course,
        'year_level' => $student->year_level,
        'clearance_status' => ucfirst($student->clearance_status),
    ]);
}

}
