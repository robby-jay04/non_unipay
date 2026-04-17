@extends('admin.layouts.app')

@section('title', 'Fee Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold" style="color: var(--text-primary);">
        <i class="fas fa-coins me-2"></i> Fee Management
    </h2>
    <button type="button"
            class="btn-add-fee rounded-pill px-4 py-2"
            data-bs-toggle="modal"
            data-bs-target="#createFeeModal">
        <i class="fas fa-plus-circle me-2"></i> Add New Fee
    </button>
</div>

@if(session('success'))
<div class="alert alert-light d-flex align-items-center shadow-sm rounded-4 mb-4 p-3" style="border-left: 4px solid #28a745; background: var(--bg-main);">
    <i class="fas fa-check-circle me-2" style="color: #28a745;"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-light d-flex align-items-center shadow-sm rounded-4 mb-4 p-3" style="border-left: 4px solid #dc3545; background: var(--bg-main);">
    <i class="fas fa-exclamation-circle me-2" style="color: #dc3545;"></i>
    {{ session('error') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-light d-flex align-items-start shadow-sm rounded-4 mb-4 p-3" style="border-left: 4px solid #dc3545; background: var(--bg-main);">
    <i class="fas fa-exclamation-circle me-3 mt-1" style="color: #dc3545; font-size: 1.2rem;"></i>
    <div class="flex-grow-1">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li style="color: var(--text-secondary);">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<!-- Filter Section -->
<div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--bg-main);">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.fees.index') }}" class="row g-3 align-items-end requires-loader">
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">School Year</label>
                <select name="school_year" id="school_year" class="form-select rounded-pill border-0 px-4 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                    <option value="">All School Years</option>
                    @foreach($schoolYears as $year)
                        <option value="{{ $year->id }}" {{ request('school_year') == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">Semester</label>
                <select name="semester" id="semester" class="form-select rounded-pill border-0 px-4 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                    <option value="">All Semesters</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester') == $sem->id ? 'selected' : '' }}>
                            {{ $sem->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">Exam Period</label>
                <select name="exam_period" id="exam_period" class="form-select rounded-pill border-0 px-4 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                    <option value="">All Exam Periods</option>
                    @foreach($examPeriods as $period)
                        <option value="{{ $period }}" {{ request('exam_period') == $period ? 'selected' : '' }}>
                            {{ $period }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 flex-grow-1">
                    <i class="fas fa-filter me-2"></i> Apply Filters
                </button>
                <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                    <i class="fas fa-undo-alt me-2"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Fees Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bg-main);">
    <div class="card-header border-0 py-3 px-4" style="background: var(--bg-main);">
        <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">
            <i class="fas fa-list me-2"></i> All Fees
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 fee-table">
                <thead style="background: var(--table-header-bg);">
                    <tr>
                        <th class="px-4 py-3" style="color: var(--text-primary);">Name</th>
                        <th class="py-3" style="color: var(--text-primary);">Type</th>
                        <th class="py-3" style="color: var(--text-primary);">Course</th>
                        <th class="py-3" style="color: var(--text-primary);">Amount</th>
                        <th class="py-3" style="color: var(--text-primary);">School Year</th>
                        <th class="py-3" style="color: var(--text-primary);">Semester</th>
                        <th class="py-3" style="color: var(--text-primary);">Exam Period</th>
                        <th class="py-3 pe-4 text-end" style="color: var(--text-primary);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $fee)
                    <tr class="fee-row">
                        <td class="px-4 py-3 fw-semibold" style="color: var(--text-primary);">{{ $fee->name }}</td>
                        <td class="py-3">
                            <span class="badge-type badge-type-{{ $fee->type }}">
                                @if($fee->type == 'tuition')
                                    <i class="fas fa-graduation-cap me-1"></i>
                                @elseif($fee->type == 'miscellaneous')
                                    <i class="fas fa-cogs me-1"></i>
                                @elseif($fee->type == 'exam')
                                    <i class="fas fa-pencil-alt me-1"></i>
                                @endif
                                {{ ucfirst($fee->type) }}
                            </span>
                        </td>
                        <td class="py-3">
                            @if($fee->course)
                                <span class="badge-course">
                                    <i class="fas fa-book me-1"></i>{{ $fee->course }}
                                </span>
                            @else
                                <span class="badge-all-courses">
                                    <i class="fas fa-globe me-1"></i> All Courses
                                </span>
                            @endif
                        </td>
                        <td class="py-3 fw-bold" style="color: #0f3c91;">₱{{ number_format($fee->amount, 2) }}</td>
                        <td class="py-3" style="color: var(--text-secondary);">{{ $fee->school_year }}</td>
                        <td class="py-3" style="color: var(--text-secondary);">{{ $fee->semester ?? '—' }}</td>
                        <td class="py-3">
                            @if($fee->exam_period)
                                <span class="badge-exam-period">
                                    <i class="fas fa-clock me-1"></i>{{ $fee->exam_period }}
                                </span>
                            @else
                                <span class="badge-all-periods">
                                    <i class="fas fa-infinity me-1"></i> All Periods
                                </span>
                            @endif
                        </td>
                        <td class="py-3 pe-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button"
                                        class="btn-action edit-fee-btn"
                                        title="Edit fee"
                                        data-id="{{ $fee->id }}"
                                        data-name="{{ $fee->name }}"
                                        data-type="{{ $fee->type }}"
                                        data-course="{{ $fee->course }}"
                                        data-amount="{{ $fee->amount }}"
                                        data-school-year-id="{{ $fee->school_year_id }}"
                                        data-semester-id="{{ $fee->semester_id }}"
                                        data-exam-period-id="{{ $fee->exam_period_id }}">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button type="button"
                                        class="btn-action delete-fee"
                                        title="Delete fee"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="{{ $fee->id }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-coins fa-4x" style="color: var(--text-muted);"></i>
                                <h6 class="fw-semibold mt-3" style="color: var(--text-primary);">No fees found</h6>
                                <p class="small" style="color: var(--text-muted);">Try adjusting your filters or add a new fee.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===================== CREATE FEE MODAL ===================== --}}
<div class="modal fade" id="createFeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: var(--bg-main);">
            <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #0f3c91, #1a4da8);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255,255,255,0.15);">
                        <i class="fas fa-plus-circle fa-lg text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Add New Fee</h5>
                        <small class="text-white-50">Fill in the details below</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="createFeeForm" action="{{ route('admin.fees.store') }}" method="POST" class="requires-loader">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">Fee Name</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-tag" style="color: #0f3c91;"></i>
                                </span>
                                <input type="text" name="name" class="form-control border-0 px-3 py-2"
                                       placeholder="e.g., Tuition Fee" value="{{ old('name') }}" required
                                       style="background: var(--input-bg); color: var(--text-primary);">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">Type</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-layer-group" style="color: #0f3c91;"></i>
                                </span>
                                <select name="type" class="form-select border-0 px-3 py-2" required style="background: var(--input-bg); color: var(--text-primary);">
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="tuition"       {{ old('type') == 'tuition'       ? 'selected' : '' }}>Tuition</option>
                                    <option value="miscellaneous" {{ old('type') == 'miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                                    <option value="exam"          {{ old('type') == 'exam'          ? 'selected' : '' }}>Exam</option>
                                </select>
                            </div>
                        </div>

                        {{-- ── Course — built from $courses (Course models) ── --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">
                                Course <small class="fw-normal" style="color: var(--text-muted);">(blank = all)</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-graduation-cap" style="color: #0f3c91;"></i>
                                </span>
                                <select name="course" class="form-select border-0 px-3 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                                    <option value="">-- All Courses --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->code }}"
                                            {{ old('course') == $course->code ? 'selected' : '' }}>
                                            {{ $course->code }}{{ $course->name ? ' — ' . $course->name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">Amount (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-coins" style="color: #0f3c91;"></i>
                                </span>
                                <input type="number" name="amount" step="0.01" min="0" class="form-control border-0 px-3 py-2"
                                       placeholder="0.00" value="{{ old('amount') }}" required
                                       style="background: var(--input-bg); color: var(--text-primary);">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">School Year</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-calendar" style="color: #0f3c91;"></i>
                                </span>
                                <select name="school_year_id" id="create_school_year_id" class="form-select border-0 px-3 py-2" required style="background: var(--input-bg); color: var(--text-primary);">
                                    <option value="" disabled {{ old('school_year_id') ? '' : 'selected' }}>-- Select School Year --</option>
                                    @foreach($schoolYears as $schoolYear)
                                        <option value="{{ $schoolYear->id }}"
                                            {{ old('school_year_id') == $schoolYear->id ? 'selected' :
                                               (!old('school_year_id') && $currentSchoolYear && $currentSchoolYear->id == $schoolYear->id ? 'selected' : '') }}>
                                            {{ $schoolYear->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">Semester</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-calendar-week" style="color: #0f3c91;"></i>
                                </span>
                                <select name="semester_id" id="create_semester_id" class="form-select border-0 px-3 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                                    <option value="">-- Select School Year First --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">
                                Exam Period <small class="fw-normal" style="color: var(--text-muted);">(blank = all)</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-clock" style="color: #0f3c91;"></i>
                                </span>
                                <select name="exam_period_id" id="create_exam_period_id" class="form-select border-0 px-3 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                                    <option value="">-- All Exam Periods --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 flex-grow-1" data-bs-dismiss="modal" style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                <button type="submit" form="createFeeForm" class="btn btn-primary rounded-pill px-4 flex-grow-1">
                    <i class="fas fa-save me-2"></i> Save Fee
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===================== EDIT FEE MODAL ===================== --}}
<div class="modal fade" id="editFeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: var(--bg-main);">
            <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #0f3c91, #1a4da8);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255,255,255,0.15);">
                        <i class="fas fa-edit fa-lg text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Edit Fee Information</h5>
                        <small class="text-white-50">Update the fee details</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editFeeForm" method="POST" class="requires-loader">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">Fee Name</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-tag" style="color: #0f3c91;"></i>
                                </span>
                                <input type="text" name="name" id="edit_name" class="form-control border-0 px-3 py-2" required style="background: var(--input-bg); color: var(--text-primary);">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">Type</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-layer-group" style="color: #0f3c91;"></i>
                                </span>
                                <select name="type" id="edit_type" class="form-select border-0 px-3 py-2" required style="background: var(--input-bg); color: var(--text-primary);">
                                    <option value="tuition">Tuition</option>
                                    <option value="miscellaneous">Miscellaneous</option>
                                    <option value="exam">Exam</option>
                                </select>
                            </div>
                        </div>

                        {{-- ── Course — built from $courses (Course models) ── --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">
                                Course <small class="fw-normal" style="color: var(--text-muted);">(blank = all)</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-graduation-cap" style="color: #0f3c91;"></i>
                                </span>
                                <select name="course" id="edit_course" class="form-select border-0 px-3 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                                    <option value="">-- All Courses --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->code }}">
                                            {{ $course->code }}{{ $course->name ? ' — ' . $course->name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">Amount (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-coins" style="color: #0f3c91;"></i>
                                </span>
                                <input type="number" name="amount" id="edit_amount" step="0.01" min="0" class="form-control border-0 px-3 py-2" required style="background: var(--input-bg); color: var(--text-primary);">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">School Year</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-calendar" style="color: #0f3c91;"></i>
                                </span>
                                <select name="school_year_id" id="edit_school_year_id" class="form-select border-0 px-3 py-2" required style="background: var(--input-bg); color: var(--text-primary);">
                                    <option value="" disabled>-- Select School Year --</option>
                                    @foreach($schoolYears as $schoolYear)
                                        <option value="{{ $schoolYear->id }}">{{ $schoolYear->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">Semester</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-calendar-week" style="color: #0f3c91;"></i>
                                </span>
                                <select name="semester_id" id="edit_semester_id" class="form-select border-0 px-3 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                                    <option value="">-- Select School Year First --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small mb-1" style="color: var(--text-muted);">
                                Exam Period <small class="fw-normal" style="color: var(--text-muted);">(blank = all)</small>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                    <i class="fas fa-clock" style="color: #0f3c91;"></i>
                                </span>
                                <select name="exam_period_id" id="edit_exam_period_id" class="form-select border-0 px-3 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                                    <option value="">-- All Exam Periods --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 flex-grow-1" data-bs-dismiss="modal" style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                <button type="submit" form="editFeeForm" class="btn btn-primary rounded-pill px-4 flex-grow-1">
                    <i class="fas fa-save me-2"></i> Update Fee
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===================== DELETE CONFIRMATION MODAL ===================== --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: var(--bg-main);">
            <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #dc3545, #b02a37);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255,255,255,0.15);">
                        <i class="fas fa-trash-alt fa-lg text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Confirm Delete</h5>
                        <small class="text-white-50">This action cannot be undone</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x mb-3" style="color: #dc3545;"></i>
                <p class="mb-0" style="color: var(--text-primary);">Are you sure you want to delete this fee?</p>
                <p class="small mt-1" style="color: var(--text-muted);">All associated records will be removed permanently.</p>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 flex-grow-1" data-bs-dismiss="modal" style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                <form id="deleteForm" method="POST" class="requires-loader flex-grow-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 w-100">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Dark mode table overrides */
    .fee-table,
    .fee-table tbody,
    .fee-table tr,
    .fee-table td {
        background-color: var(--bg-main);
        color: var(--text-secondary);
    }
    .fee-table thead th {
        background-color: var(--table-header-bg);
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
    }
    .fee-table tbody tr {
        border-bottom: 1px solid var(--table-row-border);
        transition: background 0.2s;
    }
    .fee-table tbody tr:hover {
        background-color: var(--hover-bg) !important;
    }
    .fee-table tbody td {
        background-color: var(--bg-main);
        color: var(--text-secondary);
        border-bottom: none;
    }
    .fee-table tbody td:first-child {
        color: var(--text-primary);
        font-weight: 500;
    }

    /* Placeholder dark mode */
    .form-control::placeholder,
    input::placeholder {
        color: var(--text-muted);
        opacity: 0.7;
    }
    body.dark .form-control::placeholder,
    body.dark input::placeholder {
        color: #94a3b8;
        opacity: 0.6;
    }

    /* Table Row Hover */
    .fee-row {
        transition: all 0.2s ease;
    }
    .fee-row:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }

    /* Action Buttons */
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
        color: var(--text-muted);
        padding: 0;
    }
    .btn-action:hover {
        background: rgba(15,60,145,0.1);
        color: #0f3c91;
        transform: scale(1.1);
    }
    .btn-action.delete-fee:hover {
        background: rgba(220,53,69,0.1);
        color: #dc3545;
    }

    /* Add Fee Button */
    .btn-add-fee {
        background: #0f3c91;
        color: white;
        border: none;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    .btn-add-fee:hover {
        background: #1a4da8;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(15,60,145,0.2);
        color: white;
    }
    body.dark .btn-add-fee {
        background: #3b82f6;
    }
    body.dark .btn-add-fee:hover {
        background: #2563eb;
    }

    /* Badges */
    .badge-type, .badge-course, .badge-all-courses, .badge-exam-period, .badge-all-periods {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 40px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        gap: 0.4rem;
    }
    .badge-type-tuition      { background: rgba(15,60,145,0.12); color: #0f3c91; }
    .badge-type-miscellaneous{ background: rgba(244,180,20,0.12); color: #b26a00; }
    .badge-type-exam         { background: rgba(76,175,80,0.12); color: #2e7d32; }
    .badge-course            { background: rgba(139,92,246,0.12); color: #6d28d9; }
    .badge-all-courses       { background: rgba(100,116,139,0.1); color: #64748b; font-weight: 500; }
    .badge-exam-period       { background: rgba(234,88,12,0.12); color: #c2410c; }
    .badge-all-periods       { background: rgba(100,116,139,0.1); color: #64748b; font-weight: 500; }

    body.dark .badge-type-tuition      { background: rgba(59,130,246,0.2); color: #93c5fd; }
    body.dark .badge-type-miscellaneous{ background: rgba(244,180,20,0.25); color: #ffd54f; }
    body.dark .badge-type-exam         { background: rgba(76,175,80,0.25); color: #81c784; }
    body.dark .badge-course            { background: rgba(139,92,246,0.25); color: #a78bfa; }
    body.dark .badge-all-courses       { background: rgba(100,116,139,0.2); color: #94a3b8; }
    body.dark .badge-exam-period       { background: rgba(249,115,22,0.2); color: #fdba74; }
    body.dark .badge-all-periods       { background: rgba(100,116,139,0.2); color: #94a3b8; }

    /* Empty State */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }
    .empty-state i {
        opacity: 0.7;
    }

    /* Table base styling */
    .table td {
        border-bottom: 1px solid var(--table-row-border);
        color: var(--text-secondary);
        vertical-align: middle;
    }
    .table th {
        font-weight: 600;
        color: var(--text-primary);
        border-bottom: 2px solid var(--border-color);
        background: var(--table-header-bg);
    }

    /* Form Controls */
    .form-select, .form-control {
        background-color: var(--input-bg);
        border: 1px solid var(--input-border);
        color: var(--text-primary);
        transition: all 0.2s;
    }
    .form-select:focus, .form-control:focus {
        border-color: #0f3c91;
        box-shadow: 0 0 0 3px rgba(15,60,145,0.12);
        background-color: var(--input-bg);
    }
    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    /* Buttons */
    .btn-primary {
        background: #0f3c91;
        border: none;
        font-weight: 500;
    }
    .btn-primary:hover {
        background: #1a4da8;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(15,60,145,0.2);
    }
    .btn-outline-secondary {
        border: 1px solid var(--input-border);
        color: var(--text-primary);
        background: var(--bg-main);
    }
    .btn-outline-secondary:hover {
        background: var(--hover-bg);
        border-color: var(--text-muted);
        transform: translateY(-1px);
    }
    .btn-light {
        background: var(--input-bg);
        border: none;
        color: var(--text-primary);
    }
    .btn-light:hover {
        background: var(--hover-bg);
    }
    body.dark .btn-primary {
        background: #3b82f6;
    }
    body.dark .btn-primary:hover {
        background: #2563eb;
    }

    /* Modal Input Group Tweaks */
    .modal .input-group-text {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .modal .form-control,
    .modal .form-select {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Helpers: AJAX loaders ─────────────────────────────────────────────────
    function loadSemesters(schoolYearId, semesterSelect, examPeriodSelect, preselectId = null, afterLoad = null) {
        if (!schoolYearId) {
            semesterSelect.innerHTML   = '<option value="">-- Select School Year First --</option>';
            examPeriodSelect.innerHTML = '<option value="">-- All Exam Periods --</option>';
            return;
        }
        semesterSelect.innerHTML   = '<option value="">Loading...</option>';
        examPeriodSelect.innerHTML = '<option value="">-- All Exam Periods --</option>';

        fetch(`/admin/api/semesters/${schoolYearId}`)
            .then(r => r.json())
            .then(data => {
                semesterSelect.innerHTML = '<option value="">-- Select Semester --</option>';
                data.forEach(sem => {
                    const selected = preselectId ? sem.id == preselectId : sem.is_current;
                    semesterSelect.innerHTML += `<option value="${sem.id}" ${selected ? 'selected' : ''}>${sem.name}</option>`;
                });
                if (afterLoad) afterLoad();
                else if (semesterSelect.value) {
                    loadExamPeriods(semesterSelect.value, examPeriodSelect);
                }
            })
            .catch(() => { semesterSelect.innerHTML = '<option value="">Failed to load</option>'; });
    }

    function loadExamPeriods(semesterId, examPeriodSelect, preselectId = null) {
        if (!semesterId) {
            examPeriodSelect.innerHTML = '<option value="">-- All Exam Periods --</option>';
            return;
        }
        examPeriodSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`/admin/api/exam-periods/${semesterId}`)
            .then(r => r.json())
            .then(data => {
                examPeriodSelect.innerHTML = '<option value="">-- All Exam Periods --</option>';
                data.forEach(p => {
                    const selected = preselectId && p.id == preselectId;
                    examPeriodSelect.innerHTML += `<option value="${p.id}" ${selected ? 'selected' : ''}>${p.name}</option>`;
                });
            })
            .catch(() => { examPeriodSelect.innerHTML = '<option value="">Failed to load</option>'; });
    }

    // ── CREATE modal ─────────────────────────────────────────────────────────
    const createSchoolYear  = document.getElementById('create_school_year_id');
    const createSemester    = document.getElementById('create_semester_id');
    const createExamPeriod  = document.getElementById('create_exam_period_id');
    const oldCreateSemId    = "{{ old('semester_id') }}";
    const oldCreateExPerId  = "{{ old('exam_period_id') }}";

    createSchoolYear.addEventListener('change', function () {
        loadSemesters(this.value, createSemester, createExamPeriod);
    });
    createSemester.addEventListener('change', function () {
        loadExamPeriods(this.value, createExamPeriod);
    });

    // Auto-load on page open if old() values exist (after validation error)
    if (createSchoolYear.value) {
        loadSemesters(createSchoolYear.value, createSemester, createExamPeriod, oldCreateSemId || null, function () {
            if (createSemester.value && oldCreateExPerId) {
                loadExamPeriods(createSemester.value, createExamPeriod, oldCreateExPerId);
            }
        });

        // Re-open modal automatically if there were validation errors
        <?php if($errors->any() && old('_modal') === 'create'): ?>
            new bootstrap.Modal(document.getElementById('createFeeModal')).show();
        <?php endif; ?>
    }

    // ── EDIT modal ───────────────────────────────────────────────────────────
    const editSchoolYear   = document.getElementById('edit_school_year_id');
    const editSemester     = document.getElementById('edit_semester_id');
    const editExamPeriod   = document.getElementById('edit_exam_period_id');

    editSchoolYear.addEventListener('change', function () {
        loadSemesters(this.value, editSemester, editExamPeriod);
    });
    editSemester.addEventListener('change', function () {
        loadExamPeriods(this.value, editExamPeriod);
    });

    // Populate edit modal when the edit button is clicked
    document.querySelectorAll('.edit-fee-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const feeId        = this.dataset.id;
            const semesterId   = this.dataset.semesterId   || '';
            const examPeriodId = this.dataset.examPeriodId || '';

            // Set the form action
            document.getElementById('editFeeForm').action =
                "{{ route('admin.fees.update', ':id') }}".replace(':id', feeId);

            // Fill simple fields
            document.getElementById('edit_name').value   = this.dataset.name;
            document.getElementById('edit_amount').value = this.dataset.amount;

            // Set type
            document.getElementById('edit_type').value = this.dataset.type;

            // Set course — matches against $course->code values in the select
            document.getElementById('edit_course').value = this.dataset.course || '';

            // Set school year then cascade-load semester + exam period
            editSchoolYear.value = this.dataset.schoolYearId;
            loadSemesters(editSchoolYear.value, editSemester, editExamPeriod, semesterId, function () {
                if (editSemester.value && examPeriodId) {
                    loadExamPeriods(editSemester.value, editExamPeriod, examPeriodId);
                }
            });

            // Show the modal
            new bootstrap.Modal(document.getElementById('editFeeModal')).show();
        });
    });

    // ── DELETE modal ─────────────────────────────────────────────────────────
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const feeId = event.relatedTarget.getAttribute('data-id');
            document.getElementById('deleteForm').action =
                "{{ route('admin.fees.destroy', ':id') }}".replace(':id', feeId);
        });
    }
});
</script>
@endpush