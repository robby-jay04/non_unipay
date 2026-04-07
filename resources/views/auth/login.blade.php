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
            position: relative;
        }

        /* ═══════════════════════════════════════════
           SPLASH SCREEN — improved animation
        ═══════════════════════════════════════════ */
        #splash-screen {
            position: fixed;
            inset: 0;
            z-index: 200000;
            background: linear-gradient(145deg, #0a2a6b 0%, #0f3c91 50%, #1a4da8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            opacity: 1;
            transform: scale(1);
            transition: opacity 0.7s ease, transform 0.7s ease;
            pointer-events: all;
            overflow: hidden;
        }
        #splash-screen.fade-out {
            opacity: 0;
            transform: scale(1.04);
            pointer-events: none;
        }

        /* Pulse rings */
        .splash-ring {
            position: absolute;
            border-radius: 50%;
            border: 1.5px solid rgba(255,255,255,0.07);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation: splashRingPulse 3s ease-in-out infinite;
        }
        .splash-ring:nth-child(1) { width: 340px; height: 340px; animation-delay: 0s; }
        .splash-ring:nth-child(2) { width: 520px; height: 520px; animation-delay: 1s; }
        .splash-ring:nth-child(3) { width: 700px; height: 700px; animation-delay: 2s; }
        @keyframes splashRingPulse {
            0%,100% { opacity: 0.5; transform: translate(-50%,-50%) scale(1); }
            50%      { opacity: 0.1; transform: translate(-50%,-50%) scale(1.06); }
        }

        /* Floating particles container */
        #splash-particles {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }
        .splash-particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0;
            animation: splashParticleFloat var(--pd) var(--pp) ease-in-out infinite;
        }
        @keyframes splashParticleFloat {
            0%,100% { opacity: 0;    transform: translateY(0)      scale(0.5); }
            20%      { opacity: 0.55; }
            80%      { opacity: 0.2; }
            50%      { opacity: 0.4; transform: translateY(-110px) scale(1);   }
        }

        /* Logo wrapper — bounces in */
        .splash-logo-wrap {
            position: relative;
            opacity: 0;
            animation: splashLogoIn 0.7s 0.2s cubic-bezier(0.34, 1.4, 0.64, 1) forwards;
            margin-bottom: 1.75rem;
        }
        @keyframes splashLogoIn {
            from { opacity: 0; transform: scale(0.5) translateY(20px); }
            to   { opacity: 1; transform: scale(1)   translateY(0);    }
        }

        /* Logo box */
        .splash-logo-box {
            width: 96px;
            height: 96px;
            background: white;
            border-radius: 28px;
            padding: 12px;
            position: relative;
            z-index: 1;
            animation: splashLogoPulse 2.2s 0.9s ease-in-out infinite;
        }
        .splash-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        /* Gloss overlay */
        .splash-logo-box::after {
            content: '';
            position: absolute; inset: 0;
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(255,255,255,0.30) 0%, transparent 55%);
            pointer-events: none;
        }
        @keyframes splashLogoPulse {
            0%,100% { box-shadow: 0 0 0 0   rgba(232,184,75,0.45); }
            50%      { box-shadow: 0 0 0 20px rgba(232,184,75,0);   }
        }

        /* Orbiting dot */
        .splash-orbit {
            position: absolute;
            width: 132px; height: 132px;
            border-radius: 50%;
            border: 1.5px dashed rgba(232,184,75,0.45);
            top: -18px; left: -18px;
            animation: splashOrbit 4s linear infinite;
        }
        .splash-orbit-dot {
            position: absolute;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #e8b84b;
            top: -4px; left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 0 8px rgba(232,184,75,0.9);
        }
        @keyframes splashOrbit {
            from { transform: rotate(0deg);   }
            to   { transform: rotate(360deg); }
        }

        /* App name */
        .splash-text {
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: 1.85rem;
            font-weight: 700;
            letter-spacing: 1px;
            opacity: 0;
            animation: splashSlideUp 0.6s 0.55s cubic-bezier(0.22,1,0.36,1) forwards;
            margin-bottom: 0.3rem;
        }

        /* Tagline */
        .splash-sub {
            color: rgba(255,255,255,0.6);
            font-size: 0.78rem;
            font-weight: 400;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            opacity: 0;
            animation: splashSlideUp 0.6s 0.75s cubic-bezier(0.22,1,0.36,1) forwards;
            margin-bottom: 2rem;
        }
        @keyframes splashSlideUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0);    }
        }

        /* Progress bar */
        .splash-bar-track {
            width: 160px; height: 3px;
            background: rgba(255,255,255,0.15);
            border-radius: 99px;
            overflow: hidden;
            opacity: 0;
            animation: splashSlideUp 0.4s 1s forwards;
        }
        .splash-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #e8b84b, #f4d080);
            border-radius: 99px;
            width: 0%;
            animation: splashBarFill 1.6s 1s cubic-bezier(0.4,0,0.2,1) forwards;
        }
        @keyframes splashBarFill {
            0%   { width: 0%;   }
            60%  { width: 75%;  }
            85%  { width: 88%;  }
            100% { width: 100%; }
        }

        /* Loading dots */
        .splash-dots {
            display: flex;
            gap: 6px;
            margin-top: 0.9rem;
            opacity: 0;
            animation: splashSlideUp 0.4s 1.05s forwards;
        }
        .splash-dot-ind {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            animation: splashDotBlink 1.2s 1.1s ease-in-out infinite;
        }
        .splash-dot-ind:nth-child(2) { animation-delay: 1.25s; }
        .splash-dot-ind:nth-child(3) { animation-delay: 1.40s; }
        @keyframes splashDotBlink {
            0%,100% { background: rgba(255,255,255,0.3); transform: scale(1);   }
            50%      { background: rgba(255,255,255,0.9); transform: scale(1.4); }
        }

        /* ── Content panels hidden until splash ends ── */
        .left-panel, .right-panel {
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        .content-visible .left-panel,
        .content-visible .right-panel {
            opacity: 1;
        }

        /* ═══════════════════════════════════════════
           LEFT PANEL
        ═══════════════════════════════════════════ */
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

        .feature-chip i { color: var(--accent); font-size: 0.9rem; }

        .deco-circle {
            position: absolute;
            top: -80px; right: -80px;
            width: 340px; height: 340px;
            border-radius: 50%;
            border: 60px solid rgba(232,184,75,0.12);
            z-index: 2;
        }
        .deco-circle-sm {
            position: absolute;
            top: 160px; right: -40px;
            width: 180px; height: 180px;
            border-radius: 50%;
            border: 30px solid rgba(255,255,255,0.07);
            z-index: 2;
        }

        /* ═══════════════════════════════════════════
           RIGHT PANEL
        ═══════════════════════════════════════════ */
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
            bottom: -120px; right: -120px;
            width: 380px; height: 380px;
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
            to   { opacity: 1; transform: translateY(0);    }
        }

        /* Logo */
        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 2.2rem;
        }

        .logo-img {
            width: 54px; height: 54px;
            border-radius: 16px;
            background: var(--navy);
            padding: 4px;
            box-shadow: 0 6px 18px rgba(15,60,145,0.25);
            object-fit: contain;
        }

        .logo-text { line-height: 1.1; }

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
            left: 1rem; top: 50%;
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

        .field-wrap:focus-within .field-icon { color: var(--navy); }

        .field-wrap .toggle-pw {
            position: absolute;
            right: 1rem; top: 50%;
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
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, transparent 60%);
            pointer-events: none;
        }

        .btn-login:hover {
            background: var(--navy-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(15,60,145,0.30);
        }
        .btn-login:active { transform: translateY(0); }
        .btn-login.disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

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

        /* Student toast */
        #student-toast {
            display: none;
            position: fixed;
            bottom: 2rem; left: 50%;
            transform: translateX(-50%) translateY(20px);
            z-index: 999;
            background: #fff;
            border-left: 4px solid var(--navy);
            border-radius: 12px;
            box-shadow: 0 12px 36px rgba(0,0,0,0.15);
            padding: 1rem 1.4rem;
            min-width: 300px; max-width: 380px;
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
            width: 36px; height: 36px;
            background: rgba(15,60,145,0.10);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: var(--navy);
            font-size: 1rem;
        }
        #student-toast .toast-body strong {
            display: block;
            font-size: 0.9rem; color: #111827;
            margin-bottom: 0.15rem;
        }
        #student-toast .toast-body span {
            font-size: 0.8rem; color: var(--text-muted);
        }
        #student-toast .toast-close {
            margin-left: auto; flex-shrink: 0;
            background: none; border: none;
            color: #9ca3af; cursor: pointer;
            font-size: 1rem; padding: 0;
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

        /* ═══════════════════════════════════════════
           LOADING OVERLAY (on form submit)
        ═══════════════════════════════════════════ */
        #loginLoader {
            display: none;
            position: fixed; inset: 0;
            z-index: 100000;
            background: rgba(5, 15, 50, 0.75);
            backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
        }
        .loader-card {
            background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%);
            border-radius: 28px;
            padding: 2rem 2.5rem;
            text-align: center;
            min-width: 240px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.4);
        }
        .loader-logo-ring {
            position: relative;
            width: 70px; height: 70px;
            margin: 0 auto;
        }
        .loader-logo-ring img {
            width: 70px; height: 70px;
            border-radius: 50%;
            background: white;
            padding: 6px;
            object-fit: contain;
        }
        .loader-spinner {
            position: absolute; inset: -5px;
            border-radius: 50%;
            border: 3px solid transparent;
            border-top-color: #f4b400;
            border-right-color: rgba(244,180,0,0.3);
            animation: loaderSpin 0.85s linear infinite;
        }
        .loader-text  { color: white; font-weight: 600; margin-top: 1rem; }
        .loader-subtext { color: rgba(255,255,255,0.6); font-size: 0.85rem; }
        .loader-bar-track {
            width: 140px; height: 4px;
            background: rgba(255,255,255,0.2);
            border-radius: 99px; overflow: hidden;
            margin: 0.75rem auto 0;
        }
        .loader-bar-fill {
            height: 100%;
            background: #f4b400;
            border-radius: 99px;
            animation: loaderBar 1.1s ease-in-out infinite alternate;
        }
        @keyframes loaderSpin { to { transform: rotate(360deg); } }
        @keyframes loaderBar {
            from { width: 15%; margin-left: 0;   }
            to   { width: 70%; margin-left: 30%; }
        }

        /* ═══════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════ */
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

    <!-- ══ SPLASH SCREEN ═══════════════════════════════ -->
    <div id="splash-screen">
        <div class="splash-ring"></div>
        <div class="splash-ring"></div>
        <div class="splash-ring"></div>
        <div id="splash-particles"></div>

        <div class="splash-logo-wrap">
            <div class="splash-logo-box">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay">
            </div>
            <div class="splash-orbit">
                <div class="splash-orbit-dot"></div>
            </div>
        </div>

        <div class="splash-text">Non-UniPay</div>
        <div class="splash-sub">Secure Payments · Smart Clearance</div>
        <div class="splash-bar-track">
            <div class="splash-bar-fill" id="splashBarFill"></div>
        </div>
        <div class="splash-dots">
            <div class="splash-dot-ind"></div>
            <div class="splash-dot-ind"></div>
            <div class="splash-dot-ind"></div>
        </div>
    </div>

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

            <div class="logo-wrap">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay Logo" class="logo-img">
                <div class="logo-text">
                    <span>Non-UniPay</span>
                    <small>Fee Payment &amp; Exam Clearance</small>
                </div>
            </div>

            <h1 class="welcome-heading">Welcome back</h1>
            <p class="welcome-sub">Sign in to your account to continue</p>

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

                <button type="submit" class="btn-login" id="loginBtn">
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

    <!-- ══ STUDENT TOAST ════════════════════════════════ -->
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

    <!-- ══ LOGIN LOADING OVERLAY ════════════════════════ -->
    <div id="loginLoader">
        <div class="loader-card">
            <div class="loader-logo-ring">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay">
                <div class="loader-spinner"></div>
            </div>
            <p class="loader-text">Signing in...</p>
            <p class="loader-subtext">Please wait</p>
            <div class="loader-bar-track">
                <div class="loader-bar-fill"></div>
            </div>
        </div>
    </div>

    <script>
        // ══ SPLASH: generate floating particles ═══════════
        (function () {
            var container = document.getElementById('splash-particles');
            var colors = [
                'rgba(232,184,75,0.55)',
                'rgba(255,255,255,0.35)',
                'rgba(26,77,168,0.8)'
            ];
            for (var i = 0; i < 18; i++) {
                var p = document.createElement('div');
                p.className = 'splash-particle';
                var size = 3 + Math.random() * 5;
                p.style.cssText =
                    'width:'  + size + 'px;' +
                    'height:' + size + 'px;' +
                    'background:' + colors[i % 3] + ';' +
                    'left:'   + (5 + Math.random() * 90) + '%;' +
                    'bottom:' + (Math.random() * 45)     + '%;' +
                    '--pd:'   + (3 + Math.random() * 4)  + 's;' +
                    '--pp:'   + (Math.random() * 2)      + 's;';
                container.appendChild(p);
            }
        })();

        // ══ SPLASH: dismiss after progress bar completes ══
        var splash = document.getElementById('splash-screen');
        var body   = document.body;

        // Progress bar animation is 1s delay + 1.6s fill = ~2.7s total; dismiss at 2.8s
        setTimeout(function () {
            if (splash) {
                splash.classList.add('fade-out');
                setTimeout(function () {
                    splash.style.display = 'none';
                    body.classList.add('content-visible');
                }, 700);
            } else {
                body.classList.add('content-visible');
            }
        }, 2800);

        // ══ LOGIN FORM: show loader on submit ═════════════
        var loginForm   = document.getElementById('loginForm');
        var loginLoader = document.getElementById('loginLoader');
        var loginBtn    = document.getElementById('loginBtn');

        function showLoader() {
            if (loginLoader) loginLoader.style.display = 'flex';
            if (loginBtn) { loginBtn.disabled = true; loginBtn.classList.add('disabled'); }
        }

        function hideLoader() {
            if (loginLoader) loginLoader.style.display = 'none';
            if (loginBtn) { loginBtn.disabled = false; loginBtn.classList.remove('disabled'); }
        }

        if (loginForm) {
            loginForm.addEventListener('submit', function () {
                var email    = document.getElementById('emailInput').value.trim();
                var password = document.getElementById('password').value.trim();
                if (!email || !password) return;
                showLoader();
            });
        }

        window.addEventListener('pageshow', hideLoader);

        // ══ STUDENT TOAST ═════════════════════════════════
        var STUDENT_DOMAINS  = ['student.', 'stud.', 'stu.', '@s.'];
        var STUDENT_ID_PATT  = /^\d{7,12}@/;
        var toastTimeout;

        function checkStudentEmail(email) {
            var lower = email.toLowerCase();
            var looksLikeStudent =
                STUDENT_DOMAINS.some(function (d) { return lower.includes(d); }) ||
                STUDENT_ID_PATT.test(lower);
            if (looksLikeStudent) showToast();
            else hideToast();
        }

        function showToast() {
            clearTimeout(toastTimeout);
            var toast = document.getElementById('student-toast');
            toast.style.display = 'flex';
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { toast.classList.add('show'); });
            });
            toastTimeout = setTimeout(hideToast, 7000);
        }

        function hideToast() {
            var toast = document.getElementById('student-toast');
            toast.classList.remove('show');
            setTimeout(function () { toast.style.display = 'none'; }, 380);
        }

        function closeToast() {
            clearTimeout(toastTimeout);
            hideToast();
        }

        var debounce;
        document.getElementById('emailInput').addEventListener('input', function () {
            clearTimeout(debounce);
            var val = this.value.trim();
            debounce = setTimeout(function () {
                if (val.length > 4) checkStudentEmail(val);
                else hideToast();
            }, 500);
        });

        loginForm.addEventListener('submit', function () {
            checkStudentEmail(document.getElementById('emailInput').value.trim());
        });
    </script>
</body>
</html>