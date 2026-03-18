<?php

namespace App\Http\Controllers;

use App\Models\ExamPeriod;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Fee;

class SchoolYearController extends Controller
{
    public function index()
    {
        $years = SchoolYear::with(['semesters.examPeriods'])->orderBy('name', 'desc')->get();
        return view('admin.school_years.index', compact('years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:school_years,name'
        ]);

        $year = SchoolYear::create([
            'name'       => $request->name,
            'is_current' => false,
        ]);

        // Created with is_current false — activates when set as current
        $year->semesters()->create([
            'name'       => '1st Semester',
            'is_current' => false,
        ]);

        return back()->with('success', 'School year "' . $year->name . '" added with 1st Semester.');
    }

    public function setCurrent($id)
    {
        // Unset ALL school years, ALL semesters, and ALL exam periods first
        SchoolYear::query()->update(['is_current' => false]);
        Semester::query()->update(['is_current' => false]);
        ExamPeriod::query()->update(['is_current' => false]); // ← reset exam period

        $year = SchoolYear::findOrFail($id);
        $year->update(['is_current' => true]);

        // Auto-activate 1st Semester of the newly current school year
        $semester = $year->semesters()->where('name', '1st Semester')->first();
        if ($semester) {
            $semester->update(['is_current' => true]);
            Student::query()->update(['semester' => '1st Semester']);
        }

        return back()->with('success', 'School year "' . $year->name . '" is now active with 1st Semester. Please set the exam period.');
    }

    public function setSemester(Request $request, SchoolYear $schoolYear)
    {
        $request->validate([
            'semester' => 'required|in:1st Semester,2nd Semester',
        ]);

        // Unset current for all semesters of this school year
        $schoolYear->semesters()->update(['is_current' => false]);

        // Reset exam periods for all semesters of this school year
        $semesterIds = $schoolYear->semesters()->pluck('id');
        ExamPeriod::whereIn('semester_id', $semesterIds)->update(['is_current' => false]); // ← reset exam period

        // Find or create the selected semester
        $semester = $schoolYear->semesters()->firstOrCreate(['name' => $request->semester]);
        $semester->update(['is_current' => true]);

        // Update all students' semester to the new current semester
        Student::query()->update(['semester' => $request->semester]);

        return redirect()->back()->with('success', 'Current semester updated to "' . $request->semester . '". Please set the exam period.');
    }

    // Delete school year + related semesters, exam periods, and fees
    public function destroy($id)
    {
        $year = SchoolYear::findOrFail($id);

        if ($year->is_current) {
            return redirect()->route('admin.school-years.index')
                             ->with('error', 'Cannot delete the active school year.');
        }

        // Delete related fees
        Fee::where('school_year_id', $year->id)->delete();

        // Delete exam periods belonging to semesters of this year
        $semesterIds = $year->semesters()->pluck('id');
        ExamPeriod::whereIn('semester_id', $semesterIds)->delete(); // ← clean up exam periods

        // Delete related semesters
        $year->semesters()->delete();

        // Delete the school year itself
        $year->delete();

        return redirect()->route('admin.school-years.index')
                         ->with('success', 'School year "' . $year->name . '" and all related data deleted successfully.');
    }

    public function apiIndex()
    {
        $schoolYears = SchoolYear::orderBy('name', 'desc')->get(['id', 'name', 'is_current']);

        $currentSemester = Semester::where('is_current', 1)
            ->whereHas('schoolYear', function ($q) {
                $q->where('is_current', 1);
            })
            ->first(['id', 'name', 'is_current']);

        return response()->json([
            'school_years'     => $schoolYears,
            'current_semester' => $currentSemester,
        ]);
    }
}