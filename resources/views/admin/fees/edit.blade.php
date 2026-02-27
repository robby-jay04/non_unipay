@extends('admin.layouts.app')

@section('content')
<h2>Edit Fee</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.fees.update', $fee->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Fee Name -->
    <div class="mb-3">
        <label class="form-label">Fee Name</label>
        <input type="text"
               name="name"
               class="form-control"
               value="{{ old('name', $fee->name) }}"
               required>
    </div>

    <!-- Amount -->
    <div class="mb-3">
        <label class="form-label">Amount</label>
        <input type="number"
               step="0.01"
               name="amount"
               class="form-control"
               value="{{ old('amount', $fee->amount) }}"
               required>
    </div>

    <!-- Type -->
    <div class="mb-3">
        <label class="form-label">Type</label>
        <select name="type" class="form-control" required>
            <option value="tuition"
                {{ old('type', $fee->type) == 'tuition' ? 'selected' : '' }}>
                Tuition
            </option>
            <option value="miscellaneous"
                {{ old('type', $fee->type) == 'miscellaneous' ? 'selected' : '' }}>
                Miscellaneous
            </option>
            <option value="exam"
                {{ old('type', $fee->type) == 'exam' ? 'selected' : '' }}>
                Exam
            </option>
        </select>
    </div>

    <!-- Semester -->
    <div class="mb-3">
        <label class="form-label">Semester</label>
        <select name="semester" class="form-control" required>
            <option value="">-- Select Semester --</option>
            <option value="1st Semester"
                {{ old('semester', $fee->semester) == '1st Semester' ? 'selected' : '' }}>
                1st Semester
            </option>
            <option value="2nd Semester"
                {{ old('semester', $fee->semester) == '2nd Semester' ? 'selected' : '' }}>
                2nd Semester
            </option>
            
        </select>
    </div>

    <!-- School Year -->
    <div class="mb-3">
        <label class="form-label">School Year</label>
        <input type="text"
               name="school_year"
               class="form-control"
               value="{{ old('school_year', $fee->school_year) }}"
               required>
    </div>

    <!-- Buttons -->
    <button type="submit" class="btn btn-primary">Update Fee</button>
    <a href="{{ route('admin.fees.index') }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection