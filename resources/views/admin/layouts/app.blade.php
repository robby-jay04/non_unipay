<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Non-UniPay Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .sidebar {
            background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%);
            color: white;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }
        .offcanvas.sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%);
        }
        .offcanvas.sidebar .offcanvas-header {
            border-bottom: 1px solid rgba(255,255,255,0.15);
            padding: 1.5rem 1rem;
        }
        .offcanvas.sidebar .offcanvas-header .btn-close {
            filter: brightness(0) invert(1);
        }
        .sidebar-header { text-align: center; }
        .sidebar-header img {
            width: 60px; height: 60px; object-fit: contain;
            border-radius: 30px; background: white; padding: 5px;
        }
        .sidebar-header h4 { font-weight: 700; color: white; font-size: 1.5rem; margin-bottom: 0; }
        .sidebar-header small { color: rgba(255,255,255,0.7); font-size: 0.85rem; }
        .role-badge {
            display: inline-block; font-size: 0.7rem; font-weight: 600;
            letter-spacing: 0.5px; padding: 2px 10px; border-radius: 20px; margin-top: 4px;
        }
        .role-badge.superadmin { background: linear-gradient(135deg, #f6c90e, #f39c12); color: #5a3e00; }
        .role-badge.admin { background: rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); }
        .sidebar-nav { padding: 1rem 0; }
        .sidebar-nav .nav-item { margin: 0.25rem 0.5rem; }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.85); padding: 0.85rem 1.5rem;
            border-radius: 30px; transition: all 0.2s ease;
            font-weight: 500; display: flex; align-items: center; gap: 12px;
        }
        .sidebar-nav .nav-link i { font-size: 1.2rem; width: 24px; text-align: center; }
        .sidebar-nav .nav-link:hover {
            color: white; background: rgba(255,255,255,0.15); transform: translateX(4px);
        }
        .sidebar-nav .nav-item.active .nav-link {
            background: white; color: #0f3c91; font-weight: 600;
            box-shadow: -4px 0 10px rgba(0,0,0,0.05);
        }
        .nav-section-title {
            color: rgba(255,255,255,0.5); font-size: 0.75rem;
            text-transform: uppercase; letter-spacing: 1px;
            font-weight: 600; padding: 1rem 1.5rem 0.25rem;
        }
        .superadmin-link { color: rgba(246,201,14,0.9) !important; }
        .superadmin-link:hover { background: rgba(246,201,14,0.15) !important; color: #f6c90e !important; }
        .nav-item.active .superadmin-link { background: #f6c90e !important; color: #5a3e00 !important; }
        .logout-btn {
            background: transparent; border: none; color: rgba(255,255,255,0.85);
            padding: 0.85rem 1.5rem; border-radius: 30px; margin: 1rem 0.5rem;
            width: calc(100% - 1rem); text-align: left;
            display: flex; align-items: center; gap: 12px; transition: all 0.2s;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.15); color: white; }
        .main-content {
            background: #f0f2f5; padding: 2rem;
            min-height: 100vh; border-radius: 30px 0 0 30px;
        }
        .content-card {
            background: white; border-radius: 24px;
            padding: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        @media (max-width: 767.98px) {
            .main-content { border-radius: 0; padding: 1rem; }
        }
        .navbar-toggle {
            background: #0f3c91; border: none; color: white;
            font-size: 1.5rem; padding: 0.5rem 1rem; border-radius: 10px; margin-bottom: 1rem;
        }
        .modal-content { border: none; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .modal-header {
            background: linear-gradient(135deg, #0f3c91, #1a4da8);
            color: white; border-radius: 20px 20px 0 0; padding: 1.25rem 1.5rem;
        }
        .modal-header .btn-close { filter: brightness(0) invert(1); }
        .modal-footer { border-top: 1px solid #e9ecef; padding: 1.25rem; }
        .btn-primary { background: #0f3c91; border: none; padding: 0.6rem 1.5rem; border-radius: 30px; font-weight: 500; }
        .btn-primary:hover { background: #1a4da8; }
        .btn-danger { background: #dc3545; border: none; padding: 0.6rem 1.5rem; border-radius: 30px; font-weight: 500; }
        .btn-secondary { background: #e9ecef; border: none; color: #495057; padding: 0.6rem 1.5rem; border-radius: 30px; font-weight: 500; }
        .badge-notification {
            display: inline-block; width: 8px; height: 8px;
            background-color: #ff3b30; border-radius: 50%; margin-left: 8px;
            box-shadow: 0 0 0 2px rgba(255,59,48,0.2); animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%   { box-shadow: 0 0 0 0 rgba(255,59,48,0.4); }
            70%  { box-shadow: 0 0 0 6px rgba(255,59,48,0); }
            100% { box-shadow: 0 0 0 0 rgba(255,59,48,0); }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Mobile navbar -->
    <nav class="d-md-none p-3" style="background: white;">
        <div class="d-flex align-items-center justify-content-between">
            <button class="navbar-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                <i class="fas fa-bars"></i>
            </button>
            <span class="fw-bold" style="color: #0f3c91;">Non-UniPay Admin</span>
            <div style="width: 40px;"></div>
        </div>
    </nav>

    <!-- Mobile Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarLabel">
        <div class="offcanvas-header">
            <div class="sidebar-header w-100">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay Logo">
                <h4>Non-UniPay</h4>
                <small>Admin Panel</small><br>
                <span class="role-badge {{ auth()->user()->role }}">
                    {{ auth()->user()->role === 'superadmin' ? '★ Super Admin' : 'Admin' }}
                </span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-chart-pie"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.payments') }}">
                            <i class="fas fa-money-bill-wave"></i> Payments
                            <span class="badge-notification" id="payments-badge" style="display: none;"></span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.students') }}">
                            <i class="fas fa-user-graduate"></i> Students
                            <span class="badge-notification" id="students-badge" style="display: none;"></span>
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
                        <li class="nav-item {{ request()->routeIs('admin.superadmin.*') ? 'active' : '' }}">
                            <a class="nav-link superadmin-link" href="{{ route('admin.superadmin.admins.index') }}">
                                <i class="fas fa-user-shield"></i> Manage Admins
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <button type="button" class="logout-btn" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </div>

    <!-- Main layout -->
    <div class="container-fluid p-0">
        <div class="row g-0">

            <!-- Desktop Sidebar -->
            <div class="col-auto d-none d-md-block" style="width: 280px;">
                <div class="sidebar d-flex flex-column vh-100 sticky-top">
                    <div class="sidebar-header p-4">
                        <img src="{{ asset('logo.png') }}" alt="Non-UniPay Logo">
                        <h4>Non-UniPay</h4>
                        <small>Admin Panel</small><br>
                        <span class="role-badge {{ auth()->user()->role }}">
                            {{ auth()->user()->role === 'superadmin' ? '★ Super Admin' : 'Admin' }}
                        </span>
                    </div>
                    <div class="sidebar-nav flex-grow-1">
                        <ul class="nav flex-column">
                            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-chart-pie"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.payments') }}">
                                    <i class="fas fa-money-bill-wave"></i> Payments
                                    <span class="badge-notification" id="payments-badge-desktop" style="display: none;"></span>
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.students') }}">
                                    <i class="fas fa-user-graduate"></i> Students
                                    <span class="badge-notification" id="students-badge-desktop" style="display: none;"></span>
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
                                <li class="nav-item {{ request()->routeIs('admin.superadmin.*') ? 'active' : '' }}">
                                    <a class="nav-link superadmin-link" href="{{ route('admin.superadmin.admins.index') }}">
                                        <i class="fas fa-user-shield"></i> Manage Admins
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <button type="button" class="logout-btn" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>

            <!-- Page Content -->
            <div class="col">
                <div class="main-content">
                    @yield('content')
                </div>
            </div>

        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to logout?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function fetchNotificationCounts() {
            fetch('/admin/api/pending-payments-count')
                .then(response => response.json())
                .then(data => {
                    const badges = document.querySelectorAll('#payments-badge, #payments-badge-desktop');
                    badges.forEach(badge => {
                        badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                    });
                }).catch(err => console.error('Error fetching payments count:', err));

            fetch('/admin/api/new-students-count')
                .then(response => response.json())
                .then(data => {
                    const badges = document.querySelectorAll('#students-badge, #students-badge-desktop');
                    badges.forEach(badge => {
                        badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                    });
                }).catch(err => console.error('Error fetching students count:', err));
        }

        fetchNotificationCounts();
        setInterval(fetchNotificationCounts, 5000);
    });
    </script>
</body>
</html>