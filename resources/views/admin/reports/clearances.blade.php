@extends('admin.layouts.app')

@section('title', 'Clearance Report')

@section('content')
<h2 class="mb-4">Student Clearance Status</h2>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
          <tbody>
@forelse($clearances as $student)
<tr>
    <td>{{ $student->user->name }}</td>

    <td>
        <span class="badge bg-success">
            Fully Paid
        </span>
    </td>

    <td>{{ $student->updated_at->format('M d, Y') }}</td>
</tr>
@empty
<tr>
    <td colspan="3" class="text-center text-muted">
        No fully paid students found
    </td>
</tr>
@endforelse
</tbody>
        </table>
    </div>
</div>
@endsection
