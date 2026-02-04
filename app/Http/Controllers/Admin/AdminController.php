<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;

class AdminController extends Controller
{
  public function payments()
{
    $payments = Payment::with('student.user')
        ->latest()
        ->paginate(10); // ✅ pagination

    $students = Student::with('user')->get(); // ✅ needed by filter dropdown

    return view('admin.payments', compact('payments', 'students'));
}
    public function students()
    {
        $students = Student::with('user')->get();

        return view('admin.students', compact('students'));
    }

    public function reports()
    {
        $payments = Payment::with('student.user')
            ->latest()
            ->take(20)
            ->get();

        return view('admin.reports', compact('payments'));
    }
}
