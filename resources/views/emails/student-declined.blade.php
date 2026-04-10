<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #b71c1c, #d32f2f); padding: 36px 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; }
        .header p { color: rgba(255,255,255,0.75); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 32px; color: #333; }
        .body p { line-height: 1.7; margin: 0 0 16px; }
        .badge { display: inline-block; background: rgba(183,28,28,0.1); color: #b71c1c; border-radius: 30px; padding: 6px 18px; font-weight: 600; font-size: 14px; margin-bottom: 20px; }
        .info-box { background: #f4f6fb; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; font-size: 14px; }
        .info-box p { margin: 0 0 6px; }
        .info-box p:last-child { margin: 0; }
        .info-box span { font-weight: 600; color: #b71c1c; }
        .footer { text-align: center; padding: 20px 32px; font-size: 12px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Account Registration Declined</h1>
            <p>Non-UniPay Student Portal</p>
        </div>
        <div class="body">
            <p>Hi <strong>{{ $student->user->name }}</strong>,</p>
            <p>We regret to inform you that your student account registration has been <strong>declined</strong> by the administrator.</p>
            <div class="badge">✗ Registration Declined</div>
            <div class="info-box">
                <p>Student No.: <span>{{ $student->student_no }}</span></p>
                <p>Course: <span>{{ $student->course }}</span></p>
                <p>Year Level: <span>{{ $student->year_level }}</span></p>
            </div>
            <p>This may be due to incomplete or incorrect registration details. If you believe this is a mistake or need further clarification, please contact your administrator directly.</p>
            <p>— The Non-UniPay Team</p>
        </div>
        <div class="footer">
            This is an automated message. Please do not reply to this email.
        </div>
    </div>
</body>
</html>