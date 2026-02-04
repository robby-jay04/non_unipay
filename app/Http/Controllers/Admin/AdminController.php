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
            ->get();

        return view('admin.payments', compact('payments'));
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
