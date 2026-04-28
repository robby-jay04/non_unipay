<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ExamPeriod;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Fee;
use App\Services\ClearanceService;

class SchoolYearController extends Controller
{
    // ══════════════════════════════════════════════════════════════
    //  SCHOOL YEAR METHODS
    // ══════════════════════════════════════════════════════════════

    public function index()
    {
        $years   = SchoolYear::with(['semesters.examPeriods'])->orderBy('name', 'desc')->get();
        $courses = Course::orderBy('code')->get();

        return view('admin.school_years.index', compact('years', 'courses'));
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|unique:school_years,name',
    ]);

    $year = SchoolYear::create([
        'name'       => $request->name,
        'is_current' => false,
    ]);

    $examPeriods = ['Prelim', 'Midterm', 'Semi-Final', 'Finals'];

    $sem1 = $year->semesters()->create(['name' => '1st Semester', 'is_current' => false]);
    foreach ($examPeriods as $index => $period) {
        $sem1->examPeriods()->create(['name' => $period, 'order' => $index + 1]);
    }

    $sem2 = $year->semesters()->create(['name' => '2nd Semester', 'is_current' => false]);
    foreach ($examPeriods as $index => $period) {
        $sem2->examPeriods()->create(['name' => $period, 'order' => $index + 1]);
    }

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

        app(ClearanceService::class)->resetAllClearances();
        app(ClearanceService::class)->bulkUpdateClearances();

        return back()->with('success', 'School year updated.');
    }

    public function destroy($id)
    {
        $year = SchoolYear::findOrFail($id);

        if ($year->is_current) {
            return redirect()->route('admin.school-years.index')
                             ->with('error', 'Cannot delete the active school year.');
        }

        Fee::where('school_year_id', $year->id)->delete();

        $semesterIds = $year->semesters()->pluck('id');
        ExamPeriod::whereIn('semester_id', $semesterIds)->delete();

        $year->semesters()->delete();
        $year->delete();

        return redirect()->route('admin.school-years.index')
                         ->with('success', 'School year "' . $year->name . '" and all related data deleted successfully.');
    }

    public function setSemester(Request $request, $id)
    {
        $semesterName = $request->input('semester');

        Semester::where('school_year_id', $id)->update(['is_current' => false]);

        $semester = Semester::where('school_year_id', $id)
                            ->where('name', $semesterName)
                            ->firstOrFail();

        $semester->update(['is_current' => true]);

        app(ClearanceService::class)->bulkUpdateClearances();

        return back()->with('success', 'Semester updated successfully.');
    }

  public function apiIndex()
{
    $schoolYears = SchoolYear::orderBy('name', 'desc')->get(['id', 'name', 'is_current']);

    $currentSemester = Semester::where('is_current', 1)
        ->whereHas('schoolYear', fn ($q) => $q->where('is_current', 1))
        ->first(['id', 'name', 'is_current']);

    return response()->json([
        'school_years'     => $schoolYears,
        'current_semester' => $currentSemester,
        'courses'          => \App\Models\Course::orderBy('code')->get(['id', 'code', 'name']),
    ]);
}
    // ══════════════════════════════════════════════════════════════
    //  COURSE MANAGEMENT METHODS
    // ══════════════════════════════════════════════════════════════

    /**
     * Store a new course.
     */
    public function storeCourse(Request $request)
    {
        $request->validate([
            'code'       => 'required|string|max:20|unique:courses,code',
            'name'       => 'required|string|max:150',
            'department' => 'nullable|string|max:100',
        ]);

        Course::create([
            'code'       => strtoupper(trim($request->code)),
            'name'       => trim($request->name),
            'department' => $request->department ? trim($request->department) : null,
        ]);

        return back()->with('success', 'Course "' . strtoupper($request->code) . '" added successfully.');
    }

    /**
     * Update an existing course.
     */
    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'code'       => 'required|string|max:20|unique:courses,code,' . $id,
            'name'       => 'required|string|max:150',
            'department' => 'nullable|string|max:100',
        ]);

        $course->update([
            'code'       => strtoupper(trim($request->code)),
            'name'       => trim($request->name),
            'department' => $request->department ? trim($request->department) : null,
        ]);

        return back()->with('success', 'Course "' . strtoupper($request->code) . '" updated successfully.');
    }

    /**
     * Delete a course.
     */
    public function destroyCourse($id)
    {
        $course = Course::findOrFail($id);
        $label  = $course->code;
        $course->delete();

        return back()->with('success', 'Course "' . $label . '" deleted successfully.');
    }
}