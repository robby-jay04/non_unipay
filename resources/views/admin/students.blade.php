@extends('admin.layouts.app')

@section('title', 'Students')

@section('content')
<h2 class="mb-4">Student Management</h2>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Students</h5>
        <input type="search" class="form-control w-25" placeholder="Search students...">
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student No</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>Clearance Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>{{ $student->student_no }}</td>
                        <td>{{ $student->user->name }}</td>
                        <td>{{ $student->course }}</td>
                        <td>{{ $student->year_level }}</td>
                        <td>{{ ucfirst($student->clearance_status) }}</td>
                        <td>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
