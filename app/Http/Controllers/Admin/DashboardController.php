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
        // =========================
        // FIX #3: Revenue (OPTIMIZED - 1 QUERY)
        // =========================
        $revenueRaw = Payment::paid()
            ->whereDate('payment_date', '>=', now()->subDays(89))
            ->selectRaw('DATE(payment_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $revenueLabels = [];
        $revenueData   = [];

        for ($i = 89; $i >= 0; $i--) {
            $dateKey = now()->subDays($i)->format('Y-m-d');

            $revenueLabels[] = now()->subDays($i)->format('M d');
            $revenueData[]   = $revenueRaw[$dateKey] ?? 0;
        }

        // =========================
        // Top Student
        // =========================
        $topStudent = Student::withSum(['payments' => function ($query) {
                $query->where('status', 'paid');
            }], 'total_amount')
            ->whereHas('payments', function ($query) {
                $query->where('status', 'paid');
            })
            ->orderByDesc('payments_sum_total_amount')
            ->first();

        $topStudentName = $topStudent?->user->name ?? 'N/A';

        // =========================
        // FIX #2: Recent Cleared (NO N+1)
        // =========================
        $students = Student::with([
            'user',
            'payments' => function ($q) {
                $q->where('status', 'paid')
                  ->latest('payment_date');
            }
        ])->get();

        $recentCleared = $students
            ->filter(fn ($student) => $student->clearance_status === 'cleared')
            ->map(function ($student) {

                $latestPayment = $student->payments->first();

                return (object) [
                    'student'    => $student,
                    'created_at' => $latestPayment?->payment_date ?? $student->updated_at,
                ];
            })
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        // =========================
        // FIX #1: Basic stats cleanup
        // =========================
        $stats = [
            'total_revenue'    => Payment::paid()->sum('total_amount'),
            'pending_payments' => Payment::pending()->count(),

            // FIX #1 applied
            'cleared_students' => Student::where('clearance_status', 'cleared')->count(),

            'total_students'   => Student::count(),

            'recent_payments'  => Payment::with('student.user')
                                        ->latest()
                                        ->take(5)
                                        ->get(),

            'monthly_revenue'  => Payment::paid()
                                        ->whereMonth('payment_date', now()->month)
                                        ->sum('total_amount'),

            'today_revenue'    => Payment::paid()
                                        ->whereDate('payment_date', today())
                                        ->sum('total_amount'),

            'average_payment'  => Payment::paid()->avg('total_amount') ?? 0,

            'top_student'      => $topStudentName,

            // Charts
            'revenue_labels'   => $revenueLabels,
            'revenue_data'     => $revenueData,

            // Status counts (still OK for now)
            'paid_count'       => Payment::paid()->count(),
            'pending_count'    => Payment::pending()->count(),
            'failed_count'     => Payment::where('status', 'failed')->count(),

            // Recent cleared
            'recent_cleared'   => $recentCleared,
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function apiStats()
    {
        return response()->json([
            'total_revenue'    => Payment::paid()->sum('total_amount'),
            'pending_payments' => Payment::pending()->count(),
            'cleared_students' => Clearance::cleared()->count(),
            'total_students'   => Student::count(),
            'total_fees'       => Fee::sum('amount'),
        ]);
    }

    public function pendingPaymentsCount()
    {
        $count = Payment::where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    }
}