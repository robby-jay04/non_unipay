<?php

namespace App\Http\Controllers;

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
    $semesters         = Semester::all();                                    // ✅ add
    $currentSemester   = Semester::where('is_current', true)->first();      // ✅ add
    $courses           = self::COURSES;

    return view('admin.fees.create', compact(
        'schoolYears', 'currentSchoolYear', 'semesters', 'currentSemester', 'courses'
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
        'semester'       => 'nullable|string', // ✅ accept name string as fallback
        'school_year_id' => 'required|exists:school_years,id',
    ]);

    $schoolYear = SchoolYear::find($validated['school_year_id']);

    // ✅ Resolve semester — try ID first, fallback to name string
    $semester = null;
    if (!empty($validated['semester_id'])) {
        $semester = Semester::find($validated['semester_id']);
    } elseif (!empty($validated['semester'])) {
        $semester = Semester::where('name', $validated['semester'])
                            ->whereHas('schoolYear', fn($q) => $q->where('id', $schoolYear->id))
                            ->first();
    }

    Fee::create([
        'name'           => $validated['name'],
        'amount'         => $validated['amount'],
        'type'           => $validated['type'],
        'course'         => $validated['course'] ?? null,
        'school_year'    => $schoolYear->name,
        'semester'       => $semester?->name,
        'semester_id'    => $semester?->id,
        'school_year_id' => $schoolYear->id,
    ]);

    return redirect()->route('admin.fees.index')
                     ->with('success', 'Fee created successfully.');
}

    // ✅ Fixed: pass semesters to view
    public function edit(Fee $fee)
{
    $schoolYears       = SchoolYear::orderBy('name', 'desc')->get();
    $semesters         = Semester::where('school_year_id', $fee->school_year_id)->get(); // ✅ only for this fee's school year
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();
    $courses           = self::COURSES;

    return view('admin.fees.edit', compact('fee', 'schoolYears', 'semesters', 'currentSchoolYear', 'courses'));
}
    // ✅ Fixed: lookup by ID not by name string
    public function updateWeb(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'amount'         => 'required|numeric|min:0',
            'type'           => 'required|in:tuition,miscellaneous,exam',
            'semester_id'    => 'nullable|exists:semesters,id',
            'school_year_id' => 'required|exists:school_years,id',
            'course'         => 'nullable|string',
        ]);

        $semester   = Semester::find($validated['semester_id']);
        $schoolYear = SchoolYear::find($validated['school_year_id']);

        $fee->update([
            'name'           => $validated['name'],
            'amount'         => $validated['amount'],
            'type'           => $validated['type'],
            'semester'       => $semester?->name,
            'school_year'    => $schoolYear->name,
            'semester_id'    => $semester?->id,
            'school_year_id' => $schoolYear->id,
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

   // add this at the top of the controller

public function breakdown()
{
    $student = auth()->user()->student;
    $currentSemester   = Semester::where('is_current', true)->first();
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();

    if (!$currentSemester || !$currentSchoolYear) {
        return response()->json([
            'success' => false,
            'message' => 'No active semester or school year set.',
        ], 404);
    }

    $fees = Fee::where('school_year_id', $currentSchoolYear->id)
               ->where('semester_id', $currentSemester->id)
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

    // ✅ FIX: scope total_paid only to fees in the current semester/school year
    $totalPaid = DB::table('fee_payment')
        ->join('payments', 'payments.id', '=', 'fee_payment.payment_id')
        ->where('payments.student_id', $student->id)
        ->where('payments.status', 'paid')
        ->whereIn('fee_payment.fee_id', $feeIds)   // ← was missing this scope
        ->sum('fee_payment.amount');

    $remainingBalance = max($grandTotal - $totalPaid, 0);

    // ✅ FIX: determine status from the computed balance, not per-fee loop
    // A student is only "cleared" when they have fully paid all current fees.
    if ($totalPaid <= 0) {
        $status = 'pending';
    } elseif ($remainingBalance <= 0) {
        $status = 'cleared';
    } else {
        $status = 'partial';   // partially paid — also not cleared
    }

    $breakdown = [
        'tuition'           => [
            'fees'  => $fees->where('type', 'tuition')->values(),
            'total' => $fees->where('type', 'tuition')->sum('amount'),
        ],
        'miscellaneous'     => [
            'fees'  => $fees->where('type', 'miscellaneous')->values(),
            'total' => $fees->where('type', 'miscellaneous')->sum('amount'),
        ],
        'exam'              => [
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