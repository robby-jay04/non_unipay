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

{{-- ✅ Current School Year & Semester Banner --}}
@php
    $currentYear     = $years->firstWhere('is_current', true);
    $currentSemester = $currentYear?->semesters->firstWhere('is_current', true);
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #0f3c91, #1a4da8);">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:52px; height:52px; background: rgba(255,255,255,0.15); flex-shrink:0;">
                <i class="fas fa-calendar-check fa-lg text-white"></i>
            </div>
            <div>
                <p class="mb-0 text-white-50" style="font-size:13px; font-weight:500; text-transform:uppercase; letter-spacing:0.5px;">
                    Active Academic Period
                </p>
                @if($currentYear)
                    <h4 class="mb-0 text-white fw-bold">
                        {{ $currentYear->name }}
                        @if($currentSemester)
                            &nbsp;—&nbsp;{{ $currentSemester->name }}
                        @else
                            <span class="text-white-50" style="font-size:16px; font-weight:400;">— No semester set</span>
                        @endif
                    </h4>
                @else
                    <h4 class="mb-0 text-white fw-bold">No active school year set</h4>
                @endif
            </div>
            @if($currentYear)
                <div class="ms-auto">
                    <span class="badge rounded-pill px-3 py-2"
                          style="background: rgba(255,255,255,0.2); color: #fff; font-size:12px;">
                        <i class="fas fa-circle me-1" style="font-size:8px; color: #4caf50;"></i> Active
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add School Year Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.school-years.store') }}" method="POST" class="d-flex gap-3">
            @csrf
            <div class="flex-grow-1">
                <input type="text" name="name" class="form-control rounded-pill border-0 bg-light px-4 py-2"
                       placeholder="e.g., 2025-2026" required>
            </div>
            <button type="submit" class="btn-action-submit rounded-pill px-4 py-2">
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
                        <th class="px-4 py-3">School Year</th>
                        <th class="py-3">Current</th>
                        <th class="py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($years as $year)
                    <tr class="school-year-row">
                        <td class="px-4 py-3 fw-medium">{{ $year->name }}</td>
                        <td class="py-3">
                            @if($year->is_current)
                                <span class="badge-current">
                                    <i class="fas fa-star me-1"></i> Current
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="py-3 pe-4">
                            <div class="d-flex gap-2">
                                @if(!$year->is_current)
                                    <form action="{{ route('admin.school-years.setCurrent', $year->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-action set-current" title="Set as current year">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                @endif
                                <button type="button" class="btn-action set-semester" title="Set semester"
                                        data-bs-toggle="modal" data-bs-target="#semesterModal"
                                        data-year-id="{{ $year->id }}" data-year-name="{{ $year->name }}">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times fa-4x" style="color: #d1d5db;"></i>
                                <h6 class="fw-semibold mt-3" style="color: #1e293b;">No school years found</h6>
                                <p class="text-muted small">Add a school year to get started.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Semester Modal (updated to match other modals) -->
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
                            <option value="" disabled selected>Choose...</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Update Semester</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* School year row hover */
    .school-year-row {
        transition: all 0.2s ease;
    }
    .school-year-row:hover {
        background-color: rgba(15, 60, 145, 0.02) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.02);
    }

    /* Action buttons (circular) */
    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        cursor: pointer;
        background: transparent;
        color: #64748b;
        padding: 0;
    }
    .btn-action:hover {
        background: rgba(15,60,145,0.1);
        color: #0f3c91;
        transform: scale(1.1);
    }
    .btn-action.set-current:hover {
        background: rgba(244,180,20,0.1);
        color: #b26a00;
    }
    .btn-action.set-semester:hover {
        background: rgba(15,60,145,0.1);
        color: #0f3c91;
    }

    /* Submit button (Add School Year) */
    .btn-action-submit {
        background: #0f3c91;
        color: white;
        border: none;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-action-submit:hover {
        background: #1a4da8;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(15,60,145,0.2);
    }

    /* Current badge */
    .badge-current {
        background: rgba(40, 167, 69, 0.15);
        color: #28a745;
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
    }

    /* Empty state */
    .empty-state {
        padding: 2rem;
    }
    .empty-state i {
        opacity: 0.7;
    }
    .empty-state h6 {
        font-size: 1.1rem;
    }
    .empty-state p {
        font-size: 0.9rem;
        max-width: 300px;
        margin: 0 auto;
    }

    /* Table */
    .table td {
        border-bottom: 1px solid #f0f2f5;
        color: #334155;
        vertical-align: middle;
    }
    .table th {
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e9ecef;
    }

    /* Form elements */
    .form-control:focus, .form-select:focus {
        box-shadow: none;
        border-color: #0f3c91;
    }

    /* Modal buttons (reuse from other pages) */
    .btn-primary {
        background: #0f3c91;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 30px;
        font-weight: 500;
    }
    .btn-primary:hover {
        background: #1a4da8;
    }
    .btn-secondary {
        background: #e9ecef;
        border: none;
        color: #495057;
        padding: 0.6rem 1.5rem;
        border-radius: 30px;
        font-weight: 500;
    }
    .btn-secondary:hover {
        background: #d3d8de;
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