<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriCo Account Deletion Request Cancelled</title>
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
        .status-badge {
            display: inline-block;
            background-color: #dcfce7;
            border: 1px solid #86efac;
            color: #15803d;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }
        .content {
            font-size: 15px;
            line-height: 1.6;
            color: #334155;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            margin: 20px 0;
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
                        <div class="brand-title">AgriCo</div>
                    </td>
                </tr>
            </table>
            <div class="brand-sub">Agricultural Soil Analysis & Mapping Platform</div>
        </div>

        <div class="content" style="text-align: center;">
            <div class="status-badge">✓ &nbsp; Request Cancelled</div>

            <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">Account Deletion Cancelled</h2>
            <p style="margin: 0 0 16px 0; font-size: 14.5px; color: #475569;">
                Hello <strong>{{ $userName }}</strong>, your account deletion request (<strong>{{ $requestCode }}</strong>) has been successfully cancelled.
            </p>
        </div>

        <div class="info-card">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="padding: 6px 0; color: #64748b; font-weight: 600; font-size: 13.5px;">Account:</td>
                    <td align="right" style="padding: 6px 0; color: #0f172a; font-weight: 700; font-size: 13.5px;">{{ $userEmail }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b; font-weight: 600; font-size: 13.5px;">Status:</td>
                    <td align="right" style="padding: 6px 0; color: #15803d; font-weight: 700; font-size: 13.5px;">Active & Secure</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b; font-weight: 600; font-size: 13.5px;">Cancelled At:</td>
                    <td align="right" style="padding: 6px 0; color: #0f172a; font-weight: 700; font-size: 13.5px;">{{ now()->format('M d, Y h:i A') }}</td>
                </tr>
            </table>
        </div>

        <p style="font-size: 14px; color: #475569; text-align: center; margin-top: 18px;">
            Your account remains active, and your data, scans, and bookmarks are completely unaffected.
        </p>

        <div class="footer">
            &copy; {{ date('Y') }} AgriCo System. All rights reserved.<br>
            Automated message — please do not reply to this email.
        </div>
    </div>
</body>
</html>
