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
    <a href="{{ route('admin.fees.create') }}" class="btn-add-fee rounded-pill px-4 py-2">
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
                        <th class="py-3">Course</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3">School Year</th>
                        <th class="py-3">Semester</th>
                        <th class="py-3">Exam Period</th>
                        <th class="py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $fee)
                    <tr class="fee-row">
                        <td class="px-4 py-3 fw-medium">{{ $fee->name }}</td>
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
                        <td class="py-3 fw-semibold" style="color: #0f3c91;">₱{{ number_format($fee->amount, 2) }}</td>
                        <td class="py-3">{{ $fee->school_year }}</td>
                        <td class="py-3">{{ $fee->semester ?? '—' }}</td>
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
                        <td class="py-3 pe-4">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.fees.edit', $fee) }}"
                                   class="btn-action edit-fee" title="Edit fee">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
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
                                <i class="fas fa-coins fa-4x" style="color: #d1d5db;"></i>
                                <h6 class="fw-semibold mt-3" style="color: #1e293b;">No fees found</h6>
                                <p class="text-muted small">Add a fee to get started.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
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
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .fee-row {
        transition: all 0.2s ease;
    }
    .fee-row:hover {
        background-color: rgba(15, 60, 145, 0.02) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.02);
    }

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
        color: #64748b;
        padding: 0;
        text-decoration: none;
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
        box-shadow: 0 4px 8px rgba(15,60,145,0.2);
        color: white;
    }

    .badge-type {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
    }
    .badge-type-tuition {
        background: rgba(15, 60, 145, 0.15);
        color: #0f3c91;
    }
    .badge-type-miscellaneous {
        background: rgba(244, 180, 20, 0.15);
        color: #b26a00;
    }
    .badge-type-exam {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
    }

    .badge-course {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        background: rgba(139, 92, 246, 0.12);
        color: #6d28d9;
    }
    .badge-all-courses {
        font-weight: 500;
        padding: 0.45rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        background: rgba(100, 116, 139, 0.1);
        color: #64748b;
    }

    /* Exam period badges */
    .badge-exam-period {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        background: rgba(234, 88, 12, 0.12);
        color: #c2410c;
    }
    .badge-all-periods {
        font-weight: 500;
        padding: 0.45rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        background: rgba(100, 116, 139, 0.1);
        color: #64748b;
    }

    .empty-state { padding: 2rem; }
    .empty-state i { opacity: 0.7; }
    .empty-state h6 { font-size: 1.1rem; }
    .empty-state p { font-size: 0.9rem; max-width: 300px; margin: 0 auto; }

    .table td {
        border-bottom: 1px solid #f0f2f5;
        color: #334155;
        vertical-align: middle;
    }
    .table th {
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e9ecef;
    }

    .btn-secondary {
        background: #e9ecef;
        border: none;
        color: #495057;
        font-weight: 500;
    }
    .btn-secondary:hover { background: #d3d8de; }
    .btn-danger {
        background: #dc3545;
        border: none;
        font-weight: 500;
    }
    .btn-danger:hover { background: #b02a37; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const feeId  = button.getAttribute('data-id');
            const form   = document.getElementById('deleteForm');
            form.action  = "{{ route('admin.fees.destroy', ':id') }}".replace(':id', feeId);
        });
    }
});
</script>
@endpush
