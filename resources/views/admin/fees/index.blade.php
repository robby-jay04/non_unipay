@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Fee Management</h2>
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

<!-- Add Fee Button -->
<div class="mb-4">
    <a href="{{ route('admin.fees.create') }}" class="btn rounded-pill px-4 py-2" style="background: #0f3c91; color: white;">
        <i class="fas fa-plus-circle me-2"></i> Add New Fee
    </a>
</div>

<!-- Fees Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">All Fees</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3">Semester</th>
                        <th class="py-3">School Year</th>
                        <th class="py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fees as $fee)
                    <tr>
                        <td class="px-4 py-3 fw-medium">{{ $fee->name }}</td>
                        <td class="py-3">
                            <span class="badge-type-{{ $fee->type }}">{{ ucfirst($fee->type) }}</span>
                        </td>
                        <td class="py-3 fw-semibold" style="color: #0f3c91;">₱{{ number_format($fee->amount, 2) }}</td>
                        <td class="py-3">{{ $fee->semester }}</td>
                        <td class="py-3">{{ $fee->school_year }}</td>
                        <td class="py-3 pe-4">
                            <a href="{{ route('admin.fees.edit', $fee) }}" 
                               class="btn btn-sm rounded-pill px-3 me-2"
                               style="background: rgba(244, 180, 20, 0.15); color: #b26a00; border: none;">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>

                            <button type="button"
                                    class="btn btn-sm rounded-pill px-3"
                                    style="background: rgba(220, 53, 69, 0.15); color: #dc3545; border: none;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-id="{{ $fee->id }}">
                                <i class="fas fa-trash-alt me-1"></i> Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #dc3545, #b02a37); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0">Are you sure you want to delete this fee? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn rounded-pill px-4" style="background: #dc3545; color: white;">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Badge styles for fee types */
    .badge-type-tuition {
        background: rgba(15, 60, 145, 0.15);
        color: #0f3c91;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }
    .badge-type-miscellaneous {
        background: rgba(244, 180, 20, 0.15);
        color: #b26a00;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }
    .badge-type-exam {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }

    /* Table */
    .table td {
        border-bottom: 1px solid #f0f2f5;
        color: #334155;
    }
    .table th {
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e9ecef;
    }

    /* Action buttons */
    .btn-sm {
        transition: all 0.2s;
    }
    .btn-sm:hover {
        transform: translateY(-1px);
        filter: brightness(1.05);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const feeId = button.getAttribute('data-id');
            const form = document.getElementById('deleteForm');
            form.action = "{{ route('admin.fees.destroy', ':id') }}".replace(':id', feeId);
        });
    }
});
</script>
@endpush