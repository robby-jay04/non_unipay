<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\ExamPeriod;
use Illuminate\Http\Request;
use App\Services\ClearanceService;
class ExamPeriodController extends Controller
{
   public function setCurrent(Request $request)
{
    $currentSemester = Semester::where('is_current', true)->first();

    $currentSemester->examPeriods()->update(['is_current' => false]);

    $examPeriod = $currentSemester->examPeriods()->firstOrCreate([
        'name' => $request->exam_period
    ]);

    $examPeriod->update(['is_current' => true]);

    // 🔥 ADD THIS HERE
    app(\App\Services\ClearanceService::class)->resetAllClearances();
    app(\App\Services\ClearanceService::class)->bulkUpdateClearances();

    return back()->with('success', 'Exam period updated.');
}
   public function current(Request $request)
{
    $user = $request->user();
    $student = $user->student;

    if (!$student) {
        return response()->json([
            'success' => false,
            'message' => 'Student profile not found.'
        ], 404);
    }

    $currentSemester = Semester::where('is_current', true)->first();

    if (!$currentSemester) {
        return response()->json([
            'success' => false,
            'message' => 'No active semester set.'
        ], 404);
    }

    try {
        $currentPeriod = ExamPeriod::where('semester_id', $currentSemester->id)
            ->where('is_current', true)
            ->first();
    } catch (\Exception $e) {
        // Table doesn't exist or other DB error
        $currentPeriod = null;
    }

    return response()->json([
        'success' => true,
        'exam_period' => $currentPeriod ? $currentPeriod->name : null,
        'semester' => $currentSemester->name,
        'school_year' => $currentSemester->schoolYear->name ?? null,
    ]);
}
}