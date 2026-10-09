<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriCo Password Reset Code</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 24px;
            color: #1e293b;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            padding: 36px 28px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo-table {
            margin: 0 auto;
            border-collapse: collapse;
        }
        .brand-title {
            font-size: 26px;
            font-weight: 900;
            color: #22c55e;
            margin: 0;
            line-height: 1;
            letter-spacing: -0.02em;
        }
        .brand-sub {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 10px;
        }
        .content {
            font-size: 15px;
            line-height: 1.6;
            color: #334155;
        }
        .code-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px dashed #86efac;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 28px 0;
        }
        .code-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #166534;
            margin-bottom: 8px;
        }
        .code-number {
            font-family: 'Courier New', Courier, monospace;
            font-size: 34px;
            font-weight: 800;
            letter-spacing: 6px;
            color: #15803d;
            margin: 0;
        }
        .expiry-note {
            font-size: 13px;
            color: #64748b;
            text-align: center;
            margin-top: 8px;
        }
        .footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    @php
        $logoSrc = asset('images/agrico-logo.png');
        if (isset($message) && method_exists($message, 'embed') && file_exists(public_path('images/agrico-logo.png'))) {
            try {
                $logoSrc = $message->embed(public_path('images/agrico-logo.png'));
            } catch (\Throwable $e) {
                $logoSrc = asset('images/agrico-logo.png');
            }
        }
    @endphp
    <div class="container">
        <div class="header">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" class="logo-table" style="margin: 0 auto;">
                <tr>
                    <td style="vertical-align: middle; padding-right: 12px;">
                        <img src="{{ $logoSrc }}" 
                             alt="AgriCo Logo" 
                             width="48" 
                             height="48" 
                             style="width: 48px; height: 48px; display: block; object-fit: contain; border: 0;">
                    </td>
                    <td style="vertical-align: middle; text-align: left;">
                        <div class="brand-title" style="font-size: 26px; font-weight: 900; color: #22c55e; margin: 0; line-height: 1; letter-spacing: -0.02em;">AgriCo</div>
                    </td>
                </tr>
            </table>
            <div class="brand-sub">Agricultural Soil Analysis & Mapping Platform</div>
        </div>

        <div class="content">
            <p>Hello,</p>
            <p>We received a request to reset your password for your AgriCo account. Use the 6-digit verification code below to proceed:</p>

            <div class="code-box">
                <div class="code-title">Verification Code</div>
                <div class="code-number">{{ $code }}</div>
            </div>

            <div class="expiry-note">
                ⏱️ This code will expire in <strong>15 minutes</strong>.
            </div>

            <p style="margin-top: 24px; font-size: 13px; color: #64748b;">
                If you did not request this password reset, no action is needed. Your account remains safe and secure.
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} AgriCo System. All rights reserved.<br>
            Automated message — please do not reply to this email.
        </div>
    </div>
</body>
</html>
