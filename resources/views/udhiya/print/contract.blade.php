@extends('layouts.print')
@section('title', 'صك #' . $contract->contract_number)
@section('content')
<div class="print-header">
    <h1>نظام إدارة الأضاحي</h1>
    <p>صك بيع رقم: <strong>{{ $contract->contract_number }}</strong></p>
</div>

<div class="info-grid">
    <div class="info-box">
        <label>العميل</label>
        <span>{{ $contract->customer->name }}</span>
    </div>
    <div class="info-box">
        <label>رقم الهاتف</label>
        <span>{{ $contract->customer->phone }}</span>
    </div>
    @if($contract->slaughter_day)
    <div class="info-box">
        <label>يوم الذبح</label>
        <span>{{ $contract->slaughter_day }}</span>
    </div>
    <div class="info-box">
        <label>ترتيب الذبح</label>
        <span>{{ $contract->slaughter_order ?? '—' }}</span>
    </div>
    @endif
    <div class="info-box">
        <label>تاريخ الصك</label>
        <span>{{ $contract->created_at->format('Y-m-d') }}</span>
    </div>
    <div class="info-box">
        <label>الحالة</label>
        <span>{{ ['active'=>'نشط','completed'=>'مكتمل','cancelled'=>'ملغى'][$contract->status] }}</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>الحيوان</th>
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
            <td>{{ $item->animal->code }}</td>
            <td>{{ $item->animal->product->name }}</td>
            <td>{{ $item->share_type === 'full' ? 'كامل' : ($item->share_type ?? '—') }}</td>
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
<h4 style="margin-top:20px;margin-bottom:10px;">الدفعات</h4>
<table>
    <thead><tr><th>رقم الإيصال</th><th>التاريخ</th><th>الطريقة</th><th>المبلغ</th></tr></thead>
    <tbody>
        @foreach($contract->payments as $payment)
        <tr>
            <td>{{ $payment->receipt_number }}</td>
            <td>{{ $payment->date }}</td>
            <td>{{ ['cash'=>'نقدي','bank'=>'بنك','transfer'=>'تحويل'][$payment->payment_method] }}</td>
            <td>{{ number_format($payment->amount, 2) }} ج.م</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($contract->notes)
<div style="margin-top:15px;padding:10px;background:#f8f8f8;border-radius:4px;">
    <strong>ملاحظات:</strong> {{ $contract->notes }}
</div>
@endif

<div class="signature-section">
    <div class="signature-box">توقيع العميل</div>
    <div class="signature-box">ختم وتوقيع الشركة</div>
</div>
@endsection
