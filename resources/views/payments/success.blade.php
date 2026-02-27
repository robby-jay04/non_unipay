<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen;
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 40px 30px;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
        }

        .check {
            width: 90px;
            height: 90px;
            background: #28a745;
            color: #fff;
            font-size: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        h1 {
            color: #28a745;
            font-size: 26px;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 14px 24px;
            background: #667eea;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            border-radius: 10px;
            transition: background 0.2s ease;
        }

        .btn:hover {
            background: #556cd6;
        }

        .note {
            margin-top: 20px;
            font-size: 13px;
            color: #999;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="check">✓</div>

        <h1>Payment Successful</h1>
        <p>{{ $message ?? 'Your payment has been processed successfully.' }}</p>

     
    </div>

</body>
</html>
<script>
  const backBtn = document.getElementById('backBtn');

  backBtn.addEventListener('click', () => {
    const appLink = "nonunipay://home";
   

    // Try opening the app
    window.location = appLink;

    // After 1 second, if still on web, open fallback
    setTimeout(() => {
      window.location = fallback;
    }, 1000);
  });
</script>
