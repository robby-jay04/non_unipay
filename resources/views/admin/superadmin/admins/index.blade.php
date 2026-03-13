@extends('admin.layouts.app')
@section('title', 'Manage Admins')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #0f3c91, #1a4da8);
        border-radius: 20px; color: white; padding: 1.5rem 2rem; margin-bottom: 1.5rem;
    }
    .page-header h2 { font-weight: 700; margin: 0; font-size: 1.6rem; }
    .page-header p  { color: rgba(255,255,255,0.8); margin: 0; font-size: 0.9rem; }
    .card-table { background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); overflow: hidden; }
    .card-table .card-body { padding: 1.5rem; }
    .search-bar .form-control { border-radius: 30px; border: 1.5px solid #e0e0e0; padding: 0.6rem 1.2rem; font-size: 0.9rem; }
    .search-bar .form-control:focus { border-color: #0f3c91; box-shadow: 0 0 0 3px rgba(15,60,145,0.1); }
    .search-bar .btn-search { border-radius: 30px; background: #0f3c91; color: white; border: none; padding: 0.6rem 1.4rem; }
    .btn-add { background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border: none; border-radius: 30px; padding: 0.6rem 1.4rem; font-weight: 500; font-size: 0.9rem; }
    .btn-add:hover { opacity: 0.9; color: white; }
    .table { margin: 0; }
    .table thead th { background: #f8f9fb; border-bottom: 2px solid #e9ecef; font-weight: 600; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; padding: 1rem 1.2rem; }
    .table tbody td { padding: 1rem 1.2rem; vertical-align: middle; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover { background: #fafbff; }
    .avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem; flex-shrink: 0; }
    .role-pill { display: inline-flex; align-items: center; gap: 5px; font-size: 0.78rem; font-weight: 600; padding: 4px 12px; border-radius: 20px; }
    .role-pill.superadmin { background: #fff8e1; color: #8a6000; border: 1px solid #f6c90e; }
    .role-pill.admin { background: #e8f0fe; color: #174ea6; border: 1px solid #aecbfa; }
    .btn-action { border: none; border-radius: 20px; padding: 0.35rem 0.9rem; font-size: 0.82rem; font-weight: 500; transition: all 0.15s; }
    .btn-edit { background: #e8f0fe; color: #174ea6; }
    .btn-edit:hover { background: #174ea6; color: white; }
    .btn-del { background: #fce8e6; color: #b31412; }
    .btn-del:hover { background: #b31412; color: white; }
    .you-badge { background: #e6f4ea; color: #137333; font-size: 0.72rem; font-weight: 600; padding: 2px 8px; border-radius: 12px; margin-left: 6px; }
    .empty-state { padding: 3rem; text-align: center; color: #adb5bd; }
    .empty-state i { font-size: 2.5rem; margin-bottom: 0.75rem; }
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
        <a href="{{ route('admin.superadmin.admins.create') }}" class="btn btn-add">
            <i class="fas fa-plus me-1"></i> Add Admin
        </a>
    </div>
</div>

<div class="card-table">
    <div class="card-body">
        <form method="GET" class="search-bar d-flex gap-2 mb-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email…" value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-search"><i class="fas fa-search me-1"></i> Search</button>
           
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $index => $admin)
                        <tr>
                            <td class="text-muted">{{ $admins->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                                    <span>
                                        {{ $admin->name }}
                                        @if($admin->id === auth()->id())
                                            <span class="you-badge">You</span>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $admin->email }}</td>
                            <td>
                                <span class="role-pill {{ $admin->role }}">
                                    @if($admin->role === 'superadmin')
                                        <i class="fas fa-star" style="font-size:0.7rem;"></i> Super Admin
                                    @else
                                        <i class="fas fa-user-cog" style="font-size:0.7rem;"></i> Admin
                                    @endif
                                </span>
                            </td>
                            <td class="text-muted">{{ $admin->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                @if($admin->id !== auth()->id())
                                    <a href="{{ route('admin.superadmin.admins.edit', $admin->id) }}" class="btn btn-action btn-edit me-1">
                                        <i class="fas fa-pen me-1"></i>Edit
                                    </a>
                                    <button type="button" class="btn btn-action btn-del"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="{{ $admin->id }}" data-name="{{ $admin->name }}">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                @else
                                    <span class="text-muted" style="font-size:0.82rem;">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-user-slash d-block"></i>
                                    No admin accounts found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admins->hasPages())
            <div class="mt-3 d-flex justify-content-end">{{ $admins->links() }}</div>
        @endif
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Delete Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete <strong id="deleteAdminName"></strong>? This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('deleteAdminName').textContent = btn.dataset.name;
    document.getElementById('deleteForm').action = '{{ url("admin/superadmin/admins") }}/' + btn.dataset.id;
});
</script>
@endpush