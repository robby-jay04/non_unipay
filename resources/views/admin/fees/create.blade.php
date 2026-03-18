@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Add New Fee</h2>
</div>

@if ($errors->any())
<div class="alert alert-light d-flex align-items-start shadow-sm rounded-3 mb-4 p-3" style="border-left: 4px solid #dc3545; background: white;">
    <i class="fas fa-exclamation-circle me-3 mt-1" style="color: #dc3545; font-size: 1.2rem;"></i>
    <div class="flex-grow-1">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li style="color: #58151c;">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">
            <i class="fas fa-plus-circle me-2"></i> Fee Information
        </h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.fees.store') }}" method="POST">
            @csrf

            <!-- Fee Name -->
            <div class="mb-3">
                <label class="form-label fw-medium text-secondary">
                    <i class="fas fa-tag me-1" style="color: #0f3c91;"></i> Fee Name
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-3 px-3">
                        <i class="fas fa-pencil-alt" style="color: #0f3c91;"></i>
                    </span>
                    <input type="text"
                           name="name"
                           class="form-control bg-light border-0 px-3 py-2"
                           placeholder="e.g., Tuition Fee"
                           value="{{ old('name') }}"
                           required>
                </div>
            </div>

            <!-- Type -->
            <div class="mb-3">
                <label class="form-label fw-medium text-secondary">
                    <i class="fas fa-layer-group me-1" style="color: #0f3c91;"></i> Type
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-3 px-3">
                        <i class="fas fa-list" style="color: #0f3c91;"></i>
                    </span>
                    <select name="type" class="form-select bg-light border-0 px-3 py-2" required>
                        <option value="" disabled selected>Select Type</option>
                        <option value="tuition"       {{ old('type') == 'tuition'       ? 'selected' : '' }}>Tuition</option>
                        <option value="miscellaneous" {{ old('type') == 'miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        <option value="exam"          {{ old('type') == 'exam'          ? 'selected' : '' }}>Exam</option>
                    </select>
                </div>
            </div>

            <!-- Course -->
            <div class="mb-3">
                <label class="form-label fw-medium text-secondary">
                    <i class="fas fa-graduation-cap me-1" style="color: #0f3c91;"></i> Course
                    <small class="text-muted ms-1">(leave blank to apply to all courses)</small>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-3 px-3">
                        <i class="fas fa-book" style="color: #0f3c91;"></i>
                    </span>
                    <select name="course" class="form-select bg-light border-0 px-3 py-2">
                        <option value="">-- All Courses --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course }}" {{ old('course') == $course ? 'selected' : '' }}>
                                {{ $course }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Amount -->
            <div class="mb-3">
                <label class="form-label fw-medium text-secondary">
                    <i class="fas fa-coins me-1" style="color: #0f3c91;"></i> Amount (₱)
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-3 px-3">
                        <i class="fas fa-peso-sign" style="color: #0f3c91;"></i>
                    </span>
                    <input type="number"
                           name="amount"
                           step="0.01"
                           min="0"
                           class="form-control bg-light border-0 px-3 py-2"
                           placeholder="0.00"
                           value="{{ old('amount') }}"
                           required>
                </div>
            </div>

            <!-- School Year -->
            <div class="mb-3">
                <label class="form-label fw-medium text-secondary">
                    <i class="fas fa-calendar-alt me-1" style="color: #0f3c91;"></i> School Year
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-3 px-3">
                        <i class="fas fa-calendar" style="color: #0f3c91;"></i>
                    </span>
                    <select name="school_year_id" id="school_year_id" class="form-select bg-light border-0 px-3 py-2" required>
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

            <!-- Semester -->
            <div class="mb-3">
                <label class="form-label fw-medium text-secondary">
                    <i class="fas fa-calendar-alt me-1" style="color: #0f3c91;"></i> Semester
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-3 px-3">
                        <i class="fas fa-calendar-week" style="color: #0f3c91;"></i>
                    </span>
                    <select name="semester_id" id="semester_id" class="form-select bg-light border-0 px-3 py-2">
                        <option value="">-- Select Semester --</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}"
                                {{ old('semester_id') == $semester->id
                                    ? 'selected'
                                    : (!old('semester_id') && $currentSemester && $currentSemester->id == $semester->id ? 'selected' : '') }}>
                                {{ $semester->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Exam Period -->
            <div class="mb-4">
                <label class="form-label fw-medium text-secondary">
                    <i class="fas fa-file-alt me-1" style="color: #0f3c91;"></i> Exam Period
                    <small class="text-muted ms-1">(leave blank to apply to all exam periods)</small>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-3 px-3">
                        <i class="fas fa-clock" style="color: #0f3c91;"></i>
                    </span>
                    <select name="exam_period_id" id="exam_period_id" class="form-select bg-light border-0 px-3 py-2">
                        <option value="">-- All Exam Periods --</option>
                        @foreach($examPeriods as $period)
                            <option value="{{ $period->id }}"
                                {{ old('exam_period_id') == $period->id ? 'selected' : '' }}>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2">
                    <i class="fas fa-save me-2"></i> Save Fee
                </button>
                <a href="{{ route('admin.fees.index') }}" class="btn btn-outline-secondary rounded-pill px-5 py-2">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .input-group-text {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .form-control, .form-select {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        background-color: #f8f9fa;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: #0f3c91;
        box-shadow: 0 0 0 0.2rem rgba(15, 60, 145, 0.1);
    }
    .btn-primary {
        background: #0f3c91;
        border: none;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-primary:hover {
        background: #1a4da8;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(15, 60, 145, 0.2);
    }
    .btn-outline-secondary {
        border: 1px solid #ced4da;
        color: #6c757d;
        background: transparent;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-outline-secondary:hover {
        background: #e9ecef;
        border-color: #b0b3b7;
        color: #495057;
        transform: translateY(-2px);
    }
    .form-label {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const schoolYearSelect = document.getElementById('school_year_id');
    const semesterSelect   = document.getElementById('semester_id');
    const examPeriodSelect = document.getElementById('exam_period_id');

    // When school year changes → reload semesters, clear exam periods
    schoolYearSelect.addEventListener('change', function () {
        const schoolYearId = this.value;
        semesterSelect.innerHTML = '<option value="">Loading...</option>';
        examPeriodSelect.innerHTML = '<option value="">-- All Exam Periods --</option>';

        fetch(`/admin/api/semesters/${schoolYearId}`)
            .then(res => res.json())
            .then(data => {
                semesterSelect.innerHTML = '<option value="">-- Select Semester --</option>';
                data.forEach(sem => {
                    semesterSelect.innerHTML += `<option value="${sem.id}">${sem.name}</option>`;
                });
                if (data.length === 1) {
                    semesterSelect.value = data[0].id;
                    semesterSelect.dispatchEvent(new Event('change'));
                }
            })
            .catch(() => {
                semesterSelect.innerHTML = '<option value="">Failed to load semesters</option>';
            });
    });

    // When semester changes → reload exam periods
    semesterSelect.addEventListener('change', function () {
        const semId = this.value;
        if (!semId) {
            examPeriodSelect.innerHTML = '<option value="">-- All Exam Periods --</option>';
            return;
        }

        examPeriodSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(`/admin/api/exam-periods/${semId}`)
            .then(res => res.json())
            .then(data => {
                examPeriodSelect.innerHTML = '<option value="">-- All Exam Periods --</option>';
                data.forEach(p => {
                    examPeriodSelect.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });
            })
            .catch(() => {
                examPeriodSelect.innerHTML = '<option value="">Failed to load exam periods</option>';
            });
    });
});
</script>
@endpush
