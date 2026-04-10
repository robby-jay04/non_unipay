<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Created</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admins as $index => $admin)
                <tr>
                    <td>{{ $admins->firstItem() + $index }}</td>
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
                    <td>{{ $admin->email }}</td>
                    <td>
                        <span class="role-pill {{ $admin->role }}">
                            @if($admin->role === 'superadmin')
                                <i class="fas fa-star" style="font-size:0.7rem;"></i> Super Admin
                            @else
                                <i class="fas fa-user-cog" style="font-size:0.7rem;"></i> Admin
                            @endif
                        </span>
                    </td>
                    <td>
                        @if($admin->id === auth()->id())
                            {{-- Can't toggle yourself --}}
                            <span class="status-badge active">
                                <i class="fas fa-circle" style="font-size:0.5rem;"></i> Active
                            </span>
                        @else
                            <span class="status-badge {{ $admin->is_active ? 'active' : 'inactive' }}">
                                <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                                {{ $admin->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        @endif
                    </td>
                    <td>{{ $admin->created_at->format('M d, Y') }}</td>
                    <td class="text-end">
                        @if($admin->id !== auth()->id())
                            <button type="button" class="btn btn-action btn-edit me-1"
                                    data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="{{ $admin->id }}"
                                    data-name="{{ $admin->name }}"
                                    data-email="{{ $admin->email }}"
                                    data-role="{{ $admin->role }}"
                                    data-is-active="{{ $admin->is_active ? '1' : '0' }}">
                                <i class="fas fa-pen me-1"></i>
                            </button>
                            <button type="button" class="btn btn-action btn-del"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                    data-id="{{ $admin->id }}"
                                    data-name="{{ $admin->name }}">
                                <i class="fas fa-trash me-1"></i>
                            </button>
                        @else
                            <span style="font-size:0.82rem; color: var(--text-muted);">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
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
    <div class="mt-3 d-flex justify-content-end">
        {{ $admins->appends(['search' => $search ?? ''])->links() }}
    </div>
@endif