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
        <button class="btn btn-primary">Add</button>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Current</th>
            <th>Action</th>
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
                <form action="{{ route('admin.school-years.setCurrent', $year->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-sm btn-warning">Set as Current</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection