<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Clearance;
use App\Models\Student;
use App\Models\Fee;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_revenue' => Payment::paid()->sum('total_amount'),
            'pending_payments' => Payment::pending()->count(),
            'cleared_students' => Student::with('payments')->get()
    ->where('clearance_status', 'cleared')
    ->count(),
            'total_students' => Student::count(),
            'recent_payments' => Payment::with('student.user')
                ->latest()
                ->take(10)
                ->get(),
            'monthly_revenue' => Payment::paid()
                ->whereMonth('payment_date', now()->month)
                ->sum('total_amount'),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function apiStats()
    {
        return response()->json([
            'total_revenue' => Payment::paid()->sum('total_amount'),
            'pending_payments' => Payment::pending()->count(),
            'cleared_students' => Clearance::cleared()->count(),
            'total_students' => Student::count(),
            'total_fees' => Fee::sum('amount'),
        ]);
    }
}