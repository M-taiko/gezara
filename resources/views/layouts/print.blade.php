<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'طباعة')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arabic Typesetting', 'Segoe UI', Tahoma, Arial, sans-serif; direction: rtl; font-size: 13px; color: #222; line-height: 1.6; }
        .print-container { max-width: 850px; margin: 20px auto; padding: 30px 25px; }

        /* Header styling */
        .print-header { text-align: center; border-bottom: 3px solid #2c3e50; padding-bottom: 20px; margin-bottom: 30px; }
        .print-header .logo-section { margin-bottom: 15px; }
        .print-header .system-title { font-size: 28px; font-weight: 900; color: #1a1a1a; margin-bottom: 8px; letter-spacing: 0.5px; }
        .print-header .tagline { color: #e74c3c; font-size: 13px; font-weight: 600; margin-bottom: 12px; font-style: italic; }
        .print-header .divider { height: 2px; background: linear-gradient(to left, #3498db, #e74c3c); margin: 12px 0; }
        .print-header .powered-by { font-size: 11px; color: #7f8c8d; margin-top: 10px; }
        .print-header .powered-by strong { color: #2c3e50; }
        .print-header p { color: #555; font-size: 13px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 25px; }
        .info-box { background: #f5f5f5; padding: 14px; border-radius: 5px; border: 1px solid #ccc; }
        .info-box label { font-weight: 700; display: block; margin-bottom: 5px; color: #333; font-size: 11px; text-transform: uppercase; letter-spacing: 0.3px; }
        .info-box span { font-size: 14px; color: #111; font-weight: 600; }

        /* Section headers */
        .section-header { background: #2c3e50; color: white; padding: 12px 15px; margin: 25px 0 15px 0; border-radius: 4px; font-size: 13px; font-weight: 700; }
        .section-header.alt { background: #3498db; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; border: 1px solid #999; }
        th { background: #1a1a1a; color: #fff; padding: 12px 10px; text-align: right; font-size: 12px; font-weight: 700; border: 1px solid #999; }
        td { padding: 10px; border: 1px solid #ddd; border-bottom: none; text-align: right; }
        tr:nth-child(even) td { background: #fafafa; }
        tr:last-child td { border-bottom: 1px solid #ddd; }

        /* Totals and highlights */
        .totals-section { border-top: 3px solid #1a1a1a; padding-top: 18px; margin-top: 18px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; }
        .total-row span:last-child { font-weight: 600; }
        .total-row.final { font-weight: 800; font-size: 16px; border-top: 2px solid #333; padding-top: 10px; margin-top: 10px; color: #1a1a1a; }
        .total-row.highlight { background: #f0f7ff; padding: 10px; border-radius: 4px; font-size: 14px; }

        /* Groups and items */
        .group-item { background: #f9f9f9; padding: 10px 12px; margin-bottom: 8px; border-left: 3px solid #3498db; border-radius: 3px; }
        .group-item strong { color: #2c3e50; }
        .group-item .badge { background: #3498db; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px; margin-right: 5px; }

        .signature-section { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; margin-top: 50px; }
        .signature-box { text-align: center; border-top: 1px solid #666; padding-top: 10px; font-size: 12px; color: #333; font-weight: 600; }

        .no-print { display: block; }
        .print-btn { background: #007bff; color: white; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-size: 15px; margin: 10px 5px; }
        .back-btn { background: #6c757d; color: white; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-size: 15px; margin: 10px 5px; text-decoration: none; display: inline-block; }

        h4 { font-size: 14px; font-weight: 700; margin: 20px 0 12px 0; }
        .highlight-box { background: #f0f7ff; border: 2px solid #3498db; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .highlight-box .value { font-size: 18px; font-weight: 900; color: #2980b9; }

        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
            .print-container { margin: 0; padding: 15px; }
            .info-grid { gap: 12px; }
            .info-box { padding: 10px; }
            table { margin-bottom: 18px; }
            .print-header { margin-bottom: 20px; padding-bottom: 15px; }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="no-print" style="margin-bottom: 15px; text-align: center;">
            <button class="print-btn" onclick="window.print()">طباعة</button>
            <a href="javascript:history.back()" class="back-btn">رجوع</a>
        </div>
        @yield('content')
        <div class="no-print" style="margin-top: 15px; text-align: center;">
            <button class="print-btn" onclick="window.print()">طباعة</button>
        </div>
    </div>
</body>
</html>
