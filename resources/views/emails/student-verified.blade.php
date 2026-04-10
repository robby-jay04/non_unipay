<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
       .header { background: linear-gradient(135deg, #1b5e20, #2e7d32); padding: 36px 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; }
        .header p { color: rgba(255,255,255,0.75); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px; color: #333; }
        .body p { line-height: 1.7; margin: 0 0 16px; }
        .badge { display: inline-block; background: rgba(76,175,80,0.12); color: #2e7d32; border-radius: 30px; padding: 6px 18px; font-weight: 600; font-size: 14px; margin-bottom: 20px; }
        .info-box { background: #f4f6fb; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; font-size: 14px; }
        .info-box p { margin: 0 0 6px; }
        .info-box p:last-child { margin: 0; }
        .info-box span { font-weight: 600; color: #0f3c91; }
        .footer { text-align: center; padding: 20px 32px; font-size: 12px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Account Verified ✓</h1>
            <p>Non-UniPay Student Portal</p>
        </div>
        <div class="body">
            <p>Hi <strong>{{ $student->user->name }}</strong>,</p>
            <p>Great news! Your student account has been successfully verified by the administrator. You now have full access to the Non-UniPay App.</p>
            <div class="badge">✔ Account Confirmed</div>
            <div class="info-box">
                <p>Student No.: <span>{{ $student->student_no }}</span></p>
                <p>Course: <span>{{ $student->course }}</span></p>
                <p>Year Level: <span>{{ $student->year_level }}</span></p>
            </div>
            <p>You can now view your clearance status, track your payments, and use all features of the portal.</p>
            <p>If you have any questions, please contact your administrator at <a href="mailto:nonunipay@gmail.com" style="color:#2e7d32;font-weight:600;">nonunipay@gmail.com</a>.</p>
            <p>— The Non-UniPay Team</p>
        </div>
        <div class="footer">
            This is an automated message. Please do not reply to this email.
        </div>
    </div>
</body>
</html>