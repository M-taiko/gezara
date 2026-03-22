@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">كشف حساب — {{ $customer->name }}</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('udhiya.reports.index') }}">التقارير</a></li><li class="breadcrumb-item active">{{ $customer->name }}</li></ol>
    </div>
</div>
@endsection
@section('content')
<div class="row mb-3">
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center"><h4>{{ number_format($totalAmount, 2) }}</h4><small class="text-muted">إجمالي الصكوك (ج.م)</small></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center"><h4 class="text-success">{{ number_format($paidAmount, 2) }}</h4><small class="text-muted">المدفوع (ج.م)</small></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center"><h4 class="text-danger">{{ number_format($remainingAmount, 2) }}</h4><small class="text-muted">المتبقي (ج.م)</small></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body text-center"><h4>{{ $customer->contracts->count() }}</h4><small class="text-muted">عدد الصكوك</small></div></div></div>
</div>
@foreach($customer->contracts as $contract)
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>صك #{{ $contract->contract_number }} — {{ $contract->created_at->format('Y-m-d') }}</span>
        <div>
            @if($contract->status === 'active') <span class="badge badge-warning">نشط</span>
            @elseif($contract->status === 'completed') <span class="badge badge-success">مكتمل</span>
            @else <span class="badge badge-danger">ملغى</span> @endif
            <a href="{{ route('udhiya.contracts.show', $contract) }}" class="btn btn-sm btn-outline-info mr-2">عرض</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr class="bg-light"><th>الحيوان</th><th>الحصة</th><th>السعر</th></tr></thead>
                <tbody>
                    @foreach($contract->items as $item)
                    <tr>
                        <td>{{ $item->animal->code }}</td>
                        <td>{{ $item->share_type === 'full' ? 'كامل' : $item->share_type }}</td>
                        <td>{{ number_format($item->total_price, 2) }} ج.م</td>
                    </tr>
                    @endforeach
                    <tr class="bg-light"><td colspan="2" class="font-weight-bold">الإجمالي</td><td class="font-weight-bold">{{ number_format($contract->total_amount, 2) }} ج.م</td></tr>
                </tbody>
            </table>
        </div>
        @if($contract->payments->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr class="bg-success text-white"><th>تاريخ الدفعة</th><th>الطريقة</th><th>المبلغ</th></tr></thead>
                <tbody>
                    @foreach($contract->payments as $payment)
                    <tr>
                        <td>{{ $payment->date }}</td>
                        <td>{{ ['cash'=>'نقدي','bank'=>'بنك','transfer'=>'تحويل'][$payment->payment_method] }}</td>
                        <td class="text-success">{{ number_format($payment->amount, 2) }} ج.م</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endforeach
@endsection
