<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soil Scan {{ $status === 'Verified' ? 'Approved' : 'Disapproved' }} - AgriCo</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                    {{-- Header Banner --}}
                    <tr>
                        <td style="background: {{ $status === 'Verified' ? 'linear-gradient(135deg, #15803d 0%, #16a34a 100%)' : 'linear-gradient(135deg, #b91c1c 0%, #dc2626 100%)' }}; padding: 32px 36px; text-align: center;">
                            <div style="display: inline-block; background: rgba(255, 255, 255, 0.2); border-radius: 14px; padding: 10px 14px; margin-bottom: 12px;">
                                <span style="font-size: 26px;">{{ $status === 'Verified' ? '✅' : '❌' }}</span>
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">
                                {{ $status === 'Verified' ? 'Soil Scan Approved & Published' : 'Soil Scan Disapproved' }}
                            </h1>
                            <p style="margin: 6px 0 0; color: {{ $status === 'Verified' ? '#dcfce7' : '#fee2e2' }}; font-size: 13.5px; font-weight: 500;">
                                {{ $status === 'Verified' ? 'Your coordinates are now visible on the Lal-lo Soil Map' : 'The location pin for this scan will remain hidden' }}
                            </p>
                        </td>
                    </tr>

                    {{-- Body Content --}}
                    <tr>
                        <td style="padding: 32px 36px;">
                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.6; color: #334155;">
                                Hello <strong>{{ $user?->name ?? 'AgriCo User' }}</strong>,
                            </p>
                            <p style="margin: 0 0 20px; font-size: 14px; line-height: 1.6; color: #475569;">
                                @if($status === 'Verified')
                                    Great news! An AgriCo administrator reviewed your soil sample scan <strong>#{{ $scan->soil_scan_id }}</strong> and approved it. Your sample data and location pin are now actively mapped in the AgriCo soil classification repository.
                                @else
                                    An AgriCo administrator has reviewed your soil sample scan <strong>#{{ $scan->soil_scan_id }}</strong> and marked it as <strong>Disapproved</strong>. The pinned coordinates will remain hidden on the public map. This may be due to ambiguous color reading, inadequate sample illumination, or misaligned coordinates.
                                @endif
                            </p>

                            {{-- Details Card --}}
                            <div style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; margin-bottom: 24px;">
                                <table width="100%" cellspacing="0" cellpadding="4" style="font-size: 13.5px;">
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600; width: 35%;">Scan ID:</td>
                                        <td style="color: #0f172a; font-weight: 700;">#{{ $scan->soil_scan_id }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Status:</td>
                                        <td>
                                            @if($status === 'Verified')
                                                <span style="display: inline-block; background-color: #dcfce7; color: #15803d; font-weight: 700; font-size: 11px; padding: 2px 8px; border-radius: 9999px;">
                                                    VERIFIED / APPROVED
                                                </span>
                                            @else
                                                <span style="display: inline-block; background-color: #fee2e2; color: #b91c1c; font-weight: 700; font-size: 11px; padding: 2px 8px; border-radius: 9999px;">
                                                    REJECTED / HIDDEN
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $loc = $scan->location ?? \App\Models\LocationMapping::where('soil_scan_id', $scan->soil_scan_id)->first();
                                    @endphp
                                    @if($loc)
                                        <tr>
                                            <td style="color: #64748b; font-weight: 600;">Barangay:</td>
                                            <td style="color: #0f172a; font-weight: 700;">{{ $loc->barangay ?: 'Lal-lo Jurisdiction' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color: #64748b; font-weight: 600;">Coordinates:</td>
                                            <td style="color: #0284c7; font-family: monospace; font-weight: 700;">{{ number_format((float)$loc->latitude, 5) }}, {{ number_format((float)$loc->longitude, 5) }}</td>
                                        </tr>
                                    @endif
                                    @if($scan->soil)
                                        <tr>
                                            <td style="color: #64748b; font-weight: 600;">Classified Soil:</td>
                                            <td style="color: #0f172a; font-weight: 700;">{{ $scan->soil->soil_name ?? $scan->soil->full_code ?? 'Munsell Match' }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td style="color: #64748b; font-weight: 600;">Reviewed At:</td>
                                        <td style="color: #334155; font-weight: 600;">{{ now()->format('M d, Y · h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div style="text-align: center; margin: 28px 0 10px;">
                                @if($status === 'Verified')
                                    <a href="{{ url('/map') }}" style="display: inline-block; background-color: #16a34a; color: #ffffff; text-decoration: none; font-size: 13.5px; font-weight: 700; padding: 12px 28px; border-radius: 9999px; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);">
                                        View on Soil Map
                                    </a>
                                @else
                                    <a href="{{ url('/scan') }}" style="display: inline-block; background-color: #475569; color: #ffffff; text-decoration: none; font-size: 13.5px; font-weight: 700; padding: 12px 28px; border-radius: 9999px;">
                                        Submit New Sample
                                    </a>
                                @endif
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
