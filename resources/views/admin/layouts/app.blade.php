<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Non-UniPay Admin</title>
    <!-- Bootstrap 5 & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%);
            color: white;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            text-align: center;
        }

        .sidebar-header h4 {
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
            color: white;
            font-size: 1.5rem;
        }

        .sidebar-header small {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
        }

        /* Navigation */
        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-nav .nav-item {
            margin: 0.25rem 0.5rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.85);
            padding: 0.85rem 1.5rem;
            border-radius: 30px 0 0 30px;
            transition: all 0.2s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        .sidebar-nav .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.15);
            transform: translateX(4px);
        }

        .sidebar-nav .nav-item.active .nav-link {
            background: white;
            color: #0f3c91;
            font-weight: 600;
            box-shadow: -4px 0 10px rgba(0,0,0,0.05);
            position: relative;
        }

        /* Connector for active item (subtle) */
        .sidebar-nav .nav-item.active .nav-link::after {
            content: '';
            position: absolute;
            right: -8px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 8px solid transparent;
            border-bottom: 8px solid transparent;
            border-left: 8px solid white;
            filter: drop-shadow(2px 0 2px rgba(0,0,0,0.05));
        }

        /* Section headers */
        .nav-section-title {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            padding: 1rem 1.5rem 0.25rem;
        }

        /* Logout button */
        .logout-btn {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.85);
            padding: 0.85rem 1.5rem;
            border-radius: 30px;
            margin: 1rem 0.5rem;
            width: calc(100% - 1rem);
            text-align: left;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.15);
            color: white;
        }

        /* Main content */
        .main-content {
            background: #f0f2f5;
            padding: 2rem;
            border-radius: 30px 0 0 30px;
            min-height: 100vh;
            box-shadow: inset 1px 0 0 rgba(0,0,0,0.05);
        }

        /* Content card (optional) */
        .content-card {
            background: white;
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        /* Modal styling */
        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #0f3c91, #1a4da8);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.25rem 1.5rem;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1.25rem;
        }

        .btn-primary {
            background: #0f3c91;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 30px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background: #1a4da8;
        }

        .btn-danger {
            background: #dc3545;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 30px;
            font-weight: 500;
        }

        .btn-secondary {
            background: #e9ecef;
            border: none;
            color: #495057;
            padding: 0.6rem 1.5rem;
            border-radius: 30px;
            font-weight: 500;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-auto" style="width: 280px;">
                <div class="sidebar d-flex flex-column">
                    <div class="sidebar-header">
                        <h4>Non-UniPay</h4>
                        <small>Admin Panel</small>
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
                                </a>
                            </li>
                            <li class="nav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.students') }}">
                                    <i class="fas fa-user-graduate"></i> Students
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
                        </ul>
                    </div>

                    <!-- Logout Button -->
                    <button type="button" class="logout-btn" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </div>

            <!-- Main Content -->
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
</body>
</html>