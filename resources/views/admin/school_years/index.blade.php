@extends('admin.layouts.app')

@section('title', 'School Year Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold" style="color: var(--text-primary);">
        <i class="fas fa-calendar-alt me-2"></i> School Year Management
    </h2>
    <button type="button" class="btn-add-year rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#addYearModal">
        <i class="fas fa-plus-circle me-2"></i> Add School Year
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

@php
    $currentYear       = $years->firstWhere('is_current', true);
    $currentSemester   = $currentYear?->semesters->firstWhere('is_current', true);
    $currentExamPeriod = $currentSemester?->examPeriods->firstWhere('is_current', true);
@endphp

{{-- ── Active Academic Period Banner (dark mode compatible) ── --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f3c91 0%, #1a4da8 100%);">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 56px; height: 56px; background: rgba(255,255,255,0.15);">
                        <i class="fas fa-calendar-check fa-2x text-white"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-white-50 text-uppercase small fw-semibold tracking-wide">
                            Active Academic Period
                        </p>
                        @if($currentYear)
                            <h3 class="mb-0 text-white fw-bold">
                                {{ $currentYear->name }}
                                @if($currentSemester)
                                    <span class="fw-normal">— {{ $currentSemester->name }}</span>
                                @else
                                    <span class="text-white-50 fs-6 fw-normal">(No semester set)</span>
                                @endif
                            </h3>
                            @if($currentExamPeriod)
                                <div class="mt-2">
                                    <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-1">
                                        <i class="fas fa-clock me-1"></i> {{ $currentExamPeriod->name }}
                                    </span>
                                </div>
                            @else
                                <div class="mt-2">
                                    <span class="badge bg-warning bg-opacity-25 text-warning rounded-pill px-3 py-1">
                                        <i class="fas fa-exclamation-triangle me-1"></i> No exam period set
                                    </span>
                                </div>
                            @endif
                        @else
                            <h4 class="mb-0 text-white">No active school year</h4>
                            <p class="text-white-50 mt-2 mb-0 small">Click "Add School Year" to get started.</p>
                        @endif
                    </div>
                </div>
            </div>
            @if($currentYear && $currentSemester)
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 mb-2 d-inline-flex align-items-center">
                        <i class="fas fa-circle me-1" style="font-size: 8px; color: #4caf50;"></i> Active
                    </span>
                    <button type="button"
                            class="btn btn-sm rounded-pill px-3 ms-md-2 mt-2 mt-md-0"
                            style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);"
                            data-bs-toggle="modal" data-bs-target="#examPeriodModal">
                        <i class="fas fa-edit me-1"></i>
                        {{ $currentExamPeriod ? 'Change' : 'Set' }} Exam Period
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ── School Years Table ── --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--bg-main);">
    <div class="card-header border-0 py-3 px-4" style="background: var(--bg-main);">
        <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">
            <i class="fas fa-list me-2"></i> All School Years
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 schoolyear-table">
                <thead style="background: var(--table-header-bg);">
                    <tr>
                        <th class="px-4 py-3" style="color: var(--text-primary);">School Year</th>
                        <th class="py-3" style="color: var(--text-primary);">Semester</th>
                        <th class="py-3" style="color: var(--text-primary);">Exam Period</th>
                        <th class="py-3" style="color: var(--text-primary);">Status</th>
                        <th class="py-3 pe-4 text-end" style="color: var(--text-primary);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($years as $year)
                    @php
                        $activeSem = $year->semesters->firstWhere('is_current', true);
                        $activeEp  = $activeSem?->examPeriods->firstWhere('is_current', true);
                    @endphp
                    <tr class="school-year-row">
                        <td class="px-4 py-3 fw-semibold" style="color: var(--text-primary);">{{ $year->name }}</td>
                        <td class="py-3">
                            @if($activeSem)
                                <span class="badge-semester">
                                    <i class="fas fa-calendar-week me-1"></i>{{ $activeSem->name }}
                                </span>
                            @else
                                <span class="text-muted" style="color: var(--text-muted) !important;">—</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($activeEp)
                                <span class="badge-exam-period">
                                    <i class="fas fa-clock me-1"></i>{{ $activeEp->name }}
                                </span>
                            @else
                                <span class="text-muted" style="color: var(--text-muted) !important;">—</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($year->is_current)
                                <span class="badge-current">
                                    <i class="fas fa-star me-1"></i> Current
                                </span>
                            @else
                                <span class="text-muted" style="color: var(--text-muted) !important;">—</span>
                            @endif
                        </td>
                        <td class="py-3 pe-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                @if(!$year->is_current)
                                    <form action="{{ route('admin.school-years.setCurrent', $year->id) }}" method="POST" class="d-inline requires-loader">
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
                                    <i class="fas fa-calendar-times fa-4x" style="color: var(--text-muted);"></i>
                                    <h6 class="fw-semibold mt-3" style="color: var(--text-primary);">No school years found</h6>
                                    <p class="small" style="color: var(--text-muted);">Click "Add School Year" to get started.</p>
                                </div>
                            </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Current Exam Period Card (dark mode compatible) ── --}}
<div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--bg-main);">
    <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: var(--bg-main);">
        <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">
            <i class="fas fa-stopwatch me-2"></i> Current Exam Period
        </h5>
        @if($currentSemester)
            <span class="badge rounded-pill px-3 py-1" style="background: var(--input-bg); color: var(--text-primary);">
                <i class="fas fa-info-circle me-1"></i> {{ $currentYear->name }} / {{ $currentSemester->name }}
            </span>
        @endif
    </div>
    <div class="card-body p-4">
        @if($currentSemester)
            <div class="row align-items-center gy-3">
                <div class="col-md-6">
                    @if($currentExamPeriod)
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-clock fa-2x" style="color: #0f3c91;"></i>
                                </div>
                            </div>
                            <div>
                                <span class="small text-uppercase fw-semibold" style="color: var(--text-muted);">Currently Active</span>
                                <h2 class="fw-bold mb-0" style="color: var(--text-primary);">{{ $currentExamPeriod->name }}</h2>
                                <p class="small mt-1 mb-0" style="color: var(--text-secondary);">Students will see fees for this period.</p>
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-exclamation-triangle fa-2x" style="color: #f4b400;"></i>
                                </div>
                            </div>
                            <div>
                                <span class="small text-uppercase fw-semibold" style="color: var(--text-muted);">Not Set</span>
                                <h4 class="fw-bold mb-0" style="color: var(--text-muted);">No active exam period</h4>
                                <p class="small mt-1 mb-0" style="color: #f4b400;">Students cannot view exam fees until you set one.</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="rounded-4 p-3" style="background: var(--input-bg);">
                        <div class="small fw-semibold mb-2" style="color: var(--text-muted);">Quick select</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['Prelim', 'Midterm', 'Semi-Final', 'Finals'] as $period)
                                <form method="POST" action="{{ route('admin.exam-periods.setCurrent') }}" class="d-inline requires-loader">
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
                </div>
            </div>
        @else
            <div class="text-center py-3">
                <i class="fas fa-info-circle fa-2x" style="color: var(--text-muted);"></i>
                <p class="mb-0" style="color: var(--text-muted);">No active semester. Please set a school year and semester first.</p>
            </div>
        @endif
    </div>
</div>

{{-- ── Add School Year Modal (dark mode compatible) ── --}}
<div class="modal fade" id="addYearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--bg-main);">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-plus-circle me-2"></i> Add School Year
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.school-years.store') }}" method="POST" class="requires-loader">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="year_name" class="form-label fw-semibold" style="color: var(--text-primary);">School Year</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 rounded-start-3 px-3" style="background: var(--input-bg); color: var(--text-primary);">
                                <i class="fas fa-calendar" style="color: #0f3c91;"></i>
                            </span>
                            <input type="text" name="name" id="year_name" class="form-control rounded-end-3 px-3 py-2"
                                   placeholder="e.g., 2025-2026" required autofocus
                                   style="background: var(--input-bg); color: var(--text-primary); border-color: var(--input-border);">
                        </div>
                        <div class="form-text small mt-2" style="color: var(--text-muted);">Use format: YYYY-YYYY (e.g., 2025-2026)</div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i> Create
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Semester Modal (dark mode compatible) ── --}}
<div class="modal fade" id="semesterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--bg-main);">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span id="yearName"></span> – Set Semester
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="semesterForm" action="" class="requires-loader">
                @csrf
                <input type="hidden" name="_year_id" id="semesterYearId" value="">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="semester" class="form-label fw-semibold" style="color: var(--text-primary);">Select Semester</label>
                        <select class="form-select rounded-pill border-0 px-4 py-2" id="semester" name="semester" required
                                style="background: var(--input-bg); color: var(--text-primary); border-color: var(--input-border);">
                            <option value="" disabled selected>Choose...</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                        </select>
                    </div>
                    <div class="alert alert-warning rounded-4 py-2 px-3 mb-0 small" style="background: rgba(244,180,20,0.15); color: #b26a00; border: none;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Changing the semester will reset the current exam period. Remember to set a new one afterwards.
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-check me-2"></i> Update Semester
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Exam Period Modal (dark mode compatible) ── --}}
<div class="modal fade" id="examPeriodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--bg-main);">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-clock me-2"></i> Set Current Exam Period
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.exam-periods.setCurrent') }}" class="requires-loader">
                @csrf
                <div class="modal-body p-4">
                    <p class="small mb-3" style="color: var(--text-muted);">
                        For: <strong style="color: var(--text-primary);">{{ $currentYear?->name }}</strong> &mdash; <strong style="color: var(--text-primary);">{{ $currentSemester?->name ?? 'No semester set' }}</strong>
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
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-check me-2"></i> Set Period
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Delete Confirmation Modal (dark mode compatible) ── --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--bg-main);">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-trash-alt me-2"></i> Delete School Year</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x mb-3" style="color: #dc3545;"></i>
                <p class="mb-1" style="color: var(--text-primary);">Are you sure you want to delete</p>
                <p class="fw-bold fs-5 mb-1" id="deleteYearName" style="color: var(--text-primary);"></p>
                <p class="small" style="color: var(--text-muted);">This will permanently delete all semesters, exam periods, and fees under this school year.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                <form id="deleteForm" method="POST" action="" class="requires-loader">
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

@/* The above code appears to be a comment block in PHP. The comment block starts with /* and ends with
*/. However, the code also contains some random characters like "endse" and " */

{{-- ── Course Management Card ── --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: var(--bg-main);">
    <div class="card-header border-0 py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2"
         style="background: var(--bg-main);">
        <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">
            <i class="fas fa-graduation-cap me-2"></i> Course Management
        </h5>
        <button type="button" class="btn-add-year rounded-pill px-4 py-2"
                data-bs-toggle="modal" data-bs-target="#addCourseModal">
            <i class="fas fa-plus-circle me-2"></i> Add Course
        </button>
    </div>
 
    {{-- Search / filter bar --}}
    <div class="px-4 py-3 border-bottom" style="border-color: var(--border-color) !important; background: var(--bg-main);">
        <div class="input-group" style="max-width: 340px;">
            <span class="input-group-text border-0 rounded-start-3"
                  style="background: var(--input-bg); color: var(--text-muted);">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" id="courseSearch" class="form-control border-0 rounded-end-3"
                   placeholder="Search courses…"
                   style="background: var(--input-bg); color: var(--text-primary);">
        </div>
    </div>
 
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 schoolyear-table" id="courseTable">
                <thead style="background: var(--table-header-bg);">
                    <tr>
                        <th class="px-4 py-3" style="color: var(--text-primary);">Code</th>
                        <th class="py-3"      style="color: var(--text-primary);">Course Name</th>
                        <th class="py-3"      style="color: var(--text-primary);">Department</th>
                        <th class="py-3 pe-4 text-end" style="color: var(--text-primary);">Actions</th>
                    </tr>
                </thead>
                <tbody id="courseTableBody">
                    @forelse($courses as $course)
                    <tr class="course-row">
                        <td class="px-4 py-3">
                            <span class="badge-course-code">{{ $course->code }}</span>
                        </td>
                        <td class="py-3 fw-semibold" style="color: var(--text-primary);">
                            {{ $course->name }}
                        </td>
                        <td class="py-3" style="color: var(--text-secondary);">
                            {{ $course->department ?? '—' }}
                        </td>
                        <td class="py-3 pe-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button"
                                        class="btn-action edit-course"
                                        title="Edit course"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCourseModal"
                                        data-id="{{ $course->id }}"
                                        data-code="{{ $course->code }}"
                                        data-name="{{ $course->name }}"
                                        data-department="{{ $course->department }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button"
                                        class="btn-action delete-course"
                                        title="Delete course"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteCourseModal"
                                        data-id="{{ $course->id }}"
                                        data-name="{{ $course->name }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr id="courseEmptyRow">
                        <td colspan="4" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-graduation-cap fa-4x" style="color: var(--text-muted);"></i>
                                <h6 class="fw-semibold mt-3" style="color: var(--text-primary);">No courses yet</h6>
                                <p class="small" style="color: var(--text-muted);">Click "Add Course" to get started.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
 
    {{-- Course count footer --}}
    <div class="card-footer border-0 px-4 py-2 d-flex align-items-center justify-content-between flex-wrap gap-2"
         style="background: var(--bg-main); border-top: 1px solid var(--border-color) !important;">
        <span class="small" style="color: var(--text-muted);" id="courseCount">
            {{ $courses->count() }} {{ Str::plural('course', $courses->count()) }} total
        </span>
        <span class="small" style="color: var(--text-muted);" id="courseFilterInfo"></span>
    </div>
</div>
 
 
{{-- ══════════ ADD COURSE MODAL ══════════ --}}
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--bg-main);">
            <div class="modal-header border-0"
                 style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-graduation-cap me-2"></i> Add Course
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.courses.store') }}" method="POST" class="requires-loader">
                @csrf
                <div class="modal-body p-4">
 
                    {{-- Code --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">
                            Course Code <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text border-0 rounded-start-3 px-3"
                                  style="background: var(--input-bg); color: var(--text-muted);">
                                <i class="fas fa-tag" style="color: #0f3c91;"></i>
                            </span>
                            <input type="text" name="code" class="form-control border-0 rounded-end-3 px-3 py-2"
                                   placeholder="e.g. BSIT" required maxlength="20"
                                   style="background: var(--input-bg); color: var(--text-primary);">
                        </div>
                        <div class="form-text small" style="color: var(--text-muted);">Short abbreviation (max 20 chars)</div>
                    </div>
 
                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">
                            Course Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text border-0 rounded-start-3 px-3"
                                  style="background: var(--input-bg); color: var(--text-muted);">
                                <i class="fas fa-book" style="color: #0f3c91;"></i>
                            </span>
                            <input type="text" name="name" class="form-control border-0 rounded-end-3 px-3 py-2"
                                   placeholder="e.g. BS Information Technology" required maxlength="150"
                                   style="background: var(--input-bg); color: var(--text-primary);">
                        </div>
                    </div>
 
                    {{-- Department (optional) --}}
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">
                            Department <span class="text-muted fw-normal">(optional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text border-0 rounded-start-3 px-3"
                                  style="background: var(--input-bg); color: var(--text-muted);">
                                <i class="fas fa-building" style="color: #0f3c91;"></i>
                            </span>
                            <input type="text" name="department" class="form-control border-0 rounded-end-3 px-3 py-2"
                                   placeholder="e.g. College of Engineering" maxlength="100"
                                   style="background: var(--input-bg); color: var(--text-primary);">
                        </div>
                    </div>
 
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal"
                            style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i> Save Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
 
{{-- ══════════ EDIT COURSE MODAL ══════════ --}}
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--bg-main);">
            <div class="modal-header border-0"
                 style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-edit me-2"></i> Edit Course
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCourseForm" method="POST" action="" class="requires-loader">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
 
                    {{-- Code --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">
                            Course Code <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text border-0 rounded-start-3 px-3"
                                  style="background: var(--input-bg); color: var(--text-muted);">
                                <i class="fas fa-tag" style="color: #0f3c91;"></i>
                            </span>
                            <input type="text" name="code" id="editCourseCode"
                                   class="form-control border-0 rounded-end-3 px-3 py-2"
                                   required maxlength="20"
                                   style="background: var(--input-bg); color: var(--text-primary);">
                        </div>
                    </div>
 
                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">
                            Course Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text border-0 rounded-start-3 px-3"
                                  style="background: var(--input-bg); color: var(--text-muted);">
                                <i class="fas fa-book" style="color: #0f3c91;"></i>
                            </span>
                            <input type="text" name="name" id="editCourseName"
                                   class="form-control border-0 rounded-end-3 px-3 py-2"
                                   required maxlength="150"
                                   style="background: var(--input-bg); color: var(--text-primary);">
                        </div>
                    </div>
 
                    {{-- Department --}}
                    <div class="mb-1">
                        <label class="form-label fw-semibold" style="color: var(--text-primary);">
                            Department <span class="text-muted fw-normal">(optional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text border-0 rounded-start-3 px-3"
                                  style="background: var(--input-bg); color: var(--text-muted);">
                                <i class="fas fa-building" style="color: #0f3c91;"></i>
                            </span>
                            <input type="text" name="department" id="editCourseDept"
                                   class="form-control border-0 rounded-end-3 px-3 py-2"
                                   maxlength="100"
                                   style="background: var(--input-bg); color: var(--text-primary);">
                        </div>
                    </div>
 
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal"
                            style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i> Update Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
 
{{-- ══════════ DELETE COURSE MODAL ══════════ --}}
<div class="modal fade" id="deleteCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--bg-main);">
            <div class="modal-header border-0"
                 style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-trash-alt me-2"></i> Delete Course
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-exclamation-triangle fa-3x mb-3" style="color: #dc3545;"></i>
                <p class="mb-1" style="color: var(--text-primary);">Are you sure you want to delete</p>
                <p class="fw-bold fs-5 mb-1" id="deleteCourseLabel" style="color: var(--text-primary);"></p>
                <p class="small" style="color: var(--text-muted);">
                    Students and records linked to this course may be affected.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-3 pb-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4"
                        data-bs-dismiss="modal"
                        style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                <form id="deleteCourseForm" method="POST" action="" class="requires-loader">
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
 endsection

@push('styles')
<style>
    /* Dark mode table overrides */
    .schoolyear-table,
    .schoolyear-table tbody,
    .schoolyear-table tr,
    .schoolyear-table td {
        background-color: var(--bg-main);
        color: var(--text-secondary);
    }
    .schoolyear-table thead th {
        background-color: var(--table-header-bg);
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
    }
    .schoolyear-table tbody tr {
        border-bottom: 1px solid var(--table-row-border);
        transition: background 0.2s;
    }
    .schoolyear-table tbody tr:hover {
        background-color: var(--hover-bg) !important;
    }
    .schoolyear-table tbody td {
        background-color: var(--bg-main);
        color: var(--text-secondary);
        border-bottom: none;
    }
    .schoolyear-table tbody td:first-child {
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

    /* School year row hover */
    .school-year-row {
        transition: all 0.2s ease;
    }
    .school-year-row:hover {
        background-color: var(--hover-bg) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.02);
    }

    /* Action buttons */
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
    .btn-action.set-current:hover  { background: rgba(244,180,20,0.1); color: #b26a00; }
    .btn-action.set-semester:hover { background: rgba(15,60,145,0.1);  color: #0f3c91; }
    .btn-action.delete-year:hover  { background: rgba(220,53,69,0.1);  color: #dc3545; }

    /* Add Year button */
    .btn-add-year {
        background: #0f3c91;
        color: white;
        border: none;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    .btn-add-year:hover {
        background: #1a4da8;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(15,60,145,0.2);
        color: white;
    }
    body.dark .btn-add-year {
        background: #3b82f6;
    }
    body.dark .btn-add-year:hover {
        background: #2563eb;
    }

    /* Badges */
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
    .badge-semester {
        background: rgba(15, 60, 145, 0.1);
        color: #0f3c91;
        font-weight: 500;
        padding: 0.35rem 0.85rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
    }
    .badge-exam-period {
        font-weight: 500;
        padding: 0.35rem 0.85rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        background: rgba(234, 88, 12, 0.12);
        color: #c2410c;
    }
    body.dark .badge-current {
        background: rgba(76,175,80,0.25);
        color: #81c784;
    }
    body.dark .badge-semester {
        background: rgba(59,130,246,0.2);
        color: #93c5fd;
    }
    body.dark .badge-exam-period {
        background: rgba(249,115,22,0.2);
        color: #fdba74;
    }

    /* Exam period quick buttons */
    .btn-period-active {
        background: #0f3c91;
        color: #fff;
        border: 2px solid #0f3c91;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
    }
    .btn-period-active:hover {
        background: #1a4da8;
        border-color: #1a4da8;
        color: #fff;
    }
    .btn-period-inactive {
        background: var(--input-bg);
        color: var(--text-primary);
        border: 2px solid var(--input-border);
        font-size: 13px;
        transition: all 0.2s;
    }
    .btn-period-inactive:hover {
        background: var(--hover-bg);
        border-color: #0f3c91;
        color: #0f3c91;
    }
    body.dark .btn-period-active {
        background: #3b82f6;
        border-color: #3b82f6;
    }
    body.dark .btn-period-active:hover {
        background: #2563eb;
    }

    /* Modal period radio cards */
    .period-radio-card {
        margin: 0;
    }
    .period-label {
        display: inline-block;
        padding: 0.5rem 1.25rem;
        border-radius: 30px;
        border: 2px solid var(--input-border);
        background: var(--input-bg);
        cursor: pointer;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.15s;
        color: var(--text-primary);
    }
    .form-check-input:checked + .period-label {
        background: #0f3c91;
        border-color: #0f3c91;
        color: #fff;
        font-weight: 600;
    }
    .period-label:hover {
        border-color: #0f3c91;
        color: #0f3c91;
        background: var(--hover-bg);
    }
    body.dark .form-check-input:checked + .period-label {
        background: #3b82f6;
        border-color: #3b82f6;
    }

    /* Empty state */
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

    /* Form controls */
    .form-control, .form-select {
        background-color: var(--input-bg);
        border-color: var(--input-border);
        color: var(--text-primary);
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0f3c91;
        box-shadow: 0 0 0 3px rgba(15,60,145,0.12);
        background-color: var(--input-bg);
    }
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
        background: var(--input-bg);
        border: none;
        color: var(--text-primary);
        padding: 0.6rem 1.5rem;
        border-radius: 30px;
        font-weight: 500;
    }
    .btn-secondary:hover {
        background: var(--hover-bg);
    }
    .btn-danger {
        background: #dc3545;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 30px;
        font-weight: 500;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    .tracking-wide {
        letter-spacing: 0.05em;
    }
    /* Course code badge */
.badge-course-code {
    background: rgba(15, 60, 145, 0.1);
    color: #0f3c91;
    font-weight: 700;
    padding: 0.35rem 0.85rem;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    font-size: 0.82rem;
    letter-spacing: 0.04em;
    font-family: monospace;
}
body.dark .badge-course-code {
    background: rgba(59, 130, 246, 0.2);
    color: #93c5fd;
}
 
/* Course row hover (reuses .school-year-row styles already defined) */
.course-row {
    transition: all 0.2s ease;
}
.course-row:hover {
    background-color: var(--hover-bg) !important;
}
 
/* Edit action button colour */
.btn-action.edit-course:hover {
    background: rgba(15, 60, 145, 0.1);
    color: #0f3c91;
}
 
/* Search highlight */
.search-highlight {
    background: rgba(255, 213, 0, 0.35);
    border-radius: 3px;
    padding: 0 2px;
}
body.dark .search-highlight {
    background: rgba(253, 186, 116, 0.3);
}
 
/* No-results row */
#courseNoResults td {
    color: var(--text-muted);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Semester modal — set form action
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
        });
    }

    // Delete modal — set form action
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const yearId = button.getAttribute('data-year-id');
            const yearName = button.getAttribute('data-year-name');
            document.getElementById('deleteYearName').textContent = yearName;
            document.getElementById('deleteForm').action = `/admin/school-years/${yearId}`;
        });
    }
});
(function () {
    // ── Edit course modal ──────────────────────────────────────────
    const editCourseModal = document.getElementById('editCourseModal');
    if (editCourseModal) {
        editCourseModal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            document.getElementById('editCourseCode').value       = btn.dataset.code       ?? '';
            document.getElementById('editCourseName').value       = btn.dataset.name       ?? '';
            document.getElementById('editCourseDept').value       = btn.dataset.department ?? '';
            document.getElementById('editCourseForm').action =
                `{{ url('admin/courses') }}/${btn.dataset.id}`;
        });
    }
 
    // ── Delete course modal ────────────────────────────────────────
    const deleteCourseModal = document.getElementById('deleteCourseModal');
    if (deleteCourseModal) {
        deleteCourseModal.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            document.getElementById('deleteCourseLabel').textContent = btn.dataset.name;
            document.getElementById('deleteCourseForm').action =
                `/admin/courses/${btn.dataset.id}`;
        });
    }
 
    // ── Live search / filter ───────────────────────────────────────
    const searchInput  = document.getElementById('courseSearch');
    const tableBody    = document.getElementById('courseTableBody');
    const filterInfo   = document.getElementById('courseFilterInfo');
    const countLabel   = document.getElementById('courseCount');
 
    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function () {
            const q        = this.value.trim().toLowerCase();
            const rows     = tableBody.querySelectorAll('tr.course-row');
            let   visible  = 0;
 
            // Remove old no-results row
            const old = document.getElementById('courseNoResults');
            if (old) old.remove();
 
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (!q || text.includes(q)) {
                    row.style.display = '';
                    visible++;
                } else {
                    row.style.display = 'none';
                }
            });
 
            // No-results message
            if (visible === 0 && rows.length > 0) {
                const noRow = document.createElement('tr');
                noRow.id = 'courseNoResults';
                noRow.innerHTML = `
                    <td colspan="4" class="text-center py-4">
                        <i class="fas fa-search me-2" style="color: var(--text-muted);"></i>
                        <span style="color: var(--text-muted);">No courses matching "<strong>${q}</strong>"</span>
                    </td>`;
                tableBody.appendChild(noRow);
            }
 
            filterInfo.textContent = q
                ? `Showing ${visible} of ${rows.length} course(s)`
                : '';
        });
    }
})();
</script>
@endpush