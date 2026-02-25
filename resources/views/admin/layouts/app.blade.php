<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Non-UniPay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card { border-radius: 10px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
         <!-- Sidebar -->
<div class="col-md-2 sidebar p-0">
    <div class="p-0 d-flex flex-column" style="height: 100vh;">
        <h4 class="mb-4 text-center p-3">Non-UniPay</h4>

        <div class="sidebar-container flex-grow-1">
            <ul class="nav flex-column m-0">
                <li class="nav-item mb-2 {{ request()->routeIs('admin.dashboard') ? 'active-row' : '' }}">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-dashboard me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-2 {{ request()->routeIs('admin.payments') ? 'active-row' : '' }}">
                    <a class="nav-link" href="{{ route('admin.payments') }}">
                        <i class="fas fa-money-bill me-2"></i> Payments
                    </a>
                </li>
                <li class="nav-item mb-2 {{ request()->routeIs('admin.students') ? 'active-row' : '' }}">
                    <a class="nav-link" href="{{ route('admin.students') }}">
                        <i class="fas fa-users me-2"></i> Students
                    </a>
                </li>
                <li class="nav-item mb-2 {{ request()->routeIs('admin.reports') ? 'active-row' : '' }}">
                    <a class="nav-link" href="{{ route('admin.reports') }}">
                        <i class="fas fa-chart-bar me-2"></i> Reports
                    </a>
                </li>
            </ul>
        </div>

        <hr class="text-white opacity-50 my-3">

        <button type="button" class="nav-link text-white bg-transparent border-0 w-100 text-start" data-bs-toggle="modal" data-bs-target="#logoutModal">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
    </div>
</div>

<!-- Main Content -->
<div class="col-md-10 p-4 main-content">
    @yield('content')
</div>

            
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to logout?
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

</body>


<style>
/* Sidebar */
.sidebar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

/* Sidebar container */
.sidebar-container {
    background: transparent;
    padding: 0;
}

/* Sidebar links */
.sidebar .nav-link {
    color: white;
    padding: 15px 20px;
    border-radius: 0;
    transition: all 0.3s;
    position: relative;
}

/* Hover effect: rounded left corners + slight highlight */
.sidebar .nav-link:hover {
    background: rgba(255, 255, 255, 0.15);
    border-top-left-radius: 20px;
    border-bottom-left-radius: 20px;
    left: 10px; 
}

/* Active row: extends white to main content + rounded left corners */
.sidebar .nav-item.active-row a {
    background: white;
    color: #764ba2;
    font-weight: bold;
    border-top-left-radius: 20px;
    border-bottom-left-radius: 20px;
    position: relative;
    left: 10px; 
    width: calc(100% + 50px);
     transition: all 0.4s ease;
    
}

/* Connect to main content */
.sidebar .nav-item.active-row a::after {
    content: "";
    position: absolute;
    top: 0;
    right: -100vw; /* extend to the right */
    width: 100vw;
    height: 100%;
    background: white;
    z-index: -1;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

/* Main content background */
.main-content {
    background: white;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}
</style>
</html>
