<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
</head>
<body style="margin:0;padding:0;background-color:#0c0e12;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#0c0e12;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:560px;background-color:#15181f;border:1px solid #2a2f3a;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td align="center" style="background:linear-gradient(135deg,#0c0e12 0%,#1a1f29 100%);padding:28px 24px;border-bottom:3px solid #e8a630;">
                            @if(file_exists(public_path('logo.png')))
                                <img src="{{ $message->embed(public_path('logo.png')) }}" alt="MotoLegacy" width="120" style="display:block;margin:0 auto 12px auto;max-width:120px;height:auto;">
                            @endif
                            <div style="color:#e8a630;font-size:24px;font-weight:bold;letter-spacing:1px;">MotoLegacy</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 28px;color:#f5f5f5;">
                            <p style="margin:0 0 12px 0;font-size:16px;color:#ffffff;">Hello {{ $userName }},</p>
                            <p style="margin:0 0 24px 0;font-size:14px;line-height:1.6;color:#c9c9d1;">
                                We received a request to reset your password. Use the OTP code below to continue:
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 24px 0;">
                                <tr>
                                    <td align="center" style="background-color:#0c0e12;border:1px solid #e8a630;border-radius:12px;padding:18px;">
                                        <div style="color:#e8a630;font-size:32px;font-weight:bold;letter-spacing:8px;">{{ $otp }}</div>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px 0;font-size:13px;color:#c9c9d1;">This code expires in <strong style="color:#e8a630;">10 minutes</strong>.</p>
                            <p style="margin:0;font-size:13px;color:#c9c9d1;">If you did not request this, you can ignore this email.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="background-color:#0c0e12;padding:16px;border-top:1px solid #2a2f3a;">
                            <div style="color:#8a8f9a;font-size:12px;">&copy; {{ date('Y') }} MotoLegacy</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
