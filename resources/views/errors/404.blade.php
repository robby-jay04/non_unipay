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
           SPLASH SCREEN
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
            transition: opacity 0.5s ease;
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
            justify-content: center;
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

        .error-num {
            font-family: 'Playfair Display', serif;
            font-size: clamp(5rem, 14vw, 9rem);
            font-weight: 800;
            color: #fff;
            line-height: 0.9;
            letter-spacing: -4px;
            margin-bottom: 0.5rem;
            animation: fadeUp 0.7s 0.15s both;
        }
        .error-num em {
            font-style: italic;
            color: var(--accent);
        }

        .left-panel h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(1.1rem, 2.2vw, 1.6rem);
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
            animation: fadeUp 0.7s 0.25s both;
        }

        .left-panel p.tagline {
            color: rgba(255,255,255,0.75);
            font-size: 0.9rem;
            font-weight: 300;
            line-height: 1.7;
            max-width: 340px;
            margin-bottom: 2rem;
            animation: fadeUp 0.7s 0.35s both;
        }

        .chips {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            animation: fadeUp 0.7s 0.45s both;
        }

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

        .login-inner {
            width: 100%;
            max-width: 380px;
            animation: fadeUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0);    }
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            margin-bottom: 2.2rem;
        }

        .logo-box {
            width: 54px; height: 54px;
            border-radius: 16px;
            background: var(--navy);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem; font-weight: 800; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 6px 18px rgba(15,60,145,0.25);
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

        .icon-wrap {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: rgba(15,60,145,0.08);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem;
        }
        .icon-wrap svg {
            width: 26px; height: 26px;
            stroke: var(--navy); fill: none;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
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
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
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
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            text-decoration: none;
        }
        .btn-back:hover {
            border-color: #9ca3af;
            color: #374151;
            transform: translateY(-2px);
        }

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

        /* ═════════ LIGHT BEAMS EFFECT ═════════ */
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
            animation: beamMove 8s linear infinite;
            filter: blur(8px);
        }
        .light-beams span:nth-child(2) {
            animation-duration: 12s;
            animation-delay: 2s;
            opacity: 0.7;
        }
        .light-beams span:nth-child(3) {
            animation-duration: 10s;
            animation-delay: 4s;
            opacity: 0.5;
        }
        @keyframes beamMove {
            0%   { transform: translateX(-60%) rotate(25deg); }
            100% { transform: translateX(60%)  rotate(25deg); }
        }

        /* ═══════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════ */
        @media (max-width: 860px) {
            body { flex-direction: column; overflow: auto; }
            .left-panel { flex: 0 0 240px !important; min-height: 240px; }
            .left-panel .panel-content { padding: 2rem; }
            .left-panel h2 { font-size: 1.4rem; }
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
            <div class="splash-bar-fill"></div>
        </div>
        <div class="splash-dots">
            <div class="splash-dot-ind"></div>
            <div class="splash-dot-ind"></div>
            <div class="splash-dot-ind"></div>
        </div>
    </div>

    <!-- ══ LEFT PANEL ══════════════════════════════════ -->
    <div class="left-panel" id="leftPanel">
        <div class="bg-cover"></div>
        <div class="overlay"></div>

        <div class="light-beams">
            <span></span>
            <span></span>
            <span></span>
        </div>

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

    <!-- ══ RIGHT PANEL ══════════════════════════════════ -->
    <div class="right-panel" id="rightPanel">
        <div class="login-inner">

            <div class="logo-wrap">
                <img src="{{ asset('logo.png') }}" alt="Non-UniPay Logo" class="logo-img"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="logo-box" style="display:none;">N</div>
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

            <a href="{{ route('admin.dashboard') }}" class="btn-home">
                <i class="fas fa-home" style="flex-shrink:0;"></i>
                Go to Dashboard
            </a>

            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-chevron-left" style="flex-shrink:0;"></i>
                Go Back
            </a>

            <div class="form-divider"></div>
            <p class="footer-note">Non-UniPay &copy; {{ date('Y') }} &nbsp;·&nbsp; Staff &amp; Admin Portal</p>

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
    </script>
</body>
</html>