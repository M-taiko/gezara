@extends('layouts.print')
@section('title', 'مجموعة ذبح: ' . $group->name)

@section('content')
@php
    $cat   = $group->animal?->product?->mainCategory;
    $emoji = match($cat?->code) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
    $used  = $group->usedSlots();
    $total = $group->totalSlots();
@endphp

<div class="print-header">
    <h1>{{ $emoji }} قائمة مجموعة ذبح</h1>
    <p>تفاصيل المجموعة والعملاء المشاركين</p>
</div>

{{-- Group Info --}}
<div class="info-grid">
    <div class="info-box">
        <label>اسم المجموعة</label>
        <span>{{ $group->name }}</span>
    </div>
    <div class="info-box">
        <label>نوع التقسيم</label>
        <span>{{ $group->shareLabel() }}</span>
    </div>
    <div class="info-box">
        <label>الحيوان</label>
        <span>
            @if($group->animal)
                {{ $emoji }} {{ $group->animal->code }}
            @else
                لم يتم تحديد حيوان
            @endif
        </span>
    </div>
    <div class="info-box">
        <label>حالة الذبح</label>
        <span>
            @if($group->isSlaughtered())
                ✅ تم الذبح {{ $group->animal?->slaughtered_at?->format('d/m/Y') }}
            @else
                ⏳ لم يتم الذبح بعد
            @endif
        </span>
    </div>
    @if($group->slaughter_day)
    <div class="info-box">
        <label>تاريخ الذبح المخطط</label>
        <span>{{ $group->slaughter_day->format('d/m/Y') }}</span>
    </div>
    @endif
    @if($group->notes)
    <div class="info-box">
        <label>ملاحظات</label>
        <span>{{ $group->notes }}</span>
    </div>
    @endif
</div>

{{-- Members Table --}}
<h4>قائمة العملاء والأنصبة</h4>
<table>
    <thead>
        <tr>
            <th style="width: 5%">م</th>
            <th style="width: 30%">اسم العميل</th>
            <th style="width: 15%">رقم الهاتف</th>
            <th style="width: 15%">عدد الأنصبة</th>
            <th style="width: 20%">الإجمالي</th>
            <th style="width: 15%">الحالة</th>
        </tr>
    </thead>
    <tbody>
        @forelse($group->members as $member)
        @php
            $customerName = $member->customer?->name ?? 'عميل بدون بيانات';
            $customerPhone = $member->customer?->phone ?? '—';
            $sharesCount = $member->shares_count ?? 0;
            $totalPrice = $pricePerShare * $sharesCount;
            $isDelivered = $member->contractItem?->delivered_at;
        @endphp
        <tr>
            <td style="text-align: center;">{{ $loop->iteration }}</td>
            <td><strong>{{ $customerName }}</strong></td>
            <td style="text-align: center;">{{ $customerPhone }}</td>
            <td style="text-align: center;">{{ $sharesCount }}</td>
            <td style="text-align: center;">
                @if($pricePerShare > 0)
                    {{ number_format($totalPrice, 0) }} ر.س
                @else
                    —
                @endif
            </td>
            <td style="text-align: center;">
                @if($isDelivered)
                    <strong style="color: #28a745;">✅ مسلم</strong>
                @else
                    <strong style="color: #ff6b6b;">⏳ معلق</strong>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align: center; color: #999;">لا توجد عملاء في هذه المجموعة</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Summary --}}
@php
    $totalAmount = 0;
    $paidAmount = 0;
    foreach ($group->members as $member) {
        $shares = $member->shares_count ?? 0;
        $totalAmount += $pricePerShare * $shares;

        if ($member->contractItem?->contract) {
            $paidAmount += $member->contractItem->contract->paid_amount ?? 0;
        }
    }
    $remainingAmount = $totalAmount - $paidAmount;
@endphp

<div class="totals-section">
    <div class="total-row">
        <span>إجمالي الأنصبة المعاقدة:</span>
        <span>{{ $used }} / {{ $total }}</span>
    </div>

    @if($pricePerShare > 0)
    <div class="total-row">
        <span>سعر النصيب الواحد:</span>
        <span>{{ number_format($pricePerShare, 0) }} ر.س</span>
    </div>

    <div class="total-row">
        <span>الإجمالي:</span>
        <span>{{ number_format($totalAmount, 0) }} ر.س</span>
    </div>

    <div class="total-row">
        <span>المدفوع:</span>
        <span>{{ number_format($paidAmount, 0) }} ر.س</span>
    </div>

    <div class="total-row final">
        <span>المتبقي:</span>
        <span>{{ number_format($remainingAmount, 0) }} ر.س</span>
    </div>
    @endif
</div>

{{-- Footer Note --}}
<div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ccc; text-align: center; color: #666; font-size: 11px;">
    <p>تم طباعة هذا المستند بتاريخ {{ now()->format('d/m/Y H:i') }}</p>
    <p style="margin-top: 5px;">جميع الأسعار تخضع لتأكيد العميل — قد تتغير الأسعار حسب الاتفاق</p>
</div>

@endsection
