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

    {{-- ── Loading Screen Styles ── (integrated) --}}
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* ─── Page Loading Overlay ─────────────────────────────────────────── */
        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(5, 15, 50, 0.72);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        #page-loader.visible {
            opacity: 1;
            pointer-events: all;
        }
        .loader-card {
            background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%);
            border-radius: 28px;
            padding: 2.5rem 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.25rem;
            min-width: 220px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4);
        }
        .loader-logo-ring {
            position: relative;
            width: 80px;
            height: 80px;
        }
        .loader-logo-ring img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            padding: 8px;
            object-fit: contain;
            display: block;
        }
        .loader-spinner {
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #f4b400;
            border-right-color: rgba(244, 180, 0, 0.3);
            animation: loader-spin 0.85s linear infinite;
        }
        @keyframes loader-spin {
            to { transform: rotate(360deg); }
        }
        .loader-text {
            color: white;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.3px;
            margin: 0;
        }
        .loader-subtext {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            margin: -0.75rem 0 0;
        }
        .loader-bar-track {
            width: 140px;
            height: 4px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 99px;
            overflow: hidden;
        }
        .loader-bar-fill {
            height: 100%;
            background: #f4b400;
            border-radius: 99px;
            animation: loader-bar 1.1s ease-in-out infinite alternate;
        }
        @keyframes loader-bar {
            from { width: 15%; margin-left: 0; }
            to   { width: 70%; margin-left: 30%; }
        }
        /* ─── end loader ───────────────────────────────────────────────────── */

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

        /* ── Mobile navbar ── */
        .mobile-navbar {
            background: white;
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .mobile-navbar-logo {
            width: 38px; height: 38px; object-fit: cover;
            border-radius: 50%; border: 2px solid #0f3c91; padding: 2px; background: white;
        }
        .mobile-navbar-title { font-weight: 700; color: #0f3c91; font-size: 1rem; line-height: 1.2; }
        .mobile-navbar-subtitle { font-size: 0.7rem; color: #94a3b8; line-height: 1; }
        .navbar-toggle {
            background: #0f3c91; border: none; color: white;
            font-size: 1.1rem; padding: 0.45rem 0.75rem; border-radius: 10px;
        }

        @media (max-width: 767.98px) {
            .main-content { border-radius: 0; padding: 1rem; }
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

    {{-- ── Page Loading Overlay ── --}}
    <div id="page-loader" role="status" aria-label="Loading page">
        <div class="loader-card">
            <div class="loader-logo-ring">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay">
                <div class="loader-spinner"></div>
            </div>
            <p class="loader-text">Loading…</p>
            <p class="loader-subtext">Please wait</p>
            <div class="loader-bar-track">
                <div class="loader-bar-fill"></div>
            </div>
        </div>
    </div>

    {{-- ── Mobile Navbar ── --}}
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
            <div style="width: 44px;"></div>
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
    // ── Page loading overlay ───────────────────────────────────────────────────
    (function () {
        var loader = document.getElementById('page-loader');

        function showLoader() {
            loader.classList.add('visible');
        }

        function hideLoader() {
            loader.classList.remove('visible');
        }

        // Show on every same-origin navigation link click,
        // but skip: anchor-only links (#), new-tab links, download links,
        // logout form submit, and Bootstrap modal/offcanvas toggles.
        document.addEventListener('click', function (e) {
            var target = e.target.closest('a');
            if (!target) return;

            // Skip if modifier key held (open in new tab etc.)
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

            // Skip download / target="_blank"
            if (target.hasAttribute('download') || target.getAttribute('target') === '_blank') return;

            // Skip Bootstrap data-bs-toggle (modals, offcanvas, dropdowns)
            if (target.hasAttribute('data-bs-toggle')) return;

            // Skip pagination links (handled by AJAX, not full page navigation)
if (target.closest('.pagination')) return;

 if (target.closest('#statusFilter') || 
            target.closest('#searchBtn') || 
            target.closest('#searchInput')) {
            return;
        }

            // Skip pure hash links
            var href = target.getAttribute('href') || '';
            if (!href || href.startsWith('#') || href.startsWith('javascript')) return;

            // Skip external links
            try {
                var url = new URL(href, window.location.href);
                if (url.origin !== window.location.origin) return;
            } catch (err) {
                return;
            }

            showLoader();
        });

        // Also show when a form is submitted (logout form only)
        var logoutForm = document.querySelector('#logoutModal form');
        if (logoutForm) {
            logoutForm.addEventListener('submit', showLoader);
        }

        // Hide the loader once the new page has fully loaded.
        window.addEventListener('pageshow', hideLoader);

        // Safety net: hide after 8 s in case something goes wrong.
        window.addEventListener('beforeunload', function () {
            setTimeout(hideLoader, 8000);
        });
    })();

    // ── Notification badge polling ─────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        function fetchNotificationCounts() {
            fetch('/admin/api/pending-payments-count')
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    document.querySelectorAll('#payments-badge, #payments-badge-desktop')
                        .forEach(function (b) {
                            b.style.display = data.count > 0 ? 'inline-block' : 'none';
                        });
                })
                .catch(function (err) { console.error('Payments count error:', err); });

            fetch('/admin/api/new-students-count')
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    document.querySelectorAll('#students-badge, #students-badge-desktop')
                        .forEach(function (b) {
                            b.style.display = data.count > 0 ? 'inline-block' : 'none';
                        });
                })
                .catch(function (err) { console.error('Students count error:', err); });
        }

        fetchNotificationCounts();
        setInterval(fetchNotificationCounts, 5000);
    });
    </script>
</body>
</html>