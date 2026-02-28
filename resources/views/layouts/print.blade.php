<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'طباعة')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; direction: rtl; font-size: 14px; color: #333; }
        .print-container { max-width: 800px; margin: 20px auto; padding: 30px; }
        .print-header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .print-header h1 { font-size: 22px; margin-bottom: 5px; }
        .print-header p { color: #666; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .info-box { background: #f8f8f8; padding: 12px; border-radius: 4px; border: 1px solid #ddd; }
        .info-box label { font-weight: bold; display: block; margin-bottom: 4px; color: #555; font-size: 12px; }
        .info-box span { font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #333; color: #fff; padding: 10px; text-align: right; font-size: 13px; }
        td { padding: 9px 10px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #f9f9f9; }
        .totals-section { border-top: 2px solid #333; padding-top: 15px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .total-row.final { font-weight: bold; font-size: 16px; border-top: 1px solid #ccc; padding-top: 8px; margin-top: 8px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger  { background: #f8d7da; color: #721c24; }
        .no-print { display: block; }
        .print-btn { background: #007bff; color: white; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-size: 15px; margin: 10px 5px; }
        .back-btn { background: #6c757d; color: white; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-size: 15px; margin: 10px 5px; text-decoration: none; display: inline-block; }
        .signature-section { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px; }
        .signature-box { text-align: center; border-top: 1px solid #999; padding-top: 8px; font-size: 13px; color: #666; }
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
            .print-container { margin: 0; padding: 15px; }
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
