<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clearance;
use App\Services\ClearanceService;
use App\Models\Semester;
use App\Models\Fee;

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
    if (!$currentSemester) {
        return response()->json([
            'status' => 'pending',
            'message' => 'No active semester set.',
            'exam_period' => null,
        ]);
    }

    $fees = Fee::currentSchoolYear()
                ->where('semester', $currentSemester->name)
                ->get();

    if ($fees->isEmpty()) {
        return response()->json([
            'status' => 'pending',
            'exam_period' => $currentSemester->name,
        ]);
    }

    $totalPaid = 0;
    foreach ($fees as $fee) {
        $paidForFee = $fee->payments()
            ->where('student_id', $student->id)   // ✅ FIXED
            ->where('status', 'paid')
            ->sum('payments.total_amount');
        $totalPaid += $paidForFee;
    }

    $grandTotal = $fees->sum('amount');
    $remainingBalance = $grandTotal - $totalPaid;

    $isCleared = ($remainingBalance <= 0);

    return response()->json([
        'status' => $isCleared ? 'cleared' : 'pending',
        'exam_period' => $currentSemester->name,
    ]);
}    public function updateClearance($studentId)
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
