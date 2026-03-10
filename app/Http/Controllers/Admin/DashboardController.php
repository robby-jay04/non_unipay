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
    // Generate last 7 days labels and revenue data
    $revenueLabels = [];
    $revenueData = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $revenueLabels[] = $date->format('D'); // e.g., Mon, Tue
        $revenueData[] = Payment::paid()
            ->whereDate('payment_date', $date)
            ->sum('total_amount');
    }

    // Top student (highest total paid)
    $topStudent = Student::withSum(['payments' => function ($query) {
            $query->where('status', 'paid');
        }], 'total_amount')
        ->whereHas('payments', function ($query) {
            $query->where('status', 'paid');
        })
        ->orderByDesc('payments_sum_total_amount')
        ->first();
    $topStudentName = $topStudent?->user->name ?? 'N/A';

    // Recent cleared students
    $recentCleared = Clearance::cleared()
        ->with('student.user')
        ->latest()
        ->limit(5)
        ->get();

    $stats = [
        // Existing keys
        'total_revenue'      => Payment::paid()->sum('total_amount'),
        'pending_payments'   => Payment::pending()->count(),
        'cleared_students'   => Student::with('payments')->get()
                                    ->where('clearance_status', 'cleared')
                                    ->count(),
        'total_students'     => Student::count(),
        'recent_payments'    => Payment::with('student.user')
                                    ->latest()
                                    ->take(10)
                                    ->get(),
        'monthly_revenue'    => Payment::paid()
                                    ->whereMonth('payment_date', now()->month)
                                    ->sum('total_amount'),

        // New mini stats
        'today_revenue'      => Payment::paid()
                                    ->whereDate('payment_date', today())
                                    ->sum('total_amount'),
        'average_payment'    => Payment::paid()->avg('total_amount') ?? 0,
        'top_student'        => $topStudentName,

        // Chart data
        'revenue_labels'     => $revenueLabels,
        'revenue_data'       => $revenueData,

        // Status counts for pie chart
        'paid_count'         => Payment::paid()->count(),
        'pending_count'      => Payment::pending()->count(),
        'failed_count'       => Payment::where('status', 'failed')->count(),

        // Recent clearances
        'recent_cleared'     => $recentCleared,
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
   public function pendingPaymentsCount()
{
    $count = \App\Models\Payment::where('status', 'pending')->count();
    return response()->json(['count' => $count]);
}
}