<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved - AgriCo</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    {{-- Header Banner --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #15803d 0%, #16a34a 100%); padding: 32px 36px; text-align: center;">
                            <div style="display: inline-block; background: rgba(255, 255, 255, 0.2); border-radius: 14px; padding: 10px 14px; margin-bottom: 12px;">
                                <span style="font-size: 26px;">🎉</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">
                                Account Registration Approved!
                            </h1>
                            <p style="margin: 6px 0 0; color: #dcfce7; font-size: 13.5px; font-weight: 500;">
                                Your AgriCo account is now fully active
                            </p>
                        </td>
                    </tr>

                    {{-- Body Content --}}
                    <tr>
                        <td style="padding: 32px 36px;">
                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #334155;">
                                Hello <strong>{{ $user->name ?? 'AgriCo User' }}</strong>,
                            </p>
                            <p style="margin: 0 0 20px; font-size: 14px; line-height: 1.6; color: #475569;">
                                Great news! An AgriCo administrator has reviewed and approved your account registration. Full access to the AgriCo Soil Intelligence platform—including soil sample scanning, the interactive soil map, and scan history—is now unlocked.
                            </p>

                            {{-- Details Card --}}
                            <div style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; margin-bottom: 24px;">
                                <table width="100%" cellspacing="0" cellpadding="4" style="font-size: 13.5px;">
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600; width: 35%;">User ID:</td>
                                        <td style="color: #0f172a; font-weight: 700;">{{ $user->formatted_id ?? ('USR-' . str_pad($user->id ?? $user->user_id, 4, '0', STR_PAD_LEFT)) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Full Name:</td>
                                        <td style="color: #0f172a; font-weight: 700;">{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Email:</td>
                                        <td style="color: #0f172a; font-weight: 600;">{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Role:</td>
                                        <td style="color: #0f172a; font-weight: 600;">{{ ucfirst($user->role ?? 'User') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Account Status:</td>
                                        <td>
                                            <span style="display: inline-block; background-color: #dcfce7; color: #15803d; font-weight: 700; font-size: 11px; padding: 2px 8px; border-radius: 9999px;">
                                                ACTIVE / APPROVED
                                            </span>
                                        </td>
                                    </tr>
                                    @if($adminId)
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Approved By:</td>
                                        <td style="color: #475569; font-size: 12.5px;">Admin ({{ $adminId }})</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            {{-- CTA Button --}}
                            <div style="text-align: center; margin: 28px 0 20px;">
                                <a href="{{ $loginUrl }}" style="display: inline-block; background-color: #16a34a; color: #ffffff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 12px 28px; border-radius: 12px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.35);">
                                    Log In to AgriCo Platform &rarr;
                                </a>
                            </div>

                            <p style="margin: 0; font-size: 12.5px; line-height: 1.5; color: #94a3b8; text-align: center;">
                                You can sign in using your registered email and password on both the web portal and mobile application.
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
