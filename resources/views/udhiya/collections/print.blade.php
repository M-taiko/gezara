@extends('layouts.print')

@section('title', 'إيصال الاستلام - ' . $payment->receipt_number)

@section('content')

<div class="print-header">
    <h1>🧾 إيصال الاستلام</h1>
    <p>برنامج إدارة الأضاحي</p>
</div>

<div class="info-grid">
    <div class="info-box">
        <label>رقم الإيصال</label>
        <span>#{{ $payment->receipt_number }}</span>
    </div>
    <div class="info-box">
        <label>التاريخ</label>
        <span>{{ $payment->date->format('d/m/Y') }}</span>
    </div>
    <div class="info-box">
        <label>العميل</label>
        <span>{{ $payment->contract->customer->name }}</span>
    </div>
    <div class="info-box">
        <label>رقم الهاتف</label>
        <span>{{ $payment->contract->customer->phone ?? '—' }}</span>
    </div>
    <div class="info-box">
        <label>رقم الصك</label>
        <span>{{ $payment->contract->contract_number }}</span>
    </div>
    <div class="info-box">
        <label>طريقة الدفع</label>
        <span>{{ $payment->methodLabel() }}</span>
    </div>
    @if($payment->reference_number)
    <div class="info-box">
        <label>الرقم المرجعي</label>
        <span>{{ $payment->reference_number }}</span>
    </div>
    @endif
    @if($payment->wallet)
    <div class="info-box">
        <label>الخزينة</label>
        <span>{{ $payment->wallet->getTypeLabel() }} - {{ $payment->wallet->name }}</span>
    </div>
    @endif
</div>

<table>
    <thead>
        <tr>
            <th>البيان</th>
            <th style="text-align: left;">القيمة</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>المبلغ المستلم</td>
            <td style="text-align: left; font-weight: 700;">{{ number_format($payment->amount, 2) }} ج.م</td>
        </tr>
        <tr>
            <td>المتبقي من الصك</td>
            <td style="text-align: left;">{{ number_format($payment->contract->remaining_amount, 2) }} ج.م</td>
        </tr>
        <tr>
            <td>إجمالي الصك</td>
            <td style="text-align: left;">{{ number_format($payment->contract->total_amount, 2) }} ج.م</td>
        </tr>
    </tbody>
</table>

@if($payment->notes)
<h4>📝 ملاحظات</h4>
<div style="background: #f9f9f9; padding: 12px; border-radius: 4px; border: 1px solid #ddd;">
    {{ $payment->notes }}
</div>
@endif

<div class="totals-section">
    <div class="total-row final">
        <span>إجمالي المستلم:</span>
        <span>{{ number_format($payment->amount, 2) }} ج.م</span>
    </div>
</div>

<div class="signature-section">
    <div class="signature-box">
        <p>توقيع العميل</p>
    </div>
    <div class="signature-box">
        <p>توقيع المستقبِل</p>
    </div>
</div>

<div style="text-align: center; margin-top: 30px; font-size: 11px; color: #999;">
    <p>تم إصدار هذا الإيصال بواسطة برنامج إدارة الأضاحي</p>
    <p>{{ now()->format('d/m/Y H:i') }}</p>
</div>

@endsection
