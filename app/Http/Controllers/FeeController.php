<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;
use App\Http\Controllers\SemesterController;
use App\Models\Semester;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentFee;
class FeeController extends Controller
{
    /**
     * Display a listing of fees for current school year
     */
  public function index()
{
    $current = \App\Models\SchoolYear::current();

    return response()->json([
        'current_school_year' => $current,
    ]);
}

    /**
     * Get total of all fees
     */
    public function getTotalFees()
    {
        $total = Fee::currentSchoolYear()->sum('amount');

        return response()->json([
            'success' => true,
            'total' => $total,
            'formatted' => '₱' . number_format($total, 2),
        ]);
    }

    /**
     * Display specific fee
     */
    public function show($id)
    {
        $fee = Fee::findOrFail($id);

        return response()->json([
            'success' => true,
            'fee' => $fee,
        ]);
    }

    /**
     * Get fees by type (tuition, miscellaneous, exam)
     */
    public function getByType($type)
    {
        $fees = Fee::currentSchoolYear()
            ->byType($type)
            ->get();

        $total = $fees->sum('amount');

        return response()->json([
            'success' => true,
            'type' => $type,
            'fees' => $fees,
            'total' => $total,
        ]);
    }

    /**
     * Store a new fee (Admin only)
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'type' => 'required|in:tuition,miscellaneous,exam',
        'semester' => 'nullable|string',
        'school_year' => 'required|string',
    ]);

    Fee::create($validated);

    return redirect()->route('admin.fees.index')
                     ->with('success', 'Fee created successfully!');
}

  public function create()
{
    // Get all school years ordered by name (or by creation)
    $schoolYears = SchoolYear::orderBy('name', 'desc')->get();

    // Find the current school year (if any)
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();

    return view('admin.fees.create', compact('schoolYears', 'currentSchoolYear'));
}
public function adminIndex()
{
    $fees = \App\Models\Fee::orderBy('school_year', 'desc')
        ->orderBy('type')
        ->get();

    return view('admin.fees.index', compact('fees'));
}



public function storeWeb(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'type' => 'required|in:tuition,miscellaneous,exam',
    ]);

    $currentSemester = Semester::where('is_current', true)->first();
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();

    Fee::create([
        'name' => $validated['name'],
        'amount' => $validated['amount'],
        'type' => $validated['type'],
        'school_year' => $currentSchoolYear->name,
            'semester' => $currentSemester->name,
        'semester_id' => $currentSemester->id,
        'school_year_id' => $currentSchoolYear->id,
    ]);

    return redirect()
        ->route('admin.fees.index')
        ->with('success', 'Fee created successfully.');
}

public function edit(Fee $fee)
{
    // Get all school years ordered by name (latest first)
    $schoolYears = SchoolYear::orderBy('name', 'desc')->get();

    // Optional: get the current school year (if you want to highlight it)
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();

    return view('admin.fees.edit', compact('fee', 'schoolYears', 'currentSchoolYear'));
}


public function updateWeb(Request $request, Fee $fee)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'type' => 'required|in:tuition,miscellaneous,exam',
        'semester' => 'nullable|string',
        'school_year' => 'required|string',
    ]);

    // Look up the semester by name and get its ID
    $semester = Semester::where('name', $validated['semester'])->first();
    $schoolYear = SchoolYear::where('name', $validated['school_year'])->first();

    $fee->update([
        'name'           => $validated['name'],
        'amount'         => $validated['amount'],
        'type'           => $validated['type'],
        'semester'       => $validated['semester'],
        'school_year'    => $validated['school_year'],
        'semester_id'    => $semester?->id,
        'school_year_id' => $schoolYear?->id,
    ]);

    return redirect()
        ->route('admin.fees.index')
        ->with('success', 'Fee updated successfully.');
}

public function destroyWeb($fee)
{
    $fee = Fee::findOrFail($fee);
    $fee->delete();

    return redirect()->route('admin.fees.index')
        ->with('success', 'Fee deleted successfully.');
}
    /**
     * Update existing fee (Admin only)
     */
    public function update(Request $request, $id)
    {
        $fee = Fee::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|in:tuition,miscellaneous,exam',
            'semester' => 'nullable|string',
            'school_year' => 'sometimes|string',
        ]);

        $fee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fee updated successfully',
            'fee' => $fee,
        ]);
    }

    /**
     * Delete fee (Admin only)
     */
   public function destroy(Fee $fee)
{
    $fee->delete();

    return redirect()->route('admin.fees.index')
                     ->with('success', 'Fee deleted successfully.');
}
/**
 * Get fees breakdown by type for the authenticated student (mobile app)
 */
public function breakdown()
{
    $student = auth()->user()->student;

    $currentSemester = Semester::where('is_current', true)->first();
    $currentSchoolYear = SchoolYear::where('is_current', true)->first();

    if (!$currentSemester || !$currentSchoolYear) {
        return response()->json([
            'success' => false,
            'message' => 'No active semester or school year set.'
        ], 404);
    }

    $fees = Fee::where('school_year_id', $currentSchoolYear->id)
               ->where('semester_id', $currentSemester->id)
               ->get();

    // ✅ If no fees exist yet for this semester, don't mark as cleared
    if ($fees->isEmpty()) {
        return response()->json([
            'success' => true,
            'breakdown' => [
                'tuition'           => ['fees' => [], 'total' => 0],
                'miscellaneous'     => ['fees' => [], 'total' => 0],
                'exam'              => ['fees' => [], 'total' => 0],
                'grand_total'       => 0,
                'total_paid'        => 0,
                'remaining_balance' => 0,
                'status'            => 'no_fees', // ✅ distinct status
            ],
        ]);
    }

    $grandTotal = $fees->sum('amount');
    $totalPaid = 0;

    foreach ($fees as $fee) {
        $paidForFee = $fee->payments()
            ->where('student_id', $student->id)
            ->where('status', 'paid')
            ->sum('payments.total_amount');

        $totalPaid += $paidForFee;
    }

    $remainingBalance = max($grandTotal - $totalPaid, 0);

    // ✅ Only cleared if all fees are fully paid (and fees actually exist)
    $status = 'cleared';
    foreach ($fees as $fee) {
        $paidForFee = $fee->payments()
            ->where('student_id', $student->id)
            ->where('status', 'paid')
            ->sum('payments.total_amount');

        if ($paidForFee < $fee->amount) {
            $status = 'pending';
            break;
        }
    }

    $breakdown = [
        'tuition'           => ['fees' => $fees->where('type', 'tuition')->values(),       'total' => $fees->where('type', 'tuition')->sum('amount')],
        'miscellaneous'     => ['fees' => $fees->where('type', 'miscellaneous')->values(),  'total' => $fees->where('type', 'miscellaneous')->sum('amount')],
        'exam'              => ['fees' => $fees->where('type', 'exam')->values(),           'total' => $fees->where('type', 'exam')->sum('amount')],
        'grand_total'       => $grandTotal,
        'total_paid'        => $totalPaid,
        'remaining_balance' => $remainingBalance,
        'status'            => $status,
    ];

    return response()->json(['success' => true, 'breakdown' => $breakdown]);
}
}
