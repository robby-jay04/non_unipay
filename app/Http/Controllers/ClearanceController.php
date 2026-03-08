<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clearance;
use App\Services\ClearanceService;
use App\Models\Semester;
use App\Models\Fee;

use App\Models\SchoolYear;


class ClearanceController extends Controller
{
    protected $clearanceService;

    public function __construct(ClearanceService $clearanceService)
    {
        $this->clearanceService = $clearanceService;
    }



public function show(Request $request)
{
    $student = $request->user()->student;

    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    $currentSemester = Semester::where('is_current', true)->first();
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();

    if (!$currentSemester || !$currentSchoolYear) {
        return response()->json([
            'status' => 'pending',
            'message' => 'No active semester or school year set.',
            'exam_period' => null,
        ]);
    }

    // Get total fees for current period using IDs
    $totalFees = Fee::where('school_year_id', $currentSchoolYear->id)
                    ->where('semester_id', $currentSemester->id)
                    ->sum('amount');

    // Get total paid by this student for fees in the current period
    // This assumes a many-to-many relationship with a pivot table 'fee_payment'
    $paidFees = Fee::where('school_year_id', $currentSchoolYear->id)
                   ->where('semester_id', $currentSemester->id)
                   ->get();

    $totalPaid = 0;
    foreach ($paidFees as $fee) {
        $paidForFee = $fee->payments()
            ->where('student_id', $student->id)
            ->where('status', 'paid')
            ->sum('payments.total_amount');
        $totalPaid += $paidForFee;
    }

    $isCleared = ($totalPaid >= $totalFees);

    return response()->json([
        'status' => $isCleared ? 'cleared' : 'pending',
        'exam_period' => $currentSemester->name,
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
