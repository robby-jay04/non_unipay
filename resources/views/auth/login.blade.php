<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · Non-UniPay</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}">
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
            transition: flex 0.6s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.5s ease;
        }
        .left-panel.expanded {
            flex: 0 0 100%;
            overflow: visible;
        }

        .left-panel .bg-cover {
            position: absolute;
            inset: 0;
            background: url("{{ asset('bg.png') }}") center/cover no-repeat;
            transition: background-position 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .left-panel.expanded .bg-cover {
            background-position: center top;
            background-size: 100% 55%;
            background-repeat: no-repeat;
            background-color: #0a2a6b;
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
            transition: background 0.6s ease;
        }
        .left-panel.expanded .overlay {
            background: linear-gradient(
                135deg,
                rgba(10, 42, 107, 0.65) 0%,
                rgba(15, 60, 145, 0.35) 60%,
                rgba(232, 184, 75, 0.12) 100%
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
            transition: opacity 0.25s ease, padding 0.6s ease;
        }
        .left-panel.expanded .panel-content {
            opacity: 0;
            pointer-events: none;
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

        /* ── EXPANDED FULLSCREEN SCROLLABLE ABOUT PAGE ── */
        .expanded-center {
            position: absolute;
            inset: 0;
            z-index: 10;
            overflow-y: auto;
            overflow-x: hidden;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.4s 0.15s ease;
            scroll-behavior: smooth;
        }
        .expanded-center::-webkit-scrollbar { width: 4px; }
        .expanded-center::-webkit-scrollbar-track { background: transparent; }
        .expanded-center::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 99px; }

        .left-panel.expanded .expanded-center {
            opacity: 1;
            pointer-events: all;
        }

        .about-hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            padding: 2.5rem 4rem 4rem;
            text-align: center;
        }
        .about-hero .exp-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.30);
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            color: white;
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            backdrop-filter: blur(8px);
            margin-bottom: 1.2rem;
        }
        .about-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 4.5vw, 3.6rem);
            font-weight: 800;
            color: white;
            line-height: 1.12;
            margin-bottom: 1rem;
        }
        .about-hero h1 em { font-style: italic; color: var(--accent); }
        .about-hero p {
            color: rgba(255,255,255,0.72);
            font-size: 1rem;
            max-width: 560px;
            line-height: 1.75;
            margin-bottom: 2rem;
        }
        .about-hero .hero-chips {
            display: flex; gap: 0.8rem; flex-wrap: wrap; justify-content: center;
            margin-bottom: 2rem;
        }
        .about-hero .hero-chip {
            display: flex; align-items: center; gap: 0.5rem;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 10px; padding: 0.5rem 1rem;
            color: white; font-size: 0.82rem; font-weight: 500;
        }
        .about-hero .hero-chip-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--accent); flex-shrink: 0;
        }
        .exp-signin-btn {
            display: inline-flex; align-items: center; gap: 0.6rem;
            background: white; color: var(--navy); border: none;
            border-radius: 14px; padding: 0.85rem 2.5rem;
            font-family: 'DM Sans', sans-serif; font-size: 0.95rem;
            font-weight: 600; cursor: pointer;
            box-shadow: 0 6px 24px rgba(0,0,0,0.22);
            transition: transform 0.15s, box-shadow 0.2s;
        }
        .exp-signin-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 36px rgba(0,0,0,0.28); }
        .exp-signin-btn:active { transform: translateY(0); }

        .about-body {
            background: #07193d;
            padding: 4rem 0 3rem;
        }

        .about-section {
            max-width: 860px;
            margin: 0 auto;
            padding: 0 3rem 3.5rem;
        }

        .about-section-label {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 0.6rem;
        }
        .about-section h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
            line-height: 1.25;
        }
        .about-section p {
            color: rgba(255,255,255,0.65);
            font-size: 0.95rem;
            line-height: 1.8;
        }

        .about-divider {
            max-width: 860px;
            margin: 0 auto 3.5rem;
            padding: 0 3rem;
        }
        .about-divider hr {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-top: 1.8rem;
        }
        .stat-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 16px;
            padding: 1.2rem 1rem;
            text-align: center;
        }
        .stat-card .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--accent);
            display: block;
            line-height: 1;
            margin-bottom: 0.35rem;
        }
        .stat-card .stat-label {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.5);
            font-weight: 400;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-top: 1.8rem;
        }
        .feature-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 16px;
            padding: 1.4rem;
            transition: background 0.2s;
        }
        .feature-card:hover { background: rgba(255,255,255,0.07); }
        .feature-card .fc-icon {
            width: 40px; height: 40px;
            border-radius: 12px;
            background: rgba(232,184,75,0.15);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
            color: var(--accent);
            font-size: 1rem;
        }
        .feature-card h3 {
            font-size: 0.92rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.4rem;
        }
        .feature-card p {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.65;
        }

        .tech-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        .tech-pill {
            display: flex; align-items: center; gap: 0.5rem;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            padding: 0.55rem 1rem;
            color: rgba(255,255,255,0.8);
            font-size: 0.82rem;
            font-weight: 500;
        }
        .tech-pill i { color: var(--accent); font-size: 0.85rem; }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-top: 1.8rem;
        }
        .team-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 20px;
            padding: 1.6rem 1rem 1.2rem;
            text-align: center;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
        }
        .team-card:hover {
            background: rgba(255,255,255,0.08);
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.25);
        }

        .team-avatar-wrap {
            position: relative;
            width: 72px; height: 72px;
            margin: 0 auto 1rem;
        }
        .team-avatar-img {
            width: 72px; height: 72px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .team-avatar-img::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.18) 0%, transparent 55%);
        }
        .team-initials {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 800;
            color: white;
            position: relative;
            z-index: 1;
            letter-spacing: 0.02em;
        }
        .team-avatar-ring {
            position: absolute; inset: -3px;
            border-radius: 50%;
            border: 2px solid rgba(232,184,75,0.4);
            pointer-events: none;
        }
        .team-role-badge {
            position: absolute;
            bottom: -2px; right: -2px;
            width: 24px; height: 24px;
            border-radius: 50%;
            background: #07193d;
            border: 2px solid rgba(232,184,75,0.5);
            display: flex; align-items: center; justify-content: center;
            color: var(--accent);
            font-size: 0.6rem;
        }

        .team-card h3 {
            font-size: 0.88rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.2rem;
            line-height: 1.3;
        }
        .team-card .team-role {
            font-size: 0.74rem;
            color: var(--accent);
            font-weight: 500;
            margin-bottom: 0.2rem;
        }
        .team-card .team-dept {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.38);
            margin-bottom: 0.75rem;
        }
        .team-skills {
            display: flex; flex-wrap: wrap; gap: 0.35rem; justify-content: center;
        }
        .team-skills span {
            font-size: 0.65rem;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 6px;
            padding: 0.2rem 0.5rem;
            color: rgba(255,255,255,0.55);
        }

        .team-avatar-img {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .team-avatar-img img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .about-footer {
            background: #040f25;
            padding: 2rem 3rem 10rem;
            text-align: center;
        }
        .about-footer p {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.3);
            line-height: 1.7;
        }
        .about-footer strong {
            color: rgba(255,255,255,0.55);
            font-weight: 500;
        }

        /* ── TOGGLE ARROW BUTTON ── */
        .toggle-btn {
            position: absolute;
            top: 50%;
            right: -18px;
            transform: translateY(-50%);
            z-index: 50;
            width: 36px;
            height: 36px;
            background: white;
            border: none;
            border-radius: 50%;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.18);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, box-shadow 0.2s;
            flex-shrink: 0;
        }
        .toggle-btn:hover {
            background: #f0f3fa;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.22);
        }
        .toggle-btn:active {
            transform: translateY(-50%) scale(0.95);
        }
        .toggle-btn svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: var(--navy);
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform 0.55s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
        }
        .left-panel.expanded .toggle-btn {
            right: 24px;
        }
        .left-panel.expanded .toggle-btn svg {
            transform: rotate(180deg);
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
            transition: flex 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                        opacity 0.35s ease,
                        padding 0.6s ease;
        }
        .right-panel.hidden {
            flex: 0 0 0%;
            opacity: 0;
            pointer-events: none;
            padding: 0;
            overflow: hidden;
            min-width: 0;
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
            padding: 0.85rem 3rem 0.85rem 2.8rem;
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

        /* Password toggle button - hidden on desktop by default, shown on mobile */
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
            width: 36px;
            height: 36px;
            display: none; /* hidden on desktop/large screens */
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            -webkit-tap-highlight-color: transparent;
        }
        .field-wrap .toggle-pw:hover { color: var(--navy); }
        .field-wrap .toggle-pw:active { background: rgba(15,60,145,0.08); color: var(--navy); }

        /* Only visible on mobile screens */
        @media (max-width: 768px) {
            .field-wrap .toggle-pw {
                display: flex;
            }
        }

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

        /* password strength indicators */
        .pw-bar {
            flex: 1;
            height: 4px;
            border-radius: 99px;
            background: #e5e7eb;
            transition: background 0.3s;
        }
        .pw-bar.active-weak   { background: #ef4444; }
        .pw-bar.active-fair   { background: #f59e0b; }
        .pw-bar.active-good   { background: #3b82f6; }
        .pw-bar.active-strong { background: #10b981; }

        input.input-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 4px rgba(239,68,68,0.10) !important;
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

        /* LOGIN LOADING OVERLAY */
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

        /* Responsive */
        @media (max-width: 860px) {
            body { flex-direction: column; overflow: auto; }
            .left-panel { flex: 0 0 240px !important; min-height: 240px; }
            .left-panel.expanded { flex: 0 0 100vh !important; }
            .left-panel .panel-content { padding: 2rem; }
            .left-panel h2 { font-size: 1.7rem; }
            .features-row { display: none; }
            .right-panel { padding: 2rem 1.5rem; }
            .right-panel.hidden { flex: 0 0 0 !important; max-height: 0; }
            .toggle-btn { right: 50%; transform: translateX(50%) rotate(-90deg); bottom: -18px; top: auto; }
            .left-panel.expanded .toggle-btn { bottom: 24px; right: 50%; top: auto; transform: translateX(50%) rotate(90deg); }
        }
        @media (max-width: 480px) {
            .left-panel { flex: 0 0 180px !important; min-height: 180px; }
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
        .char-counter {
            text-align: right;
            font-size: 0.72rem;
            color: #b0b7c3;
            margin-top: 3px;
            margin-bottom: 0.35rem;
            transition: color 0.2s;
            height: 14px;
        }
        .char-counter.warn  { color: #f59e0b; }
        .char-counter.danger { color: #ef4444; }
        input[maxlength] { padding-right: 3rem; }
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

        <!-- Normal bottom content -->
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

        <!-- Expanded fullscreen scrollable about page -->
        <div class="expanded-center" id="expandedCenter">

            <!-- HERO -->
            <div class="about-hero">
                <div class="exp-badge">
                    <i class="fas fa-university"></i>
                    Non-UniPay &nbsp;·&nbsp; School Management Portal
                </div>
                <h1>Smart Payments,<br><em>Seamless</em> Clearance.</h1>
                <p>
                    A centralized, secure platform for managing school fees, tracking student balances,
                    and issuing exam clearances — built specifically for higher education institutions.
                </p>
                <div class="hero-chips">
                    <div class="hero-chip"><div class="hero-chip-dot"></div> Secure Payments</div>
                    <div class="hero-chip"><div class="hero-chip-dot"></div> Instant Clearance</div>
                    <div class="hero-chip"><div class="hero-chip-dot"></div> Real-time Alerts</div>
                    <div class="hero-chip"><div class="hero-chip-dot"></div> Smart Dashboard</div>
                </div>
                <button class="exp-signin-btn" onclick="togglePanel()">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In to Your Account
                </button>
            </div>

            <!-- ABOUT BODY -->
            <div class="about-body">

                <!-- System Overview -->
                <div class="about-section">
                    <div class="about-section-label">About the System</div>
                    <h2>What is Non-UniPay?</h2>
                    <p>
                        Non-UniPay is a web-based school fee management and exam clearance system designed to digitize and
                        streamline the entire payment workflow in academic institutions. It replaces manual, paper-based
                        processes with a fast, transparent, and auditable digital platform — accessible to administrators,
                        cashiers, registrars, and department heads from a single secure portal.
                    </p>
                    <p style="margin-top:0.9rem;">
                        Students settle their balances through the companion mobile app, while staff and administrators
                        manage records, approve clearances, generate reports, and monitor payment statuses in real time
                        through this web portal.
                    </p>

                    <div class="about-stats">
                        <div class="stat-card">
                            <span class="stat-num">100%</span>
                            <span class="stat-label">Paperless Workflow</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-num">∞</span>
                            <span class="stat-label">Concurrent Users</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-num">24/7</span>
                            <span class="stat-label">System Availability</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-num">AES</span>
                            <span class="stat-label">Encrypted Data</span>
                        </div>
                    </div>
                </div>

                <div class="about-divider"><hr></div>

                <!-- Key Features -->
                <div class="about-section">
                    <div class="about-section-label">Core Features</div>
                    <h2>Everything you need in one place</h2>
                    <div class="feature-grid">
                        <div class="feature-card">
                            <div class="fc-icon"><i class="fas fa-money-check-alt"></i></div>
                            <h3>Fee Management</h3>
                            <p>Create, assign, and track tuition fees, miscellaneous charges, and payment schemes per student or per course.</p>
                        </div>
                        <div class="feature-card">
                            <div class="fc-icon"><i class="fas fa-clipboard-check"></i></div>
                            <h3>Exam Clearance</h3>
                            <p>Automatically issue or withhold exam clearances based on outstanding balances — no manual checking required.</p>
                        </div>
                        <div class="feature-card">
                            <div class="fc-icon"><i class="fas fa-chart-bar"></i></div>
                            <h3>Reports & Analytics</h3>
                            <p>Generate daily, monthly, and semester-end financial reports with exportable PDFs and Excel summaries.</p>
                        </div>
                        <div class="feature-card">
                            <div class="fc-icon"><i class="fas fa-bell"></i></div>
                            <h3>Real-time Notifications</h3>
                            <p>Instant push and in-app alerts for payment confirmations, clearance approvals, and balance reminders.</p>
                        </div>
                        <div class="feature-card">
                            <div class="fc-icon"><i class="fas fa-users-cog"></i></div>
                            <h3>Role-based Access</h3>
                            <p>Granular permissions for Super Admin, Admin (Cashier, Registrar, and Department Head roles).</p>
                        </div>
                        <div class="feature-card">
                            <div class="fc-icon"><i class="fas fa-history"></i></div>
                            <h3>Audit Trail</h3>
                            <p>Full activity logs for every transaction, clearance action, and user login — tamper-proof and searchable.</p>
                        </div>
                    </div>
                </div>

                <div class="about-divider"><hr></div>

                <!-- Tech Stack -->
                <div class="about-section">
                    <div class="about-section-label">Technology</div>
                    <h2>Built with modern, reliable tools</h2>
                    <p>Non-UniPay is engineered using a robust, industry-standard stack chosen for performance, security, and maintainability.</p>
                    <div class="tech-grid">
                        <div class="tech-pill"><i class="fab fa-laravel"></i> Laravel 11</div>
                        <div class="tech-pill"><i class="fab fa-php"></i> PHP 8.3</div>
                        <div class="tech-pill"><i class="fas fa-database"></i> MySQL 8</div>
                        <div class="tech-pill"><i class="fab fa-bootstrap"></i> Bootstrap 5</div>
                        <div class="tech-pill"><i class="fab fa-js"></i> Vanilla JS</div>
                        <div class="tech-pill"><i class="fas fa-mobile-alt"></i> Flutter (Mobile)</div>
                        <div class="tech-pill"><i class="fas fa-server"></i> Apache / Nginx</div>
                        <div class="tech-pill"><i class="fab fa-git-alt"></i> Git / GitHub</div>
                        <div class="tech-pill"><i class="fas fa-shield-alt"></i> Laravel Sanctum</div>
                        <div class="tech-pill"><i class="fas fa-file-pdf"></i> DomPDF</div>
                        <div class="tech-pill"><i class="fas fa-envelope"></i> Laravel Mail</div>
                        <div class="tech-pill"><i class="fas fa-bell"></i> Firebase FCM</div>
                    </div>
                </div>

                <div class="about-divider"><hr></div>

                <!-- Development Team -->
                <div class="about-section">
                    <div class="about-section-label">Development Team</div>
                    <h2>The people behind Non-UniPay</h2>
                    <p>Developed by a dedicated team of IT students committed to solving real problems in institutional finance management.</p>
                    <div class="team-grid">

                        <!-- Robby Jay Ibale -->
                        <div class="team-card">
                            <div class="team-avatar-wrap">
                                <div class="team-avatar-img" style="background: linear-gradient(135deg, #0f3c91, #1a4da8);">
                                    <img src="{{ asset('images/robby.jpg') }}" alt="Robby Jay Ibale" onerror="this.style.display='none'">
                                    <div class="team-avatar-ring"></div>
                                </div>
                                <div class="team-role-badge"><i class="fas fa-code"></i></div>
                            </div>
                            <h3>Robby Jay Ibale</h3>
                            <div class="team-role">Full Stack Developer</div>
                            <div class="team-dept">Frontend &amp; Backend</div>
                            <div class="team-skills">
                                <span>Laravel</span><span>JavaScript</span><span>MySQL</span><span>ReactNative</span>
                            </div>
                        </div>

                        <!-- James Cuso -->
                        <div class="team-card">
                            <div class="team-avatar-wrap">
                                <div class="team-avatar-img" style="background: linear-gradient(135deg, #1a6b3c, #2a9a58);">
                                    <img src="{{ asset('images/james.jpg') }}" alt="James Cuso" onerror="this.style.display='none'">
                                    <div class="team-avatar-ring"></div>
                                </div>
                                <div class="team-role-badge"><i class="fas fa-bug"></i></div>
                            </div>
                            <h3>James Cuso</h3>
                            <div class="team-role">QA Tester</div>
                            <div class="team-dept">Quality Assurance</div>
                            <div class="team-skills">
                                <span>Testing</span><span>Bug Reports</span>
                            </div>
                        </div>

                        <!-- Khey Marie Jardenero -->
                        <div class="team-card">
                            <div class="team-avatar-wrap">
                                <div class="team-avatar-img" style="background: linear-gradient(135deg, #7b1fa2, #ab47bc);">
                                    <img src="{{ asset('images/khey.jpg') }}" alt="Khey Marie Jardenero" onerror="this.style.display='none'">
                                    <div class="team-avatar-ring"></div>
                                </div>
                                <div class="team-role-badge"><i class="fas fa-paint-brush"></i></div>
                            </div>
                            <h3>Khey Marie Jardenero</h3>
                            <div class="team-role">UI/UX Designer</div>
                            <div class="team-dept">Interface &amp; Experience</div>
                            <div class="team-skills">
                                <span>Figma</span><span>Prototyping</span>
                            </div>
                        </div>

                        <!-- Ricianin Bontog -->
                        <div class="team-card">
                            <div class="team-avatar-wrap">
                                <div class="team-avatar-img" style="background: linear-gradient(135deg, #b8600a, #e8a020);">
                                    <img src="{{ asset('images/ricianin.jpg') }}" alt="Ricianin Bontog" onerror="this.style.display='none'">
                                    <div class="team-avatar-ring"></div>
                                </div>
                                <div class="team-role-badge"><i class="fas fa-file-alt"></i></div>
                            </div>
                            <h3>Ricianin Bontog</h3>
                            <div class="team-role">Documentation</div>
                            <div class="team-dept">Technical Writing</div>
                            <div class="team-skills">
                                <span>SRS</span><span>User Manuals</span>
                            </div>
                        </div>

                        <!-- Novy Mapute -->
                        <div class="team-card">
                            <div class="team-avatar-wrap">
                                <div class="team-avatar-img" style="background: linear-gradient(135deg, #c62828, #ef5350);">
                                    <img src="{{ asset('images/novy.jpg') }}" alt="Novy Mapute" onerror="this.style.display='none'">
                                    <div class="team-avatar-ring"></div>
                                </div>
                                <div class="team-role-badge"><i class="fas fa-file-alt"></i></div>
                            </div>
                            <h3>Novy Mapute</h3>
                            <div class="team-role">Documentation</div>
                            <div class="team-dept">Technical Writing</div>
                            <div class="team-skills">
                                <span>Reports</span><span>Research</span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="about-divider"><hr></div>

                <!-- Version & Info -->
                <div class="about-section" style="padding-bottom:1rem;">
                    <div class="about-section-label">Release Info</div>
                    <h2>System Version</h2>
                    <div class="tech-grid">
                        <div class="tech-pill"><i class="fas fa-code-branch"></i> Version 1.0.0</div>
                        <div class="tech-pill"><i class="fas fa-calendar-alt"></i> Released {{ date('Y') }}</div>
                        <div class="tech-pill"><i class="fas fa-university"></i> Academic System</div>
                        <div class="tech-pill"><i class="fas fa-lock"></i> Staff & Admin Only</div>
                    </div>
                </div>

            </div><!-- /.about-body -->

            <!-- Footer -->
            <div class="about-footer">
                <p>
                    <strong>Non-UniPay</strong> &copy; {{ date('Y') }} &nbsp;·&nbsp;
                    Fee Payment &amp; Exam Clearance System &nbsp;·&nbsp;
                    Staff &amp; Admin Portal<br>
                    <span style="font-size:0.72rem;">Students must use the Non-UniPay mobile app to access their accounts.</span>
                </p>
            </div>

        </div><!-- /.expanded-center -->

        <!-- ══ TOGGLE ARROW BUTTON ══ -->
        <button class="toggle-btn" id="toggleBtn" onclick="togglePanel()" title="Toggle login form">
            <svg viewBox="0 0 24 24">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
    </div>

    <!-- ══ RIGHT PANEL ══════════════════════════════════ -->
    <div class="right-panel" id="rightPanel">
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
    maxlength="50"
>
                </div>

                <!-- ✅ FIX: toggle-pw button added here -->
                <div class="field-wrap" id="pwWrap">
    <i class="fas fa-lock field-icon"></i>
    <input
    type="password"
    name="password"
    id="password"
    placeholder="Password"
    required
    autocomplete="current-password"
    maxlength="20"
>
    <button type="button" class="toggle-pw" onclick="togglePassword()" tabindex="-1" aria-label="Toggle password visibility">
        <i class="fas fa-eye" id="pwEyeIcon"></i>
    </button>
</div>

<!-- Caps Lock warning -->
<div id="capsWarning" style="display:none; align-items:center; gap:0.4rem;
     background:#fffbeb; border:1px solid #fde68a; border-radius:10px;
     padding:0.5rem 0.85rem; font-size:0.8rem; color:#92400e; margin-top:-0.4rem; margin-bottom:0.6rem;">
    <i class="fas fa-exclamation-triangle" style="font-size:0.75rem;"></i>
    Caps Lock is on
</div>

<!-- Strength meter -->
<div id="pwStrengthWrap" style="display:none; margin-top:-0.4rem; margin-bottom:0.8rem;">
    <div style="display:flex; gap:4px; margin-bottom:4px;">
        <div class="pw-bar" id="pwBar1"></div>
        <div class="pw-bar" id="pwBar2"></div>
        <div class="pw-bar" id="pwBar3"></div>
        <div class="pw-bar" id="pwBar4"></div>
    </div>
    <span id="pwStrengthLabel" style="font-size:0.75rem; color:#6b7280;"></span>
</div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt me-2"></i> Sign In
                </button>
            </form>

            @if($errors->any())
                <div class="error-msg" id="loginErrorMsg">
                    <i class="fas fa-exclamation-circle" style="flex-shrink:0;"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @elseif(session('error'))
                <div class="error-msg" id="loginErrorMsg">
                    <i class="fas fa-exclamation-circle" style="flex-shrink:0;"></i>
                    <span>{{ session('error') }}</span>
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

        // ══ PANEL TOGGLE ══════════════════════════════════
        var panelExpanded = false;

        function togglePanel() {
            panelExpanded = !panelExpanded;
            var leftPanel  = document.getElementById('leftPanel');
            var rightPanel = document.getElementById('rightPanel');

            if (panelExpanded) {
                leftPanel.classList.add('expanded');
                rightPanel.classList.add('hidden');
            } else {
                leftPanel.classList.remove('expanded');
                rightPanel.classList.remove('hidden');
            }
        }

        // ══ PASSWORD TOGGLE ══════════════════════════════
         function togglePassword() {
            var pwInput = document.getElementById('password');
            var pwEyeIcon = document.getElementById('pwEyeIcon');
            if (pwInput.type === 'password') {
                pwInput.type = 'text';
                pwEyeIcon.classList.remove('fa-eye');
                pwEyeIcon.classList.add('fa-eye-slash');
            } else {
                pwInput.type = 'password';
                pwEyeIcon.classList.remove('fa-eye-slash');
                pwEyeIcon.classList.add('fa-eye');
            }
        }
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

        if (loginForm) {
            loginForm.addEventListener('submit', function () {
                checkStudentEmail(document.getElementById('emailInput').value.trim());
            });
        }

        // ══ KEEP RIGHT PANEL OPEN ON ERRORS ══════════════
        <?php if($errors->any() || session('error')): ?>
            (function () {
                // Show login panel immediately — skip splash
                var leftPanel  = document.getElementById('leftPanel');
                var rightPanel = document.getElementById('rightPanel');
                leftPanel.classList.remove('expanded');
                rightPanel.classList.remove('hidden');

                var splash = document.getElementById('splash-screen');
                if (splash) splash.style.display = 'none';
                document.body.classList.add('content-visible');

                // Highlight the input fields that failed
                <?php if($errors->has('email') || session('error')): ?>
                    var emailEl = document.getElementById('emailInput');
                    if (emailEl) emailEl.classList.add('input-error');
                <?php endif; ?>

                <?php if($errors->has('password')): ?>
                    var pwEl = document.getElementById('password');
                    if (pwEl) pwEl.classList.add('input-error');
                <?php endif; ?>

                // Remove red border as soon as the user starts typing again
                ['emailInput', 'password'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', function () {
                            this.classList.remove('input-error');
                        }, { once: true });
                    }
                });

                // Scroll error into view on mobile
                var errEl = document.getElementById('loginErrorMsg');
                if (errEl) {
                    setTimeout(function () {
                        errEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 100);
                }
            })();
        <?php endif; ?>
// ── CAPS LOCK DETECTION ──────────────────────────────
document.getElementById('password').addEventListener('keyup', function(e) {
    var capsOn = e.getModifierState && e.getModifierState('CapsLock');
    var warn = document.getElementById('capsWarning');
    warn.style.display = capsOn ? 'flex' : 'none';
});
document.getElementById('password').addEventListener('blur', function() {
    document.getElementById('capsWarning').style.display = 'none';
});

// ── PASSWORD STRENGTH METER ──────────────────────────
function getStrength(pw) {
    var score = 0;
    if (pw.length >= 8)  score++;
    if (pw.length >= 12) score++;
    if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    if (score <= 1) return { level: 1, label: 'Weak',      color: 'weak'   };
    if (score <= 2) return { level: 2, label: 'Fair',      color: 'fair'   };
    if (score <= 3) return { level: 3, label: 'Good',      color: 'good'   };
    return              { level: 4, label: 'Strong',    color: 'strong' };
}

document.getElementById('password').addEventListener('input', function() {
    var pw = this.value;
    var wrap = document.getElementById('pwStrengthWrap');
    if (!pw) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';

    var s = getStrength(pw);
    var colors = { weak: 'active-weak', fair: 'active-fair', good: 'active-good', strong: 'active-strong' };
    var cls = colors[s.color];

    [1,2,3,4].forEach(function(i) {
        var bar = document.getElementById('pwBar' + i);
        bar.className = 'pw-bar' + (i <= s.level ? ' ' + cls : '');
    });

    var labelColors = { weak:'#ef4444', fair:'#f59e0b', good:'#3b82f6', strong:'#10b981' };
    var labelEl = document.getElementById('pwStrengthLabel');
    labelEl.textContent = s.label + ' password';
    labelEl.style.color = labelColors[s.color];
});
// ── INPUT LIMIT COUNTERS ─────────────────────────────
function makeCounter(inputId, max, warnAt) {
    var input = document.getElementById(inputId);
    if (!input) return;

    var counter = document.createElement('div');
    counter.className = 'char-counter';
    counter.style.display = 'none';
    input.closest('.field-wrap').insertAdjacentElement('afterend', counter);

    input.addEventListener('input', function () {
        var len = this.value.length;
        var remaining = max - len;
        counter.style.display = len > 0 ? 'block' : 'none';
        counter.textContent = remaining + ' / ' + max + ' characters remaining';
        counter.className = 'char-counter' +
            (remaining <= 0         ? ' danger' :
             remaining <= warnAt    ? ' warn'   : '');
    });

    input.addEventListener('blur', function () {
        counter.style.display = 'none';
    });
    input.addEventListener('focus', function () {
        if (this.value.length > 0) counter.style.display = 'block';
    });
}

makeCounter('emailInput', 100, 20);
makeCounter('password',   64,  10);

    </script>
</body>
</html>