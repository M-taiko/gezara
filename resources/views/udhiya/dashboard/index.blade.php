@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">لوحة التحكم — الأضاحي</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="#">الأضاحي</a></li><li class="breadcrumb-item active">لوحة التحكم</li></ol>
    </div>
</div>
@endsection
@section('content')
{{-- Stats Cards --}}
<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="icon-box bg-primary text-white rounded p-3 ml-3"><i class="fas fa-horse fa-2x"></i></div>
                    <div>
                        <p class="text-muted mb-1">إجمالي الحيوانات</p>
                        <h3 class="mb-0">{{ $stats['animals_total'] }}</h3>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-success">{{ $stats['animals_available'] }} متاح</small>
                    <small class="text-warning mr-2">{{ $stats['animals_allocated'] }} مخصص</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="icon-box bg-success text-white rounded p-3 ml-3"><i class="fas fa-file-contract fa-2x"></i></div>
                    <div>
                        <p class="text-muted mb-1">الصكوك النشطة</p>
                        <h3 class="mb-0">{{ $stats['contracts_active'] }}</h3>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted">إجمالي الصكوك: {{ $stats['contracts_total'] }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="icon-box bg-info text-white rounded p-3 ml-3"><i class="fas fa-money-bill-wave fa-2x"></i></div>
                    <div>
                        <p class="text-muted mb-1">إجمالي الإيرادات</p>
                        <h3 class="mb-0">{{ number_format($stats['revenue_total'], 2) }} ج.م</h3>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-success">محصّل: {{ number_format($stats['collected_total'], 2) }} ج.م</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="icon-box bg-warning text-white rounded p-3 ml-3"><i class="fas fa-coins fa-2x"></i></div>
                    <div>
                        <p class="text-muted mb-1">رصيد الخزنة</p>
                        <h3 class="mb-0">{{ number_format($stats['treasury_balance'], 2) }} ج.م</h3>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-danger">متبقي: {{ number_format($stats['remaining_total'], 2) }} ج.م</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Recent Contracts --}}
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">آخر الصكوك</h6>
                <a href="{{ route('udhiya.contracts.index') }}" class="btn btn-sm btn-outline-primary ml-auto">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>رقم الصك</th><th>العميل</th><th>المبلغ</th><th>الحالة</th></tr></thead>
                    <tbody>
                        @forelse($recentContracts as $c)
                        <tr>
                            <td><a href="{{ route('udhiya.contracts.show', $c) }}">{{ $c->contract_number }}</a></td>
                            <td>{{ $c->customer->name }}</td>
                            <td>{{ number_format($c->total_amount, 2) }} ج.م</td>
                            <td>
                                @if($c->status === 'active') <span class="badge badge-warning">نشط</span>
                                @elseif($c->status === 'completed') <span class="badge badge-success">مكتمل</span>
                                @else <span class="badge badge-danger">ملغى</span>
                                @endif
                            </td>
                        </tr>
                        @empty<tr><td colspan="4" class="text-center text-muted">لا توجد صكوك بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- Recent Purchases --}}
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">آخر المشتريات</h6>
                <a href="{{ route('udhiya.purchases.index') }}" class="btn btn-sm btn-outline-primary ml-auto">عرض الكل</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>المورد</th><th>التاريخ</th><th>الإجمالي</th><th>المدفوع</th></tr></thead>
                    <tbody>
                        @forelse($recentPurchases as $p)
                        <tr>
                            <td><a href="{{ route('udhiya.purchases.show', $p) }}">{{ $p->supplier->name }}</a></td>
                            <td>{{ $p->date }}</td>
                            <td>{{ number_format($p->total, 2) }} ج.م</td>
                            <td>{{ number_format($p->paid, 2) }} ج.م</td>
                        </tr>
                        @empty<tr><td colspan="4" class="text-center text-muted">لا توجد مشتريات بعد</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
