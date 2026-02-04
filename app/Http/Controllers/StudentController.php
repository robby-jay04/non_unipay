<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function profile(Request $request)
    {
        $student = $request->user()->student()->with(['clearance', 'payments'])->first();

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        return response()->json($student);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'contact' => 'sometimes|string',
            'course' => 'sometimes|string',
            'year_level' => 'sometimes|integer|min:1|max:5',
        ]);

        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        $student->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'student' => $student,
        ]);
    }

    public function paymentHistory(Request $request)
    {
        $student = $request->user()->student;
        $payments = $student->payments()
            ->with('transaction')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($payments);
    }
}