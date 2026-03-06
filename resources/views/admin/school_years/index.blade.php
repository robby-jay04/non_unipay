@extends('admin.layouts.app')

@section('content')
<h2>School Year Management</h2>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.school-years.store') }}" method="POST" class="mb-4">
    @csrf
    <div class="input-group">
        <input type="text" name="name" class="form-control" placeholder="2025-2026" required>
        <button class="btn btn-primary">Add School Year</button>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Current</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($years as $year)
        <tr>
            <td>{{ $year->name }}</td>
            <td>
                @if($year->is_current)
                    <span class="badge bg-success">Current</span>
                @endif
            </td>
            <td>
                @if(!$year->is_current)
                <form action="{{ route('admin.school-years.setCurrent', $year->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-warning">Set as Current</button>
                </form>
                @endif
                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#semesterModal"
                    data-year-id="{{ $year->id }}" data-year-name="{{ $year->name }}">
                    Set Semester
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Semester Modal -->
<div class="modal fade" id="semesterModal" tabindex="-1" aria-labelledby="semesterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.school-years.setSemester', ['schoolYear' => ':yearId']) }}" id="semesterForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="semesterModalLabel">Set Semester for <span id="yearName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="semester" class="form-label">Select Semester</label>
                        <select class="form-select" id="semester" name="semester" required>
                            <option value="">Choose...</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Semester</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const semesterModal = document.getElementById('semesterModal');
    if (semesterModal) {
        semesterModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const yearId = button.getAttribute('data-year-id');
            const yearName = button.getAttribute('data-year-name');
            const form = document.getElementById('semesterForm');
            // Replace placeholder in action URL
            form.action = form.action.replace(':yearId', yearId);
            document.getElementById('yearName').textContent = yearName;
        });
    }
});
</script>
@endpush