<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password - Non-UniPay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        .reset-container {
            max-width: 420px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px 30px;
        }
        h2 {
            color: #0f3c91;
            font-size: 28px;
            margin-top: 0;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 600;
        }
        .subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 25px;
            font-size: 14px;
        }
        .status-message {
            background-color: rgba(244, 180, 20, 0.1);
            color: rgb(244, 180, 20);
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid rgb(244, 180, 20);
            margin-bottom: 20px;
            font-weight: 500;
        }
        .error-list {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: 12px 20px;
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            margin-bottom: 20px;
            list-style-type: none;
            margin: 0 0 20px 0;
        }
        .error-list li {
            margin-bottom: 5px;
        }
        .password-wrapper {
            margin-bottom: 20px;
            position: relative;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 14px 16px;
            padding-right: 45px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            box-sizing: border-box;
            background: #f8f9fa;
        }
        input[type="password"]:focus,
        input[type="text"]:focus {
            border-color: #0f3c91;
            outline: none;
            background: white;
            box-shadow: 0 0 0 4px rgba(15, 60, 145, 0.1);
        }
        .password-wrapper i {
            position: absolute;
            right: 16px;
            top: 47px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            font-size: 18px;
            transition: color 0.2s;
        }
        .password-wrapper i:hover {
            color: #0f3c91;
        }
        button {
            background: #0f3c91;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 16px rgba(15, 60, 145, 0.2);
            letter-spacing: 0.5px;
            margin-top: 10px;
        }
        button:hover {
            background: #0a2e6f;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(15, 60, 145, 0.3);
        }
        button:active {
            transform: translateY(0);
        }
        .footer-links {
            text-align: center;
            margin-top: 25px;
        }
        .footer-links a {
            color: #0f3c91;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: rgb(244, 180, 20);
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .reset-container {
                padding: 30px 20px;
            }
            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Reset Your Password</h2>
        <p class="subtitle">Enter your new password below</p>

        @if (session('status'))
            <div class="status-message">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="password-wrapper">
                <label>New Password</label>
                <input type="password" name="password" id="password" required>
                <i class="fa-regular fa-eye" id="togglePassword"></i>
            </div>

            <div class="password-wrapper">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
                <i class="fa-regular fa-eye" id="toggleConfirmPassword"></i>
            </div>

            <button type="submit">Reset Password</button>
        </form>

       
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const passwordConfirmation = document.getElementById('password_confirmation');
        toggleConfirmPassword.addEventListener('click', function () {
            const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmation.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>