@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">كشف حساب مورد — {{ $supplier->name }}</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('udhiya.reports.index') }}">التقارير</a></li><li class="breadcrumb-item active">{{ $supplier->name }}</li></ol>
    </div>
</div>
@endsection
@section('content')
<div class="row mb-3">
    <div class="col-xl-3"><div class="card"><div class="card-body text-center"><h4>{{ number_format($totalPurchases, 2) }}</h4><small class="text-muted">إجمالي المشتريات (ج.م)</small></div></div></div>
    <div class="col-xl-3"><div class="card"><div class="card-body text-center"><h4 class="text-success">{{ number_format($totalPaid, 2) }}</h4><small class="text-muted">المدفوع (ج.م)</small></div></div></div>
    <div class="col-xl-3"><div class="card"><div class="card-body text-center"><h4 class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($balance, 2) }}</h4><small class="text-muted">الرصيد المتبقي (ج.م)</small></div></div></div>
    <div class="col-xl-3"><div class="card"><div class="card-body text-center"><h4>{{ $supplier->purchases->count() }}</h4><small class="text-muted">عدد المشتريات</small></div></div></div>
</div>
<div class="card">
    <div class="card-header"><h6 class="card-title mb-0">المشتريات</h6></div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="thead-light"><tr><th>#</th><th>التاريخ</th><th>الإجمالي</th><th>المدفوع</th><th>المتبقي</th><th></th></tr></thead>
            <tbody>
                @foreach($supplier->purchases as $purchase)
                <tr>
                    <td>{{ $purchase->id }}</td>
                    <td>{{ $purchase->date }}</td>
                    <td>{{ number_format($purchase->total, 2) }} ج.م</td>
                    <td class="text-success">{{ number_format($purchase->paid, 2) }} ج.م</td>
                    <td class="{{ ($purchase->total - $purchase->paid) > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($purchase->total - $purchase->paid, 2) }} ج.م</td>
                    <td><a href="{{ route('udhiya.purchases.show', $purchase) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
