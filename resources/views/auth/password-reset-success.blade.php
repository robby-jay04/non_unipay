<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset · Non-UniPay</title>
    <!-- Google Font & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
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
            background: rgba(255,255,255,0.1);
            width: 350px;
            height: 350px;
        }

        .success-wrapper {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 10;
        }

        /* glass card */
        .success-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 40px;
            padding: 2.5rem 2rem;
            box-shadow: 0 30px 50px rgba(0, 0, 0, 0.25),
                        0 0 0 1px rgba(255, 255, 255, 0.5) inset;
            border: 1px solid rgba(255,255,255,0.3);
            text-align: center;
        }

        /* success icon */
        .success-icon {
            background: #0f3c91;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 20px 30px -8px rgba(15,60,145,0.5);
        }
        .success-icon i {
            font-size: 48px;
            color: white;
        }

        h2 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #0f3c91;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .message {
            font-size: 1.1rem;
            color: #2d3748;
            line-height: 1.6;
            margin-bottom: 2rem;
            font-weight: 400;
        }
        .message p {
            margin: 0.5rem 0;
        }

        /* login button */
        .login-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgb(244, 180, 20);
            color: #0f3c91;
            font-weight: 700;
            font-size: 1.2rem;
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 12px 20px -10px rgba(244,180,20,0.5);
            margin-top: 1rem;
            width: 100%;
            max-width: 280px;
            margin-left: auto;
            margin-right: auto;
        }

        .login-btn i {
            font-size: 1.3rem;
            transition: transform 0.2s;
        }

        .login-btn:hover {
            background: #0f3c91;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 18px 25px -10px #0f3c91;
        }
        .login-btn:hover i {
            transform: translateX(5px);
        }

        /* secondary link (optional) */
        .home-link {
            margin-top: 2rem;
            font-size: 0.95rem;
        }
        .home-link a {
            color: #0f3c91;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }
        .home-link a:hover {
            color: rgb(244, 180, 20);
        }
        .home-link a i {
            font-size: 0.9rem;
        }

        @media (max-width: 480px) {
            .success-card {
                padding: 2rem 1.5rem;
            }
            h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- decorative blobs -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="success-wrapper">
        <div class="success-card">
            <!-- success icon -->
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h2>Password Reset Successful!</h2>

            <div class="message">
                <p>Your password has been updated successfully.</p>
                <p>You can now log in with your new password.</p>
            </div>

           
        </div>
    </div>
</body>
</html>