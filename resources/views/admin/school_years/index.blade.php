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

@if(session('error'))
<div class="alert alert-light d-flex align-items-center shadow-sm rounded-3 mb-4 p-3" style="border-left: 4px solid #dc3545; background: white;">
    <i class="fas fa-exclamation-circle me-2" style="color: #dc3545;"></i>
    {{ session('error') }}
</div>
@endif

@php
    $currentYear       = $years->firstWhere('is_current', true);
    $currentSemester   = $currentYear?->semesters->firstWhere('is_current', true);
    $currentExamPeriod = $currentSemester?->examPeriods->firstWhere('is_current', true);
@endphp

{{-- ── Active Academic Period Banner ── --}}
<div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #0f3c91, #1a4da8);">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:52px; height:52px; background: rgba(255,255,255,0.15); flex-shrink:0;">
                <i class="fas fa-calendar-check fa-lg text-white"></i>
            </div>
            <div class="flex-grow-1">
                <p class="mb-1 text-white-50" style="font-size:13px; font-weight:500; text-transform:uppercase; letter-spacing:0.5px;">
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
                    {{-- Exam Period shown in banner --}}
                    @if($currentExamPeriod)
                        <div class="mt-2">
                            <span class="exam-period-banner-badge">
                                <i class="fas fa-clock me-1"></i> {{ $currentExamPeriod->name }}
                            </span>
                        </div>
                    @else
                        <div class="mt-2">
                            <span class="exam-period-banner-badge exam-period-unset">
                                <i class="fas fa-exclamation-circle me-1"></i> No exam period set
                            </span>
                        </div>
                    @endif
                @else
                    <h4 class="mb-0 text-white fw-bold">No active school year set</h4>
                @endif
            </div>
            @if($currentYear)
                <div class="ms-auto d-flex flex-column align-items-end gap-2">
                    <span class="badge rounded-pill px-3 py-2"
                          style="background: rgba(255,255,255,0.2); color: #fff; font-size:12px;">
                        <i class="fas fa-circle me-1" style="font-size:8px; color: #4caf50;"></i> Active
                    </span>
                    @if($currentSemester)
                        <button type="button"
                                class="btn btn-sm rounded-pill px-3"
                                style="background: rgba(255,255,255,0.2); color:#fff; border: 1px solid rgba(255,255,255,0.3); font-size:12px;"
                                data-bs-toggle="modal" data-bs-target="#examPeriodModal">
                            <i class="fas fa-edit me-1"></i>
                            {{ $currentExamPeriod ? 'Change' : 'Set' }} Exam Period
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Add School Year ── --}}
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

{{-- ── School Years Table ── --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">All School Years</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">School Year</th>
                        <th class="py-3">Semester</th>
                        <th class="py-3">Exam Period</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($years as $year)
                    @php
                        $activeSem = $year->semesters->firstWhere('is_current', true);
                        $activeEp  = $activeSem?->examPeriods->firstWhere('is_current', true);
                    @endphp
                    <tr class="school-year-row">
                        <td class="px-4 py-3 fw-medium">{{ $year->name }}</td>
                        <td class="py-3">
                            {{ $activeSem?->name ?? '—' }}
                        </td>
                        <td class="py-3">
                            @if($activeEp)
                                <span class="badge-exam-period">
                                    <i class="fas fa-clock me-1"></i>{{ $activeEp->name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
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

                                @if(!$year->is_current)
                                    <button type="button" class="btn-action delete-year" title="Delete school year"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-year-id="{{ $year->id }}" data-year-name="{{ $year->name }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
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

{{-- ── Current Exam Period Card ── --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">
            <i class="fas fa-clock me-2"></i> Current Exam Period
        </h5>
    </div>
    <div class="card-body p-4">
        @if($currentSemester)
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <p class="mb-1 text-muted small">
                        <i class="fas fa-school me-1"></i>
                        {{ $currentYear->name }} &mdash; {{ $currentSemester->name }}
                    </p>
                    @if($currentExamPeriod)
                        <h3 class="fw-bold mb-0" style="color: #0f3c91;">
                            <i class="fas fa-clock me-2" style="color: #f4b414;"></i>
                            {{ $currentExamPeriod->name }}
                        </h3>
                    @else
                        <h4 class="fw-bold mb-0 text-muted">Not set</h4>
                        <p class="text-warning mb-0 mt-1 small">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Students will not see fees until an exam period is set.
                        </p>
                    @endif
                </div>

                {{-- Period quick-select buttons --}}
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @foreach(['Prelim', 'Midterm', 'Semi-Final', 'Finals'] as $period)
                        <form method="POST" action="{{ route('admin.exam-periods.setCurrent') }}">
                            @csrf
                            <input type="hidden" name="exam_period" value="{{ $period }}">
                            <button type="submit"
                                    class="btn rounded-pill px-3 py-1
                                           {{ $currentExamPeriod?->name === $period
                                               ? 'btn-period-active'
                                               : 'btn-period-inactive' }}">
                                {{ $period }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-muted mb-0">
                <i class="fas fa-info-circle me-1"></i>
                No active semester. Set a school year and semester first.
            </p>
        @endif
    </div>
</div>

{{-- ── Semester Modal ── --}}
<div class="modal fade" id="semesterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span id="yearName"></span> – Set Semester
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="semesterForm" action="">
                @csrf
                <input type="hidden" name="_year_id" id="semesterYearId" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="semester" class="form-label fw-medium">Select Semester</label>
                        <select class="form-select rounded-pill border-0 bg-light px-4 py-2" id="semester" name="semester" required>
                            <option value="" disabled selected>Choose...</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                        </select>
                    </div>
                    <div class="alert alert-warning rounded-3 py-2 px-3 mb-0" style="font-size:13px;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Changing the semester will reset the current exam period. Remember to set a new one.
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

{{-- ── Exam Period Modal (quick set via modal, kept for banner button) ── --}}
<div class="modal fade" id="examPeriodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-clock me-2"></i> Set Current Exam Period
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.exam-periods.setCurrent') }}">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        For: <strong>{{ $currentYear?->name }}</strong> &mdash; <strong>{{ $currentSemester?->name ?? 'No semester set' }}</strong>
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach(['Prelim', 'Midterm', 'Semi-Final', 'Finals'] as $period)
                        <div class="form-check period-radio-card">
                            <input class="form-check-input visually-hidden" type="radio"
                                   name="exam_period" id="modal_period{{ $loop->index }}"
                                   value="{{ $period }}"
                                   {{ $currentExamPeriod?->name === $period ? 'checked' : '' }}
                                   required>
                            <label class="form-check-label period-label" for="modal_period{{ $loop->index }}">
                                {{ $period }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-check me-1"></i> Set Period
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Delete Confirmation Modal ── --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-trash-alt me-2"></i> Delete School Year</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x mb-3" style="color: #dc3545;"></i>
                <p class="mb-1">Are you sure you want to delete</p>
                <p class="fw-bold fs-5 mb-1" id="deleteYearName"></p>
                <p class="text-muted small">This will also delete all associated semesters, exam periods, and fees.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="fas fa-trash-alt me-2"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .school-year-row { transition: all 0.2s ease; }
    .school-year-row:hover {
        background-color: rgba(15, 60, 145, 0.02) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.02);
    }
    .btn-action {
        width: 36px; height: 36px; border-radius: 50%; border: none;
        display: inline-flex; align-items: center; justify-content: center;
        transition: all 0.2s; cursor: pointer; background: transparent;
        color: #64748b; padding: 0;
    }
    .btn-action:hover { background: rgba(15,60,145,0.1); color: #0f3c91; transform: scale(1.1); }
    .btn-action.set-current:hover  { background: rgba(244,180,20,0.1); color: #b26a00; }
    .btn-action.set-semester:hover { background: rgba(15,60,145,0.1);  color: #0f3c91; }
    .btn-action.delete-year:hover  { background: rgba(220,53,69,0.1);  color: #dc3545; }

    .btn-action-submit {
        background: #0f3c91; color: white; border: none;
        font-weight: 500; transition: all 0.2s;
    }
    .btn-action-submit:hover {
        background: #1a4da8; transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(15,60,145,0.2);
    }

    .badge-current {
        background: rgba(40, 167, 69, 0.15); color: #28a745;
        font-weight: 600; padding: 0.45rem 1rem; border-radius: 30px;
        display: inline-flex; align-items: center; font-size: 0.85rem;
    }

    /* Exam period badge in table */
    .badge-exam-period {
        font-weight: 600; padding: 0.35rem 0.85rem; border-radius: 30px;
        display: inline-flex; align-items: center; font-size: 0.8rem;
        background: rgba(234, 88, 12, 0.12); color: #c2410c;
    }

    /* Exam period pill in banner */
    .exam-period-banner-badge {
        display: inline-flex; align-items: center;
        background: rgba(255,255,255,0.2); color: #fff;
        padding: 0.3rem 0.85rem; border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }
    .exam-period-banner-badge.exam-period-unset {
        background: rgba(251, 191, 36, 0.25); color: #fef08a;
    }

    /* Quick-set period buttons in the exam period card */
    .btn-period-active {
        background: #0f3c91; color: #fff; border: 2px solid #0f3c91;
        font-weight: 600; font-size: 13px; transition: all 0.2s;
    }
    .btn-period-active:hover { background: #1a4da8; border-color: #1a4da8; color: #fff; }
    .btn-period-inactive {
        background: #f1f5f9; color: #475569;
        border: 2px solid #e2e8f0; font-size: 13px; transition: all 0.2s;
    }
    .btn-period-inactive:hover {
        background: #e2e8f0; border-color: #cbd5e1; color: #1e293b;
    }

    /* Modal period radio cards */
    .period-radio-card { margin: 0; }
    .period-label {
        display: inline-block; padding: 0.5rem 1.25rem; border-radius: 30px;
        border: 2px solid #e2e8f0; background: #f8fafc;
        cursor: pointer; font-weight: 500; font-size: 14px;
        transition: all 0.15s; color: #475569;
    }
    .form-check-input:checked + .period-label {
        background: #0f3c91; border-color: #0f3c91; color: #fff; font-weight: 600;
    }
    .period-label:hover { border-color: #0f3c91; color: #0f3c91; background: #eff6ff; }

    .empty-state { padding: 2rem; }
    .empty-state i { opacity: 0.7; }
    .empty-state h6 { font-size: 1.1rem; }
    .empty-state p { font-size: 0.9rem; max-width: 300px; margin: 0 auto; }

    .table td { border-bottom: 1px solid #f0f2f5; color: #334155; vertical-align: middle; }
    .table th { font-weight: 600; color: #475569; border-bottom: 2px solid #e9ecef; }
    .form-control:focus, .form-select:focus { box-shadow: none; border-color: #0f3c91; }

    .btn-primary { background: #0f3c91; border: none; padding: 0.6rem 1.5rem; border-radius: 30px; font-weight: 500; }
    .btn-primary:hover { background: #1a4da8; }
    .btn-secondary { background: #e9ecef; border: none; color: #495057; padding: 0.6rem 1.5rem; border-radius: 30px; font-weight: 500; }
    .btn-secondary:hover { background: #d3d8de; }
    .btn-danger { background: #dc3545; border: none; padding: 0.6rem 1.5rem; border-radius: 30px; font-weight: 500; }
    .btn-danger:hover { background: #c82333; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Semester modal — inject correct year ID into form action
 const semesterModal = document.getElementById('semesterModal');
if (semesterModal) {
    const semesterForm = document.getElementById('semesterForm');
    const semesterYearId = document.getElementById('semesterYearId');

    semesterModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const yearId = button.getAttribute('data-year-id');
        const yearName = button.getAttribute('data-year-name');

        semesterYearId.value = yearId;
        semesterForm.action = `{{ url('admin/school-years') }}/${yearId}/set-semester`;
        document.getElementById('yearName').textContent = yearName;

        console.log('Action set to:', semesterForm.action);
    });

    semesterForm.addEventListener('submit', function () {
        console.log('Submitting to:', this.action, 'method:', this.method);
    });
}

    // Delete modal
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button   = event.relatedTarget;
            const yearId   = button.getAttribute('data-year-id');
            const yearName = button.getAttribute('data-year-name');
            document.getElementById('deleteYearName').textContent = yearName;
            document.getElementById('deleteForm').action = `/admin/school-years/${yearId}`;
        });
    }
});
</script>
@endpush
