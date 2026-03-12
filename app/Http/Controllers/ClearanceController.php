<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clearance;
use App\Services\ClearanceService;
use App\Models\Semester;
use App\Models\Fee;
use App\Models\Student;
use App\Models\SchoolYear;


class ClearanceController extends Controller
{
    protected $clearanceService;

    public function __construct(ClearanceService $clearanceService)
    {
        $this->clearanceService = $clearanceService;
    }
    public function index()
    {
        $currentSemester = Semester::where('is_current', true)->first();
        $clearances = Student::with('user')->get(); // or your existing query

        return view('admin.clearance.report', compact('clearances', 'currentSemester'));
    }



public function show(Request $request)
{
    $student = $request->user()->student;

    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    $currentSemester   = Semester::where('is_current', true)->first();
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();

    if (!$currentSemester || !$currentSchoolYear) {
        return response()->json([
            'status'      => 'no_fees',
            'message'     => 'No active semester or school year set.',
            'exam_period' => null,
            'total_fees'  => 0,
            'total_paid'  => 0,
            'remaining'   => 0,
        ]);
    }

    // ✅ Only get fees for this student's course OR fees that apply to all (NULL course)
    $applicableFees = Fee::where('school_year_id', $currentSchoolYear->id)
        ->where('semester_id', $currentSemester->id)
        ->where(function ($query) use ($student) {
            $query->where('course', $student->course)
                  ->orWhereNull('course');
        })
        ->get();

    $totalFees = $applicableFees->sum('amount');

    // No fees configured for this student's course in current period
    if ($totalFees <= 0) {
        return response()->json([
            'status'      => 'no_fees',
            'message'     => 'No fees assigned for this period.',
            'exam_period' => $currentSemester->name,
            'total_fees'  => 0,
            'total_paid'  => 0,
            'remaining'   => 0,
        ]);
    }

    // ✅ Sum payments made by this student for applicable fees only
    $totalPaid = 0;
    foreach ($applicableFees as $fee) {
        $totalPaid += $fee->payments()
            ->where('student_id', $student->id)
            ->where('status', 'paid')
            ->sum('payments.total_amount');
    }

    $isCleared = $totalPaid >= $totalFees;

    return response()->json([
        'status'      => $isCleared ? 'cleared' : 'pending',
        'exam_period' => $currentSemester->name,
        'total_fees'  => $totalFees,
        'total_paid'  => $totalPaid,
        'remaining'   => max($totalFees - $totalPaid, 0),
    ]);
}
   public function updateClearance($studentId)
    {
        try {
            $clearance = $this->clearanceService->updateClearance($studentId);

            return response()->json([
                'message' => 'Clearance updated successfully',
                'clearance' => $clearance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update clearance',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    
}
