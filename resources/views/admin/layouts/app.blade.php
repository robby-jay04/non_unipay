<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Non-UniPay Admin</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --bg-body: #f0f2f5;
            --bg-main: #ffffff;
            --bg-sidebar: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%);
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.05);
            --hover-bg: rgba(15,60,145,0.04);
            --modal-header-bg: linear-gradient(135deg, #0f3c91, #1a4da8);
            --btn-primary: #0f3c91;
            --btn-primary-hover: #1a4da8;
            --input-bg: #f8fafc;
            --input-border: #e2e8f0;
            --table-header-bg: #f9fafb;
            --table-row-border: #f0f2f5;
            --topbar-bg: #ffffff;
            --sidebar-width: 280px;
        }

        body.dark {
            --bg-body: #0f172a;
            --bg-main: #1e293b;
            --bg-sidebar: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --card-shadow: 0 10px 30px rgba(0,0,0,0.3);
            --hover-bg: rgba(255,255,255,0.05);
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
            margin: 0;
            padding: 0;
        }

        /* ── Page Loader ── */
        #page-loader {
            position: fixed; inset: 0; z-index: 99999;
            background: rgba(5,15,50,0.9); backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; visibility: hidden;
            transition: opacity 0.3s ease, visibility 0s linear 0.3s;
        }
        #page-loader.visible {
            opacity: 1; visibility: visible;
            transition: opacity 0.3s ease, visibility 0s linear 0s;
        }
        .loader-card {
            background: linear-gradient(135deg, #0f3c91, #1a4da8);
            border-radius: 32px; padding: 2.5rem 3rem;
            display: flex; flex-direction: column; align-items: center; gap: 1.5rem;
            min-width: 260px; box-shadow: 0 30px 60px rgba(0,0,0,0.4);
            transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.2,0.9,0.4,1.1);
        }
        #page-loader.visible .loader-card { transform: scale(1); }
        body.dark .loader-card { background: linear-gradient(135deg, #1e293b, #0f172a); }
        .loader-logo-ring { position: relative; width: 90px; height: 90px; }
        .loader-logo-ring img {
            width: 90px; height: 90px; border-radius: 50%; background: white;
            padding: 10px; object-fit: contain; display: block;
            box-shadow: 0 0 0 4px rgba(255,255,255,0.2);
            animation: pulse-logo 1.5s infinite ease-in-out;
        }
        @keyframes pulse-logo {
            0%   { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
            70%  { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(255,255,255,0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,255,255,0); }
        }
        .loader-spinner {
            position: absolute; inset: -6px; border-radius: 50%;
            border: 3px solid transparent; border-top-color: #f4b400;
            border-right-color: #f4b400; border-bottom-color: rgba(244,180,0,0.3);
            animation: loader-spin 0.9s linear infinite;
        }
        @keyframes loader-spin { to { transform: rotate(360deg); } }
        .loader-text {
            color: white; font-size: 1.2rem; font-weight: 700;
            letter-spacing: 0.5px; margin: 0;
            background: rgba(0,0,0,0.2); padding: 4px 12px; border-radius: 40px;
        }
        .loader-subtext { color: rgba(255,255,255,0.7); font-size: 0.8rem; margin: -0.5rem 0 0; font-weight: 500; }
        .loader-bar-track {
            width: 180px; height: 5px; background: rgba(255,255,255,0.2);
            border-radius: 99px; overflow: hidden; margin-top: 0.25rem;
        }
        .loader-bar-fill {
            height: 100%; background: linear-gradient(90deg, #f4b400, #ffdd77);
            border-radius: 99px; width: 0%;
            animation: loader-bar 1.8s ease-in-out infinite alternate;
        }
        @keyframes loader-bar { 0% { width: 5%; } 100% { width: 95%; } }

        /* ── Desktop Sidebar ── */
        .desktop-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-sidebar);
            color: white;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
            z-index: 200;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .desktop-sidebar.collapsed {
            transform: translateX(calc(-1 * var(--sidebar-width)));
        }

        /* ── Desktop Top Bar ── */
        .desktop-topbar {
            position: fixed;
            top: 0; right: 0; left: var(--sidebar-width);
            height: 64px;
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            transition: left 0.3s cubic-bezier(0.4,0,0.2,1), background 0.3s ease;
            z-index: 150;
        }
        .desktop-sidebar.collapsed ~ .desktop-topbar { left: 0; }

        /* ── Main Content ── */
        .main-content {
            background: var(--bg-body);
            transition: background 0.3s ease;
            min-height: 100vh;
        }
        @media (min-width: 768px) {
            .main-content {
                margin-left: var(--sidebar-width);
                padding-top: 64px;
                transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1);
            }
            .desktop-sidebar.collapsed ~ .main-content { margin-left: 0; }
        }
        @media (max-width: 767.98px) {
            .desktop-sidebar, .desktop-topbar { display: none; }
            .main-content { padding: 1rem; min-height: calc(100vh - 64px); }
        }

        /* Sidebar internal styles */
        .sidebar-header {
            padding: 1.5rem 1.25rem 1rem;
            flex-shrink: 0;
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar-header img {
            width: 52px; height: 52px; object-fit: contain;
            border-radius: 26px; background: white; padding: 5px;
            display: block; margin-bottom: 0.75rem;
        }
        .sidebar-header h4 { font-weight: 700; color: white; font-size: 1.3rem; margin-bottom: 2px; }
        .sidebar-header small { color: rgba(255,255,255,0.7); font-size: 0.82rem; }
        .role-badge {
            display: inline-block; font-size: 0.7rem; font-weight: 600;
            letter-spacing: 0.5px; padding: 2px 10px; border-radius: 20px; margin-top: 6px;
        }
        .role-badge.superadmin { background: linear-gradient(135deg, #f6c90e, #f39c12); color: #5a3e00; }
        .role-badge.admin { background: rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto; overflow-x: hidden;
            padding: 0 0.5rem;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 99px; }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.85); padding: 0.75rem 1rem;
            border-radius: 12px; transition: all 0.2s ease;
            font-weight: 500; display: flex; align-items: center; gap: 12px;
            white-space: nowrap; overflow: hidden;
        }
        .sidebar-nav .nav-link i { font-size: 1.1rem; width: 22px; text-align: center; flex-shrink: 0; }
        .sidebar-nav .nav-link:hover { color: white; background: rgba(255,255,255,0.15); transform: translateX(4px); }
        .sidebar-nav .nav-item.active .nav-link { background: white; color: #0f3c91; font-weight: 600; }
        body.dark .sidebar-nav .nav-item.active .nav-link { background: #3b82f6; color: white; }
        .nav-section-title {
            color: rgba(255,255,255,0.45); font-size: 0.7rem;
            text-transform: uppercase; letter-spacing: 1.5px; font-weight: 700;
            padding: 1rem 1rem 0.35rem; white-space: nowrap; overflow: hidden;
        }
        .superadmin-link { color: rgba(246,201,14,0.9) !important; }
        .superadmin-link:hover { background: rgba(246,201,14,0.15) !important; color: #f6c90e !important; }

        /* Topbar */
        .topbar-left { display: flex; align-items: center; gap: 0.75rem; }
        .sidebar-toggle-btn {
            width: 38px; height: 38px; border-radius: 10px;
            border: 1px solid var(--border-color);
            background: var(--input-bg); color: var(--text-primary);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; flex-shrink: 0;
        }
        .sidebar-toggle-btn:hover { background: var(--btn-primary); color: white; border-color: var(--btn-primary); transform: scale(1.05); }
        .sidebar-toggle-btn i { font-size: 0.9rem; transition: transform 0.3s ease; }
        .sidebar-toggle-btn.collapsed i { transform: rotate(180deg); }
        .topbar-title { font-weight: 700; font-size: 1rem; color: var(--text-primary); white-space: nowrap; }
        .topbar-subtitle { font-size: 0.72rem; color: var(--text-muted); }
        .topbar-right { display: flex; align-items: center; gap: 0.5rem; }

        /* ── Notification Bell ── */
        .notif-btn {
            position: relative;
            width: 42px; height: 42px; border-radius: 12px;
            border: 1px solid var(--border-color);
            background: var(--input-bg); color: var(--text-primary);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; flex-shrink: 0;
        }
        .notif-btn:hover { background: var(--btn-primary); color: white; border-color: var(--btn-primary); }
        .notif-btn i { font-size: 1rem; }
        .notif-btn.has-notifs i { animation: bell-shake 2s ease infinite; transform-origin: top center; }
        @keyframes bell-shake {
            0%,90%,100% { transform: rotate(0deg); }
            92% { transform: rotate(12deg); } 94% { transform: rotate(-10deg); }
            96% { transform: rotate(8deg); }  98% { transform: rotate(-6deg); }
        }
        .notif-count-badge {
            position: absolute; top: 6px; right: 6px;
            width: 18px; height: 18px; background: #ef4444; color: white;
            font-size: 0.62rem; font-weight: 700; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--topbar-bg);
            transform: scale(0); transition: transform 0.2s cubic-bezier(0.175,0.885,0.32,1.275);
        }
        .notif-count-badge.show { transform: scale(1); }

        /* Notification Dropdown */
        .notif-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 360px; background: var(--bg-main);
            border: 1px solid var(--border-color); border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15); z-index: 9999;
            overflow: hidden; opacity: 0;
            transform: translateY(-8px) scale(0.97); pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease; transform-origin: top right;
        }
        .notif-dropdown.open { opacity: 1; transform: translateY(0) scale(1); pointer-events: all; }
        .notif-dropdown-header {
            padding: 1rem 1.25rem 0.75rem; border-bottom: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: space-between;
        }
        .notif-dropdown-header h6 { margin: 0; font-weight: 700; font-size: 0.9rem; color: var(--text-primary); }
        .notif-mark-read { font-size: 0.75rem; color: var(--btn-primary); cursor: pointer; font-weight: 600; background: none; border: none; padding: 0; transition: opacity 0.2s; }
        .notif-mark-read:hover { opacity: 0.7; }
        .notif-list { max-height: 380px; overflow-y: auto; }
        .notif-list::-webkit-scrollbar { width: 4px; }
        .notif-list::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 99px; }
        .notif-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 0.9rem 1.25rem; cursor: pointer; transition: background 0.15s;
            border-bottom: 1px solid var(--border-color); text-decoration: none;
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: var(--hover-bg); }
        .notif-item.unread { background: rgba(15,60,145,0.04); }
        body.dark .notif-item.unread { background: rgba(59,130,246,0.08); }
        .notif-icon-wrap { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .notif-icon-wrap.payment { background: rgba(34,197,94,0.12); color: #16a34a; }
        .notif-icon-wrap.student { background: rgba(59,130,246,0.12); color: #2563eb; }
        .notif-body { flex: 1; min-width: 0; }
        .notif-title { font-size: 0.82rem; font-weight: 600; color: var(--text-primary); margin-bottom: 2px; }
        .notif-desc { font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .notif-time { font-size: 0.7rem; color: var(--text-muted); margin-top: 3px; }
        .notif-unread-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--btn-primary); flex-shrink: 0; margin-top: 6px; }
        .notif-empty { padding: 2.5rem 1.25rem; text-align: center; color: var(--text-muted); }
        .notif-empty i { font-size: 2rem; opacity: 0.4; display: block; margin-bottom: 0.5rem; }
        .notif-empty p { font-size: 0.82rem; margin: 0; }
        .notif-footer { padding: 0.75rem 1.25rem; border-top: 1px solid var(--border-color); text-align: center; }
        .notif-footer a { font-size: 0.78rem; color: var(--btn-primary); font-weight: 600; text-decoration: none; }
        .notif-footer a:hover { text-decoration: underline; }
        .notif-wrapper { position: relative; }

        /* ── Admin Profile Avatar ── */
        .admin-avatar-btn {
            width: 40px; height: 40px; border-radius: 50%;
            border: 2px solid var(--border-color);
            background: var(--btn-primary);
            color: white; font-weight: 700; font-size: 0.85rem;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s;
            overflow: hidden; flex-shrink: 0;
            position: relative;
        }
        .admin-avatar-btn:hover { border-color: var(--btn-primary); box-shadow: 0 0 0 3px rgba(15,60,145,0.15); transform: scale(1.05); }
        .admin-avatar-btn img { width: 100%; height: 100%; object-fit: cover; }
        .admin-avatar-wrapper { position: relative; }

        /* Profile Dropdown */
        .profile-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 260px; background: var(--bg-main);
            border: 1px solid var(--border-color); border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15); z-index: 9999;
            overflow: hidden; opacity: 0;
            transform: translateY(-8px) scale(0.97); pointer-events: none;
            transition: opacity 0.22s ease, transform 0.22s ease; transform-origin: top right;
        }
        .profile-dropdown.open { opacity: 1; transform: translateY(0) scale(1); pointer-events: all; }

        .profile-dropdown-header {
            background: var(--modal-header-bg);
            padding: 1.1rem 1.25rem;
            display: flex; align-items: center; gap: 12px;
        }
        .profile-dropdown-avatar {
            width: 44px; height: 44px; border-radius: 50%;
            background: rgba(255,255,255,0.25);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1rem; color: white;
            overflow: hidden; flex-shrink: 0; border: 2px solid rgba(255,255,255,0.4);
        }
        .profile-dropdown-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-dropdown-info { flex: 1; min-width: 0; }
        .profile-dropdown-name { font-weight: 700; font-size: 0.9rem; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .profile-dropdown-role { font-size: 0.72rem; color: rgba(255,255,255,0.75); margin-top: 2px; }

        .profile-dropdown-body { padding: 0.5rem; }
        .profile-dropdown-item {
            display: flex; align-items: center; gap: 10px;
            padding: 0.65rem 0.85rem; border-radius: 12px;
            cursor: pointer; transition: background 0.15s;
            color: var(--text-primary); font-size: 0.85rem; font-weight: 500;
            border: none; background: none; width: 100%; text-align: left;
            text-decoration: none;
        }
        .profile-dropdown-item:hover { background: var(--hover-bg); color: var(--btn-primary); }
        .profile-dropdown-item i { width: 18px; text-align: center; font-size: 0.9rem; color: var(--text-muted); flex-shrink: 0; }
        .profile-dropdown-item:hover i { color: var(--btn-primary); }
        .profile-dropdown-item.danger { color: #ef4444; }
        .profile-dropdown-item.danger i { color: #ef4444; }
        .profile-dropdown-item.danger:hover { background: rgba(239,68,68,0.08); color: #dc2626; }
        .profile-dropdown-divider { height: 1px; background: var(--border-color); margin: 0.4rem 0; }

        /* Mobile Navbar */
        .mobile-navbar {
            background: var(--bg-main);
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky; top: 0; z-index: 100;
            transition: background 0.3s ease;
        }
        .mobile-navbar-logo {
            width: 36px; height: 36px; object-fit: cover;
            border-radius: 50%; border: 2px solid #0f3c91; padding: 2px; background: white;
        }
        .mobile-navbar-title { font-weight: 700; color: var(--text-primary); font-size: 1rem; line-height: 1.2; }
        .mobile-navbar-subtitle { font-size: 0.7rem; color: var(--text-muted); line-height: 1; }
        .navbar-toggle { background: #0f3c91; border: none; color: white; font-size: 1.1rem; padding: 0.45rem 0.75rem; border-radius: 10px; }
        body.dark .navbar-toggle { background: #3b82f6; }

        /* Offcanvas Sidebar */
        .offcanvas.sidebar { width: 280px; background: var(--bg-sidebar); }
        .offcanvas.sidebar .offcanvas-header { position: relative; align-items: flex-start; padding: 0; }
        .offcanvas.sidebar .offcanvas-header .btn-close { position: absolute; top: 1rem; right: 1rem; margin: 0; z-index: 10; filter: brightness(0) invert(1); opacity: 0.8; }
        .offcanvas.sidebar .offcanvas-header .btn-close:hover { opacity: 1; }
        .offcanvas .sidebar-header { padding: 1.25rem; width: 100%; }
        .offcanvas .sidebar-header img { width: 52px; height: 52px; object-fit: contain; border-radius: 26px; background: white; padding: 5px; display: block; margin-bottom: 0.75rem; }
        .offcanvas .sidebar-header h4 { font-weight: 700; color: white; font-size: 1.3rem; margin-bottom: 2px; }
        .offcanvas .sidebar-header small { color: rgba(255,255,255,0.7); font-size: 0.82rem; }

        /* Mobile responsive overrides */
        @media (max-width: 767.98px) {
            .card { border-radius: 16px !important; margin-bottom: 1rem; }
            .table-responsive { border-radius: 12px; overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .row.g-3 > [class*="col-md"], .row.g-2 > [class*="col-md"] { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }
            .modal-dialog { margin: 0.5rem auto; max-width: calc(100vw - 1rem) !important; }
            .modal-lg, .modal-xl { max-width: calc(100vw - 1rem) !important; }
            .modal-body { padding: 1rem !important; }
            .modal-footer { padding: 0.75rem 1rem !important; flex-direction: column; }
            .modal-footer .btn, .modal-footer button { width: 100% !important; }
            .d-flex.justify-content-between.flex-wrap { flex-direction: column; align-items: flex-start !important; gap: 0.75rem; }
            .btn-add-fee { width: 100%; justify-content: center; }
            .notif-dropdown { width: calc(100vw - 2rem); position: fixed; top: 64px; left: 50%; right: auto; transform: translateX(-50%) translateY(-8px) scale(0.97); }
            .notif-dropdown.open { opacity: 1; transform: translateX(-50%) translateY(0) scale(1); pointer-events: all; }
            .profile-dropdown { width: calc(100vw - 2rem); position: fixed; top: 64px; right: 1rem; }
            .detail-grid { grid-template-columns: 1fr !important; }
            .diff-wrap { grid-template-columns: 1fr !important; }
            .stat-grid { grid-template-columns: repeat(2, 1fr) !important; }
            .btn-action { width: 32px !important; height: 32px !important; }
            h2.fw-bold { font-size: 1.2rem !important; }
            .row.g-4 > [class*="col-"] { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }
        }

        /* Shared components */
        .card, .modal-content { background: var(--bg-main); border: none; box-shadow: var(--card-shadow); transition: background 0.3s ease; }
        .modal-header { background: var(--modal-header-bg); color: white; border-radius: 20px 20px 0 0; }
        .modal-header .btn-close { filter: brightness(0) invert(1); }
        .modal-footer { border-top-color: var(--border-color); }
        .table { color: var(--text-primary); }
        .table td { border-bottom-color: var(--table-row-border); color: var(--text-secondary); }
        .table th { background-color: var(--table-header-bg); color: var(--text-primary); border-bottom-color: var(--border-color); }
        .form-control, .form-select { background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-primary); }
        .form-control:focus, .form-select:focus { border-color: #0f3c91; box-shadow: 0 0 0 3px rgba(15,60,145,0.1); background-color: var(--input-bg); }
        .btn-primary { background: var(--btn-primary); border: none; }
        .btn-primary:hover { background: var(--btn-primary-hover); }
        .btn-outline-secondary { border-color: var(--border-color); color: var(--text-secondary); }
        .btn-outline-secondary:hover { background: var(--hover-bg); border-color: var(--text-muted); }
        .alert-light { background: var(--bg-main); color: var(--text-primary); }
        .text-muted { color: var(--text-muted) !important; }
        .bg-light { background-color: var(--input-bg) !important; }

        /* ── Edit Profile Modal ── */
        .avatar-upload-area {
            display: flex; flex-direction: column; align-items: center; gap: 12px;
            padding: 1.25rem;
        }
        .avatar-preview {
            width: 90px; height: 90px; border-radius: 50%;
            background: var(--btn-primary); color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; font-weight: 700;
            overflow: hidden; border: 3px solid var(--border-color);
            position: relative; cursor: pointer;
            transition: all 0.2s;
        }
        .avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-preview-overlay {
            position: absolute; inset: 0; border-radius: 50%;
            background: rgba(0,0,0,0.45);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.2s; color: white; font-size: 1.2rem;
        }
        .avatar-preview:hover .avatar-preview-overlay { opacity: 1; }
        .profile-tab-nav {
            display: flex; gap: 4px;
            background: var(--input-bg); padding: 4px; border-radius: 12px;
            margin-bottom: 1.25rem;
        }
        .profile-tab-btn {
            flex: 1; border: none; background: none;
            padding: 0.5rem 0.75rem; border-radius: 9px;
            font-size: 0.8rem; font-weight: 600; color: var(--text-muted);
            cursor: pointer; transition: all 0.2s;
        }
        .profile-tab-btn.active { background: var(--bg-main); color: var(--btn-primary); box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .profile-tab-panel { display: none; }
        .profile-tab-panel.active { display: block; }

        /* Password strength */
        .pw-strength-bar { height: 4px; border-radius: 99px; background: var(--border-color); margin-top: 6px; overflow: hidden; }
        .pw-strength-fill { height: 100%; border-radius: 99px; width: 0%; transition: width 0.3s, background 0.3s; }
        .pw-strength-text { font-size: 0.72rem; margin-top: 4px; }

        /* NEW: Password toggle button styles */
        .input-group-position {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-muted);
            background: none;
            border: none;
            padding: 0;
            z-index: 10;
        }
        .password-toggle:hover {
            color: var(--btn-primary);
        }
        @media (max-width: 767.98px) {
            .password-toggle { right: 10px; }
        }

        /* Full-screen loading overlay for AJAX requests */
        #ajax-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0s linear 0.2s;
        }
        #ajax-loader.visible {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.2s ease, visibility 0s linear 0s;
        }
        .ajax-loader-content {
            background: var(--bg-main);
            padding: 2rem 2.5rem;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .ajax-loader-content i {
            font-size: 2.5rem;
            color: var(--btn-primary);
            margin-bottom: 1rem;
            display: inline-block;
        }
        .ajax-loader-content p {
            margin: 0;
            font-weight: 500;
            color: var(--text-primary);
        }
        body.dark .ajax-loader-content {
            background: var(--bg-main);
        }
        /* Force light text in dark mode for all form controls */
        body.dark .form-control,
        body.dark .form-select,
        body.dark input.form-control,
        body.dark textarea.form-control,
        body.dark select.form-select {
            color: #f1f5f9 !important;
            background-color: #334155 !important;
            border-color: #475569 !important;
        }
        body.dark .form-control::placeholder,
        body.dark input::placeholder,
        body.dark textarea::placeholder {
            color: #94a3b8 !important;
            opacity: 1;
        }
        body.dark input:-webkit-autofill,
        body.dark input:-webkit-autofill:focus {
            -webkit-text-fill-color: #f1f5f9 !important;
            -webkit-box-shadow: 0 0 0 1000px #334155 inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- Page Loader --}}
    <div id="page-loader" role="status" aria-label="Loading page">
        <div class="loader-card">
            <div class="loader-logo-ring">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay">
                <div class="loader-spinner"></div>
            </div>
            <p class="loader-text">Non-UniPay</p>
            <p class="loader-subtext">Loading your dashboard</p>
            <div class="loader-bar-track"><div class="loader-bar-fill"></div></div>
        </div>
    </div>

    {{-- Mobile Navbar (visible only on mobile) --}}
    <nav class="mobile-navbar d-md-none">
        <div class="d-flex align-items-center justify-content-between">
            <button class="navbar-toggle" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas"
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
            <div class="d-flex align-items-center gap-2">
                <div class="notif-wrapper">
                    <button class="notif-btn" id="mobileNotifBtn" aria-label="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="notif-count-badge" id="mobileNotifBadge"></span>
                    </button>
                    <div class="notif-dropdown" id="mobileNotifDropdown"></div>
                </div>
                {{-- Mobile Avatar --}}
                <div class="admin-avatar-wrapper">
                    <button class="admin-avatar-btn" id="mobileAvatarBtn" aria-label="Profile menu">
                        @if(auth()->user()->profile_picture)
                            <img src="{{ auth()->user()->profile_picture }}" alt="Avatar">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </button>
                    <div class="profile-dropdown" id="mobileProfileDropdown">
                        @include('admin.partials.profile_dropdown')
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Mobile Offcanvas Sidebar --}}
    <div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-header">
            <div class="sidebar-header">
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
                    @if(auth()->user()->role === 'superadmin')
                        <li class="nav-section-title">ADMINISTRATION</li>
                        <li class="nav-item {{ request()->routeIs('admin.superadmin.admins*') ? 'active' : '' }}">
                            <a class="nav-link superadmin-link" href="{{ route('admin.superadmin.admins.index') }}">
                                <i class="fas fa-user-shield"></i> Manage Admins
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('admin.superadmin.audit-logs*') ? 'active' : '' }}">
                            <a class="nav-link superadmin-link" href="{{ route('admin.superadmin.audit-logs.index') }}">
                                <i class="fas fa-history"></i> Audit Logs
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    {{-- Desktop Sidebar --}}
    <aside class="desktop-sidebar" id="desktopSidebar">
        <div class="sidebar-header">
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
                @if(auth()->user()->role === 'superadmin')
                    <li class="nav-section-title">ADMINISTRATION</li>
                    <li class="nav-item {{ request()->routeIs('admin.superadmin.admins*') ? 'active' : '' }}">
                        <a class="nav-link superadmin-link" href="{{ route('admin.superadmin.admins.index') }}">
                            <i class="fas fa-user-shield"></i> Manage Admins
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.superadmin.audit-logs*') ? 'active' : '' }}">
                        <a class="nav-link superadmin-link" href="{{ route('admin.superadmin.audit-logs.index') }}">
                            <i class="fas fa-history"></i> Audit Logs
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </aside>

    {{-- Desktop Top Bar --}}
    <header class="desktop-topbar" id="desktopTopbar">
        <div class="topbar-left">
            <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle sidebar" aria-label="Toggle sidebar">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div>
                <div class="topbar-title">@yield('title', 'Dashboard')</div>
                <div class="topbar-subtitle">Non-UniPay Admin Panel</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="notif-wrapper">
                <button class="notif-btn" id="desktopNotifBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notif-count-badge" id="desktopNotifBadge"></span>
                </button>
                <div class="notif-dropdown" id="desktopNotifDropdown"></div>
            </div>

            {{-- Admin Avatar with Profile Dropdown --}}
            <div class="admin-avatar-wrapper">
                <button class="admin-avatar-btn" id="desktopAvatarBtn" aria-label="Profile menu">
                    @if(auth()->user()->profile_picture)
                        <img src="{{ auth()->user()->profile_picture }}" alt="Avatar" id="topbarAvatarImg">
                    @else
                        <span id="topbarAvatarInitial">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                </button>
                <div class="profile-dropdown" id="desktopProfileDropdown">
                    @include('admin.partials.profile_dropdown')
                </div>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="main-content">
        @yield('content')
    </main>

    {{-- ════════════════════════════════════════════
         Edit Profile Modal (with password toggles)
         ════════════════════════════════════════════ --}}
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
            <div class="modal-content" style="border-radius:24px; overflow:hidden;">
                <div class="modal-header" style="border-radius:24px 24px 0 0;">
                    <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">

                    {{-- Avatar upload area (Cloudinary direct URL) --}}
                    <div class="avatar-upload-area">
                        <label for="profilePicInput" style="cursor:pointer;">
                            <div class="avatar-preview" id="avatarPreview">
                                @if(auth()->user()->profile_picture)
                                    <img src="{{ auth()->user()->profile_picture }}" alt="Avatar" id="avatarPreviewImg">
                                    <span id="avatarPreviewInitial" style="display:none;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                @else
                                    <span id="avatarPreviewInitial">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                    <img src="" alt="" id="avatarPreviewImg" style="display:none;">
                                @endif
                                <div class="avatar-preview-overlay"><i class="fas fa-camera"></i></div>
                            </div>
                        </label>
                        <input type="file" id="profilePicInput" accept="image/*" style="display:none;">
                        <span style="font-size:0.78rem; color:var(--text-muted);">Click avatar to upload photo</span>
                    </div>

                    {{-- Tab navigation --}}
                    <div class="profile-tab-nav">
                        <button class="profile-tab-btn active" data-tab="email">
                            <i class="fas fa-envelope me-1"></i> Email
                        </button>
                        <button class="profile-tab-btn" data-tab="password">
                            <i class="fas fa-lock me-1"></i> Password
                        </button>
                    </div>

                    {{-- Email Tab with password toggle --}}
                    <div class="profile-tab-panel active" id="tab-email">
                        <form id="updateEmailForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-600" style="font-size:0.85rem; font-weight:600;">Name</label>
                                <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-600" style="font-size:0.85rem; font-weight:600;">Email Address</label>
                                <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                            </div>
                            <div class="mb-3 input-group-position">
                                <label class="form-label fw-600" style="font-size:0.85rem; font-weight:600;">Confirm Current Password</label>
                                <input type="password" class="form-control" name="current_password" id="currentPasswordEmail" placeholder="Enter current password to confirm" required>
                                <button type="button" class="password-toggle" data-target="currentPasswordEmail">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="emailFormAlert" class="alert d-none" style="font-size:0.82rem; border-radius:10px;"></div>
                            <button type="submit" class="btn btn-primary w-100" style="border-radius:12px; font-weight:600;">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </form>
                    </div>

                    {{-- Password Tab with toggles for all three fields --}}
                    <div class="profile-tab-panel" id="tab-password">
                        <form id="updatePasswordForm">
                            @csrf
                            <div class="mb-3 input-group-position">
                                <label class="form-label" style="font-size:0.85rem; font-weight:600;">Current Password</label>
                                <input type="password" class="form-control" name="current_password" id="currentPasswordPass" placeholder="Your current password" required>
                                <button type="button" class="password-toggle" data-target="currentPasswordPass">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="mb-3 input-group-position">
                                <label class="form-label" style="font-size:0.85rem; font-weight:600;">New Password</label>
                                <input type="password" class="form-control" name="new_password" id="newPasswordInput" placeholder="At least 8 characters" required>
                                <button type="button" class="password-toggle" data-target="newPasswordInput">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <div class="pw-strength-bar"><div class="pw-strength-fill" id="pwStrengthFill"></div></div>
                                <div class="pw-strength-text text-muted" id="pwStrengthText"></div>
                            </div>
                            <div class="mb-3 input-group-position">
                                <label class="form-label" style="font-size:0.85rem; font-weight:600;">Confirm New Password</label>
                                <input type="password" class="form-control" name="new_password_confirmation" id="confirmPasswordInput" placeholder="Repeat new password" required>
                                <button type="button" class="password-toggle" data-target="confirmPasswordInput">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="passwordFormAlert" class="alert d-none" style="font-size:0.82rem; border-radius:10px;"></div>
                            <button type="submit" class="btn btn-primary w-100" style="border-radius:12px; font-weight:600;">
                                <i class="fas fa-key me-2"></i>Update Password
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Logout Modal --}}
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
// ── Dark Mode ─────────────────────────────────────────
(function() {
    const STORAGE_KEY = 'admin_dark_mode';

    function syncDarkModeUI() {
        const isDark = document.body.classList.contains('dark');
        document.querySelectorAll('.dark-mode-switch').forEach(sw => {
            sw.checked = isDark;
        });
    }

    function setDarkMode(isDark) {
        document.body.classList.toggle('dark', isDark);
        localStorage.setItem(STORAGE_KEY, isDark ? 'true' : 'false');
        syncDarkModeUI();
    }

    window.toggleDarkMode = function() {
        setDarkMode(!document.body.classList.contains('dark'));
    };

    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored !== null) {
        setDarkMode(stored === 'true');
    } else {
        setDarkMode(window.matchMedia('(prefers-color-scheme: dark)').matches);
    }

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (localStorage.getItem(STORAGE_KEY) === null) {
            setDarkMode(e.matches);
        }
    });

    function bindSwitchEvents() {
        document.querySelectorAll('.dark-mode-switch').forEach(sw => {
            sw.removeEventListener('change', window.toggleDarkMode);
            sw.addEventListener('change', window.toggleDarkMode);
        });
    }
    bindSwitchEvents();

    function bindDropdownSync() {
        const dropdownButtons = ['#desktopAvatarBtn', '#mobileAvatarBtn'];
        dropdownButtons.forEach(selector => {
            const btn = document.querySelector(selector);
            if (btn) {
                btn.addEventListener('click', function() {
                    setTimeout(syncDarkModeUI, 50);
                });
            }
        });
    }
    bindDropdownSync();
})();

    // ── Sidebar Toggle ──────────────────────────────────────────────────────
    (function () {
        const sidebar   = document.getElementById('desktopSidebar');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        const KEY = 'admin_sidebar_collapsed';
        function setSidebarState(collapsed) {
            if (!sidebar) return;
            sidebar.classList.toggle('collapsed', collapsed);
            if (toggleBtn) toggleBtn.classList.toggle('collapsed', collapsed);
            localStorage.setItem(KEY, collapsed ? 'true' : 'false');
        }
        if (localStorage.getItem(KEY) === 'true') setSidebarState(true);
        if (toggleBtn) toggleBtn.addEventListener('click', () => setSidebarState(!sidebar.classList.contains('collapsed')));
    })();

    // ── Page Loader ──────────────────────────────────────────────────────────
    (function () {
        const loader        = document.getElementById('page-loader');
        const loaderText    = loader?.querySelector('.loader-text');
        const loaderSubtext = loader?.querySelector('.loader-subtext');
        let activeRequests = 0, hideTimeout = null;
        function showLoader(msg, sub) {
            if (hideTimeout) clearTimeout(hideTimeout);
            activeRequests++;
            if (loaderText && msg) loaderText.innerText = msg;
            if (loaderSubtext && sub) loaderSubtext.innerText = sub;
            if (loader) loader.classList.add('visible');
        }
        function hideLoader() {
            activeRequests--;
            if (activeRequests <= 0) {
                hideTimeout = setTimeout(() => {
                    if (loader) loader.classList.remove('visible');
                    if (loaderText) loaderText.innerText = 'Non-UniPay';
                    if (loaderSubtext) loaderSubtext.innerText = 'Loading your dashboard';
                }, 150);
            }
        }
        document.addEventListener('click', e => {
            const target = e.target.closest('a');
            if (!target || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
            if (target.hasAttribute('download') || target.getAttribute('target') === '_blank') return;
            if (target.hasAttribute('data-bs-toggle')) return;
            const href = target.getAttribute('href') || '';
            if (!href || href.startsWith('#') || href.startsWith('javascript')) return;
            try {
                const url = new URL(href, window.location.href);
                if (url.origin !== window.location.origin) return;
            } catch { return; }
            showLoader('Loading...', 'Please wait');
        });
        document.addEventListener('submit', e => {
            const form = e.target.closest('form.requires-loader');
            if (!form) return;
            const msg = form.action && form.action.includes('/logout') ? 'Logging out...' : 'Processing...';
            showLoader(msg, 'Please wait');
        });
        window.addEventListener('load', hideLoader);
        window.addEventListener('pageshow', e => { if (e.persisted) hideLoader(); });
        setInterval(() => {
            if (loader && loader.classList.contains('visible') && activeRequests === 0) loader.classList.remove('visible');
        }, 8000);
    })();

    // ── Notifications ─────────────────────────────────────────────────────────
    (function () {
        const NOTIF_KEY    = 'admin_notifications';
        const LAST_PAY_KEY = 'admin_last_payment_count';
        const LAST_STU_KEY = 'admin_last_student_count';

        function getStored() { try { return JSON.parse(localStorage.getItem(NOTIF_KEY) || '[]'); } catch { return []; } }
        function setStored(arr) { localStorage.setItem(NOTIF_KEY, JSON.stringify(arr.slice(0, 50))); }
        function timeAgo(ts) {
            const diff = Math.floor((Date.now() - ts) / 1000);
            if (diff < 60) return 'just now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            return Math.floor(diff / 86400) + 'd ago';
        }
        function buildDropdownHTML(notifs) {
            const unreadCount = notifs.filter(n => !n.read).length;
            let html = `<div class="notif-dropdown-header">
                <h6><i class="fas fa-bell me-2" style="color:#0f3c91;"></i> Notifications
                    ${unreadCount > 0 ? `<span style="font-size:0.72rem;background:rgba(15,60,145,0.1);color:#0f3c91;padding:2px 8px;border-radius:99px;margin-left:6px;font-weight:600;">${unreadCount} new</span>` : ''}
                </h6>
                ${unreadCount > 0 ? `<button class="notif-mark-read" onclick="window._markAllNotifRead(event)">Mark all read</button>` : ''}
            </div><div class="notif-list">`;
            if (notifs.length === 0) {
                html += `<div class="notif-empty"><i class="fas fa-bell-slash"></i><p>No notifications yet</p></div>`;
            } else {
                notifs.forEach(n => {
                    const isPayment = n.type === 'payment';
                    html += `<a href="${n.url}" class="notif-item ${n.read ? '' : 'unread'}" onclick="window._handleNotifClick(event, '${n.uid}', '${n.url}')">
                        <div class="notif-icon-wrap ${n.type}"><i class="fas ${isPayment ? 'fa-money-bill-wave' : 'fa-user-graduate'}"></i></div>
                        <div class="notif-body">
                            <div class="notif-title">${n.title}</div>
                            <div class="notif-desc">${n.desc}</div>
                            <div class="notif-time">${timeAgo(n.ts)}</div>
                        </div>
                        ${!n.read ? '<div class="notif-unread-dot"></div>' : ''}
                    </a>`;
                });
            }
            html += `</div>`;
            if (notifs.length > 0) {
                html += `<div class="notif-footer">
                    <a href="{{ route('admin.payments') }}">View all payments</a> &nbsp;·&nbsp;
                    <a href="{{ route('admin.students') }}">View all students</a>
                </div>`;
            }
            return html;
        }
        function renderAll() {
            const notifs = getStored();
            const html   = buildDropdownHTML(notifs);
            document.querySelectorAll('#desktopNotifDropdown, #mobileNotifDropdown').forEach(el => { if (el) el.innerHTML = html; });
            const unread = notifs.filter(n => !n.read).length;
            document.querySelectorAll('#desktopNotifBadge, #mobileNotifBadge').forEach(badge => {
                if (!badge) return;
                badge.textContent = unread > 9 ? '9+' : unread;
                badge.classList.toggle('show', unread > 0);
            });
            document.querySelectorAll('#desktopNotifBtn, #mobileNotifBtn').forEach(btn => {
                if (btn) btn.classList.toggle('has-notifs', unread > 0);
            });
        }
        window._markAllNotifRead = function (e) {
            e.stopPropagation();
            const notifs = getStored();
            notifs.forEach(n => n.read = true);
            setStored(notifs); renderAll();
        };
        window._handleNotifClick = function (e, uid, url) {
            e.preventDefault();
            const notifs = getStored();
            notifs.forEach(n => { if (n.uid === uid) n.read = true; });
            setStored(notifs); renderAll();
            window.location.href = url;
        };
        function toggleDropdown(btnId, dropId) {
            const btn  = document.getElementById(btnId);
            const drop = document.getElementById(dropId);
            if (!btn || !drop) return;
            btn.addEventListener('click', e => {
                e.stopPropagation();
                document.querySelectorAll('.notif-dropdown').forEach(d => { if (d !== drop) d.classList.remove('open'); });
                drop.classList.toggle('open');
            });
        }
        toggleDropdown('desktopNotifBtn', 'desktopNotifDropdown');
        toggleDropdown('mobileNotifBtn',  'mobileNotifDropdown');
        document.addEventListener('click', e => {
            if (!e.target.closest('.notif-wrapper')) {
                document.querySelectorAll('.notif-dropdown').forEach(d => d.classList.remove('open'));
            }
        });
        function pushNotif(type, title, desc, url) {
            const notifs = getStored();
            notifs.unshift({ uid: type + '_' + Date.now(), type, title, desc, url, ts: Date.now(), read: false });
            setStored(notifs); renderAll();
        }
        function getLastCount(key) { const v = localStorage.getItem(key); return v === null ? null : parseInt(v, 10); }
        function setLastCount(key, val) { localStorage.setItem(key, String(val)); }
        function checkPayments() {
            fetch('/admin/api/pending-payments-count', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.json() : null).then(data => {
                    if (!data) return;
                    const count = data.count || 0, last = getLastCount(LAST_PAY_KEY);
                    if (last !== null && count > last) {
                        const diff = count - last;
                        pushNotif('payment', `${diff} new payment${diff > 1 ? 's' : ''} pending`, 'New payment submission(s) awaiting your review.', '{{ route("admin.payments") }}');
                    }
                    setLastCount(LAST_PAY_KEY, count);
                }).catch(() => {});
        }
        function checkStudents() {
            fetch('/admin/api/new-students-count', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.json() : null).then(data => {
                    if (!data) return;
                    const count = data.count || 0, last = getLastCount(LAST_STU_KEY);
                    if (last !== null && count > last) {
                        const diff = count - last;
                        pushNotif('student', `${diff} new student${diff > 1 ? 's' : ''} registered`, 'New student registration(s) need confirmation.', '{{ route("admin.students") }}');
                    }
                    setLastCount(LAST_STU_KEY, count);
                }).catch(() => {});
        }
        renderAll(); checkPayments(); checkStudents();
        setInterval(() => { checkPayments(); checkStudents(); }, 5000);
    })();

    // ── Profile Avatar Dropdown ───────────────────────────────────────────────
    (function () {
        function setupAvatarDropdown(btnId, dropId) {
            const btn  = document.getElementById(btnId);
            const drop = document.getElementById(dropId);
            if (!btn || !drop) return;
            btn.addEventListener('click', e => {
                e.stopPropagation();
                document.querySelectorAll('.notif-dropdown').forEach(d => d.classList.remove('open'));
                document.querySelectorAll('.profile-dropdown').forEach(d => { if (d !== drop) d.classList.remove('open'); });
                drop.classList.toggle('open');
            });
        }
        setupAvatarDropdown('desktopAvatarBtn', 'desktopProfileDropdown');
        setupAvatarDropdown('mobileAvatarBtn',  'mobileProfileDropdown');
        document.addEventListener('click', e => {
            if (!e.target.closest('.admin-avatar-wrapper')) {
                document.querySelectorAll('.profile-dropdown').forEach(d => d.classList.remove('open'));
            }
        });
    })();

    // ── Profile Edit Modal Tabs ───────────────────────────────────────────────
    document.querySelectorAll('.profile-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;
            document.querySelectorAll('.profile-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.profile-tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('tab-' + tab)?.classList.add('active');
        });
    });

    // ── Avatar Preview & Upload (Cloudinary) ──────────────────────────────────
    document.getElementById('profilePicInput')?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById('avatarPreviewImg');
            const initial = document.getElementById('avatarPreviewInitial');
            if (img) { img.src = e.target.result; img.style.display = 'block'; }
            if (initial) initial.style.display = 'none';

            const formData = new FormData();
            formData.append('profile_picture', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            fetch('{{ route("admin.profile.picture") }}', { method: 'POST', body: formData })
                .then(r => r.json()).then(data => {
                    if (data.success) {
                        document.querySelectorAll('.admin-avatar-btn img, .profile-dropdown-avatar img, #avatarPreviewImg').forEach(i => {
                            i.src = data.url; i.style.display = 'block';
                        });
                        document.querySelectorAll('#topbarAvatarInitial, #avatarPreviewInitial').forEach(s => s.style.display = 'none');
                    }
                }).catch(() => {});
        };
        reader.readAsDataURL(file);
    });

    // ── Password Strength ─────────────────────────────────────────────────────
    document.getElementById('newPasswordInput')?.addEventListener('input', function () {
        const val = this.value;
        const fill = document.getElementById('pwStrengthFill');
        const text = document.getElementById('pwStrengthText');
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = [
            { pct: '0%',  color: '',        label: '' },
            { pct: '25%', color: '#ef4444', label: 'Weak' },
            { pct: '50%', color: '#f97316', label: 'Fair' },
            { pct: '75%', color: '#eab308', label: 'Good' },
            { pct: '100%',color: '#22c55e', label: 'Strong' },
        ];
        const lv = levels[score] || levels[0];
        if (fill) { fill.style.width = lv.pct; fill.style.background = lv.color; }
        if (text) { text.textContent = lv.label; text.style.color = lv.color; }
    });

    // ── PASSWORD TOGGLE FUNCTIONALITY (NEW) ─────────────────────────────────
    function initPasswordToggles() {
        const toggleButtons = document.querySelectorAll('.password-toggle');
        toggleButtons.forEach(btn => {
            btn.removeEventListener('click', toggleHandler);
            btn.addEventListener('click', toggleHandler);
        });
    }
    function toggleHandler(e) {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (!input) return;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        const icon = this.querySelector('i');
        if (type === 'text') {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    initPasswordToggles();
    document.getElementById('editProfileModal')?.addEventListener('shown.bs.modal', initPasswordToggles);

    // ── Update Email/Name Form (full-screen loader) ─────────────────────────
    document.getElementById('updateEmailForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const alertBox = document.getElementById('emailFormAlert');
        const loader = document.getElementById('ajax-loader');
        
        if (loader) loader.classList.add('visible');
        
        alertBox.className = 'alert d-none';
        const formData = new FormData(this);
        try {
            const res = await fetch('{{ route("admin.profile.update") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: formData,
            });
            const json = await res.json();
            alertBox.classList.remove('d-none');
            if (json.success) {
                alertBox.className = 'alert alert-success';
                alertBox.textContent = json.message || 'Profile updated successfully.';
            } else {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = json.message || 'Update failed.';
            }
        } catch (error) {
            alertBox.classList.remove('d-none');
            alertBox.className = 'alert alert-danger';
            alertBox.textContent = 'An error occurred. Please try again.';
        } finally {
            if (loader) loader.classList.remove('visible');
        }
    });

    // ── Update Password Form (full-screen loader) ───────────────────────────
    document.getElementById('updatePasswordForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const alertBox = document.getElementById('passwordFormAlert');
        const loader = document.getElementById('ajax-loader');
        
        if (loader) loader.classList.add('visible');
        
        alertBox.className = 'alert d-none';
        const formData = new FormData(this);
        try {
            const res = await fetch('{{ route("admin.profile.password") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: formData,
            });
            const json = await res.json();
            alertBox.classList.remove('d-none');
            if (json.success) {
                alertBox.className = 'alert alert-success';
                alertBox.textContent = json.message || 'Password changed successfully.';
                this.reset();
                document.getElementById('pwStrengthFill').style.width = '0%';
                document.getElementById('pwStrengthText').textContent = '';
            } else {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = json.message || 'Update failed.';
            }
        } catch (error) {
            alertBox.classList.remove('d-none');
            alertBox.className = 'alert alert-danger';
            alertBox.textContent = 'An error occurred. Please try again.';
        } finally {
            if (loader) loader.classList.remove('visible');
        }
    });
    </script>

    {{-- Global AJAX loading overlay --}}
    <div id="ajax-loader">
        <div class="ajax-loader-content">
            <i class="fas fa-spinner fa-pulse"></i>
            <p>Updating profile...</p>
        </div>
    </div>
</body>
</html>