@extends('admin.layouts.app')

@section('title', 'Students')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Student Management</h2>
</div>

<!-- Hidden element to store current page (used by JS) -->
<input type="hidden" id="initialPage" data-page="{{ request('page', 1) }}">

<!-- Main Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">All Students</h5>

        <form method="GET" class="d-flex gap-2" action="{{ route('admin.students') }}" id="searchForm">
            <!-- Course filter -->
            <select name="course" class="form-select rounded-pill border-0 bg-light px-4 py-2" style="width: 150px;">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course }}" {{ request('course') == $course ? 'selected' : '' }}>
                        {{ $course }}
                    </option>
                @endforeach
            </select>

            <!-- Year Level filter -->
            <select name="year_level" class="form-select rounded-pill border-0 bg-light px-4 py-2" style="width: 190px;">
                <option value="">All Year Level</option>
                @foreach($yearLevels as $level)
                    <option value="{{ $level }}" {{ request('year_level') == $level ? 'selected' : '' }}>
                        {{ $level }}
                    </option>
                @endforeach
            </select>

            <!-- Clearance Status filter -->
            <select name="clearance_status" class="form-select rounded-pill border-0 bg-light px-4 py-2" style="width: 150px;">
                <option value="">All Status</option>
                @foreach($clearanceStatuses as $status)
                    <option value="{{ $status }}" {{ request('clearance_status') == $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>

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
                            <div class="d-flex gap-2 align-items-center">
                                <!-- View Button -->
                                <button class="btn-action view-student" title="View details"
                                        data-id="{{ $student->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                @if(!$student->is_confirmed)
                                    <!-- Confirm Button -->
                                    <button type="button"
                                            class="btn-action confirm-student trigger-confirm"
                                            title="Confirm account"
                                            data-action="{{ route('admin.students.confirm', $student) }}"
                                            data-method="POST"
                                            data-type="confirm"
                                            data-title="Confirm Student Account"
                                            data-message="Are you sure you want to confirm this student account?"
                                            data-confirm-text="Yes, Confirm"
                                            data-icon-bg="rgba(15,60,145,0.12)"
                                            data-icon="fas fa-check-circle"
                                            data-icon-color="#0f3c91"
                                            data-btn-bg="#0f3c91">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                @else
                                    <span class="badge-confirmed">
                                        <i class="fas fa-check-circle me-1"></i> Confirmed
                                    </span>
                                @endif

                                <!-- Delete Button -->
                                <button type="button"
                                        class="btn-action delete-student trigger-confirm"
                                        title="Delete student"
                                        data-action="{{ route('admin.students.destroy', $student) }}"
                                        data-method="DELETE"
                                        data-type="delete"
                                        data-title="Delete Student"
                                        data-message="Are you sure you want to delete this student? This action cannot be undone."
                                        data-confirm-text="Yes, Delete"
                                        data-icon-bg="rgba(220,53,69,0.12)"
                                        data-icon="fas fa-trash-alt"
                                        data-icon-color="#a71d2a"
                                        data-btn-bg="#a71d2a">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
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
            {{ $students->appends(request()->only(['search', 'course', 'year_level', 'clearance_status']))->links('pagination::no-summary') }}
        </div>
        @endif
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div id="confirmIconWrap" class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width:64px;height:64px;"></div>
            </div>
            <div class="modal-body text-center px-4 pb-2 mt-2">
                <h5 class="fw-bold mb-2" id="confirmTitle"></h5>
                <p class="text-muted mb-0" id="confirmMessage"></p>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn rounded-pill px-4 fw-semibold" id="confirmActionBtn"></button>
            </div>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div id="resultIconWrap" class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width:64px;height:64px;"></div>
            </div>
            <div class="modal-body text-center px-4 pb-2 mt-2">
                <h5 class="fw-bold mb-2" id="resultTitle"></h5>
                <p class="text-muted mb-0" id="resultMessage"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn rounded-pill px-5 fw-semibold" id="resultOkBtn" data-bs-dismiss="modal" style="background:#0f3c91;color:white;">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Student Details Modal -->
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

{{-- Global Page Loader for filtering/pagination --}}
<div id="pageLoader" style="display: none; position: fixed; inset: 0; z-index: 100000; background: rgba(5, 15, 50, 0.75); backdrop-filter: blur(6px); align-items: center; justify-content: center; flex-direction: column; gap: 1rem;">
    <div class="loader-card" style="background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%); border-radius: 28px; padding: 2rem 2.5rem; text-align: center; min-width: 240px;">
        <div class="loader-logo-ring" style="position: relative; width: 70px; height: 70px; margin: 0 auto;">
            <img src="{{ asset('logo.png') }}" alt="Non-UniPay" style="width: 70px; height: 70px; border-radius: 50%; background: white; padding: 6px; object-fit: contain;">
            <div class="loader-spinner" style="position: absolute; inset: -5px; border-radius: 50%; border: 3px solid transparent; border-top-color: #f4b400; border-right-color: rgba(244, 180, 0, 0.3); animation: loader-spin 0.85s linear infinite;"></div>
        </div>
        <p class="loader-text" style="color: white; font-weight: 600; margin-top: 1rem;">Loading Data</p>
        <p class="loader-subtext" style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">Please wait...</p>
        <div class="loader-bar-track" style="width: 140px; height: 4px; background: rgba(255,255,255,0.2); border-radius: 99px; overflow: hidden; margin: 0.75rem auto 0;">
            <div class="loader-bar-fill" style="height: 100%; background: #f4b400; border-radius: 99px; animation: loader-bar 1.1s ease-in-out infinite alternate;"></div>
        </div>
    </div>
</div>

{{-- Loading Overlay for confirm/delete actions --}}
<div id="studentActionLoader" style="display: none; position: fixed; inset: 0; z-index: 100000; background: rgba(5, 15, 50, 0.75); backdrop-filter: blur(6px); align-items: center; justify-content: center; flex-direction: column; gap: 1rem;">
    <div class="loader-card" style="background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%); border-radius: 28px; padding: 2rem 2.5rem; text-align: center; min-width: 240px;">
        <div class="loader-logo-ring" style="position: relative; width: 70px; height: 70px; margin: 0 auto;">
            <img src="{{ asset('logo.png') }}" alt="Non-UniPay" style="width: 70px; height: 70px; border-radius: 50%; background: white; padding: 6px; object-fit: contain;">
            <div class="loader-spinner" style="position: absolute; inset: -5px; border-radius: 50%; border: 3px solid transparent; border-top-color: #f4b400; border-right-color: rgba(244, 180, 0, 0.3); animation: loader-spin 0.85s linear infinite;"></div>
        </div>
        <p class="loader-text" style="color: white; font-weight: 600; margin-top: 1rem;">Processing Request</p>
        <p class="loader-subtext" style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">Please wait...</p>
        <div class="loader-bar-track" style="width: 140px; height: 4px; background: rgba(255,255,255,0.2); border-radius: 99px; overflow: hidden; margin: 0.75rem auto 0;">
            <div class="loader-bar-fill" style="height: 100%; background: #f4b400; border-radius: 99px; animation: loader-bar 1.1s ease-in-out infinite alternate;"></div>
        </div>
    </div>
</div>

<style>
    @keyframes loader-spin {
        to { transform: rotate(360deg); }
    }
    @keyframes loader-bar {
        from { width: 15%; margin-left: 0; }
        to   { width: 70%; margin-left: 30%; }
    }
    .student-row {
        transition: all 0.2s ease;
    }
    .student-row:hover {
        background-color: rgba(15, 60, 145, 0.02) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.02);
    }
    .student-avatar {
        transition: all 0.2s;
    }
    .student-row:hover .student-avatar {
        background: rgba(15,60,145,0.15) !important;
        transform: scale(1.02);
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
    .empty-state {
        padding: 2rem;
    }
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
    .form-control:focus {
        box-shadow: none;
        border-color: #0f3c91;
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const studentModal = new bootstrap.Modal(document.getElementById('studentModal'));
    const actionLoader = document.getElementById('studentActionLoader');
    const pageLoader = document.getElementById('pageLoader');

    function showActionLoader() { if (actionLoader) actionLoader.style.display = 'flex'; }
    function hideActionLoader() { if (actionLoader) actionLoader.style.display = 'none'; }
    function showPageLoader()  { if (pageLoader) pageLoader.style.display = 'flex'; }
    function hidePageLoader()  { if (pageLoader) pageLoader.style.display = 'none'; }

    // --- Modal helpers (unchanged) ---
    function showConfirm({ title, message, confirmText, confirmStyle, onConfirm }) {
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        const iconWrap = document.getElementById('confirmIconWrap');
        iconWrap.style.background = confirmStyle.iconBg;
        iconWrap.innerHTML = `<i class="${confirmStyle.icon}" style="font-size:1.6rem;color:${confirmStyle.iconColor};"></i>`;
        const oldBtn = document.getElementById('confirmActionBtn');
        const freshBtn = oldBtn.cloneNode(true);
        oldBtn.parentNode.replaceChild(freshBtn, oldBtn);
        freshBtn.textContent = confirmText;
        freshBtn.style.background = confirmStyle.btnBg;
        freshBtn.style.color = 'white';
        freshBtn.addEventListener('click', () => {
            bootstrap.Modal.getInstance(document.getElementById('confirmActionModal')).hide();
            onConfirm();
        });
        new bootstrap.Modal(document.getElementById('confirmActionModal')).show();
    }

    function showResult({ type, title, message, onOk }) {
        const palettes = {
            success: { iconBg: 'rgba(76,175,80,0.12)', icon: 'fas fa-check-circle', iconColor: '#2e7d32' },
            error: { iconBg: 'rgba(220,53,69,0.12)', icon: 'fas fa-times-circle', iconColor: '#a71d2a' },
            info: { iconBg: 'rgba(15,60,145,0.12)', icon: 'fas fa-info-circle', iconColor: '#0f3c91' },
        };
        const p = palettes[type] || palettes.info;
        document.getElementById('resultTitle').textContent = title;
        document.getElementById('resultMessage').textContent = message;
        const iconWrap = document.getElementById('resultIconWrap');
        iconWrap.style.background = p.iconBg;
        iconWrap.innerHTML = `<i class="${p.icon}" style="font-size:1.6rem;color:${p.iconColor};"></i>`;
        const oldOkBtn = document.getElementById('resultOkBtn');
        const freshOkBtn = oldOkBtn.cloneNode(true);
        oldOkBtn.parentNode.replaceChild(freshOkBtn, oldOkBtn);
        if (onOk) freshOkBtn.addEventListener('click', onOk);
        new bootstrap.Modal(document.getElementById('resultModal')).show();
    }

    async function submitFormAjax(action, method) {
        const formData = new FormData();
        formData.append('_token', csrfToken);
        if (method === 'DELETE') formData.append('_method', 'DELETE');
        return await fetch(action, { method: 'POST', body: formData });
    }

    // --- Action handlers (confirm/delete) ---
    function attachActionHandlers() {
        document.querySelectorAll('.trigger-confirm').forEach(btn => {
            const handler = function () {
                const action = this.dataset.action;
                const method = this.dataset.method;
                const type = this.dataset.type;
                const title = this.dataset.title;
                const message = this.dataset.message;
                const confirmText = this.dataset.confirmText;
                const iconBg = this.dataset.iconBg;
                const icon = this.dataset.icon;
                const iconColor = this.dataset.iconColor;
                const btnBg = this.dataset.btnBg;
                const row = this.closest('tr');
                showConfirm({
                    title, message, confirmText,
                    confirmStyle: { iconBg, icon, iconColor, btnBg },
                    onConfirm: async () => {
                        showActionLoader();
                        try {
                            const res = await submitFormAjax(action, method);
                            hideActionLoader();
                            if (res.ok) {
                                if (type === 'delete') {
                                    row?.remove();
                                    showResult({ type: 'success', title: 'Student Deleted', message: 'The student account has been permanently removed.' });
                                } else {
                                    showResult({ type: 'success', title: 'Account Confirmed', message: 'The student account has been confirmed successfully.', onOk: () => window.location.reload() });
                                }
                            } else {
                                showResult({ type: 'error', title: 'Action Failed', message: 'Something went wrong. Please try again.' });
                            }
                        } catch (error) {
                            hideActionLoader();
                            showResult({ type: 'error', title: 'Error', message: 'An unexpected error occurred. Please try again.' });
                        }
                    }
                });
            };
            btn.removeEventListener('click', btn._listener);
            btn.addEventListener('click', handler);
            btn._listener = handler;
        });
    }

    function attachViewHandlers() {
        document.querySelectorAll('.view-student').forEach(btn => {
            const handler = function () {
                const studentId = this.dataset.id;
                fetch(`/admin/students/${studentId}/json`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('modalStudentName').textContent = data.name;
                        document.getElementById('modalStudentNo').textContent = data.student_no;
                        document.getElementById('modalStudentEmail').textContent = data.email;
                        document.getElementById('modalStudentContact').textContent = data.contact || 'N/A';
                        document.getElementById('modalStudentCourse').textContent = data.course;
                        document.getElementById('modalStudentYear').textContent = `Year ${data.year_level}`;
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
                        document.getElementById('modalStudentConfirmed').innerHTML = data.is_confirmed
                            ? '<span style="background:rgba(15,60,145,0.1);color:#0f3c91;font-weight:500;padding:0.4rem 1rem;border-radius:30px;font-size:0.85rem;"><i class="fas fa-check-circle me-1"></i>Confirmed</span>'
                            : '<span style="background:rgba(244,180,20,0.15);color:#b26a00;font-weight:500;padding:0.4rem 1rem;border-radius:30px;font-size:0.85rem;"><i class="fas fa-clock me-1"></i>Pending Approval</span>';
                        document.getElementById('modalStudentStatus').innerHTML = data.clearance_status === 'cleared'
                            ? '<span style="background:rgba(76,175,80,0.15);color:#2e7d32;font-weight:500;padding:0.4rem 1rem;border-radius:30px;font-size:0.85rem;"><i class="fas fa-shield-alt me-1"></i>Cleared</span>'
                            : '<span style="background:rgba(220,53,69,0.15);color:#a71d2a;font-weight:500;padding:0.4rem 1rem;border-radius:30px;font-size:0.85rem;"><i class="fas fa-times-circle me-1"></i>Not Cleared</span>';
                        studentModal.show();
                    })
                    .catch(err => console.error(err));
            };
            btn.removeEventListener('click', btn._viewListener);
            btn.addEventListener('click', handler);
            btn._viewListener = handler;
        });
    }

    // --- AJAX Filtering & Pagination (with global loader) ---
    let currentStudentPage = parseInt(document.getElementById('initialPage').dataset.page) || 1;
    let isLoading = false;

    function buildFilterUrl(page) {
        const formData = new FormData(document.getElementById('searchForm'));
        const url = new URL(window.location.href);
        url.searchParams.set('search', formData.get('search') || '');
        url.searchParams.set('course', formData.get('course') || '');
        url.searchParams.set('year_level', formData.get('year_level') || '');
        url.searchParams.set('clearance_status', formData.get('clearance_status') || '');
        if (page && page > 1) {
            url.searchParams.set('page', page);
        } else {
            url.searchParams.delete('page');
        }
        return url.toString();
    }

    async function loadStudents(url) {
        if (isLoading) return;
        isLoading = true;
        showPageLoader();  // show global overlay
        try {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newTbody = doc.getElementById('students-table-body');
            const newPagination = doc.getElementById('students-pagination');
            if (newTbody) document.getElementById('students-table-body').innerHTML = newTbody.innerHTML;
            if (newPagination) document.getElementById('students-pagination').innerHTML = newPagination.innerHTML;
            attachViewHandlers();
            attachActionHandlers();
        } catch (err) {
            console.error(err);
        } finally {
            hidePageLoader();
            isLoading = false;
        }
    }

    function applyFiltersAndLoad() {
        currentStudentPage = 1;
        loadStudents(buildFilterUrl(1));
    }

    // Filters: change events and search input (debounced)
    const searchInput = document.querySelector('input[name="search"]');
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(applyFiltersAndLoad, 500);
        });
    }

    const courseSelect = document.querySelector('select[name="course"]');
    if (courseSelect) courseSelect.addEventListener('change', applyFiltersAndLoad);
    const yearSelect = document.querySelector('select[name="year_level"]');
    if (yearSelect) yearSelect.addEventListener('change', applyFiltersAndLoad);
    const clearanceSelect = document.querySelector('select[name="clearance_status"]');
    if (clearanceSelect) clearanceSelect.addEventListener('change', applyFiltersAndLoad);

    // Form submit (search button)
    document.getElementById('searchForm').addEventListener('submit', function (e) {
        e.preventDefault();
        applyFiltersAndLoad();
    });

    // Pagination clicks
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.pagination a');
        if (link && !link.classList.contains('disabled')) {
            e.preventDefault();
            const targetUrl = new URL(link.href);
            currentStudentPage = parseInt(targetUrl.searchParams.get('page') || '1');
            loadStudents(link.href);
        }
    });

    // Poll for new students (also uses global loader)
    let lastUnconfirmedCount = null;
    function pollForNewStudents() {
        fetch('/admin/api/new-students-count')
            .then(res => res.json())
            .then(data => {
                if (lastUnconfirmedCount !== null && data.count > lastUnconfirmedCount) {
                    loadStudents(buildFilterUrl(currentStudentPage));
                }
                lastUnconfirmedCount = data.count;
            })
            .catch(err => console.error('Poll error:', err));
    }
    setInterval(pollForNewStudents, 5000);
    pollForNewStudents();

    // Initial attachments
    attachViewHandlers();
    attachActionHandlers();
});
</script>
@endpush