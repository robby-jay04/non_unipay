<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Student;


class SchoolYearController extends Controller
{
    public function index()
    {
        $years = SchoolYear::orderBy('name', 'desc')->get();
        return view('admin.school_years.index', compact('years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:school_years,name'
        ]);

        SchoolYear::create([
            'name' => $request->name,
            'is_current' => false
        ]);

        return back()->with('success', 'School year added.');
    }

    public function setCurrent($id)
    {
        SchoolYear::query()->update(['is_current' => false]);

        $year = SchoolYear::findOrFail($id);
        $year->update(['is_current' => true]);

        return back()->with('success', 'Current school year updated.');
    }
    

public function setSemester(Request $request, SchoolYear $schoolYear)
{
    $request->validate([
        'semester' => 'required|in:1st Semester,2nd Semester',
    ]);

    // Unset current for all semesters of this school year
    $schoolYear->semesters()->update(['is_current' => false]);

    // Find or create the selected semester
    $semester = $schoolYear->semesters()->firstOrCreate(['name' => $request->semester]);
    $semester->update(['is_current' => true]);

    // Update all students' semester to the new current semester
    Student::query()->update(['semester' => $request->semester]);

    return redirect()->back()->with('success', 'Current semester updated for all students.');
}
}