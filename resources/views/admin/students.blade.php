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
        <form method="GET" class="d-flex gap-2" action="{{ route('admin.students') }}" id="searchForm">
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
                <tbody id="students-table-body">
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
        <div class="d-flex justify-content-center py-4" id="students-pagination">
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
            <!-- Header -->
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); border-radius: 20px 20px 0 0;">
    <div class="w-100 text-center py-3">
        <!-- Profile Picture -->
        <div class="mx-auto mb-2" style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid rgba(255,255,255,0.5);">
            <img id="modalStudentAvatar"
                 src=""
                 alt="Profile"
                 style="width: 100%; height: 100%; object-fit: cover; display: none;">
            <div id="modalStudentAvatarFallback"
                 class="d-flex align-items-center justify-content-center w-100 h-100"
                 style="background: rgba(255,255,255,0.2);">
                <i class="fas fa-user-graduate fa-2x text-white"></i>
            </div>
        </div>
        <h5 class="fw-bold text-white mb-0" id="modalStudentName">—</h5>
        <small class="text-white-50" id="modalStudentNo">—</small>
    </div>
    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
</div>

            <!-- Body -->
            <div class="modal-body p-4">

                <!-- Status Badges Row -->
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <span id="modalStudentConfirmed"></span>
                    <span id="modalStudentStatus"></span>
                </div>

                <!-- Info Grid -->
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded-3 h-100" style="background: #f8f9fc;">
                            <small class="text-muted d-block mb-1"><i class="fas fa-envelope me-1"></i> Email</small>
                            <span class="fw-medium" id="modalStudentEmail" style="font-size: 0.9rem;">—</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 h-100" style="background: #f8f9fc;">
                            <small class="text-muted d-block mb-1"><i class="fas fa-phone me-1"></i> Mobile</small>
                            <span class="fw-medium" id="modalStudentContact" style="font-size: 0.9rem;">—</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 h-100" style="background: #f8f9fc;">
                            <small class="text-muted d-block mb-1"><i class="fas fa-book me-1"></i> Course</small>
                            <span class="fw-medium" id="modalStudentCourse" style="font-size: 0.9rem;">—</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 h-100" style="background: #f8f9fc;">
                            <small class="text-muted d-block mb-1"><i class="fas fa-layer-group me-1"></i> Year Level</small>
                            <span class="fw-medium" id="modalStudentYear" style="font-size: 0.9rem;">—</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn w-100 rounded-pill py-2 fw-medium"
                        style="background: #f0f2f5; color: #475569;"
                        data-bs-dismiss="modal">
                    Close
                </button>
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

  function attachViewHandlers() {
    document.querySelectorAll('.view-student').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.dataset.id;
            fetch(`/admin/students/${studentId}/json`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modalStudentName').textContent    = data.name;
                    document.getElementById('modalStudentNo').textContent      = data.student_no;
                    document.getElementById('modalStudentEmail').textContent   = data.email;
                    document.getElementById('modalStudentContact').textContent = data.contact || 'N/A';
                    document.getElementById('modalStudentCourse').textContent  = data.course;
                    document.getElementById('modalStudentYear').textContent    = `Year ${data.year_level}`;

                    // ✅ Profile picture
                    const avatar = document.getElementById('modalStudentAvatar');
                    const fallback = document.getElementById('modalStudentAvatarFallback');
                    if (data.profile_picture) {
                        avatar.src = data.profile_picture;
                        avatar.style.display = 'block';
                        fallback.style.display = 'none';
                    } else {
                        avatar.style.display = 'none';
                        fallback.style.display = 'flex';
                    }

                    // Account confirmed badge
                    const confirmed = document.getElementById('modalStudentConfirmed');
                    confirmed.innerHTML = data.is_confirmed
                        ? '<span style="background:rgba(15,60,145,0.1);color:#0f3c91;font-weight:500;padding:0.4rem 1rem;border-radius:30px;font-size:0.85rem;"><i class="fas fa-check-circle me-1"></i>Confirmed</span>'
                        : '<span style="background:rgba(244,180,20,0.15);color:#b26a00;font-weight:500;padding:0.4rem 1rem;border-radius:30px;font-size:0.85rem;"><i class="fas fa-clock me-1"></i>Pending Approval</span>';

                    // Clearance badge
                    const status = document.getElementById('modalStudentStatus');
                    status.innerHTML = data.clearance_status === 'cleared'
                        ? '<span style="background:rgba(76,175,80,0.15);color:#2e7d32;font-weight:500;padding:0.4rem 1rem;border-radius:30px;font-size:0.85rem;"><i class="fas fa-shield-alt me-1"></i>Cleared</span>'
                        : '<span style="background:rgba(220,53,69,0.15);color:#a71d2a;font-weight:500;padding:0.4rem 1rem;border-radius:30px;font-size:0.85rem;"><i class="fas fa-times-circle me-1"></i>Not Cleared</span>';

                    studentModal.show();
                })
                .catch(err => console.error(err));
        });
    });
}
    function loadStudents(url) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTbody = doc.getElementById('students-table-body');
                const newPagination = doc.getElementById('students-pagination');
                if (newTbody) document.getElementById('students-table-body').innerHTML = newTbody.innerHTML;
                if (newPagination) document.getElementById('students-pagination').innerHTML = newPagination.innerHTML;
                attachViewHandlers();
            })
            .catch(err => console.error(err));
    }

    attachViewHandlers();

    // ✅ Only refresh when a NEW unconfirmed student is detected
    let lastUnconfirmedCount = null;

    function pollForNewStudents() {
        fetch('/admin/api/new-students-count')
            .then(res => res.json())
            .then(data => {
                if (lastUnconfirmedCount === null) {
                    // First load — just store the count, don't refresh
                    lastUnconfirmedCount = data.count;
                } else if (data.count > lastUnconfirmedCount) {
                    // New student registered — refresh the table
                    lastUnconfirmedCount = data.count;
                    loadStudents(window.location.href);
                } else {
                    // Count decreased (confirmed/deleted) — update silently
                    lastUnconfirmedCount = data.count;
                }
            })
            .catch(err => console.error('Poll error:', err));
    }

    // Poll every 5 seconds
    setInterval(pollForNewStudents, 5000);
    pollForNewStudents(); // run immediately on load

    // Search
    const searchForm = document.getElementById('searchForm');
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const search = new FormData(searchForm).get('search');
        const url = new URL(window.location.href);
        url.searchParams.set('search', search);
        url.searchParams.delete('page');
        loadStudents(url.toString());
    });

    // Pagination
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.pagination a');
        if (link && !link.classList.contains('disabled')) {
            e.preventDefault();
            loadStudents(link.href);
        }
    });
});
</script>
@endpush