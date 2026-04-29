<div class="profile-dropdown-header">
    <div class="profile-dropdown-avatar">
        @if(auth()->user()->profile_picture)
            <img src="{{ auth()->user()->profile_picture }}" alt="Avatar">
        @else
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        @endif
    </div>
    <div class="profile-dropdown-info">
        <div class="profile-dropdown-name">{{ auth()->user()->name }}</div>
        <div class="profile-dropdown-role">{{ auth()->user()->email }}</div>
        <span class="role-badge {{ auth()->user()->role }}" style="margin-top:4px;display:inline-block;">
            {{ auth()->user()->role === 'superadmin' ? '★ Super Admin' : 'Admin' }}
        </span>
    </div>
</div>

<div class="profile-dropdown-body">
    <button class="profile-dropdown-item"
        data-bs-toggle="modal" data-bs-target="#editProfileModal">
        <i class="fas fa-user-edit"></i> Edit profile
    </button>

    {{-- Dark mode toggle row with switch and dynamic label --}}
    <div class="profile-dropdown-item" style="cursor:default; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:10px;">
            <i class="fas fa-moon" style="width:18px; text-align:center; font-size:0.9rem; color:var(--text-muted);"></i>
            <span class="dark-mode-label" style="font-size:0.85rem; font-weight:500; color:var(--text-primary);">Dark mode</span>
        </div>
        <label class="dm-switch">
            <input type="checkbox" class="dark-mode-switch">
            <span class="dm-slider"></span>
        </label>
    </div>

    <div class="profile-dropdown-divider"></div>
    <button class="profile-dropdown-item danger"
        data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="fas fa-right-from-bracket"></i> Logout
    </button>
</div>

<style>
.dm-switch {
    position: relative;
    display: inline-block;
    width: 36px;
    height: 20px;
    flex-shrink: 0;
    cursor: pointer;
}
.dm-switch input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}
.dm-slider {
    position: absolute;
    inset: 0;
    background: var(--border-color);
    border-radius: 99px;
    transition: background 0.25s ease;
}
.dm-slider::before {
    content: '';
    position: absolute;
    width: 14px;
    height: 14px;
    left: 3px;
    top: 3px;
    background: white;
    border-radius: 50%;
    transition: transform 0.25s ease, background 0.25s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
.dm-switch input:checked + .dm-slider {
    background: var(--btn-primary);
}
.dm-switch input:checked + .dm-slider::before {
    transform: translateX(16px);
}
</style>