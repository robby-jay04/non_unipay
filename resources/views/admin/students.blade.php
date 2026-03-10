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
                        <th class="px-4 py-3">Student No.</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Course</th>
                        <th class="py-3">Year Level</th>
                        <th class="py-3">Clearance Status</th>
                        <th class="py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="students-table-body">
                    @forelse($students as $student)
                    <tr class="student-row">
                        <td class="px-4 py-3 fw-medium" style="color: #1e293b;">{{ $student->student_no }}</td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="student-avatar rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 38px; height: 38px; background: rgba(15,60,145,0.1); font-weight: 600; color: #0f3c91;">
                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                </div>
                                <span class="fw-medium">{{ $student->user->name }}</span>
                            </div>
                        </td>
                        <td class="py-3">{{ $student->course }}</td>
                        <td class="py-3">{{ $student->year_level }}</td>
                        <td class="py-3">
                            @if($student->clearance_status === 'cleared')
                                <span class="badge-paid">
                                    <i class="fas fa-check-circle me-1"></i> Cleared
                                </span>
                            @else
                                <span class="badge-pending">
                                    <i class="fas fa-clock me-1"></i> Not Cleared
                                </span>
                            @endif
                        </td>
                        <td class="py-3 pe-4">
                            <div class="d-flex gap-2">
                                <!-- View Button -->
                                <button class="btn-action view-student" title="View details"
                                        data-id="{{ $student->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                @if(!$student->is_confirmed)
                                    <!-- Confirm Button -->
                                    <form action="{{ route('admin.students.confirm', $student) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Confirm this student account?')">
                                        @csrf
                                        <button type="submit" class="btn-action confirm-student" title="Confirm account">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge-confirmed">
                                        <i class="fas fa-check-circle me-1"></i> Confirmed
                                    </span>
                                @endif

                                <!-- Delete Button -->
                                <form action="{{ route('admin.students.destroy', $student) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this student? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete-student" title="Delete student">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-user-graduate fa-4x" style="color: #d1d5db;"></i>
                                <h6 class="fw-semibold mt-3" style="color: #1e293b;">No students found</h6>
                                <p class="text-muted small">Students who register will appear here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="d-flex justify-content-center py-4" id="students-pagination">
            {{ $students->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<!-- Student Details Modal (unchanged, but kept for completeness) -->
<div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); border-radius: 20px 20px 0 0;">
                <div class="w-100 text-center py-3">
                    <div class="mx-auto mb-2" style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid rgba(255,255,255,0.5);">
                        <img id="modalStudentAvatar" src="" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        <div id="modalStudentAvatarFallback" class="d-flex align-items-center justify-content-center w-100 h-100" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-user-graduate fa-2x text-white"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-white mb-0" id="modalStudentName">—</h5>
                    <small class="text-white-50" id="modalStudentNo">—</small>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <span id="modalStudentConfirmed"></span>
                    <span id="modalStudentStatus"></span>
                </div>
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
    /* Student row hover effect */
    .student-row {
        transition: all 0.2s ease;
    }
    .student-row:hover {
        background-color: rgba(15, 60, 145, 0.02) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.02);
    }

    /* Student avatar */
    .student-avatar {
        transition: all 0.2s;
    }
    .student-row:hover .student-avatar {
        background: rgba(15,60,145,0.15) !important;
        transform: scale(1.02);
    }

    /* Action buttons (circular) */
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
    }
    .btn-action:hover {
        background: rgba(15,60,145,0.1);
        color: #0f3c91;
        transform: scale(1.1);
    }
    .btn-action.confirm-student:hover {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    .btn-action.delete-student:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    /* Badges */
    .badge-paid, .badge-pending, .badge-confirmed {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
    }
    .badge-paid {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
    }
    .badge-pending {
        background: rgba(244, 180, 20, 0.15);
        color: #b26a00;
    }
    .badge-confirmed {
        background: rgba(15, 60, 145, 0.1);
        color: #0f3c91;
    }

    /* Empty state */
    .empty-state {
        padding: 2rem;
    }
    .empty-state i {
        opacity: 0.7;
    }
    .empty-state h6 {
        font-size: 1.1rem;
    }
    .empty-state p {
        font-size: 0.9rem;
        max-width: 300px;
        margin: 0 auto;
    }

    /* Table */
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

    /* Pagination (Bootstrap 5 styling is fine, but ensure our custom buttons don't interfere) */
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

    /* Search input */
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

                        // Profile picture
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

    // Poll for new unconfirmed students
    let lastUnconfirmedCount = null;

    function pollForNewStudents() {
        fetch('/admin/api/new-students-count')
            .then(res => res.json())
            .then(data => {
                if (lastUnconfirmedCount === null) {
                    lastUnconfirmedCount = data.count;
                } else if (data.count > lastUnconfirmedCount) {
                    lastUnconfirmedCount = data.count;
                    loadStudents(window.location.href);
                } else {
                    lastUnconfirmedCount = data.count;
                }
            })
            .catch(err => console.error('Poll error:', err));
    }

    setInterval(pollForNewStudents, 5000);
    pollForNewStudents();

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