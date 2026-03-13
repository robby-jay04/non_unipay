<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\ExamPeriod;
use Illuminate\Http\Request;

class ExamPeriodController extends Controller
{
    public function setCurrent(Request $request)
    {
        $request->validate([
            'exam_period' => 'required|in:Prelim,Midterm,Semi-Final,Finals',
        ]);

        // Get the current semester
        $currentSemester = Semester::where('is_current', true)->first();

        if (!$currentSemester) {
            return back()->with('error', 'No active semester found.');
        }

        // Unset any current exam period for this semester
        $currentSemester->examPeriods()->update(['is_current' => false]);

        // Find or create the selected exam period for this semester
        $examPeriod = $currentSemester->examPeriods()->firstOrCreate(
            ['name' => $request->exam_period],
            ['is_current' => true]
        );

        // If it already existed but wasn't current, update it
        if (!$examPeriod->wasRecentlyCreated) {
            $examPeriod->update(['is_current' => true]);
        }

        return back()->with('success', $request->exam_period . ' is now the current exam period.');
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