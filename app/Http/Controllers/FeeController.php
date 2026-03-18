<?php

namespace App\Http\Controllers;

use App\Models\ExamPeriod;
use App\Models\Fee;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentFee;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $schoolYears       = SchoolYear::orderBy('name', 'desc')->get();
        $currentSchoolYear = SchoolYear::where('is_current', true)->first();
        $semesters         = Semester::all();
        $currentSemester   = Semester::where('is_current', true)->first();
        $examPeriods       = $currentSemester
                                ? ExamPeriod::where('semester_id', $currentSemester->id)->get()
                                : collect();
        $courses           = self::COURSES;

        return view('admin.fees.create', compact(
            'schoolYears', 'currentSchoolYear',
            'semesters', 'currentSemester',
            'examPeriods', 'courses'
        ));
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
            'name'           => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0',
            'type'           => 'required|in:tuition,miscellaneous,exam',
            'course'         => 'nullable|string',
            'semester_id'    => 'nullable|exists:semesters,id',
            'school_year_id' => 'required|exists:school_years,id',
            'exam_period_id' => 'nullable|exists:exam_periods,id',
        ]);

        $schoolYear = SchoolYear::find($validated['school_year_id']);
        $semester   = Semester::find($validated['semester_id'] ?? null);
        $examPeriod = ExamPeriod::find($validated['exam_period_id'] ?? null);

        Fee::create([
            'name'           => $validated['name'],
            'amount'         => $validated['amount'],
            'type'           => $validated['type'],
            'course'         => $validated['course'] ?? null,
            'school_year'    => $schoolYear->name,
            'semester'       => $semester?->name,
            'semester_id'    => $semester?->id,
            'school_year_id' => $schoolYear->id,
            'exam_period'    => $examPeriod?->name,
            'exam_period_id' => $examPeriod?->id,
        ]);

        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee created successfully.');
    }

    public function edit(Fee $fee)
    {
        $schoolYears       = SchoolYear::orderBy('name', 'desc')->get();
        $semesters         = Semester::where('school_year_id', $fee->school_year_id)->get();
        $examPeriods       = $fee->semester_id
                                ? ExamPeriod::where('semester_id', $fee->semester_id)->get()
                                : collect();
        $currentSchoolYear = SchoolYear::where('is_current', true)->first();
        $courses           = self::COURSES;

        return view('admin.fees.edit', compact(
            'fee', 'schoolYears', 'semesters',
            'examPeriods', 'currentSchoolYear', 'courses'
        ));
    }

    public function updateWeb(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0',
            'type'           => 'required|in:tuition,miscellaneous,exam',
            'semester_id'    => 'nullable|exists:semesters,id',
            'school_year_id' => 'required|exists:school_years,id',
            'exam_period_id' => 'nullable|exists:exam_periods,id',
            'course'         => 'nullable|string',
        ]);

        $semester   = Semester::find($validated['semester_id']);
        $schoolYear = SchoolYear::find($validated['school_year_id']);
        $examPeriod = ExamPeriod::find($validated['exam_period_id'] ?? null);

        $fee->update([
            'name'           => $validated['name'],
            'amount'         => $validated['amount'],
            'type'           => $validated['type'],
            'semester'       => $semester?->name,
            'school_year'    => $schoolYear->name,
            'semester_id'    => $semester?->id,
            'school_year_id' => $schoolYear->id,
            'exam_period'    => $examPeriod?->name,
            'exam_period_id' => $examPeriod?->id,
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
        $currentExamPeriod = $currentSemester
                                ? ExamPeriod::where('semester_id', $currentSemester->id)
                                            ->where('is_current', true)
                                            ->first()
                                : null;

        if (!$currentSemester || !$currentSchoolYear) {
            return response()->json([
                'success' => false,
                'message' => 'No active semester or school year set.',
            ], 404);
        }

        $fees = Fee::where('school_year_id', $currentSchoolYear->id)
                   ->where('semester_id', $currentSemester->id)
                   ->where(function ($q) use ($currentExamPeriod) {
                       if ($currentExamPeriod) {
                           /*
                            * ✅ FIXED:
                            * Only include a fee if:
                            *   (A) exam_period_id IS NULL  → applies to ALL periods always
                            *   (B) exam_period_id = current exam period's ID exactly
                            *
                            * A fee pinned to "Semi-Final" will NOT appear during "Finals".
                            * A fee with no exam period set will ALWAYS appear.
                            */
                           $q->whereNull('exam_period_id')
                             ->orWhere('exam_period_id', $currentExamPeriod->id);
                       } else {
                           /*
                            * No exam period is active.
                            * Only show fees with no exam period restriction.
                            * Period-specific fees are hidden until their period is activated.
                            */
                           $q->whereNull('exam_period_id');
                       }
                   })
                   ->where(function ($q) use ($student) {
                       $q->where('course', $student->course)
                         ->orWhereNull('course');
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
        $feeIds     = $fees->pluck('id');

        $totalPaid = DB::table('fee_payment')
            ->join('payments', 'payments.id', '=', 'fee_payment.payment_id')
            ->where('payments.student_id', $student->id)
            ->where('payments.status', 'paid')
            ->whereIn('fee_payment.fee_id', $feeIds)
            ->sum('fee_payment.amount');

        $remainingBalance = max($grandTotal - $totalPaid, 0);

        if ($totalPaid <= 0) {
            $status = 'pending';
        } elseif ($remainingBalance <= 0) {
            $status = 'cleared';
        } else {
            $status = 'partial';
        }

        $breakdown = [
            'tuition' => [
                'fees'  => $fees->where('type', 'tuition')->values(),
                'total' => $fees->where('type', 'tuition')->sum('amount'),
            ],
            'miscellaneous' => [
                'fees'  => $fees->where('type', 'miscellaneous')->values(),
                'total' => $fees->where('type', 'miscellaneous')->sum('amount'),
            ],
            'exam' => [
                'fees'  => $fees->where('type', 'exam')->values(),
                'total' => $fees->where('type', 'exam')->sum('amount'),
            ],
            'grand_total'       => $grandTotal,
            'total_paid'        => $totalPaid,
            'remaining_balance' => $remainingBalance,
            'status'            => $status,
        ];

        return response()->json(['success' => true, 'breakdown' => $breakdown]);
    }
}
