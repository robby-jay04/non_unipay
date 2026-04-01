<?php

namespace App\Http\Controllers;

use App\Models\ExamPeriod;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Fee;
use App\Services\ClearanceService;
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

    $year->semesters()->create([
        'name'       => '1st Semester',
        'is_current' => false,
    ]);

    $year->semesters()->create([
        'name'       => '2nd Semester',
        'is_current' => false,
    ]);

    return back()->with('success', 'School year "' . $year->name . '" added with 1st and 2nd Semester.');
}

   public function setCurrent($id)
{
    SchoolYear::query()->update(['is_current' => false]);
    Semester::query()->update(['is_current' => false]);
    ExamPeriod::query()->update(['is_current' => false]);

    $year = SchoolYear::findOrFail($id);
    $year->update(['is_current' => true]);

    $semester = $year->semesters()->where('name', '1st Semester')->first();
    if ($semester) {
        $semester->update(['is_current' => true]);
        Student::query()->update(['semester' => '1st Semester']);
    }

    // 🔥 ADD THIS HERE
    app(\App\Services\ClearanceService::class)->resetAllClearances();
    app(\App\Services\ClearanceService::class)->bulkUpdateClearances();

    return back()->with('success', 'School year updated.');
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

   
    // Example method
 public function setSemester(Request $request, $id)
{
    $semesterName = $request->input('semester');

    Semester::where('school_year_id', $id)
            ->update(['is_current' => false]);

    $semester = Semester::where('school_year_id', $id)
                        ->where('name', $semesterName)
                        ->firstOrFail();

    $semester->is_current = true;
    $semester->save();

    app(\App\Services\ClearanceService::class)->bulkUpdateClearances();

    return redirect()->back()->with('success', 'Semester updated successfully.');
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