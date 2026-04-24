<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 · Non-UniPay</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
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
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            flex: 0 0 55%;
            position: relative;
            overflow: hidden;
        }

        .left-panel .bg-cover {
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, #0a2a6b 0%, #0f3c91 55%, #1a4da8 100%);
        }

        .left-panel .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(10,42,107,0.82) 0%,
                rgba(15,60,145,0.55) 50%,
                rgba(232,184,75,0.20) 100%
            );
        }

        .light-beams {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 2;
            pointer-events: none;
        }
        .light-beams span {
            position: absolute;
            width: 200%;
            height: 120%;
            top: -10%;
            left: -50%;
            background: linear-gradient(
                120deg,
                transparent 0%,
                rgba(255,255,255,0.08) 20%,
                rgba(232,184,75,0.12) 40%,
                transparent 70%
            );
            transform: rotate(25deg);
            filter: blur(8px);
        }

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
        .deco-circle-bot {
            position: absolute;
            bottom: -60px; left: -60px;
            width: 200px; height: 200px;
            border-radius: 50%;
            border: 40px solid rgba(255,255,255,0.05);
            z-index: 2;
        }

        .panel-content {
            position: relative;
            z-index: 5;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3.5rem;
        }

        .brand-badge {
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

        .error-num {
            font-family: 'Playfair Display', serif;
            font-size: clamp(5rem, 14vw, 9rem);
            font-weight: 800;
            color: #fff;
            line-height: 0.9;
            letter-spacing: -4px;
            margin-bottom: 0.5rem;
        }
        .error-num em { font-style: italic; color: var(--accent); }

        .panel-content h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(1.1rem, 2.2vw, 1.6rem);
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
        }

        .tagline {
            color: rgba(255,255,255,0.75);
            font-size: 0.9rem;
            font-weight: 300;
            line-height: 1.7;
            max-width: 340px;
            margin-bottom: 2rem;
        }

        .chips { display: flex; gap: 0.75rem; flex-wrap: wrap; }
        .chip {
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
        }
        .chip i { color: var(--accent); font-size: 0.9rem; }

        /* ── RIGHT PANEL ── */
        .right-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 3rem;
            background: var(--card-bg);
            position: relative;
            overflow: hidden;
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

        .login-inner { width: 100%; max-width: 380px; }

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
        .logo-box {
            width: 54px; height: 54px;
            border-radius: 16px;
            background: var(--navy);
            display: none;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 6px 18px rgba(15,60,145,0.25);
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

        .icon-wrap {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: rgba(15,60,145,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }
        .icon-wrap svg {
            width: 26px; height: 26px;
            stroke: var(--navy);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
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
            margin-bottom: 1.75rem;
            line-height: 1.6;
        }

        .btn-home {
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
            margin-bottom: 0.75rem;
            position: relative;
            overflow: hidden;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .btn-home::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, transparent 60%);
            pointer-events: none;
        }
        .btn-home:hover {
            background: var(--navy-light);
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(15,60,145,0.30);
            color: #fff;
        }
        .btn-home:active { transform: translateY(0); }

        .btn-back {
            width: 100%;
            background: transparent;
            border: 1.5px solid #e5e7eb;
            border-radius: 14px;
            padding: 0.85rem;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-muted);
            cursor: pointer;
            transition: border-color 0.2s, color 0.2s, transform 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .btn-back:hover {
            border-color: #9ca3af;
            color: #374151;
            transform: translateY(-2px);
        }

        .form-divider { height: 1px; background: #e5e7eb; margin: 1.75rem 0 1.25rem; }
        .footer-note { text-align: center; font-size: 0.78rem; color: #b0b7c3; }

        /* ── RESPONSIVE ── */
        @media (max-width: 860px) {
            body { flex-direction: column; overflow: auto; }
            .left-panel { flex: 0 0 240px !important; min-height: 240px; }
            .panel-content { padding: 2rem; }
            .panel-content h2 { font-size: 1.4rem; }
            .error-num { font-size: 5rem; }
            .right-panel { padding: 2rem 1.5rem; }
        }
        @media (max-width: 480px) {
            .left-panel { flex: 0 0 180px !important; min-height: 180px; }
            .error-num { font-size: 4rem; }
        }
    </style>
</head>
<body>

    <!-- LEFT PANEL -->
    <div class="left-panel">
        <div class="bg-cover"></div>
        <div class="overlay"></div>
        <div class="light-beams"><span></span><span></span><span></span></div>
        <div class="deco-circle"></div>
        <div class="deco-circle-sm"></div>
        <div class="deco-circle-bot"></div>

        <div class="panel-content">
            <div class="brand-badge">
                <i class="fas fa-university"></i>
                Non-UniPay Portal
            </div>
            <div class="error-num">4<em>0</em>4</div>
            <h2>Page Not Found</h2>
            <p class="tagline">
                The page you're looking for doesn't exist or has been moved. Let's get you back to a safe place.
            </p>
            <div class="chips">
                <div class="chip"><i class="fas fa-shield-alt"></i> Secure Portal</div>
                <div class="chip"><i class="fas fa-user-lock"></i> Admin Access</div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <div class="login-inner">

            <div class="logo-wrap">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay Logo" class="logo-img"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="logo-box">N</div>
                <div class="logo-text">
                    <span>Non-UniPay</span>
                    <small>Fee Payment &amp; Exam Clearance</small>
                </div>
            </div>

            <div class="icon-wrap">
                <svg viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    <line x1="11" y1="8" x2="11" y2="14"/>
                    <line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
            </div>

            <h1 class="welcome-heading">Oops, lost?</h1>
            <p class="welcome-sub">
                This page isn't part of the system. It may have been removed, renamed, or you may have mistyped the URL.
            </p>

          

            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-chevron-left"></i>
                Go Back
            </a>

            <div class="form-divider"></div>
            <p class="footer-note">Non-UniPay &copy; {{ date('Y') }} &nbsp;·&nbsp; Staff &amp; Admin Portal</p>

        </div>
    </div>

</body>
</html>