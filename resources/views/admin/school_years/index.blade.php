@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">School Year Management</h2>
</div>

@if(session('success'))
<div class="alert alert-light d-flex align-items-center shadow-sm rounded-3 mb-4 p-3" style="border-left: 4px solid #28a745; background: white;">
    <i class="fas fa-check-circle me-2" style="color: #28a745;"></i>
    {{ session('success') }}
</div>
@endif

<!-- Add School Year Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.school-years.store') }}" method="POST" class="d-flex gap-3">
            @csrf
            <div class="flex-grow-1">
                <input type="text" name="name" class="form-control rounded-pill border-0 bg-light px-4 py-2" 
                       placeholder="e.g., 2025-2026" required>
            </div>
            <button type="submit" class="btn rounded-pill px-4" style="background: #0f3c91; color: white;">
                <i class="fas fa-plus-circle me-2"></i> Add School Year
            </button>
        </form>
    </div>
</div>

<!-- School Years Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">All School Years</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="py-3">Current</th>
                        <th class="py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($years as $year)
                    <tr>
                        <td class="px-4 py-3 fw-medium">{{ $year->name }}</td>
                        <td class="py-3">
                            @if($year->is_current)
                                <span class="badge-paid">Current</span>
                            @endif
                        </td>
                        <td class="py-3 pe-4">
                            @if(!$year->is_current)
                                <form action="{{ route('admin.school-years.setCurrent', $year->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill px-3"
                                            style="background: rgba(244, 180, 20, 0.15); color: #b26a00; border: none;">
                                        <i class="fas fa-star me-1"></i> Set as Current
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="btn btn-sm rounded-pill px-3"
                                    style="background: rgba(15, 60, 145, 0.1); color: #0f3c91; border: none;"
                                    data-bs-toggle="modal" data-bs-target="#semesterModal"
                                    data-year-id="{{ $year->id }}" data-year-name="{{ $year->name }}">
                                <i class="fas fa-calendar-week me-1"></i> Set Semester
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Semester Modal -->
<div class="modal fade" id="semesterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-calendar-alt me-2"></i> <span id="yearName"></span> – Set Semester</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.school-years.setSemester', ['schoolYear' => ':yearId']) }}" id="semesterForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="semester" class="form-label fw-medium">Select Semester</label>
                        <select class="form-select rounded-pill border-0 bg-light px-4 py-2" id="semester" name="semester" required>
                            <option value="">Choose...</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn rounded-pill px-4" style="background: #0f3c91; color: white;">Update Semester</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Badge style (reused from other pages) */
    .badge-paid {
        background: rgba(40, 167, 69, 0.15);
        color: #28a745;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }

    /* Table */
    .table td {
        border-bottom: 1px solid #f0f2f5;
        color: #334155;
    }
    .table th {
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e9ecef;
    }

    /* Input focus */
    .form-control:focus {
        box-shadow: none;
        border-color: #0f3c91;
    }
</style>
@endpush

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
            form.action = form.action.replace(':yearId', yearId);
            document.getElementById('yearName').textContent = yearName;
        });
    }
});
</script>
@endpush