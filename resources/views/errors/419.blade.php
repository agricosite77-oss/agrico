<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
    <title>Session Expired — AgriCo</title>
    <script>
        // Immediately replace the history entry to avoid "Confirm Form Resubmission" dialogs
        window.location.replace("{{ route('login') }}");
    </script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            color: #475569;
        }
        .container {
            text-align: center;
            padding: 32px 24px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            max-width: 360px;
            width: 90%;
            border: 1px solid #e2e8f0;
        }
        .btn {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 24px;
            background: #22c55e;
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            border-radius: 9999px;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background: #16a34a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 style="margin: 0 0 8px 0; color: #0f172a; font-size: 18px; font-weight: 700;">Session Expired</h3>
        <p style="margin: 0 0 12px 0; font-size: 13.5px; line-height: 1.5; color: #64748b;">Your session has timed out. Redirecting to login...</p>
        <a href="{{ route('login') }}" class="btn">Return to Login</a>
    </div>
</body>
</html>
