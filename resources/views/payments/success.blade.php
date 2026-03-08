<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful · Non-UniPay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome for icon (optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            min-height: 100vh;
            background: linear-gradient(145deg, #0f3c91 0%, #1a4da8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* decorative background blobs */
        .blob {
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(244, 180, 20, 0.15);
            border-radius: 50%;
            filter: blur(70px);
            z-index: 0;
        }
        .blob-1 {
            top: -100px;
            right: -50px;
        }
        .blob-2 {
            bottom: -80px;
            left: -30px;
            background: rgba(255,255,255,0.08);
            width: 350px;
            height: 350px;
        }

        .success-wrapper {
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 10;
            animation: fadeInUp 0.7s ease-out;
        }

        /* glass card */
        .success-card {
            background: rgba(255, 255, 255, 0.93);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 40px;
            padding: 2.8rem 2rem;
            box-shadow: 0 30px 50px rgba(0, 0, 0, 0.25),
                        0 0 0 1px rgba(255, 255, 255, 0.5) inset;
            border: 1px solid rgba(255,255,255,0.3);
            text-align: center;
        }

        /* success icon */
        .success-icon {
            background: #0f3c91;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.8rem;
            box-shadow: 0 20px 30px -8px rgba(15,60,145,0.5);
        }
        .success-icon i {
            font-size: 54px;
            color: white;
        }
        /* fallback if no FA */
        .success-icon .check-mark {
            font-size: 54px;
            line-height: 1;
            color: white;
            font-weight: 300;
        }

        h1 {
            font-size: 2.3rem;
            font-weight: 700;
            color: #0f3c91;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .message {
            font-size: 1.1rem;
            color: #2d3748;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            font-weight: 400;
            opacity: 0.9;
        }

        /* button */
        .app-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: rgb(244, 180, 20);
            color: #0f3c91;
            font-weight: 700;
            font-size: 1.2rem;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 60px;
            text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 12px 25px -8px rgba(244,180,20,0.6);
            width: 100%;
            max-width: 280px;
            margin: 0 auto 1.5rem;
            cursor: pointer;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .app-btn i {
            font-size: 1.3rem;
            transition: transform 0.2s;
        }

        .app-btn:hover {
            background: #0f3c91;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 18px 30px -8px #0f3c91;
        }
        .app-btn:hover i {
            transform: translateX(5px);
        }

        .note {
            font-size: 0.9rem;
            color: #4a5568;
            border-top: 1px solid rgba(0,0,0,0.08);
            padding-top: 1.5rem;
            margin-top: 0.5rem;
        }

        .note a {
            color: #0f3c91;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px dotted #0f3c91;
        }
        .note a:hover {
            color: rgb(244, 180, 20);
            border-bottom-color: rgb(244, 180, 20);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .success-card { padding: 2rem 1.5rem; }
            h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <!-- decorative blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="success-wrapper">
        <div class="success-card">
            <!-- success icon (Font Awesome used, fallback text) -->
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
                <!-- fallback if FA fails: <span class="check-mark">✓</span> -->
            </div>

            <h1>Payment Successful!</h1>
            <div class="message">
                {{ $message ?? 'Your payment has been processed successfully.' }}
            </div>
<!-- Simple instruction -->
            <div class="note" style="margin-top: 2rem;">
                <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                You may now close this tab and return to the app.
            </div>
           
        </div>
    </div>

   
</body>
</html>