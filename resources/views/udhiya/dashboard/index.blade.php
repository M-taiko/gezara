@extends('layouts.master')

@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="page-title-emoji">📊</span> لوحة التحكم</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">برنامج الأضاحي 🐄</li>
        </ol>
    </div>
    <div class="page-rightheader d-flex align-items-center">
        <span style="font-size:.82rem;color:var(--text-muted);">📅 {{ now()->format('Y/m/d') }}</span>
    </div>
</div>
@endsection

@section('content')

{{-- Stats Row 1 --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card purple h-100">
            <span class="stat-icon">🐄</span>
            <div class="stat-value">{{ $stats['animals_total'] }}</div>
            <div class="stat-label">إجمالي الحيوانات</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card green h-100">
            <span class="stat-icon">✅</span>
            <div class="stat-value">{{ $stats['animals_available'] }}</div>
            <div class="stat-label">متاح للبيع</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card orange h-100">
            <span class="stat-icon">📋</span>
            <div class="stat-value">{{ $stats['contracts_active'] }}</div>
            <div class="stat-label">صكوك نشطة</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card pink h-100">
            <span class="stat-icon">🙋</span>
            <div class="stat-value">{{ $stats['customers_total'] }}</div>
            <div class="stat-label">إجمالي العملاء</div>
        </div>
    </div>
</div>

{{-- Stats Row 2 --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card blue h-100">
            <span class="stat-icon">💰</span>
            <div class="stat-value">{{ number_format($stats['revenue_total']/1000,1) }}k</div>
            <div class="stat-label">إجمالي المبيعات ج.م</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card teal h-100">
            <span class="stat-icon">💵</span>
            <div class="stat-value">{{ number_format($stats['collected_total']/1000,1) }}k</div>
            <div class="stat-label">المحصّل ج.م</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card orange h-100">
            <span class="stat-icon">⏳</span>
            <div class="stat-value">{{ number_format($stats['remaining_total']/1000,1) }}k</div>
            <div class="stat-label">المتبقي ج.م</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card green h-100">
            <span class="stat-icon">🏦</span>
            <div class="stat-value">{{ number_format($stats['treasury_balance']/1000,1) }}k</div>
            <div class="stat-label">رصيد الخزينة ج.م</div>
        </div>
    </div>
</div>

{{-- Collection Progress --}}
@if($stats['revenue_total'] > 0)
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span style="font-weight:700;font-size:.9rem;">💹 نسبة التحصيل</span>
            <span style="font-weight:700;color:var(--primary);">
                {{ number_format(($stats['collected_total']/$stats['revenue_total'])*100,1) }}%
            </span>
        </div>
        <div class="progress">
            <div class="progress-bar" style="width:{{ ($stats['collected_total']/$stats['revenue_total'])*100 }}%"></div>
        </div>
        <div class="d-flex justify-content-between mt-2" style="font-size:.78rem;color:var(--text-muted);">
            <span>محصّل: {{ number_format($stats['collected_total']) }} ج.م</span>
            <span>إجمالي: {{ number_format($stats['revenue_total']) }} ج.م</span>
        </div>
    </div>
</div>
@endif

{{-- Recent Tables --}}
<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>📋 آخر الصكوك</span>
                <a href="{{ route('udhiya.contracts.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>رقم الصك</th><th>العميل</th><th>المبلغ</th><th>الحالة</th></tr></thead>
                        <tbody>
                            @forelse($recentContracts as $c)
                            <tr>
                                <td><strong style="color:var(--primary);">{{ $c->contract_number }}</strong></td>
                                <td>{{ $c->customer->name }}</td>
                                <td>{{ number_format($c->total_amount) }} <small class="text-muted">ج.م</small></td>
                                <td>
                                    @if($c->status==='active') <span class="badge badge-warning">🟡 نشط</span>
                                    @elseif($c->status==='completed') <span class="badge badge-success">✅ مكتمل</span>
                                    @else <span class="badge badge-secondary">{{ $c->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="empty-state"><span class="empty-icon">📋</span><p>لا توجد صكوك</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>🛒 آخر المشتريات</span>
                <a href="{{ route('udhiya.purchases.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>المورد</th><th>التاريخ</th><th>الإجمالي</th><th>الحالة</th></tr></thead>
                        <tbody>
                            @forelse($recentPurchases as $p)
                            <tr>
                                <td><strong>{{ $p->supplier->name }}</strong></td>
                                <td style="font-size:.82rem;color:var(--text-muted);">{{ $p->date }}</td>
                                <td>{{ number_format($p->total) }} <small class="text-muted">ج.م</small></td>
                                <td>
                                    @if($p->status==='confirmed') <span class="badge badge-success">✅ مؤكدة</span>
                                    @elseif($p->status==='pending') <span class="badge badge-warning">⏳ معلقة</span>
                                    @else <span class="badge badge-secondary">{{ $p->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="empty-state"><span class="empty-icon">🛒</span><p>لا توجد مشتريات</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
