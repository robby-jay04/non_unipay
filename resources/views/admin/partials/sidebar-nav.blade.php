<ul class="nav flex-column">
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.payments') }}">
            <i class="fas fa-money-bill-wave"></i> Payments
            <span class="badge-notification payments-badge" style="display: none;"></span>
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.students') }}">
            <i class="fas fa-user-graduate"></i> Students
            <span class="badge-notification students-badge" style="display: none;"></span>
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.reports') }}">
            <i class="fas fa-chart-bar"></i> Reports
        </a>
    </li>

    <li class="nav-section-title">ACADEMIC</li>
    <li class="nav-item {{ request()->routeIs('admin.school-years*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.school-years.index') }}">
            <i class="fas fa-calendar-alt"></i> School Years
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.fees*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.fees.index') }}">
            <i class="fas fa-coins"></i> Fee Management
        </a>
    </li>

    @if(auth()->user()->role === 'superadmin')
        <li class="nav-section-title">ADMINISTRATION</li>
        <li class="nav-item superadmin-only {{ request()->routeIs('admin.superadmin.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.superadmin.admins.index') }}">
                <i class="fas fa-user-shield"></i> Manage Admins
            </a>
        </li>
    @endif
</ul>

<div class="d-md-none">
    <button type="button" class="logout-btn" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="fas fa-sign-out-alt"></i> Logout
    </button>
</div>