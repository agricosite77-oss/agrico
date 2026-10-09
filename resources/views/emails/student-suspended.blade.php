<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Suspended Notification</title>
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
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
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
            margin-top: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .alert-banner {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 16px 20px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-badge {
            display: inline-block;
            background-color: #ef4444;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 9999px;
            letter-spacing: 0.04em;
        }
        .content {
            font-size: 14.5px;
            line-height: 1.6;
            color: #334155;
        }
        .details-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }
        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .detail-label {
            color: #64748b;
            font-weight: 600;
        }
        .detail-val {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }
        .reason-box {
            background-color: #fff7ed;
            border: 1px solid #ffedd5;
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 14px;
        }
        .btn-container {
            text-align: center;
            margin-top: 28px;
        }
        .btn {
            display: inline-block;
            background-color: #22c55e;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            padding: 12px 28px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }
        .footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="brand-title">AgriCo</h1>
            <div class="brand-sub">Soil Management System</div>
        </div>

        <div style="text-align: center; margin-bottom: 16px;">
            <span class="alert-badge">Student Suspended</span>
        </div>

        <div class="content">
            <p style="margin-top: 0;">
                Hello <strong>{{ $admin?->full_name ?: 'Administrator' }}</strong>,
            </p>
            <p>
                This is an official administrative alert notifying you that student <strong>{{ $student->name }}</strong> has been temporarily suspended from the AgriCo system.
            </p>

            <div class="details-card">
                <div class="detail-row">
                    <span class="detail-label">Student Name:</span>
                    <span class="detail-val">{{ $student->name }}</span>
                </div>
                @if(!empty($student->student_id))
                <div class="detail-row">
                    <span class="detail-label">Student ID:</span>
                    <span class="detail-val" style="font-family: monospace;">{{ $student->student_id }}</span>
                </div>
                @endif
                @if(!empty($student->section))
                <div class="detail-row">
                    <span class="detail-label">Section:</span>
                    <span class="detail-val">{{ $student->section }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Student Email:</span>
                    <span class="detail-val">{{ $student->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Suspended Until:</span>
                    <span class="detail-val" style="color: #ea580c;">
                        {{ $until ? $until->format('M d, Y h:i A') : 'Manual / Indefinite' }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Suspended By:</span>
                    <span class="detail-val">{{ $suspendedBy }}</span>
                </div>

                <div class="reason-box">
                    <div style="font-size: 11px; font-weight: 700; color: #c2410c; text-transform: uppercase; margin-bottom: 4px;">
                        Suspension Reason:
                    </div>
                    <div style="font-size: 13.5px; font-weight: 600; color: #9a3412;">
                        "{{ $reason }}"
                    </div>
                </div>
            </div>

            <div class="btn-container">
                <a href="{{ route('students.record') }}" class="btn">
                    View Student Records
                </a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} AgriCo Soil Management. All rights reserved.</p>
            <p>You received this automated notification because you are assigned as the administrator for this student account.</p>
        </div>
    </div>
</body>
</html>
