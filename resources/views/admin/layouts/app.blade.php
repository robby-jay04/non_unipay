<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Non-UniPay Admin</title>

    {{-- ── Favicon ── --}}
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ── Global CSS Variables & Dark Mode Support ── --}}
    <style>
        :root {
            /* Light mode (default) */
            --bg-body: #f0f2f5;
            --bg-main: #ffffff;
            --bg-sidebar: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%);
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            --hover-bg: rgba(15, 60, 145, 0.04);
            --modal-header-bg: linear-gradient(135deg, #0f3c91, #1a4da8);
            --btn-primary: #0f3c91;
            --btn-primary-hover: #1a4da8;
            --input-bg: #f8fafc;
            --input-border: #e2e8f0;
            --table-header-bg: #f9fafb;
            --table-row-border: #f0f2f5;
            --topbar-bg: #ffffff;
        }

        body.dark {
            --bg-body: #0f172a;
            --bg-main: #1e293b;
            --bg-sidebar: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            --hover-bg: rgba(255, 255, 255, 0.05);
            --modal-header-bg: linear-gradient(135deg, #1e293b, #0f172a);
            --btn-primary: #3b82f6;
            --btn-primary-hover: #2563eb;
            --input-bg: #334155;
            --input-border: #475569;
            --table-header-bg: #1e293b;
            --table-row-border: #334155;
            --topbar-bg: #1e293b;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            transition: background-color 0.3s ease, color 0.2s ease;
        }

        /* ─── Enhanced Page Loading Overlay ─────────────────────────────────── */
        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(5, 15, 50, 0.9);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0s linear 0.3s;
        }
        #page-loader.visible {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.3s ease, visibility 0s linear 0s;
        }
        .loader-card {
            background: linear-gradient(135deg, #0f3c91, #1a4da8);
            border-radius: 32px;
            padding: 2.5rem 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            min-width: 260px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            transform: scale(0.95);
            transition: transform 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        #page-loader.visible .loader-card {
            transform: scale(1);
        }
        body.dark .loader-card {
            background: linear-gradient(135deg, #1e293b, #0f172a);
        }
        .loader-logo-ring {
            position: relative;
            width: 90px;
            height: 90px;
        }
        .loader-logo-ring img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: white;
            padding: 10px;
            object-fit: contain;
            display: block;
            box-shadow: 0 0 0 4px rgba(255,255,255,0.2);
            animation: pulse-logo 1.5s infinite ease-in-out;
        }
        @keyframes pulse-logo {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255,255,255,0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,255,255,0); }
        }
        .loader-spinner {
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #f4b400;
            border-right-color: #f4b400;
            border-bottom-color: rgba(244, 180, 0, 0.3);
            animation: loader-spin 0.9s linear infinite;
        }
        @keyframes loader-spin {
            to { transform: rotate(360deg); }
        }
        .loader-text {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
            background: rgba(0,0,0,0.2);
            padding: 4px 12px;
            border-radius: 40px;
        }
        .loader-subtext {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            margin: -0.5rem 0 0;
            font-weight: 500;
        }
        .loader-bar-track {
            width: 180px;
            height: 5px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 99px;
            overflow: hidden;
            margin-top: 0.25rem;
        }
        .loader-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #f4b400, #ffdd77);
            border-radius: 99px;
            width: 0%;
            animation: loader-bar 1.8s ease-in-out infinite alternate;
        }
        @keyframes loader-bar {
            0% { width: 5%; }
            100% { width: 95%; }
        }

        /* Rest of your existing styles (unchanged) */
        /* ─── Desktop Top Bar ───────────────────────────────────────── */
        .desktop-topbar {
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
            transition: background 0.3s ease;
        }
        .desktop-topbar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .desktop-topbar-logo img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            background: white;
            padding: 4px;
            border: 1px solid var(--border-color);
        }
        .desktop-topbar-title {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--text-primary);
        }
        .desktop-topbar-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .desktop-theme-toggle {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 30px;
            padding: 0.5rem 1rem;
            color: var(--text-primary);
            transition: all 0.2s;
        }
        .desktop-theme-toggle:hover {
            background: var(--hover-bg);
            transform: translateY(-1px);
        }

        /* ─── Sidebar & Navigation ───────────────────────────────────────── */
        .sidebar {
            background: var(--bg-sidebar);
            color: white;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            transition: background 0.3s ease;
        }
        .offcanvas.sidebar {
            width: 280px;
            background: var(--bg-sidebar);
        }
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
        body.dark .sidebar-nav .nav-item.active .nav-link {
            background: #3b82f6; color: white;
        }
        .nav-section-title {
            color: rgba(255,255,255,0.5); font-size: 0.75rem;
            text-transform: uppercase; letter-spacing: 1px;
            font-weight: 600; padding: 1rem 1.5rem 0.25rem;
        }
        .superadmin-link { color: rgba(246,201,14,0.9) !important; }
        .superadmin-link:hover { background: rgba(246,201,14,0.15) !important; color: #f6c90e !important; }
        .logout-btn {
            background: transparent; border: none; color: rgba(255,255,255,0.85);
            padding: 0.85rem 1.5rem; border-radius: 30px; margin: 1rem 0.5rem;
            width: calc(100% - 1rem); text-align: left;
            display: flex; align-items: center; gap: 12px; transition: all 0.2s;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.15); color: white; }

        /* Mobile navbar */
        .mobile-navbar {
            background: var(--bg-main);
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: background 0.3s ease;
        }
        .mobile-navbar-logo {
            width: 38px; height: 38px; object-fit: cover;
            border-radius: 50%; border: 2px solid #0f3c91; padding: 2px; background: white;
        }
        .mobile-navbar-title { font-weight: 700; color: var(--text-primary); font-size: 1rem; line-height: 1.2; }
        .mobile-navbar-subtitle { font-size: 0.7rem; color: var(--text-muted); line-height: 1; }
        .navbar-toggle {
            background: #0f3c91; border: none; color: white;
            font-size: 1.1rem; padding: 0.45rem 0.75rem; border-radius: 10px;
        }
        body.dark .navbar-toggle {
            background: #3b82f6;
        }
        .theme-toggle-mobile {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 30px;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            color: var(--text-primary);
        }

        /* Main content adjustment */
        .main-content {
            background: var(--bg-body);
            padding: 2rem;
            min-height: calc(100vh - 65px);
            transition: background 0.3s ease;
        }
        @media (max-width: 767.98px) {
            .main-content { padding: 1rem; min-height: auto; }
        }
        .content-card {
            background: var(--bg-main);
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }

        /* Cards, Tables, Modals (themed via variables) */
        .card, .modal-content {
            background: var(--bg-main);
            border: none;
            box-shadow: var(--card-shadow);
            transition: background 0.3s ease;
        }
        .modal-header {
            background: var(--modal-header-bg);
            color: white;
            border-radius: 20px 20px 0 0;
        }
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        .modal-footer {
            border-top-color: var(--border-color);
        }
        .table {
            color: var(--text-primary);
        }
        .table td {
            border-bottom-color: var(--table-row-border);
            color: var(--text-secondary);
        }
        .table th {
            background-color: var(--table-header-bg);
            color: var(--text-primary);
            border-bottom-color: var(--border-color);
        }
        .form-control, .form-select {
            background-color: var(--input-bg);
            border-color: var(--input-border);
            color: var(--text-primary);
        }
        .form-control:focus, .form-select:focus {
            border-color: #0f3c91;
            box-shadow: 0 0 0 3px rgba(15,60,145,0.1);
            background-color: var(--input-bg);
        }
        .btn-primary {
            background: var(--btn-primary);
            border: none;
        }
        .btn-primary:hover {
            background: var(--btn-primary-hover);
        }
        .btn-outline-secondary {
            border-color: var(--border-color);
            color: var(--text-secondary);
        }
        .btn-outline-secondary:hover {
            background: var(--hover-bg);
            border-color: var(--text-muted);
        }
        .alert-light {
            background: var(--bg-main);
            color: var(--text-primary);
            border-left-color: #28a745;
        }
        .text-muted {
            color: var(--text-muted) !important;
        }
        .bg-light {
            background-color: var(--input-bg) !important;
        }
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

    {{-- ── Enhanced Page Loading Overlay ── --}}
    <div id="page-loader" role="status" aria-label="Loading page">
        <div class="loader-card">
            <div class="loader-logo-ring">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay">
                <div class="loader-spinner"></div>
            </div>
            <p class="loader-text">Non-UniPay</p>
            <p class="loader-subtext">Loading your dashboard</p>
            <div class="loader-bar-track">
                <div class="loader-bar-fill"></div>
            </div>
        </div>
    </div>

    {{-- ── Mobile Navbar (with dark mode toggle) ── --}}
    <nav class="mobile-navbar d-md-none">
        <div class="d-flex align-items-center justify-content-between">
            <button class="navbar-toggle" type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#sidebarOffcanvas"
                aria-controls="sidebarOffcanvas">
                <i class="fas fa-bars"></i>
            </button>
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay Logo" class="mobile-navbar-logo">
                <div>
                    <div class="mobile-navbar-title">Non-UniPay</div>
                    <div class="mobile-navbar-subtitle">Admin Panel</div>
                </div>
            </div>
            <button class="theme-toggle-mobile" id="mobileThemeToggle">
                <i class="fas fa-moon"></i>
            </button>
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
                        {{-- ✅ NEW: Audit Logs (Super Admin only) --}}
                                <li class="nav-item {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}">
                                    <a class="nav-link superadmin-link" href="{{ route('admin.audit-logs.index') }}">
                                        <i class="fas fa-history"></i> Audit Logs
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
                                 </li>
                        {{-- ✅ NEW: Audit Logs (Super Admin only) --}}
                                <li class="nav-item {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}">
                                    <a class="nav-link superadmin-link" href="{{ route('admin.audit-logs.index') }}">
                                        <i class="fas fa-history"></i> Audit Logs
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

            <!-- Page Content with Desktop Top Bar -->
            <div class="col">
                <div class="desktop-topbar d-none d-md-flex">
                    <div class="desktop-topbar-logo">
                        <img src="{{ asset('logo.png') }}" alt="Logo">
                        <div>
                            <div class="desktop-topbar-title">Non-UniPay Admin</div>
                            <div class="desktop-topbar-subtitle">Control Panel</div>
                        </div>
                    </div>
                    <button class="desktop-theme-toggle" id="desktopThemeToggle">
                        <i class="fas fa-moon"></i> Dark Mode
                    </button>
                </div>
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
                <form action="{{ route('logout') }}" method="POST" class="d-inline requires-loader">
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
    // ── Dark Mode Toggle & Persistence ─────────────────────────────────────
    (function() {
        const darkModeKey = 'admin_dark_mode';

        function setDarkMode(isDark) {
            if (isDark) {
                document.body.classList.add('dark');
                localStorage.setItem(darkModeKey, 'true');
            } else {
                document.body.classList.remove('dark');
                localStorage.setItem(darkModeKey, 'false');
            }
            const desktopToggle = document.getElementById('desktopThemeToggle');
            const mobileToggle = document.getElementById('mobileThemeToggle');
            if (desktopToggle) {
                desktopToggle.innerHTML = isDark ? '<i class="fas fa-sun"></i> ' : '<i class="fas fa-moon"></i> ';
            }
            if (mobileToggle) {
                mobileToggle.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
            }
        }

        function initDarkMode() {
            const stored = localStorage.getItem(darkModeKey);
            if (stored !== null) {
                setDarkMode(stored === 'true');
            } else {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                setDarkMode(prefersDark);
            }
        }

        function bindToggleButtons() {
            const desktopBtn = document.getElementById('desktopThemeToggle');
            const mobileBtn = document.getElementById('mobileThemeToggle');
            if (desktopBtn) {
                desktopBtn.addEventListener('click', () => setDarkMode(!document.body.classList.contains('dark')));
            }
            if (mobileBtn) {
                mobileBtn.addEventListener('click', () => setDarkMode(!document.body.classList.contains('dark')));
            }
        }

        initDarkMode();
        bindToggleButtons();

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (localStorage.getItem(darkModeKey) === null) setDarkMode(e.matches);
        });
    })();

    // ── Enhanced Page Loading Overlay ─────────────────────────────────────
    // ── Enhanced Page Loading Overlay with dynamic messages ─────────────────────
(function () {
    const loader = document.getElementById('page-loader');
    const loaderText = loader?.querySelector('.loader-text');
    const loaderSubtext = loader?.querySelector('.loader-subtext');
    let activeRequests = 0;
    let hideTimeout = null;

    function showLoader(customMessage, customSubtext) {
        if (hideTimeout) clearTimeout(hideTimeout);
        activeRequests++;
        if (loaderText && customMessage) loaderText.innerText = customMessage;
        if (loaderSubtext && customSubtext) loaderSubtext.innerText = customSubtext;
        loader.classList.add('visible');
    }

    function hideLoader() {
        activeRequests--;
        if (activeRequests <= 0) {
            hideTimeout = setTimeout(() => {
                loader.classList.remove('visible');
                // Reset texts after hiding (optional)
                if (loaderText) loaderText.innerText = 'Non-UniPay';
                if (loaderSubtext) loaderSubtext.innerText = 'Loading your dashboard';
                hideTimeout = null;
            }, 150);
        }
    }

    // Intercept all link clicks (same-origin, non-special)
    document.addEventListener('click', function (e) {
        const target = e.target.closest('a');
        if (!target) return;
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        if (target.hasAttribute('download') || target.getAttribute('target') === '_blank') return;
        if (target.hasAttribute('data-bs-toggle')) return;
        if (target.closest('.pagination')) return;
        if (target.closest('#statusFilter') || target.closest('#searchBtn') || target.closest('#searchInput')) return;

        const href = target.getAttribute('href') || '';
        if (!href || href.startsWith('#') || href.startsWith('javascript')) return;

        try {
            const url = new URL(href, window.location.href);
            if (url.origin !== window.location.origin) return;
        } catch (err) {
            return;
        }

        showLoader('Loading...', 'Please wait');
    });

    // Intercept form submissions
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('form');
        if (!form) return;
        if (form.classList.contains('requires-loader')) {
            // Check if it's a logout form
            if (form.action && form.action.includes('/logout')) {
                showLoader('Logging out...', 'Redirecting to login');
            } else {
                showLoader('Processing...', 'Please wait');
            }
        }
    });

    // Hide loader when page is fully loaded
    window.addEventListener('load', hideLoader);
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) hideLoader();
    });

    // Safety: if loader stays visible for more than 8 seconds, hide it
    setInterval(() => {
        if (loader.classList.contains('visible') && activeRequests === 0) {
            loader.classList.remove('visible');
        }
    }, 8000);
})();

    // ── Notification badge polling ─────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        function fetchNotificationCounts() {
           fetch('/admin/api/pending-payments-count', {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
})
    .then(r => r.json())
    .then(data => {

                    document.querySelectorAll('#payments-badge, #payments-badge-desktop').forEach(b => {
                        b.style.display = data.count > 0 ? 'inline-block' : 'none';
                    });
                })
                .catch(err => console.error('Payments count error:', err));

            fetch('/admin/api/new-students-count', {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
})
    .then(r => r.json())
    .then(data => {
                    document.querySelectorAll('#students-badge, #students-badge-desktop').forEach(b => {
                        b.style.display = data.count > 0 ? 'inline-block' : 'none';
                    });
                })
                .catch(err => console.error('Students count error:', err));
        }
        fetchNotificationCounts();
        setInterval(fetchNotificationCounts, 5000);
    });
    </script>
</body>
</html>