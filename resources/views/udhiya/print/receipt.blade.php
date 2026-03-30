@extends('layouts.print')
@section('title', 'إيصال #' . $payment->receipt_number)
@section('content')
<div class="print-header">
    <h1>نظام إدارة الأضاحي</h1>
    <p>إيصال قبض رقم: <strong>{{ $payment->receipt_number }}</strong></p>
</div>

<div class="info-grid">
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">استلمنا من</label>
        <span>{{ $payment->contract->customer->name }}</span>
    </div>
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">رقم الهاتف</label>
        <span>{{ $payment->contract->customer->phone }}</span>
    </div>
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">رقم الصك</label>
        <span>{{ $payment->contract->contract_number }}</span>
    </div>
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">تاريخ الإيصال</label>
        <span>{{ $payment->date }}</span>
    </div>
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">طريقة الدفع</label>
        <span>{{ ['cash'=>'نقدي','bank'=>'بنك','transfer'=>'تحويل'][$payment->payment_method] }}</span>
    </div>
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">رصيد الصك بعد الدفعة</label>
        <span>{{ number_format($payment->contract->remaining_amount, 2) }} ج.م</span>
    </div>
</div>

<div style="background:#f0f7ff;border:2px solid #007bff;border-radius:8px;padding:25px;text-align:center;margin:20px 0;">
    <div style="font-size:13px;color:#555;margin-bottom:5px;">المبلغ المستلم</div>
    <div style="font-size:36px;font-weight:bold;color:#007bff;">{{ number_format($payment->amount, 2) }} ج.م</div>
</div>

@if($payment->notes)
<div style="margin-top:15px;padding:10px;background:#f8f8f8;border-radius:4px;">
    <strong>ملاحظات:</strong> {{ $payment->notes }}
</div>
@endif

<div class="signature-section">
    <div class="signature-box">توقيع العميل</div>
    <div class="signature-box">توقيع المحصّل</div>
</div>
@endsection
