<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soil Location Pin Submitted - AgriCo</title>
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
                                <span style="font-size: 24px;">📍</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">Soil Pin Submitted</h1>
                            <p style="margin: 6px 0 0; color: #dcfce7; font-size: 13.5px; font-weight: 500;">Your soil coordinates are pending administrator review</p>
                        </td>
                    </tr>

                    {{-- Body Content --}}
                    <tr>
                        <td style="padding: 32px 36px;">
                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #334155;">
                                Hello <strong>{{ $user?->name ?? 'AgriCo User' }}</strong>,
                            </p>
                            <p style="margin: 0 0 20px; font-size: 14px; line-height: 1.6; color: #475569;">
                                We've received your soil sample location pin from the <strong>{{ ucfirst($platform) }}</strong>. It has been queued for verification by an AgriCo Administrator.
                            </p>

                            {{-- Details Card --}}
                            <div style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; margin-bottom: 24px;">
                                <table width="100%" cellspacing="0" cellpadding="4" style="font-size: 13.5px;">
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600; width: 35%;">Barangay:</td>
                                        <td style="color: #0f172a; font-weight: 700;">{{ $location->barangay ?: 'Lal-lo Overall' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Coordinates:</td>
                                        <td style="color: #0284c7; font-family: monospace; font-weight: 700;">{{ number_format((float)$location->latitude, 5) }}, {{ number_format((float)$location->longitude, 5) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Status:</td>
                                        <td>
                                            <span style="display: inline-block; background-color: #fef3c7; color: #b45309; font-weight: 700; font-size: 11px; padding: 2px 8px; border-radius: 9999px;">
                                                ⏳ PENDING VERIFICATION
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Submitted At:</td>
                                        <td style="color: #334155; font-weight: 600;">{{ now()->format('M d, Y · h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <p style="margin: 0 0 24px; font-size: 13.5px; line-height: 1.6; color: #475569;">
                                Once an administrator reviews and approves this scan, your pin will automatically appear as an active data point on the public AgriCo Soil Map.
                            </p>

                            <div style="text-align: center; margin: 28px 0 10px;">
                                <a href="{{ url('/map') }}" style="display: inline-block; background-color: #16a34a; color: #ffffff; text-decoration: none; font-size: 13.5px; font-weight: 700; padding: 12px 28px; border-radius: 9999px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                                    Explore AgriCo Soil Map
                                </a>
                            </div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 36px; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8; line-height: 1.5;">
                                Cagayan State University at Lal-lo &bull; AgriCo Soil Color & Classification System<br>
                                Automated Notification &bull; Please do not reply directly to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
