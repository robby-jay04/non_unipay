@extends('admin.layouts.app')
@section('title', 'Manage Admins')

@push('styles')
<style>
    .page-header {
        background: var(--modal-header-bg);
        border-radius: 20px;
        color: white;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
    }
    .page-header h2 { font-weight: 700; margin: 0; font-size: 1.6rem; color: white; }
    .page-header p  { color: rgba(255,255,255,0.8); margin: 0; font-size: 0.9rem; }

    .card-table {
        background: var(--bg-main);
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: background 0.3s ease, box-shadow 0.3s ease;
    }
    .card-table .card-body { padding: 1.5rem; }

    /* Search Bar */
    .search-bar .search-wrap { position: relative; flex: 1;width: 260px; margin-left: auto;  }
    .search-bar .form-control {
        border-radius: 30px;
        border: 1.5px solid var(--input-border);
        background: var(--input-bg);
        color: var(--text-primary);
        padding: 0.6rem 2.8rem 0.6rem 1.2rem;
        font-size: 0.9rem;
        transition: background 0.3s, color 0.3s, border-color 0.3s;
        width: 100%;
    }
    .search-bar .form-control::placeholder { color: var(--text-muted); }
    .search-bar .form-control:focus {
        border-color: var(--btn-primary);
        box-shadow: 0 0 0 3px rgba(15,60,145,0.1);
        background: var(--input-bg);
        color: var(--text-primary);
    }
    #adminSearchSpinner {
        position: absolute; right: 14px; top: 50%;
        transform: translateY(-50%);
        color: #0f3c91; font-size: 0.8rem;
        display: none; pointer-events: none;
    }

    .btn-add {
        background: white; color: var(--btn-primary);
        border: none; border-radius: 30px;
        padding: 0.6rem 1.4rem; font-weight: 600; font-size: 0.9rem;
        transition: all 0.2s;
    }
    .btn-add:hover { background: rgba(255,255,255,0.85); transform: translateY(-1px); color: var(--btn-primary); }

    .table { margin: 0; color: var(--text-secondary); }
    .table thead th {
        background: var(--table-header-bg);
        border-bottom: 2px solid var(--border-color);
        font-weight: 600; font-size: 0.82rem;
        text-transform: uppercase; letter-spacing: 0.5px;
        color: var(--text-muted); padding: 1rem 1.2rem;
        transition: background 0.3s, color 0.3s;
    }
    .table tbody td {
        padding: 1rem 1.2rem; vertical-align: middle;
        border-bottom: 1px solid var(--table-row-border);
        font-size: 0.9rem; background-color: var(--bg-main);
        color: var(--text-secondary); transition: background 0.3s, color 0.3s;
    }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover td      { background-color: var(--hover-bg); }

    .avatar {
        width: 38px; height: 38px; border-radius: 50%;
        background: var(--modal-header-bg); color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 0.9rem; flex-shrink: 0;
    }

    .role-pill {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 0.78rem; font-weight: 600;
        padding: 4px 12px; border-radius: 20px;
    }
    .role-pill.superadmin { background: #fff8e1; color: #8a6000; border: 1px solid #f6c90e; }
    .role-pill.admin      { background: #e8f0fe; color: #174ea6; border: 1px solid #aecbfa; }
    body.dark .role-pill.superadmin { background: #332700; color: #ffd966; border-color: #ffc107; }
    body.dark .role-pill.admin      { background: #1e2a4a; color: #90caf9; border-color: #3b82f6; }

    /* Status Toggle Switch */
    .status-switch { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
    .status-switch input[type="checkbox"] { display: none; }
    .status-switch .track {
        width: 40px; height: 22px; border-radius: 11px;
        background: #e2e8f0; transition: background 0.25s;
        position: relative; flex-shrink: 0;
    }
    .status-switch input:checked ~ .track { background: #22c55e; }
    .status-switch .track::after {
        content: ''; position: absolute;
        width: 16px; height: 16px; border-radius: 50%;
        background: white; top: 3px; left: 3px;
        transition: transform 0.25s; box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .status-switch input:checked ~ .track::after { transform: translateX(18px); }
    .status-switch .track-label {
        font-size: 0.8rem; font-weight: 600;
        color: var(--text-muted); transition: color 0.25s;
    }
    .status-switch input:checked ~ .track-label { color: #16a34a; }
    body.dark .status-switch .track { background: #334155; }
    body.dark .status-switch input:checked ~ .track { background: #22c55e; }
    body.dark .status-switch input:checked ~ .track-label { color: #4ade80; }

    /* Status badge in table */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 0.76rem; font-weight: 600;
        padding: 3px 10px; border-radius: 20px;
    }
    .status-badge.active   { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .status-badge.inactive { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
    body.dark .status-badge.active   { background: #14532d; color: #4ade80; border-color: #22c55e; }
    body.dark .status-badge.inactive { background: #1e293b; color: #94a3b8; border-color: #334155; }

    .btn-action {
        border: none; border-radius: 20px;
        padding: 0.35rem 0.9rem; font-size: 0.82rem;
        font-weight: 500; transition: all 0.15s;
    }
    .btn-edit       { background: #e8f0fe; color: #174ea6; }
    .btn-edit:hover { background: #174ea6; color: white; }
    .btn-del        { background: #fce8e6; color: #b31412; }
    .btn-del:hover  { background: #b31412; color: white; }

    body.dark .btn-edit       { background: #1e2a4a; color: #90caf9; }
    body.dark .btn-edit:hover { background: #3b82f6; color: white; }
    body.dark .btn-del        { background: #3b1e1e; color: #f87171; }
    body.dark .btn-del:hover  { background: #ef4444; color: white; }

    .you-badge {
        background: #e6f4ea; color: #137333;
        font-size: 0.72rem; font-weight: 600;
        padding: 2px 8px; border-radius: 12px; margin-left: 6px;
    }
    body.dark .you-badge { background: #1e3a2f; color: #4ade80; }

    .empty-state { padding: 3rem; text-align: center; color: var(--text-muted); }
    .empty-state i { font-size: 2.5rem; margin-bottom: 0.75rem; }

    .modal-content {
        background: var(--bg-main); border-radius: 20px;
        border: none; box-shadow: 0 8px 40px rgba(0,0,0,0.12);
        transition: background 0.3s;
    }
    .modal-header {
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
        background: var(--modal-header-bg);
        border-radius: 20px 20px 0 0;
    }
    .modal-header .modal-title { color: white; }
    .modal-header .btn-close   { filter: brightness(0) invert(1); }
    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 1rem 1.5rem; background: var(--bg-main);
        transition: background 0.3s;
    }
    .modal-body { padding: 1.5rem; background: var(--bg-main); transition: background 0.3s; }

    .section-title {
        font-size: 0.75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 1px;
        color: var(--text-muted); margin-bottom: 1rem; margin-top: 1.25rem;
    }
    .section-title:first-child { margin-top: 0; }

    .form-label { font-weight: 600; font-size: 0.88rem; color: var(--text-primary); }

    .modal .form-control,
    .modal .form-select {
        background: var(--input-bg); border: 1.5px solid var(--input-border);
        border-radius: 12px; padding: 0.65rem 1rem;
        font-size: 0.92rem; color: var(--text-primary);
        transition: background 0.3s, color 0.3s, border-color 0.3s;
    }
    .modal .form-control::placeholder,
    .modal .form-select option { color: var(--text-muted); }
    .modal .form-control:focus,
    .modal .form-select:focus {
        border-color: var(--btn-primary);
        box-shadow: 0 0 0 3px rgba(15,60,145,0.1);
        background: var(--input-bg); color: var(--text-primary);
    }
    .modal .form-control.is-invalid,
    .modal .form-select.is-invalid { border-color: #dc3545; }
    .invalid-feedback           { color: #dc3545; }
    body.dark .invalid-feedback { color: #f87171; }

    body:not(.dark) .modal .form-control:-webkit-autofill,
    body:not(.dark) .modal .form-select:-webkit-autofill {
        -webkit-text-fill-color: #1e293b;
        -webkit-box-shadow: 0 0 0px 1000px #f8fafc inset;
    }
    body.dark .modal .form-control:-webkit-autofill,
    body.dark .modal .form-select:-webkit-autofill {
        -webkit-text-fill-color: #f1f5f9;
        -webkit-box-shadow: 0 0 0px 1000px #334155 inset;
    }

    .pw-hint { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }

    .btn-submit {
        background: var(--btn-primary); color: white; border: none;
        border-radius: 30px; padding: 0.6rem 1.6rem;
        font-weight: 600; transition: all 0.2s;
    }
    .btn-submit:hover { background: var(--btn-primary-hover); transform: translateY(-1px); color: white; }
    .btn-cancel-modal {
        border-radius: 30px; padding: 0.6rem 1.2rem; font-weight: 500;
        color: var(--text-secondary); background: var(--input-bg);
        border: 1px solid var(--input-border); transition: background 0.2s, color 0.2s;
    }
    .btn-cancel-modal:hover { background: var(--hover-bg); }

    .alert-success { background-color: rgba(34,197,94,0.1); border-color: #22c55e; color: #15803d; }
    body.dark .alert-success { color: #4ade80; }
    .alert-danger  { background-color: rgba(239,68,68,0.1);  border-color: #ef4444; color: #b91c1c; }
    body.dark .alert-danger  { color: #f87171; }
    .alert .btn-close { filter: none; }
    body.dark .alert .btn-close { filter: invert(0.8); }

    body.dark .pagination .page-link { background: var(--bg-main); border-color: var(--border-color); color: var(--text-secondary); }
    body.dark .pagination .page-item.active   .page-link { background: var(--btn-primary); border-color: var(--btn-primary); color: white; }
    body.dark .pagination .page-item.disabled .page-link { background: var(--bg-main); color: var(--text-muted); }

    /* Active status toggle in modal */
    .toggle-row {
        display: flex; align-items: center; justify-content: space-between;
        background: var(--input-bg); border: 1.5px solid var(--input-border);
        border-radius: 12px; padding: 0.75rem 1rem;
        transition: background 0.3s, border-color 0.3s;
    }
    .toggle-row .toggle-info { display: flex; flex-direction: column; gap: 2px; }
    .toggle-row .toggle-info span:first-child { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
    .toggle-row .toggle-info span:last-child  { font-size: 0.78rem; color: var(--text-muted); }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-3">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-3">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fas fa-user-shield me-2"></i>Manage Admins</h2>
            <p>Create, edit, and remove admin accounts for this system.</p>
        </div>
        <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus me-1"></i> Add Admin
        </button>
    </div>
</div>

<div class="card-table">
    <div class="card-body">
        <div class="search-bar mb-4">
            <div class="search-wrap">
                <input type="text" id="adminSearchInput" class="form-control"
                       placeholder="Search by name or email…"
                       value="{{ $search ?? '' }}" autocomplete="off">
                <i class="fas fa-circle-notch fa-spin" id="adminSearchSpinner"></i>
            </div>
        </div>

        <div id="adminsTableWrap">
            @include('admin.superadmin.admins.partials.admins_table')
        </div>
    </div>
</div>

{{-- ===================== CREATE MODAL ===================== --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.superadmin.admins.store') }}" id="createForm" class="requires-loader">
                @csrf
                <div class="modal-body">

                    @if($errors->createBag->any())
                        <div class="alert alert-danger rounded-4 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->createBag->all() as $error)
                                    <li style="font-size:0.88rem;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="section-title">Account Details</div>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name"
                                       class="form-control @error('name', 'createBag') is-invalid @enderror"
                                       value="{{ old('name') }}" placeholder="e.g. Maria Santos" required autofocus>
                                @error('name', 'createBag')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email"
                                       class="form-control @error('email', 'createBag') is-invalid @enderror"
                                       value="{{ old('email') }}" placeholder="admin@example.com" required>
                                @error('email', 'createBag')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="role"
                                        class="form-select @error('role', 'createBag') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select role…</option>
                                    <option value="admin"      {{ old('role') === 'admin'      ? 'selected' : '' }}>Admin</option>
                                    <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                </select>
                                @error('role', 'createBag')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            {{-- Active Status Toggle --}}
                            <div class="mb-3">
                                <label class="form-label">Account Status</label>
                                <div class="toggle-row">
                                    <div class="toggle-info">
                                        <span>Active Account</span>
                                        <span>Allow this admin to log in</span>
                                    </div>
                                    <label class="status-switch mb-0">
                                        <input type="checkbox" name="is_active" value="1"
                                               {{ old('is_active', '1') === '1' ? 'checked' : '' }}>
                                        <span class="track"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
    <div class="section-title">Password</div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-group">
            <input type="password" name="password" id="createPassword"
                   class="form-control @error('password', 'createBag') is-invalid @enderror"
                   placeholder="Min. 8 chars, upper + lower + number" required>
            <button class="btn btn-outline-secondary" type="button"
                    onclick="togglePassword('createPassword', 'createPasswordIcon')">
                <svg id="createPasswordIcon" width="16" height="16"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
            @error('password', 'createBag')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <div class="input-group">
            <input type="password" name="password_confirmation" id="createPasswordConfirm"
                   class="form-control"
                   placeholder="Repeat password" required>
            <button class="btn btn-outline-secondary" type="button"
                    onclick="togglePassword('createPasswordConfirm', 'createPasswordConfirmIcon')">
                <svg id="createPasswordConfirmIcon" width="16" height="16"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
    </div>
</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save me-1"></i> Create Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== EDIT MODAL ===================== --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Admin Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm" class="requires-loader">
                @csrf @method('PUT')
                <div class="modal-body">

                    @if($errors->editBag->any())
                        <div class="alert alert-danger rounded-4 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->editBag->all() as $error)
                                    <li style="font-size:0.88rem;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="section-title">Account Details</div>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" id="edit_name" name="name"
                                       class="form-control @error('name', 'editBag') is-invalid @enderror"
                                       value="{{ old('name') }}" required>
                                @error('name', 'editBag')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" id="edit_email" name="email"
                                       class="form-control @error('email', 'editBag') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email', 'editBag')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select id="edit_role" name="role"
                                        class="form-select @error('role', 'editBag') is-invalid @enderror" required>
                                    <option value="admin">Admin</option>
                                    <option value="superadmin">Super Admin</option>
                                </select>
                                @error('role', 'editBag')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            {{-- Active Status Toggle --}}
                            <div class="mb-3">
                                <label class="form-label">Account Status</label>
                                <div class="toggle-row">
                                    <div class="toggle-info">
                                        <span>Active Account</span>
                                        <span>Allow this admin to log in</span>
                                    </div>
                                    <label class="status-switch mb-0">
                                        <input type="checkbox" id="edit_is_active" name="is_active" value="1">
                                        <span class="track"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="section-title">Change Password</div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" id="edit_password" name="password"
                                       class="form-control @error('password', 'editBag') is-invalid @enderror"
                                       placeholder="Leave blank to keep current password">
                                <div class="pw-hint">Min. 8 characters, upper &amp; lowercase, at least one number.</div>
                                @error('password', 'editBag')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" id="edit_password_confirmation" name="password_confirmation"
                                       class="form-control" placeholder="Repeat new password">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================== DELETE MODAL ===================== --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Are you sure you want to delete <strong id="deleteAdminName"></strong>?
                    This cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline requires-loader">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-submit" style="background:#dc3545;">
                        <i class="fas fa-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Delete modal ─────────────────────────────────────────────────────────────
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('deleteAdminName').textContent = btn.dataset.name;
    document.getElementById('deleteForm').action =
        '{{ url("admin/superadmin/admins") }}/' + btn.dataset.id;
});

// ── Edit modal ───────────────────────────────────────────────────────────────
document.getElementById('editModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('edit_name').value    = btn.dataset.name;
    document.getElementById('edit_email').value   = btn.dataset.email;
    document.getElementById('edit_role').value    = btn.dataset.role;
    document.getElementById('edit_is_active').checked = btn.dataset.isActive === '1';
    document.getElementById('edit_password').value = '';
    document.getElementById('edit_password_confirmation').value = '';
    document.getElementById('editForm').action =
        '{{ url("admin/superadmin/admins") }}/' + btn.dataset.id;
});

// ── Re-open modals on validation errors ──────────────────────────────────────
<?php if($errors->createBag->any()): ?>
    new bootstrap.Modal(document.getElementById('createModal')).show();
<?php endif; ?>
<?php if($errors->editBag->any()): ?>
    new bootstrap.Modal(document.getElementById('editModal')).show();
    <?php if(old('_edit_id')): ?>
        document.getElementById('editForm').action =
            '<?php echo url("admin/superadmin/admins") . '/' . old("_edit_id"); ?>';
    <?php endif; ?>
<?php endif; ?>



// ── Live search ──────────────────────────────────────────────────────────────
(function () {
    const input   = document.getElementById('adminSearchInput');
    const spinner = document.getElementById('adminSearchSpinner');
    const wrap    = document.getElementById('adminsTableWrap');
    let abortCtrl = null;
    let debounce  = null;

    function showSpinner() { spinner.style.display = 'inline-block'; }
    function hideSpinner() { spinner.style.display = 'none'; }

    function buildUrl(base) {
        const q   = input.value.trim();
        const url = new URL(base || '{{ url("admin/superadmin/admins") }}', window.location.href);
        if (q) url.searchParams.set('search', q);
        else   url.searchParams.delete('search');
        return url.toString();
    }

    async function loadAdmins(url) {
        if (abortCtrl) abortCtrl.abort();
        abortCtrl = new AbortController();
        showSpinner();
        try {
            const sep = url.includes('?') ? '&' : '?';
            const res = await fetch(url + sep + 'ajax=1', {
                signal:  abortCtrl.signal,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            wrap.innerHTML = (await res.json()).html;
        } catch (err) {
            if (err.name !== 'AbortError') console.error('Admin search error:', err);
        } finally {
            hideSpinner();
            abortCtrl = null;
        }
    }

    input.addEventListener('input', function () {
        clearTimeout(debounce);
        debounce = setTimeout(() => loadAdmins(buildUrl()), 350);
    });

    document.addEventListener('click', function (e) {
        const link = e.target.closest('#adminsTableWrap a');
        if (!link) return;
        const href = link.getAttribute('href') || '';
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        e.preventDefault();
        loadAdmins(buildUrl(link.href));
    });
    // ── Toggle password visibility ───────────────────────────────────────────────
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    const show  = input.type === 'password';

    input.type = show ? 'text' : 'password';

    icon.innerHTML = show
        ? `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
           <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
           <line x1="1" y1="1" x2="23" y2="23"/>`
        : `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
           <circle cx="12" cy="12" r="3"/>`;
}
})();
</script>
@endpush