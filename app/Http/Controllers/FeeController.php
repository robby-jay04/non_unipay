<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentFee;

class FeeController extends Controller
{
    const COURSES = ['BSIT', 'BEED', 'BSED', 'BSCRIM', 'BSOA', 'BSPOLSCI'];

    public function index()
    {
        $current = SchoolYear::current();
        return response()->json(['current_school_year' => $current]);
    }

    public function getTotalFees()
    {
        $total = Fee::currentSchoolYear()->sum('amount');
        return response()->json([
            'success'   => true,
            'total'     => $total,
            'formatted' => '₱' . number_format($total, 2),
        ]);
    }

    public function show($id)
    {
        $fee = Fee::findOrFail($id);
        return response()->json(['success' => true, 'fee' => $fee]);
    }

    public function getByType($type)
    {
        $fees  = Fee::currentSchoolYear()->byType($type)->get();
        $total = $fees->sum('amount');
        return response()->json([
            'success' => true,
            'type'    => $type,
            'fees'    => $fees,
            'total'   => $total,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'type'        => 'required|in:tuition,miscellaneous,exam',
            'semester'    => 'nullable|string',
            'school_year' => 'required|string',
            'course'      => 'nullable|string',
        ]);

        Fee::create($validated);

        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee created successfully!');
    }

    public function create()
    {
        $schoolYears        = SchoolYear::orderBy('name', 'desc')->get();
        $currentSchoolYear  = SchoolYear::where('is_current', true)->first();
        $courses            = self::COURSES;

        return view('admin.fees.create', compact('schoolYears', 'currentSchoolYear', 'courses'));
    }

    public function adminIndex()
    {
        $fees    = Fee::orderBy('school_year', 'desc')->orderBy('type')->get();
        $courses = self::COURSES;

        return view('admin.fees.index', compact('fees', 'courses'));
    }

    public function storeWeb(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type'   => 'required|in:tuition,miscellaneous,exam',
            'course' => 'nullable|string',
        ]);

        $currentSemester   = Semester::where('is_current', true)->first();
        $currentSchoolYear = SchoolYear::where('is_current', true)->first();

        Fee::create([
            'name'           => $validated['name'],
            'amount'         => $validated['amount'],
            'type'           => $validated['type'],
            'course'         => $validated['course'] ?? null,
            'school_year'    => $currentSchoolYear->name,
            'semester'       => $currentSemester->name,
            'semester_id'    => $currentSemester->id,
            'school_year_id' => $currentSchoolYear->id,
        ]);

        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee created successfully.');
    }

    public function edit(Fee $fee)
    {
        $schoolYears       = SchoolYear::orderBy('name', 'desc')->get();
        $currentSchoolYear = SchoolYear::where('is_current', true)->first();
        $courses           = self::COURSES;

        return view('admin.fees.edit', compact('fee', 'schoolYears', 'currentSchoolYear', 'courses'));
    }

    public function updateWeb(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'type'        => 'required|in:tuition,miscellaneous,exam',
            'semester'    => 'nullable|string',
            'school_year' => 'required|string',
            'course'      => 'nullable|string',
        ]);

        $semester   = Semester::where('name', $validated['semester'])->first();
        $schoolYear = SchoolYear::where('name', $validated['school_year'])->first();

        $fee->update([
            'name'           => $validated['name'],
            'amount'         => $validated['amount'],
            'type'           => $validated['type'],
            'semester'       => $validated['semester'],
            'school_year'    => $validated['school_year'],
            'semester_id'    => $semester?->id,
            'school_year_id' => $schoolYear?->id,
            'course'         => $validated['course'] ?? null,
        ]);

        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee updated successfully.');
    }

    public function destroyWeb($fee)
    {
        Fee::findOrFail($fee)->delete();
        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee deleted successfully.');
    }

    public function update(Request $request, $id)
    {
        $fee       = Fee::findOrFail($id);
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'amount'      => 'sometimes|numeric|min:0',
            'type'        => 'sometimes|in:tuition,miscellaneous,exam',
            'semester'    => 'nullable|string',
            'school_year' => 'sometimes|string',
            'course'      => 'nullable|string',
        ]);

        $fee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fee updated successfully',
            'fee'     => $fee,
        ]);
    }

    public function destroy(Fee $fee)
    {
        $fee->delete();
        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee deleted successfully.');
    }

    public function breakdown()
    {
        $student           = auth()->user()->student;
        $currentSemester   = Semester::where('is_current', true)->first();
        $currentSchoolYear = SchoolYear::where('is_current', true)->first();

        if (!$currentSemester || !$currentSchoolYear) {
            return response()->json([
                'success' => false,
                'message' => 'No active semester or school year set.',
            ], 404);
        }

        // Filter fees by course as well
        $fees = Fee::where('school_year_id', $currentSchoolYear->id)
                   ->where('semester_id', $currentSemester->id)
                   ->where(function ($q) use ($student) {
                       $q->where('course', $student->course)
                         ->orWhereNull('course'); // fees with no course = apply to all
                   })
                   ->get();

        if ($fees->isEmpty()) {
            return response()->json([
                'success'   => true,
                'breakdown' => [
                    'tuition'           => ['fees' => [], 'total' => 0],
                    'miscellaneous'     => ['fees' => [], 'total' => 0],
                    'exam'              => ['fees' => [], 'total' => 0],
                    'grand_total'       => 0,
                    'total_paid'        => 0,
                    'remaining_balance' => 0,
                    'status'            => 'no_fees',
                ],
            ]);
        }

        $grandTotal = $fees->sum('amount');
        $totalPaid  = 0;
        $status     = 'cleared';

        foreach ($fees as $fee) {
            $paidForFee = $fee->payments()
                ->where('student_id', $student->id)
                ->where('status', 'paid')
                ->sum('payments.total_amount');

            $totalPaid += $paidForFee;

            if ($paidForFee < $fee->amount) {
                $status = 'pending';
            }
        }

        $breakdown = [
            'tuition'           => ['fees' => $fees->where('type', 'tuition')->values(),      'total' => $fees->where('type', 'tuition')->sum('amount')],
            'miscellaneous'     => ['fees' => $fees->where('type', 'miscellaneous')->values(), 'total' => $fees->where('type', 'miscellaneous')->sum('amount')],
            'exam'              => ['fees' => $fees->where('type', 'exam')->values(),          'total' => $fees->where('type', 'exam')->sum('amount')],
            'grand_total'       => $grandTotal,
            'total_paid'        => $totalPaid,
            'remaining_balance' => max($grandTotal - $totalPaid, 0),
            'status'            => $status,
        ];

        return response()->json(['success' => true, 'breakdown' => $breakdown]);
    }
}