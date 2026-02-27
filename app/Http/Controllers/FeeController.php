<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;

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
    return view('admin.fees.create');
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
        'semester' => 'nullable|string',
        'school_year' => 'required|string',
    ]);

    Fee::create($validated);

    return redirect()
        ->route('admin.fees.index')
        ->with('success', 'Fee created successfully.');
}

public function edit(Fee $fee)
{
    return view('admin.fees.edit', compact('fee'));
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

    $fee->update($validated);

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
     * Get fees breakdown by type
     */
    public function breakdown()
{
    $student = auth()->user()->student;

    $fees = Fee::currentSchoolYear()->get();

    $grandTotal = $fees->sum('amount');

    $totalPaid = $student->payments()
        ->where('status', 'paid')
        ->sum('total_amount');

    $remainingBalance = $grandTotal - $totalPaid;

    $breakdown = [
        'tuition' => [
            'fees' => $fees->where('type', 'tuition')->values(),
            'total' => $fees->where('type', 'tuition')->sum('amount'),
        ],
        'miscellaneous' => [
            'fees' => $fees->where('type', 'miscellaneous')->values(),
            'total' => $fees->where('type', 'miscellaneous')->sum('amount'),
        ],
        'exam' => [
            'fees' => $fees->where('type', 'exam')->values(),
            'total' => $fees->where('type', 'exam')->sum('amount'),
        ],
        'grand_total' => $grandTotal,
        'total_paid' => $totalPaid,
        'remaining_balance' => max($remainingBalance, 0),
    ];

    return response()->json([
        'success' => true,
        'breakdown' => $breakdown,
    ]);
}
}
