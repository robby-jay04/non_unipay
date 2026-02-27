@extends('admin.layouts.app')

@section('content')
<h2>Add New Fee</h2>

<form action="{{ route('admin.fees.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label">Fee Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Type</label>
        <select name="type" class="form-control" required>
            <option value="" disabled selected>Select Type</option>
            <option value="tuition">Tuition</option>
            <option value="miscellaneous">Miscellaneous</option>
            <option value="exam">Exam</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Amount (₱)</label>
        <input type="number" name="amount" step="0.01" min="0" class="form-control" required>
    </div>

    <!-- ✅ Semester Dropdown -->
    <div class="mb-3">
        <label class="form-label">Semester</label>
        <select name="semester" class="form-control">
            <option value="" selected>-- Select Semester --</option>
            <option value="1st Semester">1st Semester</option>
            <option value="2nd Semester">2nd Semester</option>
          
        </select>
    </div>

    <!-- ✅ Smart School Year Dropdown -->
    <div class="mb-3">
        <label class="form-label">School Year</label>
        <select name="school_year" class="form-control" required>
            @php
                $currentYear = date('Y');
            @endphp

            @for ($i = 0; $i < 5; $i++)
                <option value="{{ $currentYear+$i }}-{{ $currentYear+$i+1 }}">
                    {{ $currentYear+$i }}-{{ $currentYear+$i+1 }}
                </option>
            @endfor
        </select>
    </div>

    <button class="btn btn-success">Save Fee</button>
    <a href="{{ route('admin.fees.index') }}" class="btn btn-secondary">Cancel</a>

</form>
@endsection