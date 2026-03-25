<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::with('semesters')->orderBy('name', 'desc')->get();
        return view('admin.semesters.index', compact('schoolYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'name' => 'required|string|max:255',
        ]);

        Semester::create($request->only('school_year_id', 'name'));

        return redirect()->route('admin.semesters.index')->with('success', 'Semester added.');
    }

    public function setCurrent(Semester $semester)
{
    Semester::where('school_year_id', $semester->school_year_id)
        ->update(['is_current' => false]);

    $semester->update(['is_current' => true]);

    // 🔥 ADD THIS HERE
    app(\App\Services\ClearanceService::class)->resetAllClearances();
    app(\App\Services\ClearanceService::class)->bulkUpdateClearances();

    return back()->with('success', 'Current semester updated.');
}

    public function destroy(Semester $semester)
    {
        $semester->delete();
        return redirect()->route('admin.semesters.index')->with('success', 'Semester deleted.');
    }
}