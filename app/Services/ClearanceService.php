<?php

namespace App\Services;

use App\Models\Clearance;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\Log;

class ClearanceService
{
    public function updateClearance($studentId)
    {
        $student = Student::findOrFail($studentId);

        // Check if student has paid all required fees
        $hasPaid = Payment::where('student_id', $studentId)
            ->where('status', 'paid')
            ->exists();

        if ($hasPaid) {
            $clearance = Clearance::updateOrCreate(
                ['student_id' => $studentId],
                [
                    'status' => 'cleared',
                    'exam_period' => config('app.current_exam_period', now()->format('Y-m')),
                ]
            );

            Log::info('Clearance updated to cleared', [
                'student_id' => $studentId,
                'clearance_id' => $clearance->id,
            ]);

            return $clearance;
        }

        // If not paid, set to pending
        $clearance = Clearance::updateOrCreate(
            ['student_id' => $studentId],
            ['status' => 'pending']
        );

        return $clearance;
    }

    public function checkClearanceStatus($studentId)
    {
        $clearance = Clearance::where('student_id', $studentId)->first();

        if (!$clearance) {
            return [
                'status' => 'pending',
                'message' => 'No clearance record found',
            ];
        }

        return [
            'status' => $clearance->status,
            'exam_period' => $clearance->exam_period,
            'updated_at' => $clearance->updated_at,
        ];
    }

    public function bulkUpdateClearances()
    {
        $paidStudents = Payment::paid()
            ->pluck('student_id')
            ->unique();

        foreach ($paidStudents as $studentId) {
            $this->updateClearance($studentId);
        }

        return [
            'updated' => $paidStudents->count(),
            'message' => 'Clearances updated successfully',
        ];
    }
}