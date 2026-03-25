<?php

namespace App\Services;

use App\Models\Clearance;
use App\Models\Student;
use App\Models\Fee;
use App\Models\Semester;
use App\Models\SchoolYear;
use App\Models\ExamPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearanceService
{
    public function updateClearance($studentId)
    {
        $student = Student::findOrFail($studentId);

        $currentSemester   = Semester::where('is_current', true)->first();
        $currentSchoolYear = SchoolYear::where('is_current', true)->first();

        // ✅ If no active period → reset
        if (!$currentSemester || !$currentSchoolYear) {
            return $this->setStatus($student, 'not_cleared', null, null, null);
        }

        // ✅ FIX: scoped exam period (IMPORTANT)
        $currentExamPeriod = ExamPeriod::where('semester_id', $currentSemester->id)
            ->where('is_current', true)
            ->first();

        // ✅ Get applicable fees
        $applicableFees = Fee::where('school_year_id', $currentSchoolYear->id)
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

        $totalFees = $applicableFees->sum('amount');

        // ✅ No fees → NOT CLEARED (important for reset)
        if ($totalFees <= 0) {
            return $this->setStatus(
                $student,
                'not_cleared',
                $currentSemester,
                $currentSchoolYear,
                $currentExamPeriod
            );
        }

        $feeIds = $applicableFees->pluck('id');

        $totalPaid = DB::table('fee_payment')
            ->join('payments', 'payments.id', '=', 'fee_payment.payment_id')
            ->where('payments.student_id', $student->id)
            ->where('payments.status', 'paid')
            ->whereIn('fee_payment.fee_id', $feeIds)
            ->sum('fee_payment.amount');

        $isCleared = $totalPaid >= $totalFees;

        $status = $isCleared ? 'cleared' : 'not_cleared';

        return $this->setStatus(
            $student,
            $status,
            $currentSemester,
            $currentSchoolYear,
            $currentExamPeriod
        );
    }

    /**
     * ✅ CENTRAL STATUS HANDLER
     */
    private function setStatus(
        Student $student,
        string $status,
        $semester = null,
        $schoolYear = null,
        $examPeriod = null
    ): Clearance {

        // ✅ Update student table (for web display)
        $student->clearance_status = $status;
        $student->save();

        // ✅ Map to DB ENUM (clearances table)
        $dbStatus = $status === 'cleared' ? 'cleared' : 'pending';

        // ✅ Prevent duplicate wrong period data
        $clearance = Clearance::updateOrCreate(
            [
                'student_id'     => $student->id,
                'semester_id'    => $semester?->id,
                'school_year_id' => $schoolYear?->id,
                'exam_period_id' => $examPeriod?->id,
            ],
            [
                'status' => $dbStatus
            ]
        );

        Log::info('Clearance synced', [
            'student_id' => $student->id,
            'status'     => $status,
            'semester'   => $semester?->id,
            'school_year'=> $schoolYear?->id,
            'exam_period'=> $examPeriod?->id,
        ]);

        return $clearance;
    }

    /**
     * ✅ BULK UPDATE (used when period changes)
     */
    public function bulkUpdateClearances()
    {
        $students = Student::all();

        foreach ($students as $student) {
            $this->updateClearance($student->id);
        }

        Log::info('Bulk clearance update executed', [
            'total_students' => $students->count()
        ]);

        return [
            'updated' => $students->count(),
            'message' => 'Clearances updated successfully'
        ];
    }

    /**
     * ✅ FORCE RESET (use when changing semester/year)
     */
    public function resetAllClearances()
    {
        Student::query()->update(['clearance_status' => 'not_cleared']);

        Clearance::query()->update(['status' => 'pending']);

        Log::warning('All clearances reset due to period change');

        return [
            'message' => 'All clearances have been reset'
        ];
    }
}