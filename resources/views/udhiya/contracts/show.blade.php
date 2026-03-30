@extends('layouts.master')

@php
$shareLabels = ['full'=>'كامل','seven'=>'سُبع','six'=>'سُدس','five'=>'خُمس','quarter'=>'ربع','third'=>'ثُلث','half'=>'نصف'];
$methodLabels = ['cash'=>'نقدي','bank'=>'بنك','check'=>'شيك','transfer'=>'تحويل'];
$paidPct = $contract->total_amount > 0 ? round(($contract->paid_amount / $contract->total_amount) * 100) : 0;

// ── WhatsApp ──────────────────────────────────────────────────────────────
$rawPhone = preg_replace('/\D/', '', $contract->customer->phone ?? '');
// Convert Egyptian 01x → 201x, otherwise prepend 20
if (strlen($rawPhone) === 11 && str_starts_with($rawPhone, '0')) {
    $waPhone = '2' . $rawPhone;
} elseif (strlen($rawPhone) >= 10) {
    $waPhone = $rawPhone;
} else {
    $waPhone = '';
}

$animalLines = $contract->items->map(function ($item) use ($shareLabels) {
    $label = $shareLabels[$item->share_type] ?? $item->share_type;
    return '🐄 ' . $item->animal->code . ' — ' . ($item->animal->product->name ?? '') . ' (' . $label . ')';
})->implode("\n");

$isFullyPaid = $contract->remaining_amount <= 0;

if ($isFullyPaid) {
    $waMessage =
        "السلام عليكم ورحمة الله وبركاته 🌙\n" .
        "أخي / أختي *{$contract->customer->name}*،\n\n" .
        "يسعدنا إبلاغكم بأن صك الأضحية الخاص بكم قد اكتمل سداده بنجاح ✅\n\n" .
        "📋 *رقم الصك:* {$contract->contract_number}\n" .
        "{$animalLines}\n" .
        "💰 *إجمالي الصك:* " . number_format($contract->total_amount, 2) . " ج.م\n" .
        "✅ *تم سداد:* " . number_format($contract->paid_amount, 2) . " ج.م\n\n" .
        "جزاكم الله خيراً وتقبّل الله منا ومنكم صالح الأعمال 🤲";
} else {
    $waMessage =
        "السلام عليكم ورحمة الله وبركاته 🌙\n" .
        "أخي / أختي *{$contract->customer->name}*،\n\n" .
        "نُذكّركم بأن لديكم صك أضحية لم يكتمل سداده بعد.\n\n" .
        "📋 *رقم الصك:* {$contract->contract_number}\n" .
        "{$animalLines}\n\n" .
        "💰 *إجمالي الصك:* " . number_format($contract->total_amount, 2) . " ج.م\n" .
        "✅ *تم سداد:* " . number_format($contract->paid_amount, 2) . " ج.م\n" .
        "⏳ *المتبقي:* " . number_format($contract->remaining_amount, 2) . " ج.م\n\n" .
        "نرجو التكرم بسداد المبلغ المتبقي في أقرب وقت ممكن 🙏\n" .
        "جزاكم الله خيراً وبارك فيكم 🤲";
}

$waUrl = $waPhone ? 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($waMessage) : '';
@endphp

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🧾</span>
            صك {{ $contract->contract_number }}
            @if($contract->status === 'active')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-amber-100 text-amber-700 border border-amber-200">نشط</span>
            @elseif($contract->status === 'completed')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-emerald-100 text-emerald-700 border border-emerald-200">✅ مكتمل</span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-black bg-rose-100 text-rose-700 border border-rose-200">ملغى</span>
            @endif
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.contracts.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">إدارة الصكوك</a>
            / {{ $contract->contract_number }}
        </p>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        @if($waUrl)
        <a href="{{ $waUrl }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl transition-all border
                  {{ $isFullyPaid
                      ? 'bg-emerald-500 text-white hover:bg-emerald-600 border-emerald-500 shadow-md shadow-emerald-200/60'
                      : 'bg-amber-50 text-amber-700 hover:bg-amber-500 hover:text-white border-amber-200' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            {{ $isFullyPaid ? 'إرسال الفاتورة' : 'تذكير بالدفع' }}
        </a>
        @endif
        <a href="{{ route('udhiya.contracts.print', $contract) }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-100 transition-all">
            🖨️ طباعة الصك
        </a>
        @if($contract->status !== 'cancelled')
        <form action="{{ route('udhiya.contracts.destroy', $contract) }}" method="POST"
              onsubmit="return confirm('هل تريد إلغاء هذا الصك؟')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white border border-rose-100 transition-all">
                🚫 إلغاء الصك
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="flex flex-col lg:flex-row gap-6 pb-16">

    {{-- ═══════════ RIGHT SIDEBAR ═══════════ --}}
    <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-5">

        {{-- Contract Info --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-indigo-50 to-white">
                <h6 class="text-base font-black text-indigo-900 m-0">تفاصيل الصك</h6>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">رقم الصك</span>
                    <span class="text-sm font-black text-slate-800">{{ $contract->contract_number }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">العميل</span>
                    <span class="text-sm font-black text-slate-800">{{ $contract->customer->name }}</span>
                </div>
                @if($contract->customer->phone)
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">الهاتف</span>
                    <span class="text-sm font-bold text-slate-600 font-mono" dir="ltr">{{ $contract->customer->phone }}</span>
                </div>
                @endif
                @if($contract->slaughter_day)
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">يوم الذبح</span>
                    <span class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($contract->slaughter_day)->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($contract->slaughter_order)
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">ترتيب الذبح</span>
                    <span class="text-sm font-black text-indigo-700">#{{ $contract->slaughter_order }}</span>
                </div>
                @endif
                @if($contract->notes)
                <div class="py-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">ملاحظات</span>
                    <p class="text-sm text-slate-600 font-medium leading-relaxed m-0">{{ $contract->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-emerald-50 to-white">
                <h6 class="text-base font-black text-emerald-900 m-0">الملخص المالي</h6>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500">إجمالي الصك</span>
                    <span class="text-lg font-black text-slate-800">{{ number_format($contract->total_amount, 2) }} <span class="text-xs text-slate-400">ج.م</span></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500">المحصّل</span>
                    <span class="text-lg font-black text-emerald-600">{{ number_format($contract->paid_amount, 2) }} <span class="text-xs text-emerald-400">ج.م</span></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-500">المتبقي</span>
                    <span class="text-lg font-black {{ $contract->remaining_amount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ number_format($contract->remaining_amount, 2) }} <span class="text-xs {{ $contract->remaining_amount > 0 ? 'text-rose-400' : 'text-emerald-400' }}">ج.م</span>
                    </span>
                </div>

                {{-- Progress bar --}}
                <div class="pt-2">
                    <div class="flex justify-between text-xs font-bold text-slate-400 mb-1.5">
                        <span>نسبة السداد</span>
                        <span>{{ $paidPct }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                        <div class="h-2.5 rounded-full transition-all duration-500 {{ $paidPct >= 100 ? 'bg-emerald-500' : ($paidPct > 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                             style="width: {{ $paidPct }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Payment Form --}}
        @if($contract->status === 'active' && $contract->remaining_amount > 0)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-emerald-100 bg-gradient-to-b from-emerald-50 to-white">
                <h6 class="text-base font-black text-emerald-900 m-0">💰 تسجيل دفعة</h6>
            </div>
            <form action="{{ route('udhiya.payments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            المبلغ <span class="text-rose-500">*</span>
                            <span class="text-slate-400 font-normal">(الحد الأقصى: {{ number_format($contract->remaining_amount, 2) }})</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="amount" step="0.01" min="0.01"
                                   max="{{ $contract->remaining_amount }}" required
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">ج.م</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">طريقة الدفع</label>
                        <select name="payment_method"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                            <option value="cash">نقدي</option>
                            <option value="bank">بنك</option>
                            <option value="check">شيك</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">التاريخ <span class="text-rose-500">*</span></label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">ملاحظات</label>
                        <textarea name="notes" rows="2"
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors resize-none"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 text-sm font-black rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-200/60 transition-all">
                        ✅ تسجيل الدفعة
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>

    {{-- ═══════════ LEFT MAIN ═══════════ --}}
    <div class="flex-1 min-w-0 flex flex-col gap-5">

        {{-- Contract Items --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-lg font-black text-slate-800 m-0 flex items-center gap-2">
                    🐄 الحيوانات والأنصبة
                    <span class="text-sm font-bold text-slate-400">({{ $contract->items->count() }} بند)</span>
                </h6>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">الحيوان</th>
                            <th class="px-5 py-3">النوع</th>
                            <th class="px-5 py-3">الحصة</th>
                            <th class="px-5 py-3 text-center">العدد</th>
                            <th class="px-5 py-3 text-left">سعر الوحدة</th>
                            <th class="px-5 py-3 text-left">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($contract->items as $item)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('udhiya.animals.show', $item->animal) }}"
                                   class="font-black text-indigo-600 hover:text-indigo-800 hover:underline text-sm">
                                    {{ $item->animal->code }}
                                </a>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-700">
                                {{ $item->animal->product->name ?? '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black
                                    {{ $item->share_type === 'full' ? 'bg-indigo-100 text-indigo-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $shareLabels[$item->share_type] ?? $item->share_type }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm font-black text-slate-800">
                                {{ $item->shares_count }}
                            </td>
                            <td class="px-5 py-4 text-left text-sm font-semibold text-slate-600">
                                {{ number_format($item->unit_price, 2) }} <span class="text-xs text-slate-400">ج.م</span>
                            </td>
                            <td class="px-5 py-4 text-left text-sm font-black text-slate-800">
                                {{ number_format($item->total_price, 2) }} <span class="text-xs text-slate-400">ج.م</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-900 text-white">
                            <td colspan="5" class="px-5 py-4 text-sm font-bold text-slate-400">الإجمالي الكلي</td>
                            <td class="px-5 py-4 text-left text-xl font-black">
                                {{ number_format($contract->total_amount, 2) }} <span class="text-sm text-indigo-400">ج.م</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Payments History --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-lg font-black text-slate-800 m-0 flex items-center gap-2">
                    💳 سجل الدفعات
                    <span class="text-sm font-bold text-slate-400">({{ $contract->payments->count() }})</span>
                </h6>
                @if($contract->payments->count() > 0)
                <span class="text-sm font-black text-emerald-600">
                    {{ number_format($contract->paid_amount, 2) }} ج.م محصّل
                </span>
                @endif
            </div>
            <div class="overflow-x-auto">
                @if($contract->payments->count() > 0)
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">رقم الإيصال</th>
                            <th class="px-5 py-3">التاريخ</th>
                            <th class="px-5 py-3">طريقة الدفع</th>
                            <th class="px-5 py-3 text-left">المبلغ</th>
                            <th class="px-5 py-3">ملاحظات</th>
                            <th class="px-5 py-3 w-16"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($contract->payments as $payment)
                        <tr class="hover:bg-emerald-50/30 transition-colors">
                            <td class="px-5 py-4">
                                <span class="text-xs font-black text-slate-600 font-mono bg-slate-100 px-2 py-1 rounded-lg">
                                    {{ $payment->receipt_number }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-600">
                                {{ \Carbon\Carbon::parse($payment->date)->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black
                                    {{ $payment->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-left text-base font-black text-emerald-600">
                                {{ number_format($payment->amount, 2) }} <span class="text-xs text-emerald-400">ج.م</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-500">
                                {{ $payment->notes ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('udhiya.payments.print', $payment) }}" target="_blank"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white transition-colors text-xs">
                                    🖨️
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="px-6 py-12 text-center">
                    <div class="text-4xl mb-3">💳</div>
                    <p class="text-slate-400 font-semibold text-sm">لا توجد دفعات مسجّلة بعد</p>
                    @if($contract->status === 'active')
                    <p class="text-slate-400 text-xs mt-1">استخدم النموذج على اليمين لتسجيل أول دفعة</p>
                    @endif
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
