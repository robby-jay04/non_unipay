@extends('admin.layouts.app')

@section('content')
<h2>Semester Management</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-4">
    <div class="card-header">Add New Semester</div>
    <div class="card-body">
        <form action="{{ route('admin.semesters.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <select name="school_year_id" class="form-select" required>
                        <option value="">Select School Year</option>
                        @foreach($schoolYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="e.g., 1st Semester" required>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary">Add Semester</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Semesters by School Year</div>
    <div class="card-body">
        @forelse($schoolYears as $year)
            <h5 class="mt-3">{{ $year->name }} 
                @if($year->is_current)
                    <span class="badge bg-success ms-2">Current School Year</span>
                @endif
            </h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($year->semesters as $semester)
                        <tr>
                            <td>{{ $semester->name }}</td>
                            <td>
                                @if($semester->is_current)
                                    <span class="badge bg-success">Current</span>
                                @endif
                            </td>
                            <td>
                                @if(!$semester->is_current)
                                    <form action="{{ route('admin.semesters.setCurrent', $semester->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-warning">Set as Current</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.semesters.destroy', $semester->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this semester?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-muted">No semesters added.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @empty
            <p>No school years found. Please add a school year first.</p>
        @endforelse
    </div>
</div>
@endsection