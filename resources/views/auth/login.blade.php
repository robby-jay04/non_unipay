<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · Non-UniPay</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy: #0f3c91;
            --navy-dark: #0a2a6b;
            --navy-light: #1a4da8;
            --accent: #e8b84b;
            --bg: #f4f6fb;
            --text-muted: #6b7280;
            --card-bg: #ffffff;
            --input-bg: #f0f3fa;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* ── LEFT PANEL ─────────────────────────────── */
        .left-panel {
            flex: 0 0 55%;
            position: relative;
            overflow: hidden;
        }

        .left-panel .bg-cover {
            position: absolute;
            inset: 0;
            background: url("{{ asset('bg.PNG') }}") center/cover no-repeat;
        }

        .left-panel .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(10, 42, 107, 0.82) 0%,
                rgba(15, 60, 145, 0.55) 50%,
                rgba(232, 184, 75, 0.20) 100%
            );
        }

        .left-panel .panel-content {
            position: relative;
            z-index: 5;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 3.5rem;
        }

        .left-panel .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            backdrop-filter: blur(8px);
            border-radius: 50px;
            padding: 0.45rem 1rem;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            width: fit-content;
        }

        .left-panel h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(2rem, 3.5vw, 3rem);
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            margin-bottom: 1rem;
        }

        .left-panel h2 em {
            font-style: italic;
            color: var(--accent);
        }

        .left-panel p.tagline {
            color: rgba(255,255,255,0.75);
            font-size: 0.95rem;
            font-weight: 300;
            line-height: 1.7;
            max-width: 380px;
            margin-bottom: 2.5rem;
        }

        .features-row {
            display: flex;
            gap: 1.2rem;
            flex-wrap: wrap;
        }

        .feature-chip {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.20);
            border-radius: 12px;
            padding: 0.55rem 1rem;
            color: #fff;
            font-size: 0.82rem;
            font-weight: 500;
            backdrop-filter: blur(6px);
            transition: background 0.2s;
        }

        .feature-chip i {
            color: var(--accent);
            font-size: 0.9rem;
        }

        /* Decorative floating circle */
        .deco-circle {
            position: absolute;
            top: -80px;
            right: -80px;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            border: 60px solid rgba(232,184,75,0.12);
            z-index: 2;
        }
        .deco-circle-sm {
            position: absolute;
            top: 160px;
            right: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 30px solid rgba(255,255,255,0.07);
            z-index: 2;
        }

        /* ── RIGHT PANEL ─────────────────────────────── */
        .right-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 3rem;
            background: var(--card-bg);
            position: relative;
            overflow-y: auto;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            bottom: -120px;
            right: -120px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(15,60,145,0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-inner {
            width: 100%;
            max-width: 380px;
            animation: fadeUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo */
        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 2.2rem;
        }

        .logo-img {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: var(--navy);
            padding: 4px;
            box-shadow: 0 6px 18px rgba(15,60,145,0.25);
            object-fit: contain;
        }

        .logo-text {
            line-height: 1.1;
        }

        .logo-text span {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--navy);
            display: block;
        }

        .logo-text small {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 400;
            letter-spacing: 0.02em;
        }

        .welcome-heading {
            font-family: 'Playfair Display', serif;
            font-size: 1.85rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.35rem;
        }

        .welcome-sub {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        /* Input fields */
        .field-wrap {
            position: relative;
            margin-bottom: 1rem;
        }

        .field-wrap .field-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .field-wrap input {
            width: 100%;
            background: var(--input-bg);
            border: 1.5px solid transparent;
            border-radius: 14px;
            padding: 0.85rem 1rem 0.85rem 2.8rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            color: #111827;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .field-wrap input::placeholder { color: #b0b7c3; }

        .field-wrap input:focus {
            border-color: var(--navy);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(15,60,145,0.08);
        }

        .field-wrap input:focus + .field-icon-right,
        .field-wrap:focus-within .field-icon {
            color: var(--navy);
        }

        .field-wrap .toggle-pw {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            font-size: 1rem;
            transition: color 0.2s;
            background: none;
            border: none;
            padding: 0;
        }

        .field-wrap .toggle-pw:hover { color: var(--navy); }

        /* Login button */
        .btn-login {
            width: 100%;
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 0.9rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            cursor: pointer;
            margin-top: 0.5rem;
            position: relative;
            overflow: hidden;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, transparent 60%);
            pointer-events: none;
        }

        .btn-login:hover {
            background: var(--navy-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(15,60,145,0.30);
        }

        .btn-login:active { transform: translateY(0); }

        /* Error */
        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            color: #dc2626;
            font-size: 0.82rem;
            padding: 0.6rem 0.9rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Student notification toast */
        #student-toast {
            display: none;
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            z-index: 999;
            background: #fff;
            border-left: 4px solid var(--navy);
            border-radius: 12px;
            box-shadow: 0 12px 36px rgba(0,0,0,0.15);
            padding: 1rem 1.4rem;
            min-width: 300px;
            max-width: 380px;
            opacity: 0;
            transition: opacity 0.35s ease, transform 0.35s ease;
        }

        #student-toast.show {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        #student-toast .toast-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            background: rgba(15,60,145,0.10);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--navy);
            font-size: 1rem;
        }

        #student-toast .toast-body strong {
            display: block;
            font-size: 0.9rem;
            color: #111827;
            margin-bottom: 0.15rem;
        }

        #student-toast .toast-body span {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        #student-toast .toast-close {
            margin-left: auto;
            flex-shrink: 0;
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
            align-self: flex-start;
        }

        /* Divider + footer */
        .form-divider {
            height: 1px;
            background: #e5e7eb;
            margin: 1.75rem 0 1.25rem;
        }

        .footer-note {
            text-align: center;
            font-size: 0.78rem;
            color: #b0b7c3;
        }

        /* ── RESPONSIVE ─────────────────────────────── */
        @media (max-width: 860px) {
            body { flex-direction: column; overflow: auto; }
            .left-panel { flex: 0 0 240px; min-height: 240px; }
            .left-panel .panel-content { padding: 2rem; }
            .left-panel h2 { font-size: 1.7rem; }
            .features-row { display: none; }
            .right-panel { padding: 2rem 1.5rem; }
        }

        @media (max-width: 480px) {
            .left-panel { flex: 0 0 180px; min-height: 180px; }
        }
    </style>
</head>
<body>

    <!-- ══ LEFT PANEL ══════════════════════════════════ -->
    <div class="left-panel">
        <div class="bg-cover"></div>
        <div class="overlay"></div>
        <div class="deco-circle"></div>
        <div class="deco-circle-sm"></div>

        <div class="panel-content">
            <div class="brand-badge">
                <i class="fas fa-university"></i>
                School Management Portal
            </div>

            <h2>Smart Payments,<br><em>Seamless</em> Clearance.</h2>

            <p class="tagline">
                Manage school fees, track balances, and get exam clearance — all from one secure platform.
            </p>

            <div class="features-row">
                <div class="feature-chip"><i class="fas fa-shield-alt"></i> Secure Payments</div>
                <div class="feature-chip"><i class="fas fa-check-circle"></i> Instant Clearance</div>
                <div class="feature-chip"><i class="fas fa-bell"></i> Real-time Alerts</div>
            </div>
        </div>
    </div>

    <!-- ══ RIGHT PANEL ══════════════════════════════════ -->
    <div class="right-panel">
        <div class="login-inner">

            <!-- Logo -->
            <div class="logo-wrap">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay Logo" class="logo-img">
                <div class="logo-text">
                    <span>Non-UniPay</span>
                    <small>Fee Payment &amp; Exam Clearance</small>
                </div>
            </div>

            <!-- Heading -->
            <h1 class="welcome-heading">Welcome back</h1>
            <p class="welcome-sub">Sign in to your account to continue</p>

            <!-- Form -->
            <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                @csrf

                <div class="field-wrap">
                    <i class="fas fa-envelope field-icon"></i>
                    <input
                        type="email"
                        name="email"
                        id="emailInput"
                        placeholder="Email address"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                    >
                </div>

                <div class="field-wrap">
                    <i class="fas fa-lock field-icon"></i>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Password"
                        required
                        autocomplete="current-password"
                    >
                    
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>
            </form>

            @if(session('error'))
                <div class="error-msg">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="form-divider"></div>
            <p class="footer-note">Non-UniPay &copy; {{ date('Y') }} &nbsp;·&nbsp; Staff &amp; Admin Portal</p>

        </div>
    </div>

    <!-- ══ STUDENT TOAST (shown only on student-like email detection) ══ -->
    <div id="student-toast" role="alert">
        <div class="toast-icon">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <div class="toast-body">
            <strong>Are you a student?</strong>
            <span>Please use the <strong>Non-UniPay mobile app</strong> to access your account.</span>
        </div>
        <button class="toast-close" onclick="closeToast()" title="Dismiss">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <script>
      

        /* ── Student toast logic ──
           We detect a "student" email by checking for common student patterns:
           student ID numbers, or a configurable student domain keyword.
           Adjust the STUDENT_DOMAINS / pattern to match your school's email convention. */
        const STUDENT_DOMAINS = ['student.', 'stud.', 'stu.', '@s.'];   // substrings in student emails
        const STUDENT_ID_PATTERN = /^\d{7,12}@/;                         // numeric IDs before @

        let toastTimeout;

        function checkStudentEmail(email) {
            const lower = email.toLowerCase();
            const looksLikeStudent =
                STUDENT_DOMAINS.some(d => lower.includes(d)) ||
                STUDENT_ID_PATTERN.test(lower);

            if (looksLikeStudent) {
                showToast();
            } else {
                hideToast();
            }
        }

        function showToast() {
            clearTimeout(toastTimeout);
            const toast = document.getElementById('student-toast');
            toast.style.display = 'flex';
            // Force reflow then add .show for transition
            requestAnimationFrame(() => {
                requestAnimationFrame(() => toast.classList.add('show'));
            });
            // Auto-hide after 7 s
            toastTimeout = setTimeout(hideToast, 7000);
        }

        function hideToast() {
            const toast = document.getElementById('student-toast');
            toast.classList.remove('show');
            setTimeout(() => { toast.style.display = 'none'; }, 380);
        }

        function closeToast() {
            clearTimeout(toastTimeout);
            hideToast();
        }

        // Listen on email input with a small debounce
        let debounce;
        document.getElementById('emailInput').addEventListener('input', function () {
            clearTimeout(debounce);
            const val = this.value.trim();
            debounce = setTimeout(() => {
                if (val.length > 4) checkStudentEmail(val);
                else hideToast();
            }, 500);
        });

        // Also check on form submit (fallback)
        document.getElementById('loginForm').addEventListener('submit', function () {
            const email = document.getElementById('emailInput').value.trim();
            checkStudentEmail(email);
        });
    </script>
</body>
</html>
