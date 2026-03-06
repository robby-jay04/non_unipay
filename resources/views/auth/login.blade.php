<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · Non-UniPay</title>
    <!-- Bootstrap 5 + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            position: relative;
        }
        .bg-image {
            background: url("{{ asset('bg.jpg') }}");
            background-size: cover;
            background-position: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 45vh;
            z-index: 0;
        }
        .bg-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
        }
        .login-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding: 0 1.5rem 2rem;
        }
    .glass-card {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-radius: 40px;
    padding: 2.5rem 2rem 2rem;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 30px 50px rgba(0,0,0,0.25);
    border: 1px solid rgba(255,255,255,0.5);
    text-align: center;

    animation: fadeInUp 0.7s ease-out;
}
        .logo {
            position: absolute;
            top: 38%;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 60px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px;
            z-index: 20;
        }
        .logo img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border-radius: 50px;
        }
        .logo-placeholder {
            background: #0f3c91;
            color: white;
            font-weight: bold;
            font-size: 2rem;
            width: 100px;
            height: 100px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .title {
            font-size: 2rem;
            font-weight: 700;
            color: #0f3c91;
            margin-bottom: 0.25rem;
        }
        .subtitle {
            font-size: 0.9rem;
            color: #4b5563;
            margin-bottom: 1.5rem;
        }
        .input-group-custom {
            background: #f2f2f2;
            border-radius: 30px;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            border: 1px solid #e2e8f0;
            margin-bottom: 1rem;
        }
        .input-group-custom i {
            color: #6b7280;
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .input-group-custom input {
            border: none;
            background: transparent;
            padding: 0.9rem 0;
            width: 100%;
            outline: none;
            font-size: 1rem;
        }
        .input-group-custom input::placeholder {
            color: #9ca3af;
        }
        

        .btn-login {
            background: #0f3c91;
            color: white;
            border: none;
            padding: 0.9rem;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.2rem;
            width: 100%;
            transition: all 0.2s;
            margin-top: 0.5rem;
        }
        .btn-login:hover {
            background: #1a4da8;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(15,60,145,0.3);
        }
     
      
        /* Password eye icon */
        .password-toggle {
            cursor: pointer;
            color: #6b7280;
            font-size: 1.2rem;
        }
      
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Background gradient (simulating image) -->
    <div class="bg-image">
        
    </div>

    <!-- Logo (floating) -->
    <div class="logo">
        <!-- Replace with your actual logo image -->
        
          <img src="{{ asset('logo.png') }}" alt="Non-UniPay">
    </div>

    <div class="login-wrapper">
        <div class="glass-card">
            <h1 class="title">Non-UniPay</h1>
            <p class="subtitle">School Fee Payment and Exam Clearance System</p>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <!-- Email -->
                <div class="input-group-custom">
                    <i class="fas fa-user"></i>
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                </div>
                <!-- Password with eye toggle -->
                <div class="input-group-custom">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <span class="password-toggle" onclick="togglePassword()">
                        
                    </span>
                </div>

              

                <!-- Login Button -->
                <button type="submit" class="btn-login">Login</button>
            </form>

            
            <!-- Error message placeholder -->
            @if(session('error'))
                <div class="mt-3 text-danger small">{{ session('error') }}</div>
            @endif
        </div>
    </div>

  
  
  
</body>
</html>