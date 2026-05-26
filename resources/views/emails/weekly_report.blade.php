<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Weekly Performance Report</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f9; color: #333; padding: 20px; }
        .card { background: #fff; border-radius: 8px; padding: 25px; max-width: 600px; margin: 0 auto; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #1e3a8a; font-size: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; margin-top: 0; }
        ul { list-style: none; padding: 0; }
        li { padding: 12px 0; border-bottom: 1px solid #f3f4f6; overflow: hidden; }
        .label { font-weight: 600; color: #4b5563; }
        .value { float: right; font-weight: bold; color: #10b981; }
        .footer { font-size: 12px; color: #9ca3af; text-align: center; margin-top: 25px; }
    </style>
</head>
<body>

    <div class="card">
        <h1>Lelyan Express: Weekly Executive Performance Report</h1>
        <p>Dear Administrator,</p>
        <p>Please find below the comprehensive performance metrics for the platform for the past week:</p>
        
        <ul>
            <li>
                <span class="label">Total Published Articles:</span>
                <span class="value">{{ $reportData['total_articles'] ?? 0 }}</span>
            </li>
            <li>
                <span class="label">Most Read Articles Count:</span>
                <span class="value">{{ $reportData['top_viewed_count'] ?? 0 }}</span>
            </li>
            <li>
                <span class="label">Active Authors this Week:</span>
                <span class="value">{{ $reportData['active_authors'] ?? 0 }}</span>
            </li>
        </ul>

        <p class="footer">
            This email was generated automatically by the platform's central scheduling system.
        </p>
    </div>

</body>
</html>