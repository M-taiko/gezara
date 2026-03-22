@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">تقرير الأرباح</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('udhiya.reports.index') }}">التقارير</a></li><li class="breadcrumb-item active">الأرباح</li></ol>
    </div>
</div>
@endsection
@section('content')
<div class="row mb-4">
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center"><h4>{{ number_format($totalRevenue, 2) }}</h4><small class="text-muted">الإيرادات (ج.م)</small></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center"><h4>{{ number_format($totalCost, 2) }}</h4><small class="text-muted">التكاليف (ج.م)</small></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center"><h4 class="{{ $totalProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($totalProfit, 2) }}</h4><small class="text-muted">الربح الصافي (ج.م)</small></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center"><h4 class="text-info">{{ number_format($totalCollected, 2) }}</h4><small class="text-muted">المحصّل (ج.م)</small></div></div></div>
</div>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="thead-light"><tr><th>رقم الصك</th><th>العميل</th><th>الإيراد</th><th>التكلفة</th><th>الربح</th><th>المحصّل</th><th>المتبقي</th></tr></thead>
                <tbody>
                    @foreach($contracts as $c)
                    @php $cost = $c->items->sum(fn($i) => $i->animal->cost); $profit = $c->total_amount - $cost; @endphp
                    <tr>
                        <td><a href="{{ route('udhiya.contracts.show', $c) }}">{{ $c->contract_number }}</a></td>
                        <td>{{ $c->customer->name }}</td>
                        <td>{{ number_format($c->total_amount, 2) }}</td>
                        <td>{{ number_format($cost, 2) }}</td>
                        <td class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}"><strong>{{ number_format($profit, 2) }}</strong></td>
                        <td>{{ number_format($c->paid_amount, 2) }}</td>
                        <td class="{{ $c->remaining_amount > 0 ? 'text-danger' : '' }}">{{ number_format($c->remaining_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
