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
                @forelse($clearances as $clearance)
                <tr>
                    <td>{{ $clearance->student->user->name }}</td>
                    <td>
                        <span class="badge bg-{{ $clearance->status === 'cleared' ? 'success' : 'warning' }}">
                            {{ ucfirst($clearance->status) }}
                        </span>
                    </td>
                    <td>{{ $clearance->updated_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        No clearance records found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
