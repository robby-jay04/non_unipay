<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use Illuminate\Http\Request;

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
}