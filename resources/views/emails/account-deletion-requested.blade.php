<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriCo Account Deletion Request Received</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 24px;
            color: #1e293b;
        }
        .container {
            max-width: 540px;
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
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            color: #b45309;
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
        .details-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            margin: 22px 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 7px 0;
            font-size: 13.5px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .details-label {
            color: #64748b;
            font-weight: 600;
        }
        .details-value {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }
        .what-next {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 16px 18px;
            margin: 20px 0;
            font-size: 13.5px;
            color: #166534;
            line-height: 1.5;
        }
        .btn-cancel {
            display: inline-block;
            background-color: #ef4444;
            color: #ffffff !important;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
            text-align: center;
        }
        .btn-cancel:hover {
            background-color: #dc2626;
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
            <div class="status-badge">⏳ &nbsp; Deletion Request Pending Review</div>

            <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">We received your account deletion request</h2>
            <p style="margin: 0 0 16px 0; font-size: 14.5px; color: #475569;">
                Hello <strong>{{ $userName }}</strong>, we have received your request to permanently delete your AgriCo account.
            </p>
        </div>

        <div class="details-card">
            <table class="details-table">
                <tr>
                    <td class="details-label">Request Code:</td>
                    <td class="details-value" style="font-family: monospace; color: #15803d;">{{ $requestCode }}</td>
                </tr>
                <tr>
                    <td class="details-label">Registered Account:</td>
                    <td class="details-value">{{ $userEmail }}</td>
                </tr>
                <tr>
                    <td class="details-label">Submitted On:</td>
                    <td class="details-value">{{ $submittedAt }}</td>
                </tr>
                <tr>
                    <td class="details-label">Status:</td>
                    <td class="details-value" style="color: #b45309;">Pending Administrative Review</td>
                </tr>
                @if(!empty($reason))
                <tr>
                    <td class="details-label">Reason:</td>
                    <td class="details-value" style="font-weight: 500; color: #334155;">{{ $reason }}</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="what-next">
            <strong>What happens next?</strong><br>
            An administrator will review your request. Once approved, your personal credentials and personal information will be permanently expunged. Contributed municipal soil readings will remain anonymized to preserve Lal-lo agricultural research data.
        </div>

        @if(!empty($cancelUrl))
        <div style="text-align: center; margin: 26px 0 16px 0;">
            <p style="font-size: 13.5px; color: #475569; margin-bottom: 12px;">
                Changed your mind? You can cancel this request at any time before it is reviewed:
            </p>
            <a href="{{ $cancelUrl }}" class="btn-cancel" target="_blank">
                Cancel Deletion Request
            </a>
        </div>
        @endif

        <div style="margin-top: 24px; padding: 14px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; font-size: 12.5px; color: #9f1239; line-height: 1.5;">
            <strong>Did not request this?</strong><br>
            If you did not submit this account deletion request, someone may have accessed your account. Please <a href="{{ route('password.request') }}" style="color: #be123c; font-weight: 700; text-decoration: underline;">change your password immediately</a> or click the Cancel Deletion Request button above.
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} AgriCo System. All rights reserved.<br>
            Automated message — please do not reply to this email.
        </div>
    </div>
</body>
</html>
