@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Edit Fee</h2>
</div>

<!-- Error Alert -->
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

<!-- Edit Fee Card -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.fees.update', $fee->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Fee Name -->
            <div class="mb-3">
                <label class="form-label fw-medium">Fee Name</label>
                <input type="text"
                       name="name"
                       class="form-control rounded-3 border-0 bg-light px-4 py-2"
                       value="{{ old('name', $fee->name) }}"
                       required>
            </div>

            <!-- Amount -->
            <div class="mb-3">
                <label class="form-label fw-medium">Amount</label>
                <input type="number"
                       step="0.01"
                       name="amount"
                       class="form-control rounded-3 border-0 bg-light px-4 py-2"
                       value="{{ old('amount', $fee->amount) }}"
                       required>
            </div>

            <!-- Type -->
            <div class="mb-3">
                <label class="form-label fw-medium">Type</label>
                <select name="type" class="form-select rounded-3 border-0 bg-light px-4 py-2" required>
                    <option value="tuition" {{ old('type', $fee->type) == 'tuition' ? 'selected' : '' }}>Tuition</option>
                    <option value="miscellaneous" {{ old('type', $fee->type) == 'miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                    <option value="exam" {{ old('type', $fee->type) == 'exam' ? 'selected' : '' }}>Exam</option>
                </select>
            </div>

            <!-- Semester -->
            <div class="mb-3">
                <label class="form-label fw-medium">Semester</label>
                <select name="semester" class="form-select rounded-3 border-0 bg-light px-4 py-2" required>
                    <option value="">-- Select Semester --</option>
                    <option value="1st Semester" {{ old('semester', $fee->semester) == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                    <option value="2nd Semester" {{ old('semester', $fee->semester) == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                </select>
            </div>

            <!-- School Year -->
            <div class="mb-3">
                <label class="form-label fw-medium">School Year</label>
                <input type="text"
                       name="school_year"
                       class="form-control rounded-3 border-0 bg-light px-4 py-2"
                       value="{{ old('school_year', $fee->school_year) }}"
                       required>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn rounded-pill px-4 py-2" style="background: #0f3c91; color: white;">
                    <i class="fas fa-save me-2"></i> Update Fee
                </button>
                <a href="{{ route('admin.fees.index') }}" class="btn btn-light rounded-pill px-4 py-2" style="background: #e9ecef; color: #495057;">
                    <i class="fas fa-times me-2"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Form focus */
    .form-control:focus, .form-select:focus {
        box-shadow: none;
        border-color: #0f3c91;
    }
</style>
@endpush