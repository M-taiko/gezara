@extends('layouts.print')

@section('title', 'إيصال - ' . $payment->receipt_number)

@section('content')

<div class="print-header">
    <div class="logo-section">
        <div class="system-title">نظام إدارة الأضاحي</div>
        <div class="tagline">✨ تم تصميم وإنشاء البرنامج بواسطة masarsoft.io ✨</div>
        <div class="divider"></div>
        <div style="font-size: 16px; font-weight: 700; color: #2c3e50; margin-top: 10px;">🧾 إيصال قبض</div>
    </div>
</div>

<div class="info-grid">
    <div class="info-box">
        <label>🧾 رقم الإيصال</label>
        <span style="color: #2980b9; font-size: 15px;">{{ $payment->receipt_number }}</span>
    </div>
    <div class="info-box">
        <label>📅 التاريخ</label>
        <span>{{ $payment->date->format('d/m/Y') }}</span>
    </div>
    <div class="info-box">
        <label>👤 العميل</label>
        <span>{{ $payment->contract->customer->name }}</span>
    </div>
    <div class="info-box">
        <label>📞 رقم الهاتف</label>
        <span>{{ $payment->contract->customer->phone ?? '—' }}</span>
    </div>
    <div class="info-box">
        <label>📋 رقم الصك</label>
        <span>{{ $payment->contract->contract_number }}</span>
    </div>
    <div class="info-box">
        <label>💳 طريقة الدفع</label>
        <span>{{ $payment->methodLabel() }}</span>
    </div>
    @if($payment->reference_number)
    <div class="info-box">
        <label>🔢 الرقم المرجعي</label>
        <span style="color: #2c3e50;">{{ $payment->reference_number }}</span>
    </div>
    @endif
    @if($payment->wallet)
    <div class="info-box">
        <label>💰 الخزينة</label>
        <span>{{ $payment->wallet->getTypeLabel() }} - {{ $payment->wallet->name }}</span>
    </div>
    @endif
</div>

<div class="section-header">📊 تفاصيل الدفعة</div>

<table>
    <thead>
        <tr>
            <th>البيان</th>
            <th style="text-align: left;">القيمة</th>
        </tr>
    </thead>
    <tbody>
        <tr style="background: #f0f7ff;">
            <td><strong>💰 المبلغ المستلم</strong></td>
            <td style="text-align: left; font-weight: 900; color: #2980b9; font-size: 15px;">{{ number_format($payment->amount, 2) }} ج.م</td>
        </tr>
        <tr>
            <td>📋 رقم الصك</td>
            <td style="text-align: left;">{{ $payment->contract->contract_number }}</td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td>📌 إجمالي الصك</td>
            <td style="text-align: left; font-weight: 600;">{{ number_format($payment->contract->total_amount, 2) }} ج.م</td>
        </tr>
        <tr>
            <td>✅ إجمالي المدفوع</td>
            <td style="text-align: left; font-weight: 600; color: #27ae60;">{{ number_format($payment->contract->paid_amount, 2) }} ج.م</td>
        </tr>
        <tr style="background: #f9f9f9;">
            <td><strong>⏳ المتبقي من الصك</strong></td>
            <td style="text-align: left; font-weight: 700; color: {{ $payment->contract->remaining_amount > 0 ? '#e74c3c' : '#27ae60' }};">
                {{ number_format($payment->contract->remaining_amount, 2) }} ج.م
            </td>
        </tr>
    </tbody>
</table>

@if($payment->notes)
<div class="section-header">📝 ملاحظات</div>
<div style="background: #f9f9f9; padding: 12px; border-radius: 4px; border: 1px solid #ddd;">
    {{ $payment->notes }}
</div>
@endif

<div class="totals-section">
    <div class="highlight-box">
        <div style="font-size: 12px; color: #555; margin-bottom: 8px;">المبلغ الإجمالي المستلم</div>
        <div class="value">{{ number_format($payment->amount, 2) }} ج.م</div>
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

<div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
    <p style="font-size: 11px; color: #7f8c8d;">
        تم إصدار هذا الإيصال بواسطة <strong>نظام إدارة الأضاحي</strong> من تطوير <strong>masarsoft.io</strong>
    </p>
    <p style="font-size: 10px; color: #bdc3c7; margin-top: 5px;">
        {{ now()->format('d/m/Y H:i') }}
    </p>
</div>

{{-- Footer with customer statement link --}}
<div style="text-align: center; margin-top: 30px; padding: 15px 0; border-top: 1px solid #eee;">
    <p style="font-size: 11px; color: #999; margin-bottom: 10px;">
        <a href="{{ route('udhiya.customers.statement', $payment->contract->customer) }}"
           style="color: #3498db; text-decoration: none;">
           📄 اطبع كشف حساب العميل الكامل
        </a>
    </p>
</div>

@endsection
