<?php

namespace App\Http\Controllers;

use App\Models\ExamPeriod;
use App\Models\Fee;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentFee;
use App\Services\ClearanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    const COURSES = ['BSIT', 'BEED', 'BSED', 'BSCRIM', 'BSOA', 'BSPOLSCI'];

    protected $clearanceService;

    public function __construct(ClearanceService $clearanceService)
    {
        $this->clearanceService = $clearanceService;
    }

    public function index(Request $request)
{
    $query = Fee::query();

    // Apply filters
    if ($request->filled('school_year')) {
        $query->where('school_year_id', $request->school_year);
    }

    if ($request->filled('semester')) {
        $query->where('semester_id', $request->semester);
    }

    if ($request->filled('exam_period')) {
        $query->where('exam_period', $request->exam_period);
    }

    $fees = $query->orderBy('school_year_id', 'desc')
                  ->orderBy('type', 'asc')
                  ->get();

    // ✅ FIX HERE: filter semesters by selected school year
    $semesters = Semester::when($request->school_year, function ($q) use ($request) {
            $q->where('school_year_id', $request->school_year);
        })
        ->orderBy('name')
        ->get();

    $schoolYears = SchoolYear::orderBy('name', 'desc')->get();
    $examPeriods = ['Prelim', 'Midterm', 'Semi-Final', 'Finals'];

    return view('admin.fees.index', compact('fees', 'schoolYears', 'semesters', 'examPeriods'));
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
        $currentSemester   = Semester::where('is_current', true)->first();
        $courses           = self::COURSES;

        return view('admin.fees.create', compact(
            'schoolYears',
            'currentSchoolYear',
            'currentSemester',
            'courses'
        ));
    }

public function adminIndex(Request $request)
{
    $query = Fee::with(['schoolYear', 'semester']);

    // Apply filters
    if ($request->filled('school_year')) {
        $query->where('school_year_id', $request->school_year);
    }

    if ($request->filled('semester')) {
        $query->where('semester_id', $request->semester);
    }

    if ($request->filled('exam_period')) {
        $query->where('exam_period', $request->exam_period);
    }

    $fees = $query->orderBy('school_year_id', 'desc')
                  ->orderBy('type', 'asc')
                  ->get();

    // ✅ FIX HERE ALSO
    $semesters = Semester::when($request->school_year, function ($q) use ($request) {
            $q->where('school_year_id', $request->school_year);
        })
        ->orderBy('name')
        ->get();

    $schoolYears = SchoolYear::orderBy('name', 'desc')->get();
    $examPeriods = ['Prelim', 'Midterm', 'Semi-Final', 'Finals'];

    return view('admin.fees.index', compact('fees', 'schoolYears', 'semesters', 'examPeriods'));
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

        $semesterId   = !empty($validated['semester_id'])    ? $validated['semester_id']    : null;
        $examPeriodId = !empty($validated['exam_period_id']) ? $validated['exam_period_id'] : null;

        $schoolYear = SchoolYear::find($validated['school_year_id']);
        $semester   = $semesterId   ? Semester::find($semesterId)     : null;
        $examPeriod = $examPeriodId ? ExamPeriod::find($examPeriodId) : null;

        Fee::create([
            'name'           => $validated['name'],
            'amount'         => $validated['amount'],
            'type'           => $validated['type'],
            'course'         => !empty($validated['course']) ? $validated['course'] : null,
            'school_year'    => $schoolYear->name,
            'semester'       => $semester?->name,
            'semester_id'    => $semester?->id,
            'school_year_id' => $schoolYear->id,
            'exam_period'    => $examPeriod?->name,
            'exam_period_id' => $examPeriod?->id,
        ]);

        // ✅ Re-sync all clearances since a new fee was added
        $this->clearanceService->bulkUpdateClearances();

        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee created successfully.');
    }

    public function edit(Fee $fee)
    {
        $schoolYears       = SchoolYear::orderBy('name', 'desc')->get();
        $currentSchoolYear = SchoolYear::where('is_current', true)->first();
        $courses           = self::COURSES;

        return view('admin.fees.edit', compact(
            'fee',
            'schoolYears',
            'currentSchoolYear',
            'courses'
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

        $semesterId   = !empty($validated['semester_id'])    ? $validated['semester_id']    : null;
        $examPeriodId = !empty($validated['exam_period_id']) ? $validated['exam_period_id'] : null;

        $semester   = $semesterId   ? Semester::find($semesterId)     : null;
        $schoolYear = SchoolYear::find($validated['school_year_id']);
        $examPeriod = $examPeriodId ? ExamPeriod::find($examPeriodId) : null;

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
            'course'         => !empty($validated['course']) ? $validated['course'] : null,
        ]);

        // ✅ Re-sync all clearances since a fee was changed
        $this->clearanceService->bulkUpdateClearances();

        return redirect()->route('admin.fees.index')
                         ->with('success', 'Fee updated successfully.');
    }

    public function destroyWeb($fee)
    {
        Fee::findOrFail($fee)->delete();

        // ✅ Re-sync all clearances since a fee was deleted
        $this->clearanceService->bulkUpdateClearances();

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