<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clearance;
use App\Services\ClearanceService;
use App\Models\ExamPeriod;
use App\Models\Semester;
use App\Models\Fee;
use App\Models\Student;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\DB;

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
        $clearances      = Student::with('user')->get();

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

        // ✅ FIX: get the current exam period so we filter fees the same way
        // breakdown() does — only fees for the active exam period (or null = always apply)
        $currentExamPeriod = ExamPeriod::where('semester_id', $currentSemester->id)
                                       ->where('is_current', true)
                                       ->first();

        // ✅ FIX: mirror the EXACT same query as FeeController@breakdown
        // so that totalFees here always matches what students see on their fees screen.
        $applicableFees = Fee::where('school_year_id', $currentSchoolYear->id)
            ->where('semester_id', $currentSemester->id)
            ->where(function ($q) use ($currentExamPeriod) {
                if ($currentExamPeriod) {
                    // Only fees with no exam period (always apply)
                    // OR fees pinned to the current exam period exactly
                    $q->whereNull('exam_period_id')
                      ->orWhere('exam_period_id', $currentExamPeriod->id);
                } else {
                    // No active exam period — only show semester-wide fees
                    $q->whereNull('exam_period_id');
                }
            })
            ->where(function ($q) use ($student) {
                $q->where('course', $student->course)
                  ->orWhereNull('course');
            })
            ->get();

        $totalFees = $applicableFees->sum('amount');

        if ($totalFees <= 0) {
            return response()->json([
                'status'      => 'no_fees',
                'message'     => 'No fees assigned for this period.',
                'exam_period' => $currentExamPeriod?->name ?? $currentSemester->name,
                'total_fees'  => 0,
                'total_paid'  => 0,
                'remaining'   => 0,
            ]);
        }

        $feeIds = $applicableFees->pluck('id');

        // Scope payment lookup to ONLY the applicable fee IDs
        $totalPaid = DB::table('fee_payment')
            ->join('payments', 'payments.id', '=', 'fee_payment.payment_id')
            ->where('payments.student_id', $student->id)
            ->where('payments.status', 'paid')
            ->whereIn('fee_payment.fee_id', $feeIds)
            ->sum('fee_payment.amount');

        $remaining = max($totalFees - $totalPaid, 0);
        $isCleared = $totalPaid >= $totalFees;

        return response()->json([
            'status'      => $isCleared ? 'cleared' : 'pending',
            'exam_period' => $currentExamPeriod?->name ?? $currentSemester->name,
            'total_fees'  => $totalFees,
            'total_paid'  => $totalPaid,
            'remaining'   => $remaining,
            'cleared_at'  => $isCleared ? now()->toDateTimeString() : null,
        ]);
    }

    public function updateClearance($studentId)
    {
        try {
            $clearance = $this->clearanceService->updateClearance($studentId);

            return response()->json([
                'message'   => 'Clearance updated successfully',
                'clearance' => $clearance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update clearance',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }
}