
@extends('admin.layouts.app')

@section('content')
<h2>Fee Management</h2>

<a href="{{ route('admin.fees.create') }}" class="btn btn-primary mb-3">
    Add New Fee
</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Semester</th>
            <th>School Year</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($fees as $fee)
        <tr>
            <td>{{ $fee->name }}</td>
            <td>{{ ucfirst($fee->type) }}</td>
            <td>₱{{ number_format($fee->amount, 2) }}</td>
            <td>{{ $fee->semester }}</td>
            <td>{{ $fee->school_year }}</td>
           <td>
    <a href="{{ route('admin.fees.edit', $fee) }}" 
       class="btn btn-sm btn-warning">
        Edit
    </a>

    <button 
        type="button"
        class="btn btn-sm btn-danger"
        data-bs-toggle="modal"
        data-bs-target="#deleteModal"
        data-id="{{ $fee->id }}"
    >
        Delete
    </button>
</td>
        </tr>
        @endforeach
    </tbody>
</table>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to delete this fee?
            </div>

            <div class="modal-footer">
   <form id="deleteForm" method="POST">
    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-danger">
        Yes, Delete
    </button>
</form>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const deleteModal = document.getElementById('deleteModal');

    deleteModal.addEventListener('show.bs.modal', function (event) {

        const button = event.relatedTarget;
        const feeId = button.getAttribute('data-id');

        const form = document.getElementById('deleteForm');

        form.action = "{{ route('admin.fees.destroy', ':id') }}".replace(':id', feeId);

    });

});
</script>
@endsection