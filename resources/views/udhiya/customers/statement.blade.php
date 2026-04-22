@extends('layouts.print')

@section('title', 'كشف حساب - ' . $customer->name)

@section('content')

<div class="print-header">
    <div class="logo-section">
        <div class="system-title">نظام إدارة الأضاحي</div>
        <div class="tagline">✨ تم تصميم وإنشاء البرنامج بواسطة masarsoft.io ✨</div>
        <div class="divider"></div>
    </div>
</div>

{{-- Customer Info --}}
<div class="info-grid">
    <div class="info-box">
        <label>اسم العميل</label>
        <span>{{ $customer->name }}</span>
    </div>
    <div class="info-box">
        <label>رقم الهاتف</label>
        <span>{{ $customer->phone ?? '—' }}</span>
    </div>
    <div class="info-box">
        <label>عدد الصكوك</label>
        <span>{{ $customer->contracts->count() }}</span>
    </div>
    <div class="info-box">
        <label>تاريخ الكشف</label>
        <span>{{ now()->format('d/m/Y') }}</span>
    </div>
</div>

{{-- Groups Section --}}
@if($customer->groupMembers->count() > 0)
<div class="section-header">📦 المجموعات المشترك فيها</div>
<div style="margin-bottom: 20px;">
    @foreach($customer->groupMembers->groupBy('group_id') as $groupMembers)
        @php $group = $groupMembers->first()->group; @endphp
        <div class="group-item">
            <strong>{{ $group->animal->code ?? 'مجموعة #' . $group->id }}</strong>
            <span class="badge">{{ $groupMembers->sum('shares_count') }} أنصبة</span>
            <span class="badge" style="background: #27ae60;">{{ count($groupMembers) }} أعضاء</span>
            @if($group->animal)
                <div style="font-size: 11px; color: #555; margin-top: 5px;">
                    📍 {{ $group->animal->product->name ?? '—' }}
                    @if($group->animal->status == 'slaughtered')
                        <span style="color: #e74c3c;">✓ تم الذبح</span>
                    @endif
                </div>
            @endif
        </div>
    @endforeach
</div>
@endif

{{-- Contracts Section --}}
<div class="section-header alt">📋 الصكوك والدفعات</div>

@forelse($customer->contracts as $contract)
    @php
        // Get animals from contract items
        $animals = $contract->items->pluck('animal')->filter()->unique('id');
    @endphp
    <table>
        <thead>
            <tr>
                <th style="padding: 14px 10px;">الصك: <strong>{{ $contract->contract_number }}</strong></th>
                <th style="text-align: left; padding: 14px 10px;">الحالة: <strong>{{ ['active' => 'نشط', 'completed' => 'مكتمل', 'cancelled' => 'ملغى'][$contract->status] ?? $contract->status }}</strong></th>
            </tr>
        </thead>
        <tbody>
            {{-- Contract Details --}}
            <tr style="background: #f0f7ff;">
                <td><strong>🐑 نوع الذبيحة</strong></td>
                <td style="text-align: left; font-weight: 600;">
                    @if($animals->count() > 0)
                        @foreach($animals as $animal)
                            {{ $animal->product->name ?? '—' }}
                            <span style="color: #666; font-size: 11px;">({{ $animal->code }})</span>
                            @if(!$loop->last)<br/>@endif
                        @endforeach
                    @else
                        <span style="color: #e74c3c;">❌ لم يحدد ذبيحة بعد</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>💰 الإجمالي</strong></td>
                <td style="text-align: left; font-weight: 700; color: #2c3e50;">{{ number_format($contract->total_amount, 2) }} ج.م</td>
            </tr>
            <tr style="background: #f0f7ff;">
                <td><strong>✅ المدفوع</strong></td>
                <td style="text-align: left; font-weight: 700; color: #27ae60;">{{ number_format($contract->paid_amount, 2) }} ج.م</td>
            </tr>
            <tr>
                <td><strong>⏳ المتبقي</strong></td>
                <td style="text-align: left; font-weight: 700; color: {{ $contract->remaining_amount > 0 ? '#e74c3c' : '#27ae60' }};">
                    {{ number_format($contract->remaining_amount, 2) }} ج.م
                </td>
            </tr>

            {{-- Payments for this contract --}}
            @if($contract->payments->count() > 0)
            <tr style="background: #ecf0f1; border-top: 2px solid #95a5a6;">
                <td colspan="2" style="padding: 12px 10px; font-weight: 700; text-align: right;">📝 الدفعات المسجلة:</td>
            </tr>
            @foreach($contract->payments as $payment)
            <tr>
                <td style="padding: 8px 10px;">
                    <div style="font-size: 12px;">
                        <strong>{{ $payment->date->format('d/m/Y') }}</strong>
                        <span style="color: #666; font-size: 11px;">({{ $payment->methodLabel() }})</span>
                    </div>
                </td>
                <td style="text-align: left; padding: 8px 10px;">
                    <div style="font-weight: 700; color: #27ae60;">{{ number_format($payment->amount, 2) }} ج.م</div>
                    @if($payment->receipt_number || $payment->reference_number)
                    <div style="font-size: 11px; color: #555; margin-top: 3px;">
                        @if($payment->receipt_number)
                        📄 إيصال: <strong>{{ $payment->receipt_number }}</strong>
                        @endif
                        @if($payment->reference_number)
                        <br/>🔢 رقم مرجعي: <strong>{{ $payment->reference_number }}</strong>
                        @endif
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
@empty
    <div class="highlight-box" style="text-align: center;">
        <p style="color: #e74c3c; font-weight: 600;">لا توجد صكوك مسجلة للعميل</p>
    </div>
@endforelse

{{-- Summary --}}
@php
    $totalContracts = $customer->contracts->sum('total_amount');
    $totalPaid = $customer->contracts->sum('paid_amount');
    $totalRemaining = $customer->contracts->sum('remaining_amount');
@endphp

<div class="totals-section">
    <div class="total-row highlight">
        <span>📊 إجمالي جميع الصكوك</span>
        <span>{{ number_format($totalContracts, 2) }} ج.م</span>
    </div>
    <div class="total-row highlight">
        <span>✅ إجمالي المدفوع</span>
        <span style="color: #27ae60;">{{ number_format($totalPaid, 2) }} ج.م</span>
    </div>
    <div class="total-row final" style="{{ $totalRemaining > 0 ? 'color: #e74c3c;' : 'color: #27ae60;' }}">
        <span>⏳ الرصيد المتبقي الكلي</span>
        <span>{{ number_format($totalRemaining, 2) }} ج.م</span>
    </div>
</div>

<div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd;">
    <p style="font-size: 11px; color: #7f8c8d;">
        تم إصدار هذا الكشف بواسطة <strong>نظام إدارة الأضاحي</strong> من تطوير <strong>masarsoft.io</strong>
    </p>
    <p style="font-size: 10px; color: #bdc3c7; margin-top: 5px;">
        {{ now()->format('d/m/Y H:i') }}
    </p>
</div>

@endsection
