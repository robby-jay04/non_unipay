<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clearance;
use App\Services\ClearanceService;

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

    $requiredAmount = 58000;

    $totalPaid = $student->payments()
        ->where('status', 'paid')
        ->sum('total_amount');

    $status = ($requiredAmount - $totalPaid) <= 0
        ? 'cleared'
        : 'pending';

    return response()->json([
        'status' => $status,
        'total_paid' => $totalPaid,
        'required' => $requiredAmount,
        'remaining' => max($requiredAmount - $totalPaid, 0),
    ]);
}

    public function checkClearance($studentId)
    {
        $clearance = Clearance::where('student_id', $studentId)
            ->with('student.user')
            ->first();

        if (!$clearance) {
            return response()->json([
                'status' => 'pending',
                'message' => 'No clearance record',
            ], 404);
        }

        return response()->json($clearance);
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
