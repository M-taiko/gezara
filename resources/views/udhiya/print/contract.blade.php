@extends('layouts.print')
@php
$shareLabels = ['full'=>'كامل','seven'=>'سُبع','six'=>'سُدس','five'=>'خُمس','quarter'=>'ربع','third'=>'ثُلث','half'=>'نصف'];
@endphp
@section('title', 'صك #' . $contract->contract_number)
@section('content')
<div class="print-header">
    <h1>🐑 نظام إدارة الأضاحي</h1>
    <p>صك بيع رقم: <strong>{{ $contract->contract_number }}</strong></p>
</div>

<div class="info-grid">
    <div class="info-box">
        <label>العميل</label>
        <span>{{ $contract->customer->name }}</span>
    </div>
    <div class="info-box">
        <label>رقم الهاتف</label>
        <span>{{ $contract->customer->phone ?? '—' }}</span>
    </div>
    @if($contract->slaughter_day)
    <div class="info-box">
        <label>يوم الذبح</label>
        <span>{{ \Carbon\Carbon::parse($contract->slaughter_day)->format('d/m/Y') }}</span>
    </div>
    <div class="info-box">
        <label>ترتيب الذبح</label>
        <span>{{ $contract->slaughter_order ?? '—' }}</span>
    </div>
    @endif
    <div class="info-box">
        <label>تاريخ الصك</label>
        <span>{{ $contract->created_at->format('d/m/Y') }}</span>
    </div>
    <div class="info-box">
        <label>الحالة</label>
        <span>{{ ['active'=>'نشط','completed'=>'مكتمل','cancelled'=>'ملغى'][$contract->status] ?? $contract->status }}</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الأضحية</th>
            <th>النوع</th>
            <th>نوع الحصة</th>
            <th>الأنصبة</th>
            <th>سعر الوحدة</th>
            <th>الإجمالي</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contract->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->animal?->code ?? '—' }}</td>
            <td>{{ $item->animal?->product?->name ?? '—' }}</td>
            <td>{{ $shareLabels[$item->share_type] ?? $item->share_type ?? '—' }}</td>
            <td>{{ $item->shares_count }}</td>
            <td>{{ number_format($item->unit_price, 2) }} ج.م</td>
            <td>{{ number_format($item->total_price, 2) }} ج.م</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="totals-section">
    <div class="total-row"><span>الإجمالي:</span><span>{{ number_format($contract->total_amount, 2) }} ج.م</span></div>
    <div class="total-row"><span>المدفوع:</span><span>{{ number_format($contract->paid_amount, 2) }} ج.م</span></div>
    <div class="total-row final"><span>المتبقي:</span><span>{{ number_format($contract->remaining_amount, 2) }} ج.م</span></div>
</div>

@if($contract->payments->count() > 0)
<h4>الدفعات</h4>
<table>
    <thead><tr><th>رقم الإيصال</th><th>التاريخ</th><th>طريقة الدفع</th><th>المبلغ</th></tr></thead>
    <tbody>
        @foreach($contract->payments as $payment)
        <tr>
            <td>{{ $payment->receipt_number }}</td>
            <td>{{ \Carbon\Carbon::parse($payment->date)->format('d/m/Y') }}</td>
            <td>{{ ['cash'=>'نقدي','bank'=>'بنك','check'=>'شيك','transfer'=>'تحويل'][$payment->payment_method] ?? $payment->payment_method }}</td>
            <td>{{ number_format($payment->amount, 2) }} ج.م</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($contract->notes)
<div style="margin-top:18px;padding:12px;background:#f0f0f0;border-radius:5px;border-right:3px solid #333;">
    <strong>ملاحظات:</strong> {{ $contract->notes }}
</div>
@endif

<div class="signature-section">
    <div class="signature-box">توقيع العميل</div>
    <div class="signature-box">ختم وتوقيع الشركة</div>
</div>
@endsection
