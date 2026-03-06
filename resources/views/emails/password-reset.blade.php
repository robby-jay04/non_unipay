<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset - Non-UniPay</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; color: #333;">
    <h2 style="color: #0f3c91;">Reset Your Password</h2>
    <p>You requested to reset your password. Click the button below to proceed:</p>

    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <a href="{{ $resetUrl }}" 
                   style="background-color: #0f3c91; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                    Reset Password
                </a>
            </td>
        </tr>
    </table>

    <p style="margin-top: 20px; color: #666;">
        If you did not request a password reset, please ignore this email.
    </p>

    <p style="color: #999; font-size: 12px;">
        This link will expire in 60 minutes.
    </p>
</body>
</html>