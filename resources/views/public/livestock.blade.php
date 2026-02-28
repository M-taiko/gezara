<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مشروع الأضاحي - الحصص المتاحة</title>
    <link rel="icon" href="{{ URL::asset('assets/img/brand/favicon.png') }}" type="image/x-icon">
    <link href="{{ URL::asset('assets/css-rtl/style.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/css/icons.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
        }
        .public-navbar {
            background: linear-gradient(135deg, #5f46e4 0%, #4a35c8 100%);
            padding: 14px 0;
            box-shadow: 0 4px 20px rgba(95,70,228,.3);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .public-navbar .brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
        }
        .public-navbar .brand span { color: #ffd700; }

        .hero-section {
            background: linear-gradient(135deg, #5f46e4 0%, #7c64f0 50%, #9b84ff 100%);
            padding: 60px 0 80px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -40%; left: -10%;
            width: 500px; height: 500px;
            background: rgba(255,255,255,.05);
            border-radius: 50%;
        }
        .hero-section h1 { font-size: 2.5rem; font-weight: 800; color: #fff; }
        .hero-section p  { color: rgba(255,255,255,.85); font-size: 1.1rem; }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
            text-align: center;
            transition: transform .2s;
            margin-top: -40px;
        }
        .stat-card:hover { transform: translateY(-4px); }
        .stat-card .stat-icon   { font-size: 2.5rem; margin-bottom: 8px; display: block; }
        .stat-card .stat-num    { font-size: 2rem; font-weight: 800; line-height: 1; }
        .stat-card .stat-label  { font-size: .85rem; color: #666; margin-top: 4px; }

        .filter-bar {
            background: #fff;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            margin-bottom: 24px;
        }
        .filter-btn {
            border-radius: 50px;
            padding: 6px 20px;
            border: 2px solid #5f46e4;
            color: #5f46e4;
            background: transparent;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            margin-left: 8px;
        }
        .filter-btn.active, .filter-btn:hover { background: #5f46e4; color: #fff; }

        .animal-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,.07);
            overflow: hidden;
            transition: transform .25s, box-shadow .25s;
            height: 100%;
        }
        .animal-card:hover { transform: translateY(-6px); box-shadow: 0 12px 35px rgba(0,0,0,.14); }
        .animal-card.status-slaughtered { opacity: .65; }

        .animal-emoji-placeholder {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 90px;
        }
        .bg-cattle { background: linear-gradient(135deg, #e3f0ff, #c8e1ff); }
        .bg-sheep  { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); }
        .bg-goat   { background: linear-gradient(135deg, #fff3e0, #ffe0b2); }
        .bg-camel  { background: linear-gradient(135deg, #fdf3e7, #f5d0a9); }

        .animal-body { padding: 18px; }

        .type-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 14px; border-radius: 50px;
            font-size: .8rem; font-weight: 700; margin-bottom: 10px;
        }
        .type-badge.BQR { background: #e3f0ff; color: #1565c0; }
        .type-badge.GHN { background: #e8f5e9; color: #2e7d32; }
        .type-badge.JDN { background: #fff3e0; color: #e65100; }
        .type-badge.JML { background: #fdf3e7; color: #795548; }

        .status-badge {
            display: inline-block; padding: 3px 12px;
            border-radius: 50px; font-size: .75rem; font-weight: 700;
        }
        .status-available         { background: #e8f5e9; color: #2e7d32; }
        .status-partially_allocated { background: #fff8e1; color: #f57f17; }
        .status-fully_allocated   { background: #fce4ec; color: #c62828; }
        .status-slaughtered       { background: #f3f3f3; color: #777; }

        .price-tag { font-size: 1.4rem; font-weight: 800; color: #5f46e4; }
        .price-per-share { font-size: .8rem; color: #888; }

        .shares-progress { margin: 14px 0; }
        .shares-progress .label {
            display: flex; justify-content: space-between;
            font-size: .82rem; margin-bottom: 5px; color: #555;
        }
        .shares-track { background: #f0f0f0; border-radius: 50px; height: 10px; overflow: hidden; }
        .shares-fill  { height: 100%; border-radius: 50px; transition: width .6s ease; }
        .fill-available  { background: linear-gradient(90deg, #43a047, #66bb6a); }
        .fill-partial    { background: linear-gradient(90deg, #f57f17, #ffa726); }
        .fill-full       { background: linear-gradient(90deg, #c62828, #ef5350); }
        .fill-slaughtered { background: linear-gradient(90deg, #777, #aaa); }

        .share-dots { display: flex; gap: 5px; flex-wrap: wrap; margin-top: 10px; }
        .share-dot {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem; font-weight: 700; border: 2px solid;
        }
        .dot-taken { background: #5f46e4; border-color: #5f46e4; color: #fff; }
        .dot-free  { background: #fff; border-color: #ddd; color: #bbb; }

        .remaining-badge {
            background: #f3f0ff; color: #5f46e4;
            padding: 6px 14px; border-radius: 10px;
            font-size: .85rem; font-weight: 700;
            text-align: center; margin-top: 10px;
        }
        .remaining-badge.none { background: #fce4ec; color: #c62828; }

        .public-footer {
            background: #2d2d3f; color: rgba(255,255,255,.6);
            text-align: center; padding: 24px; font-size: .85rem; margin-top: 60px;
        }
        .section-title { font-size: 1.6rem; font-weight: 800; color: #2d2d3f; margin-bottom: 4px; }
        .section-sub   { color: #888; font-size: .95rem; margin-bottom: 28px; }
        .empty-state   { text-align: center; padding: 60px 20px; color: #aaa; }
        .empty-state .empty-icon { font-size: 80px; margin-bottom: 16px; }

        @media (max-width: 576px) {
            .hero-section h1 { font-size: 1.7rem; }
            .stat-card { margin-top: 12px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="public-navbar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ url('/') }}" class="brand">
                🐄 مشروع <span>الأضاحي</span>
            </a>
            <div class="d-flex gap-2">
                @auth
                <a href="{{ route('udhiya.dashboard') }}" class="btn btn-sm btn-light font-weight-bold">
                    <i class="las la-tachometer-alt mr-1"></i> لوحة التحكم
                </a>
                @else
                <a href="{{ route('signin') }}" class="btn btn-sm btn-light font-weight-bold">
                    <i class="las la-sign-in-alt mr-1"></i> تسجيل الدخول
                </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-section">
    <div class="container text-center position-relative" style="z-index:1;">
        <h1>🐄 حصص الأضاحي المتاحة</h1>
        <p class="mb-0">تصفح الأضاحي المتوفرة واشترك في حصتك قبل نفاد الأماكن</p>
    </div>
</section>

<!-- STATS -->
<div class="container">
    <div class="row">
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card">
                <span class="stat-icon">🐄</span>
                <div class="stat-num text-primary">{{ $stats['total'] }}</div>
                <div class="stat-label">إجمالي الأضاحي</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card">
                <span class="stat-icon">✅</span>
                <div class="stat-num text-success">{{ $stats['available'] }}</div>
                <div class="stat-label">متاح للاشتراك</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card">
                <span class="stat-icon">🎫</span>
                <div class="stat-num text-warning">{{ $stats['total_spots'] }}</div>
                <div class="stat-label">حصة متبقية</div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card">
                <span class="stat-icon">🔪</span>
                <div class="stat-num text-danger">{{ $stats['slaughtered'] }}</div>
                <div class="stat-label">تم ذبحها</div>
            </div>
        </div>
    </div>
</div>

<!-- GRID -->
<div class="container mt-4">

    <!-- Filter Bar -->
    <div class="filter-bar d-flex align-items-center flex-wrap gap-2">
        <strong class="ml-3" style="color:#444;">تصفية:</strong>
        <button class="filter-btn active" onclick="filterCards('all', this)">الكل ({{ $animals->count() }})</button>
        @foreach($categories as $cat)
        @php $catCount = $animals->filter(fn($a) => $a->product?->mainCategory?->id === $cat->id)->count(); @endphp
        <button class="filter-btn" onclick="filterCards('{{ $cat->code }}', this)">
            {{ match($cat->code) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' } }}
            {{ $cat->name }} ({{ $catCount }})
        </button>
        @endforeach
        <button class="filter-btn" onclick="filterCards('available', this)">✅ متاح</button>
        <button class="filter-btn" onclick="filterCards('fully_allocated', this)">🔒 مكتمل</button>
    </div>

    <div>
        <h2 class="section-title">قائمة الأضاحي</h2>
        <p class="section-sub">يمكنك الاشتراك في أي أضحية متاحة — تواصل مع المسؤول لتأكيد حجزك</p>
    </div>

    @if($animals->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">🐄</div>
        <h4 class="text-muted">لا توجد أضاحي مضافة بعد</h4>
    </div>
    @else
    <div class="row" id="livestockGrid">
        @foreach($animals as $animal)
        @php
            $catCode  = $animal->product?->mainCategory?->code ?? 'BQR';
            $catName  = $animal->product?->mainCategory?->name ?? 'حيوان';
            $emoji    = match($catCode) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
            $bgClass  = match($catCode) { 'BQR' => 'bg-cattle', 'GHN' => 'bg-sheep', 'JDN' => 'bg-goat', 'JML' => 'bg-camel', default => 'bg-cattle' };
            $price    = $animal->price_full ?? $animal->cost ?? 0;
            $setting  = $animal->shareSetting;

            $maxShares = $setting ? $setting->total_shares : 1;
            $usedShares = $setting ? $setting->sold_shares : ($animal->status === 'fully_allocated' ? 1 : 0);
            $remainingShares = $setting ? $setting->remaining_shares : ($animal->status === 'available' ? 1 : 0);
            $pct = $maxShares > 0 ? round(($usedShares / $maxShares) * 100) : 0;

            $fillClass = match($animal->status) {
                'available'           => 'fill-available',
                'partially_allocated' => 'fill-partial',
                'fully_allocated'     => 'fill-full',
                'slaughtered'         => 'fill-slaughtered',
                default               => 'fill-available'
            };

            $statusLabel = match($animal->status) {
                'available'           => 'متاح',
                'partially_allocated' => 'متاح جزئياً',
                'fully_allocated'     => 'مكتمل',
                'slaughtered'         => 'تم الذبح',
                default               => $animal->status
            };

            $pricePerShare = $maxShares > 0 ? round($price / $maxShares) : $price;
        @endphp
        <div class="col-lg-4 col-md-6 mb-4 card-item"
             data-category="{{ $catCode }}"
             data-status="{{ $animal->status }}">
            <div class="animal-card {{ $animal->status === 'slaughtered' ? 'status-slaughtered' : '' }}">

                <div class="animal-emoji-placeholder {{ $bgClass }}">{{ $emoji }}</div>

                <div class="animal-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="type-badge {{ $catCode }}">{{ $emoji }} {{ $catName }}</span>
                        <span class="status-badge status-{{ $animal->status }}">{{ $statusLabel }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <div>
                            <div class="price-tag">{{ number_format($price, 0) }} <small>ج.م</small></div>
                            @if($animal->is_grouped)
                            <div class="price-per-share">≈ {{ number_format($pricePerShare, 0) }} ج.م / حصة</div>
                            @endif
                        </div>
                        @if($animal->weight)
                        <div class="text-muted text-left" style="font-size:.85rem;">
                            <i class="las la-weight-hanging"></i>
                            {{ number_format($animal->weight, 0) }} كجم
                        </div>
                        @endif
                    </div>

                    @if($animal->is_grouped && $setting)
                    <div class="shares-progress">
                        <div class="label">
                            <span>الحصص المشتركة</span>
                            <span><strong>{{ $usedShares }}</strong> / {{ $maxShares }} حصة</span>
                        </div>
                        <div class="shares-track">
                            <div class="shares-fill {{ $fillClass }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    <div class="share-dots">
                        @for($i = 1; $i <= $maxShares; $i++)
                        <div class="share-dot {{ $i <= $usedShares ? 'dot-taken' : 'dot-free' }}"
                             title="{{ $i <= $usedShares ? 'حصة مشغولة' : 'حصة متاحة' }}">
                            {{ $i }}
                        </div>
                        @endfor
                    </div>
                    @endif

                    @if(in_array($animal->status, ['available', 'partially_allocated']))
                    <div class="remaining-badge {{ $remainingShares === 0 ? 'none' : '' }}">
                        @if($remainingShares > 0)
                            <i class="las la-check-circle mr-1"></i>
                            متاح — {{ $remainingShares }} {{ $remainingShares === 1 ? 'حصة متبقية' : 'حصص متبقية' }}
                        @else
                            <i class="las la-times-circle mr-1"></i> لا توجد حصص متاحة
                        @endif
                    </div>
                    @elseif($animal->status === 'fully_allocated')
                    <div class="remaining-badge none">
                        <i class="las la-lock mr-1"></i> مكتمل — لا تتوفر حصص
                    </div>
                    @elseif($animal->status === 'slaughtered')
                    <div class="remaining-badge none">
                        <i class="las la-calendar-check mr-1"></i> تم الذبح
                    </div>
                    @endif

                    @if($animal->supplier)
                    <div class="mt-2 text-muted" style="font-size:.8rem;">
                        <i class="las la-truck mr-1"></i> المورد: {{ $animal->supplier->name }}
                    </div>
                    @endif

                    <div class="mt-1 text-muted" style="font-size:.75rem;">
                        <i class="las la-tag mr-1"></i> {{ $animal->code }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="alert alert-info text-center mt-2" style="border-radius:12px; font-size:.95rem;">
        <i class="las la-info-circle tx-18 mr-1"></i>
        لحجز حصتك أو الاستفسار، تواصل مع مسؤول المشروع مباشرةً
    </div>

</div>

<footer class="public-footer">
    <p class="mb-0">مشروع الأضاحي &copy; {{ date('Y') }} — جميع الحقوق محفوظة</p>
</footer>

<script src="{{ URL::asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script>
function filterCards(filter, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('.card-item').forEach(function(card) {
        var cat    = card.dataset.category;
        var status = card.dataset.status;
        var show   = false;

        if (filter === 'all')      show = true;
        else if (filter === 'available') show = (status === 'available' || status === 'partially_allocated');
        else if (filter === 'fully_allocated') show = (status === 'fully_allocated');
        else show = (cat === filter);

        card.style.display = show ? '' : 'none';
    });
}
</script>
</body>
</html>
