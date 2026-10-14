<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Registration Update - AgriCo</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    {{-- Header Banner --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%); padding: 32px 36px; text-align: center;">
                            <div style="display: inline-block; background: rgba(255, 255, 255, 0.2); border-radius: 14px; padding: 10px 14px; margin-bottom: 12px;">
                                <span style="font-size: 26px;">⚠️</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">
                                Account Registration Disapproved
                            </h1>
                            <p style="margin: 6px 0 0; color: #fee2e2; font-size: 13.5px; font-weight: 500;">
                                Application reviewed by AgriCo Administration
                            </p>
                        </td>
                    </tr>

                    {{-- Body Content --}}
                    <tr>
                        <td style="padding: 32px 36px;">
                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #334155;">
                                Hello <strong>{{ $userName ?? 'AgriCo Applicant' }}</strong>,
                            </p>
                            <p style="margin: 0 0 20px; font-size: 14px; line-height: 1.6; color: #475569;">
                                An AgriCo administrator has reviewed your account registration request and marked it as <strong>Disapproved</strong>. 
                                @if(!empty($reason))
                                    Reason provided: <em>{{ $reason }}</em>
                                @else
                                    This may occur if the submitted credentials, student/institution ID, or contact details could not be validated against official records.
                                @endif
                            </p>

                            {{-- Details Card --}}
                            <div style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; margin-bottom: 24px;">
                                <table width="100%" cellspacing="0" cellpadding="4" style="font-size: 13.5px;">
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600; width: 35%;">Applicant Name:</td>
                                        <td style="color: #0f172a; font-weight: 700;">{{ $userName }}</td>
                                    </tr>
                                    @if(!empty($userEmail))
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Submitted Email:</td>
                                        <td style="color: #0f172a; font-weight: 600;">{{ $userEmail }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Status:</td>
                                        <td>
                                            <span style="display: inline-block; background-color: #fee2e2; color: #b91c1c; font-weight: 700; font-size: 11px; padding: 2px 8px; border-radius: 9999px;">
                                                DISAPPROVED
                                            </span>
                                        </td>
                                    </tr>
                                    @if($adminId)
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Reviewed By:</td>
                                        <td style="color: #475569; font-size: 12.5px;">Admin ({{ $adminId }})</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <p style="margin: 0 0 20px; font-size: 13.5px; line-height: 1.5; color: #64748b;">
                                If you believe this decision was made in error or would like to re-apply with verified credentials, please register again or contact the system administrator at <a href="mailto:agricosite77@gmail.com" style="color: #16a34a; text-decoration: underline;">agricosite77@gmail.com</a>.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f1f5f9; padding: 18px 36px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 12px; color: #64748b;">
                                &copy; {{ date('Y') }} AgriCo Soil Intelligence Platform &bull; Lal-lo, Cagayan
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
