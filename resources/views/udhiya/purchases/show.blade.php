@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">مشترى #{{ $purchase->id }}</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.purchases.index') }}">المشتريات</a></li>
            <li class="breadcrumb-item active">#{{ $purchase->id }}</li>
        </ol>
    </div>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">تفاصيل المشترى</h6></div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted">المورد</th><td>{{ $purchase->supplier->name }}</td></tr>
                    <tr><th class="text-muted">التاريخ</th><td>{{ $purchase->date }}</td></tr>
                    <tr><th class="text-muted">الإجمالي</th><td><strong>{{ number_format($purchase->total, 2) }} ج.م</strong></td></tr>
                    <tr><th class="text-muted">المدفوع</th><td class="text-success">{{ number_format($purchase->paid, 2) }} ج.م</td></tr>
                    <tr><th class="text-muted">المتبقي</th><td class="text-danger">{{ number_format($purchase->total - $purchase->paid, 2) }} ج.م</td></tr>
                    <tr><th class="text-muted">الحالة</th><td>
                        @if($purchase->status === 'confirmed') <span class="badge badge-success">مؤكد</span>
                        @else <span class="badge badge-secondary">مسودة</span> @endif
                    </td></tr>
                    @if($purchase->notes)<tr><th class="text-muted">ملاحظات</th><td>{{ $purchase->notes }}</td></tr>@endif
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">أصناف المشترى</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr><th>المنتج</th><th>الكمية</th><th>الوزن</th><th>سعر الوحدة</th><th>الإجمالي</th></tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ $item->product->mainCategory->name }} — {{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->weight ? $item->weight . ' كجم' : '—' }}</td>
                            <td>{{ number_format($item->cost_per_unit, 2) }} ج.م</td>
                            <td>{{ number_format($item->total, 2) }} ج.م</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="card-title mb-0">الحيوانات المنشأة ({{ $purchase->animals->count() }})</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="thead-light">
                        <tr><th>الكود</th><th>النوع</th><th>المخزن</th><th>الحالة</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->animals as $animal)
                        <tr>
                            <td>{{ $animal->code }}</td>
                            <td>{{ $animal->product->name }}</td>
                            <td>{{ $animal->warehouse->name }}</td>
                            <td>
                                @php $labels = \App\Models\Animal::STATUS_LABELS; @endphp
                                <span class="badge badge-{{ ['available'=>'success','partially_allocated'=>'warning','fully_allocated'=>'primary','slaughtered'=>'dark'][$animal->status] ?? 'secondary' }}">
                                    {{ $labels[$animal->status] ?? $animal->status }}
                                </span>
                            </td>
                            <td><a href="{{ route('udhiya.animals.show', $animal) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
