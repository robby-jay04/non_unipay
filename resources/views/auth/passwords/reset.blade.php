<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password - Non-UniPay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f3c91 0%, #1a56c4 50%, #0a2e6f 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card {
            max-width: 420px;
            width: 100%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        /* Top banner */
        .card-header {
            background: linear-gradient(135deg, #0f3c91, #1a56c4);
            padding: 32px 30px 28px;
            text-align: center;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.15);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            backdrop-filter: blur(10px);
        }

        .logo-icon i {
            font-size: 26px;
            color: white;
        }

        .card-header h2 {
            color: white;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .card-header p {
            color: rgba(255,255,255,0.75);
            font-size: 13px;
        }

        /* Body */
        .card-body {
            padding: 32px 30px;
        }

        /* Alerts */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.08);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .alert-success {
            background: rgba(25, 135, 84, 0.08);
            color: #198754;
            border: 1px solid rgba(25, 135, 84, 0.2);
        }

        .alert i { margin-top: 1px; flex-shrink: 0; }

        .alert ul {
            list-style: none;
            padding: 0;
        }

        .alert ul li + li { margin-top: 4px; }

        /* Form fields */
        .field { margin-bottom: 20px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 13px;
            letter-spacing: 0.3px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap input {
            width: 100%;
            padding: 13px 44px 13px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            color: #111827;
            background: #f9fafb;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .input-wrap input:focus {
            border-color: #0f3c91;
            background: white;
            outline: none;
            box-shadow: 0 0 0 4px rgba(15, 60, 145, 0.1);
        }

        .input-wrap input.is-invalid {
            border-color: #dc3545;
            background: white;
        }

        .toggle-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            font-size: 16px;
            transition: color 0.2s;
            user-select: none;
        }

        .toggle-eye:hover { color: #0f3c91; }

        /* Password strength */
        .strength-bar {
            display: flex;
            gap: 4px;
            margin-top: 8px;
        }

        .strength-bar span {
            flex: 1;
            height: 4px;
            border-radius: 99px;
            background: #e5e7eb;
            transition: background 0.3s;
        }

        .strength-label {
            font-size: 11px;
            margin-top: 5px;
            color: #9ca3af;
            font-weight: 500;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0f3c91, #1a56c4);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(15, 60, 145, 0.3);
            letter-spacing: 0.4px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(15, 60, 145, 0.4);
        }

        .btn-submit:active { transform: translateY(0); }

        /* Footer */
        .card-footer {
            text-align: center;
            padding: 0 30px 28px;
            color: #6b7280;
            font-size: 12px;
        }

        .card-footer i { color: #f4b414; margin-right: 4px; }

        @media (max-width: 480px) {
            .card-header { padding: 24px 20px 20px; }
            .card-body { padding: 24px 20px; }
            .card-footer { padding: 0 20px 24px; }
        }
    </style>
</head>
<body>
    <div class="card">

        <div class="card-header">
            <div class="logo-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h2>Reset Your Password</h2>
            <p>Create a strong new password for your account</p>
        </div>

        <div class="card-body">

            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="field">
                    <label for="password">New Password</label>
                    <div class="input-wrap">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter new password"
                            required
                            autocomplete="new-password"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        >
                        <i class="fa-regular fa-eye toggle-eye" id="togglePassword"></i>
                    </div>
                    <div class="strength-bar">
                        <span id="s1"></span>
                        <span id="s2"></span>
                        <span id="s3"></span>
                        <span id="s4"></span>
                    </div>
                    <div class="strength-label" id="strengthLabel">Enter a password</div>
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirm New Password</label>
                    <div class="input-wrap">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="Re-enter your password"
                            required
                            autocomplete="new-password"
                        >
                        <i class="fa-regular fa-eye toggle-eye" id="toggleConfirm"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-shield-alt"></i>
                    Reset Password
                </button>
            </form>
        </div>

        <div class="card-footer">
            <i class="fas fa-bolt"></i>
            This link expires in 60 minutes. If you didn't request this, ignore the email.
        </div>

    </div>

    <script>
        // Toggle password visibility
        function toggleVisibility(toggleId, inputId) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            toggle.addEventListener('click', function () {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        toggleVisibility('togglePassword', 'password');
        toggleVisibility('toggleConfirm', 'password_confirmation');

        // Password strength meter
        const passwordInput = document.getElementById('password');
        const bars = [document.getElementById('s1'), document.getElementById('s2'),
                      document.getElementById('s3'), document.getElementById('s4')];
        const strengthLabel = document.getElementById('strengthLabel');

        const levels = [
            { color: '#dc3545', label: 'Too weak' },
            { color: '#fd7e14', label: 'Weak' },
            { color: '#f4b414', label: 'Fair' },
            { color: '#198754', label: 'Strong' },
        ];

        passwordInput.addEventListener('input', function () {
            const val = this.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            bars.forEach((bar, i) => {
                bar.style.background = i < score ? levels[score - 1].color : '#e5e7eb';
            });

            strengthLabel.textContent = val.length === 0 ? 'Enter a password' : levels[score - 1]?.label ?? '';
            strengthLabel.style.color = val.length === 0 ? '#9ca3af' : levels[score - 1]?.color;
        });
    </script>
</body>
</html>