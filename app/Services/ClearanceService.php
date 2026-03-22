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

        if (!$currentSemester || !$currentSchoolYear) {
            return $this->setStatus($student, 'not_cleared');
        }

        $currentExamPeriod = ExamPeriod::where('semester_id', $currentSemester->id)
                                       ->where('is_current', true)
                                       ->first();

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

        if ($totalFees <= 0) {
            return $this->setStatus($student, 'not_cleared');
        }

        $feeIds = $applicableFees->pluck('id');

        $totalPaid = DB::table('fee_payment')
            ->join('payments', 'payments.id', '=', 'fee_payment.payment_id')
            ->where('payments.student_id', $student->id)
            ->where('payments.status', 'paid')
            ->whereIn('fee_payment.fee_id', $feeIds)
            ->sum('fee_payment.amount');

        $isCleared = $totalPaid >= $totalFees;
        $status    = $isCleared ? 'cleared' : 'not_cleared';

        return $this->setStatus($student, $status);
    }

    private function setStatus(Student $student, string $status): Clearance
    {
        $student->clearance_status = $status;
        $student->save();

        $clearance = Clearance::updateOrCreate(
            ['student_id' => $student->id],
            ['status'     => $status]
        );

        Log::info('Clearance synced', [
            'student_id' => $student->id,
            'status'     => $status,
        ]);

        return $clearance;
    }

    public function checkClearanceStatus($studentId)
    {
        $clearance = Clearance::where('student_id', $studentId)->first();

        return $clearance
            ? ['status' => $clearance->status, 'updated_at' => $clearance->updated_at]
            : ['status' => 'not_cleared', 'message' => 'No clearance record found'];
    }

    public function bulkUpdateClearances()
    {
        $students = Student::all();

        foreach ($students as $student) {
            $this->updateClearance($student->id);
        }

        return ['updated' => $students->count(), 'message' => 'Clearances updated successfully'];
    }
}