@extends('layouts.print')
@section('title', 'صك #' . $contract->contract_number)
@section('content')
<div class="print-header">
    <h1>نظام إدارة الأضاحي</h1>
    <p>صك بيع رقم: <strong>{{ $contract->contract_number }}</strong></p>
</div>

<div class="info-grid">
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">العميل</label>
        <span>{{ $contract->customer->name }}</span>
    </div>
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">رقم الهاتف</label>
        <span>{{ $contract->customer->phone }}</span>
    </div>
    @if($contract->slaughter_day)
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">يوم الذبح</label>
        <span>{{ $contract->slaughter_day }}</span>
    </div>
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">ترتيب الذبح</label>
        <span>{{ $contract->slaughter_order ?? '—' }}</span>
    </div>
    @endif
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">تاريخ الصك</label>
        <span>{{ $contract->created_at->format('Y-m-d') }}</span>
    </div>
    <div class="info-box">
        <label class="block text-sm font-semibold text-slate-700 mb-4">الحالة</label>
        <span>{{ ['active'=>'نشط','completed'=>'مكتمل','cancelled'=>'ملغى'][$contract->status] }}</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th class="px-6 py-4 font-bold tracking-wider">#</th>
            <th class="px-6 py-4 font-bold tracking-wider">الحيوان</th>
            <th class="px-6 py-4 font-bold tracking-wider">النوع</th>
            <th class="px-6 py-4 font-bold tracking-wider">نوع الحصة</th>
            <th class="px-6 py-4 font-bold tracking-wider">الأنصبة</th>
            <th class="px-6 py-4 font-bold tracking-wider">سعر الوحدة</th>
            <th class="px-6 py-4 font-bold tracking-wider">الإجمالي</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contract->items as $i => $item)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $i + 1 }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $item->animal->code }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $item->animal->product->name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $item->share_type === 'full' ? 'كامل' : ($item->share_type ?? '—') }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $item->shares_count }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->unit_price, 2) }} ج.م</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->total_price, 2) }} ج.م</td>
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
    <thead class="text-xs text-slate-600 uppercase bg-slate-100/80 font-bold border-b border-slate-200"><tr><th class="px-6 py-4 font-bold tracking-wider">رقم الإيصال</th><th class="px-6 py-4 font-bold tracking-wider">التاريخ</th><th class="px-6 py-4 font-bold tracking-wider">الطريقة</th><th class="px-6 py-4 font-bold tracking-wider">المبلغ</th></tr></thead>
    <tbody>
        @foreach($contract->payments as $payment)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">{{ $payment->receipt_number }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ $payment->date }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ ['cash'=>'نقدي','bank'=>'بنك','transfer'=>'تحويل'][$payment->payment_method] }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($payment->amount, 2) }} ج.م</td>
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
