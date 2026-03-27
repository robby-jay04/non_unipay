<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful · Non-UniPay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f3c91 0%, #1a56c4 60%, #0a2e6f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Decorative blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }
        .blob-1 {
            width: 400px; height: 400px;
            background: rgba(244, 180, 20, 0.18);
            top: -120px; right: -80px;
        }
        .blob-2 {
            width: 350px; height: 350px;
            background: rgba(255, 255, 255, 0.07);
            bottom: -100px; left: -60px;
        }
        .blob-3 {
            width: 200px; height: 200px;
            background: rgba(244, 180, 20, 0.1);
            bottom: 60px; right: 40px;
        }

        .wrapper {
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 10;
            animation: fadeInUp 0.65s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 28px;
            padding: 44px 36px 36px;
            box-shadow:
                0 40px 80px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.4) inset;
            text-align: center;
        }

        /* Animated success icon */
        .icon-wrap {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 28px;
        }

        .icon-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid rgba(15, 60, 145, 0.15);
            animation: ringPulse 2s ease-out infinite;
        }

        .icon-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0f3c91, #1a56c4);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 16px 32px -8px rgba(15, 60, 145, 0.5);
            position: relative;
        }

        .icon-circle i {
            font-size: 44px;
            color: white;
        }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
            border: 1px solid rgba(25, 135, 84, 0.25);
            border-radius: 99px;
            padding: 5px 14px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .badge i { font-size: 10px; }

        h1 {
            font-size: 28px;
            font-weight: 800;
            color: #0f3c91;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .message {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.7;
            margin-bottom: 32px;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin-bottom: 28px;
        }

        /* Back to app button */
        .btn-app {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 15px 24px;
            background: linear-gradient(135deg, #f4b414, #f5c842);
            color: #0f3c91;
            font-size: 16px;
            font-weight: 800;
            border: none;
            border-radius: 14px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 24px -6px rgba(244, 180, 20, 0.55);
            letter-spacing: 0.2px;
            margin-bottom: 14px;
        }

        .btn-app:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 32px -6px rgba(244, 180, 20, 0.65);
            background: linear-gradient(135deg, #f5c842, #f4b414);
        }

        .btn-app:active { transform: translateY(0); }

        .btn-app .btn-icon {
            width: 32px;
            height: 32px;
            background: rgba(15, 60, 145, 0.12);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .btn-app .arrow {
            margin-left: auto;
            font-size: 14px;
            transition: transform 0.2s;
        }

        .btn-app:hover .arrow { transform: translateX(4px); }

        /* Footer note */
        .note {
            font-size: 12px;
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .note i { color: #d1d5db; }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes ringPulse {
            0%   { transform: scale(1);   opacity: 0.6; }
            70%  { transform: scale(1.35); opacity: 0; }
            100% { transform: scale(1.35); opacity: 0; }
        }

        @media (max-width: 480px) {
            .card { padding: 32px 22px 28px; }
            h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="wrapper">
        <div class="card">

            <div class="icon-wrap">
                <div class="icon-ring"></div>
                <div class="icon-circle">
                    <i class="fas fa-check"></i>
                </div>
            </div>

            <div class="badge">
                <i class="fas fa-circle"></i>
                Confirmed
            </div>

            <h1>Payment Successful!</h1>
            <p class="message">
                {{ $message ?? 'Your payment has been processed and confirmed. You\'re all set!' }}
            </p>

            <div class="divider"></div>

            <!-- Back to App Button -->
            <a href="nonunipay://payment-success" class="btn-app">
                <span class="btn-icon">
                    <i class="fas fa-mobile-alt"></i>
                </span>
                Back to Non-UniPay App
                <i class="fas fa-arrow-right arrow"></i>
            </a>

            <p class="note">
                <i class="fas fa-lock"></i>
                If the app doesn't open, you may safely close this tab.
            </p>

        </div>
    </div>
</body>
</html>