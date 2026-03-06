@extends('admin.layouts.app')

@section('title', 'Students')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Student Management</h2>
</div>

<!-- Main Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">All Students</h5>
        <form method="GET" class="d-flex gap-2" action="{{ route('admin.students') }}">
            <input type="search" name="search" class="form-control rounded-pill border-0 bg-light px-4 py-2" 
                   placeholder="Search students..." value="{{ request('search') }}" style="min-width: 250px;">
            <button type="submit" class="btn rounded-pill px-4" style="background: #0f3c91; color: white;">
                <i class="fas fa-search me-2"></i> Search
            </button>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Student No</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Course</th>
                        <th class="py-3">Year Level</th>
                        <th class="py-3">Clearance Status</th>
                        <th class="py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td class="px-4 py-3">{{ $student->student_no }}</td>
                        <td class="py-3">{{ $student->user->name }}</td>
                        <td class="py-3">{{ $student->course }}</td>
                        <td class="py-3">{{ $student->year_level }}</td>
                        <td class="py-3">
                            @if($student->clearance_status === 'cleared')
                                <span class="badge-paid">Cleared</span>
                            @else
                                <span class="badge-pending">Not Cleared</span>
                            @endif
                        </td>
                        <td class="py-3 pe-4">
                            <button class="btn btn-sm rounded-pill px-3 view-student" 
                                    style="background: rgba(15, 60, 145, 0.1); color: #0f3c91; border: none;"
                                    data-id="{{ $student->id }}">
                                <i class="fas fa-eye me-1"></i> View
                            </button>

                            @if(!$student->is_confirmed)
                                <form action="{{ route('admin.students.confirm', $student) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Confirm this student account?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill px-3"
                                            style="background: rgba(40, 167, 69, 0.1); color: #28a745; border: none;">
                                        <i class="fas fa-check-circle me-1"></i> Confirm
                                    </button>
                                </form>
                            @else
                                <span class="badge-confirmed me-2">Confirmed</span>
                            @endif

                            <form action="{{ route('admin.students.destroy', $student) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this student? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm rounded-pill px-3"
                                        style="background: rgba(220, 53, 69, 0.1); color: #dc3545; border: none;">
                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No students found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="d-flex justify-content-center py-4">
            <ul class="pagination pagination-sm mb-0">
                @if($students->onFirstPage())
                    <li class="page-item disabled"><span class="page-link rounded-start-3">&laquo;</span></li>
                @else
                    <li class="page-item"><a class="page-link rounded-start-3" href="{{ $students->previousPageUrl() }}">&laquo;</a></li>
                @endif

                @foreach(range(1, $students->lastPage()) as $i)
                    <li class="page-item {{ $students->currentPage() == $i ? 'active' : '' }}">
                        <a class="page-link" href="{{ $students->url($i) }}">{{ $i }}</a>
                    </li>
                @endforeach

                @if($students->hasMorePages())
                    <li class="page-item"><a class="page-link rounded-end-3" href="{{ $students->nextPageUrl() }}">&raquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link rounded-end-3">&raquo;</span></li>
                @endif
            </ul>
        </div>
        @endif
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-graduate me-2"></i> Student Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3"><strong>Student No:</strong> <span id="modalStudentNo" class="ms-2"></span></div>
                <div class="mb-3"><strong>Name:</strong> <span id="modalStudentName" class="ms-2"></span></div>
                <div class="mb-3"><strong>Email:</strong> <span id="modalStudentEmail" class="ms-2"></span></div>
                <div class="mb-3"><strong>Course:</strong> <span id="modalStudentCourse" class="ms-2"></span></div>
                <div class="mb-3"><strong>Year Level:</strong> <span id="modalStudentYear" class="ms-2"></span></div>
                <div class="mb-3"><strong>Clearance Status:</strong> <span id="modalStudentStatus" class="ms-2"></span></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Badge styles */
    .badge-paid {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }
    .badge-pending {
        background: rgba(244, 180, 20, 0.15);
        color: #b26a00;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }
    .badge-confirmed {
        background: rgba(15, 60, 145, 0.1);
        color: #0f3c91;
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

    /* Pagination */
    .pagination .page-link {
        border: none;
        color: #64748b;
        font-weight: 500;
        padding: 0.5rem 1rem;
        margin: 0 0.2rem;
        border-radius: 8px;
        background: transparent;
    }
    .pagination .page-link:hover {
        background: rgba(15, 60, 145, 0.1);
        color: #0f3c91;
    }
    .pagination .active .page-link {
        background: #0f3c91;
        color: white;
        box-shadow: 0 4px 8px rgba(15, 60, 145, 0.2);
    }
    .pagination .disabled .page-link {
        color: #cbd5e0;
        background: transparent;
    }

    /* Search input focus */
    .form-control:focus {
        box-shadow: none;
        border-color: #0f3c91;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentModal = new bootstrap.Modal(document.getElementById('studentModal'));

    document.querySelectorAll('.view-student').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.dataset.id;

            fetch(`/admin/students/${studentId}/json`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalStudentNo').textContent = data.student_no;
                    document.getElementById('modalStudentName').textContent = data.name;
                    document.getElementById('modalStudentEmail').textContent = data.email;
                    document.getElementById('modalStudentCourse').textContent = data.course;
                    document.getElementById('modalStudentYear').textContent = data.year_level;
                    document.getElementById('modalStudentStatus').textContent = data.clearance_status;

                    studentModal.show();
                })
                .catch(err => console.error(err));
        });
    });
});
</script>
@endpush