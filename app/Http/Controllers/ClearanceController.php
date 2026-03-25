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
        // ✅ auto sync before showing
        $this->clearanceService->bulkUpdateClearances();

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
                'status' => 'no_fees',
                'message' => 'No active semester or school year set.'
            ]);
        }

        $currentExamPeriod = ExamPeriod::where('semester_id', $currentSemester->id)
            ->where('is_current', true)
            ->first();

        $fees = Fee::where('school_year_id', $currentSchoolYear->id)
            ->where('semester_id', $currentSemester->id)
            ->where(function ($q) use ($currentExamPeriod) {
                if ($currentExamPeriod) {
                    $q->whereNull('exam_period_id')
                      ->orWhere('exam_period_id', $currentExamPeriod->id);
                } else {
                    $q->whereNull('exam_period_id');
                }
            })
            ->where(function ($q) use ($student) {
                $q->where('course', $student->course)
                  ->orWhereNull('course');
            })
            ->get();

        $totalFees = $fees->sum('amount');

        $feeIds = $fees->pluck('id');

        $totalPaid = DB::table('fee_payment')
            ->join('payments', 'payments.id', '=', 'fee_payment.payment_id')
            ->where('payments.student_id', $student->id)
            ->where('payments.status', 'paid')
            ->whereIn('fee_payment.fee_id', $feeIds)
            ->sum('fee_payment.amount');

        $isCleared = $totalPaid >= $totalFees;

        // ✅ Sync DB
        $this->clearanceService->updateClearance($student->id);

        return response()->json([
            'status' => $isCleared ? 'cleared' : 'pending',
            'total_fees' => $totalFees,
            'total_paid' => $totalPaid,
        ]);
    }
    public function updateClearance($studentId)
    {
        return response()->json([
            'clearance' => $this->clearanceService->updateClearance($studentId)
        ]);
    }
}