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
        $fees = Fee::currentSchoolYear()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'fees' => $fees,
            'count' => $fees->count(),
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

        $fee = Fee::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fee created successfully',
            'fee' => $fee,
        ], 201);
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
    public function destroy($id)
    {
        $fee = Fee::findOrFail($id);
        $fee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fee deleted successfully',
        ]);
    }

    /**
     * Get fees breakdown by type
     */
    public function breakdown()
    {
        $fees = Fee::currentSchoolYear()->get();

        $breakdown = [
            'tuition' => [
                'fees' => $fees->where('type', 'tuition'),
                'total' => $fees->where('type', 'tuition')->sum('amount'),
            ],
            'miscellaneous' => [
                'fees' => $fees->where('type', 'miscellaneous'),
                'total' => $fees->where('type', 'miscellaneous')->sum('amount'),
            ],
            'exam' => [
                'fees' => $fees->where('type', 'exam'),
                'total' => $fees->where('type', 'exam')->sum('amount'),
            ],
            'grand_total' => $fees->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'breakdown' => $breakdown,
        ]);
    }
}
