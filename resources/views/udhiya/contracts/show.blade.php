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
    $animalInfo = $item->animal ? ($item->animal->code . ' — ' . ($item->animal->product->name ?? '')) : '(لم يتم تخصيص حيوان)';
    return '🐄 ' . $animalInfo . ' (' . $label . ')';
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
        @php
            $hasGroupItem = $contract->items->some(fn($item) => $item->group_id !== null);
        @endphp
        @if($hasGroupItem)
        <button onclick="openTransferGroupModal()"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition-all">
            🔄 نقل إلى مجموعة أخرى
        </button>
        @endif
        <a href="{{ route('udhiya.contracts.edit', $contract) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200 transition-all">
            ✏️ تعديل صك
        </a>
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
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-indigo-50 to-white flex items-center justify-between">
                <h6 class="text-base font-black text-indigo-900 m-0">تفاصيل الصك</h6>
                <button type="button" onclick="openCustomerModal()" class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 hover:bg-indigo-200 flex items-center justify-center text-sm transition-colors" title="تعديل بيانات العميل">
                    ✏️
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">رقم الصك</span>
                    <span class="text-sm font-black text-slate-800">{{ $contract->contract_number }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">العميل</span>
                    <span class="text-sm font-black text-slate-800" id="customerNameDisplay">{{ $contract->customer->name }}</span>
                </div>
                @if($contract->customer->phone)
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">الهاتف</span>
                    <span class="text-sm font-bold text-slate-600 font-mono" dir="ltr" id="customerPhoneDisplay">{{ $contract->customer->phone }}</span>
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

{{-- attachments moved to dedicated card below --}}
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

        {{-- Attachments Card --}}
        @if($contract->attachments && count($contract->attachments) > 0)
        @php $attachPaths = json_decode($contract->attachment_paths, true) ?? []; @endphp
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-indigo-100 bg-gradient-to-b from-indigo-50 to-white flex items-center justify-between">
                <h6 class="text-sm font-black text-indigo-900 m-0">📎 المرفقات <span class="text-indigo-400 font-bold text-xs">({{ count($contract->attachments) }})</span></h6>
                <a href="{{ route('udhiya.contracts.edit', $contract) }}#attachments"
                   class="text-xs font-bold px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors">
                    ✏️ تعديل
                </a>
            </div>
            <div class="px-5 py-4 space-y-2">
                @foreach($contract->attachments as $index => $filename)
                @php
                    $filePath = $attachPaths[$index] ?? '';
                    $fullPath = storage_path('app/public/' . $filePath);
                    $fileSize = file_exists($fullPath) ? round(filesize($fullPath)/1024, 1).' KB' : '';
                    $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $isImage  = in_array($ext, ['jpg','jpeg','png','gif']);
                    $icon     = $isImage ? '🖼' : '📄';
                @endphp
                <button type="button"
                        onclick="gzLightboxOpen('{{ asset('storage/' . $filePath) }}', '{{ addslashes($filename) }}')"
                        class="w-full flex items-center gap-3 bg-slate-50 hover:bg-indigo-50 rounded-xl px-3 py-2.5 border border-slate-100 hover:border-indigo-200 transition-colors text-right group">
                    <span class="text-xl flex-shrink-0">{{ $icon }}</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-slate-700 group-hover:text-indigo-700 truncate m-0">{{ $filename }}</p>
                        @if($fileSize)<p class="text-xs text-slate-400 m-0">{{ $fileSize }}</p>@endif
                    </div>
                    <span class="text-xs text-slate-300 group-hover:text-indigo-400 flex-shrink-0">👁 عرض</span>
                </button>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Add Payment Form --}}
        @if($contract->status === 'active' && $contract->remaining_amount > 0)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-emerald-100 bg-gradient-to-b from-emerald-50 to-white flex items-center justify-between">
                <h6 class="text-base font-black text-emerald-900 m-0">💰 تسجيل دفعة</h6>
                <button type="button" onclick="togglePaymentForm()"
                        id="togglePaymentBtn"
                        class="text-xs font-bold px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition-colors">
                    ＋ إضافة دفعة
                </button>
            </div>
            <div id="paymentFormBody" class="hidden">
            <form action="{{ route('udhiya.payments.store') }}" method="POST" enctype="multipart/form-data">
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
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            الخزينة <span class="text-slate-400 font-normal text-xs">(اختياري)</span>
                        </label>
                        <select name="wallet_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                            <option value="">— بدون خزينة —</option>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}">
                                {{ $wallet->getTypeLabel() }} — {{ $wallet->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">رقم الإيصال <span class="text-slate-400 font-normal text-xs">(اختياري)</span></label>
                            <input type="text" name="receipt_number" placeholder="سيتم إنشاء رقم تلقائي"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">الرقم المرجعي <span class="text-slate-400 font-normal text-xs">(اختياري)</span></label>
                            <input type="text" name="reference_number" placeholder="مثال: فاتورة رقم..."
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                        </div>
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
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            المرفقات <span class="text-slate-400 font-normal text-xs">(PDF, JPG, PNG, GIF — حد أقصى 5 ملفات)</span>
                        </label>
                        <div class="relative">
                            <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.gif,application/pdf,image/*"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm text-slate-600 transition-colors file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        </div>
                    </div>
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 text-sm font-black rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-200/60 transition-all">
                        ✅ تسجيل الدفعة
                    </button>
                </div>
            </form>
            </div>{{-- end #paymentFormBody --}}
        </div>
        @endif

    </div>

    {{-- ═══════════ LEFT MAIN ═══════════ --}}
    <div class="flex-1 min-w-0 flex flex-col gap-5">

        {{-- Contract Items --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-lg font-black text-slate-800 m-0 flex items-center gap-2">
                    🐑 الأضاحي والأنصبة
                    <span class="text-sm font-bold text-slate-400">({{ $contract->items->count() }} بند)</span>
                </h6>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">الأضحية</th>
                            <th class="px-5 py-3">النوع</th>
                            <th class="px-5 py-3">الحصة</th>
                            <th class="px-5 py-3 text-center">العدد</th>
                            <th class="px-5 py-3 text-left">سعر الوحدة</th>
                            <th class="px-5 py-3 text-left">الإجمالي</th>
                            <th class="px-5 py-3 w-20"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($contract->items as $item)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="px-5 py-4">
                                @if($item->animal)
                                <a href="{{ route('udhiya.animals.show', $item->animal) }}"
                                   class="font-black text-indigo-600 hover:text-indigo-800 hover:underline text-sm">
                                    {{ $item->animal->code }}
                                </a>
                                @else
                                <span class="text-sm font-semibold text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-700">
                                {{ $item->animal?->product?->name ?? '—' }}
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
                            <td class="px-5 py-4 text-center flex gap-1">
                                <button type="button" onclick="openItemEditModal({{ $item->id }}, {{ $item->unit_price }}, {{ $item->shares_count }})"
                                        class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-colors text-xs" title="تعديل العنصر">
                                    ✏️
                                </button>
                                <form action="{{ route('udhiya.contract-items.destroy', $item) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل تريد حذف هذا العنصر من الصك؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors text-xs">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-900 text-white">
                            <td colspan="6" class="px-5 py-4 text-sm font-bold text-slate-400">الإجمالي الكلي</td>
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
                            <th class="px-5 py-3">الخزينة</th>
                            <th class="px-5 py-3 text-left">المبلغ</th>
                            <th class="px-5 py-3">ملاحظات</th>
                            <th class="px-5 py-3 w-16"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($contract->payments as $payment)
                        <tr class="hover:bg-emerald-50/30 transition-colors cursor-pointer"
                            onclick="openPaymentModal('payment-modal-{{ $payment->id }}')">
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
                            <td class="px-5 py-4 text-sm">
                                @if($payment->wallet)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-black bg-indigo-100 text-indigo-700">
                                        {{ $payment->wallet->getTypeLabel() }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-left text-base font-black text-emerald-600">
                                {{ number_format($payment->amount, 2) }} <span class="text-xs text-emerald-400">ج.م</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-500">
                                {{ $payment->notes ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-center" onclick="event.stopPropagation()">
                                <a href="{{ route('udhiya.payments.print', $payment) }}" target="_blank"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white transition-colors text-xs">
                                    🖨️
                                </a>
                            </td>
                        </tr>

                        {{-- Payment Details Modal --}}
                        <div id="payment-modal-{{ $payment->id }}" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                             onclick="if(event.target === this) closePaymentModal('payment-modal-{{ $payment->id }}')">
                            <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                                 onclick="event.stopPropagation()">
                                {{-- Modal Header --}}
                                <div class="sticky top-0 bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-5 flex items-center justify-between border-b border-emerald-600">
                                    <h3 class="text-xl font-black text-white flex items-center gap-2">
                                        💳 تفاصيل الدفعة {{ $payment->receipt_number }}
                                    </h3>
                                    <button type="button" onclick="closePaymentModal('payment-modal-{{ $payment->id }}')"
                                            class="text-white hover:bg-emerald-700 p-2 rounded-lg transition-colors">
                                        ✕
                                    </button>
                                </div>

                                {{-- Modal Body --}}
                                <div class="p-6 space-y-6">
                                    {{-- Payment Info Grid --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-slate-50 rounded-xl p-4">
                                            <p class="text-xs font-bold text-slate-500 mb-1">رقم الإيصال</p>
                                            <p class="text-lg font-black text-slate-800">{{ $payment->receipt_number }}</p>
                                        </div>
                                        <div class="bg-slate-50 rounded-xl p-4">
                                            <p class="text-xs font-bold text-slate-500 mb-1">التاريخ</p>
                                            <p class="text-lg font-black text-slate-800">{{ \Carbon\Carbon::parse($payment->date)->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="bg-slate-50 rounded-xl p-4">
                                            <p class="text-xs font-bold text-slate-500 mb-1">المبلغ</p>
                                            <p class="text-lg font-black text-emerald-600">{{ number_format($payment->amount, 2) }} ج.م</p>
                                        </div>
                                        <div class="bg-slate-50 rounded-xl p-4">
                                            <p class="text-xs font-bold text-slate-500 mb-1">طريقة الدفع</p>
                                            <p class="text-sm font-black text-slate-800">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black
                                                    {{ $payment->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                                    {{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Additional Fields --}}
                                    @if($payment->reference_number || $payment->wallet)
                                    <div class="border-t border-slate-200 pt-4 space-y-4">
                                        @if($payment->reference_number)
                                        <div>
                                            <p class="text-xs font-bold text-slate-500 mb-2">الرقم المرجعي</p>
                                            <p class="text-sm font-semibold text-slate-700 bg-slate-50 rounded-lg px-3 py-2">
                                                {{ $payment->reference_number }}
                                            </p>
                                        </div>
                                        @endif
                                        @if($payment->wallet)
                                        <div>
                                            <p class="text-xs font-bold text-slate-500 mb-2">الخزينة</p>
                                            <p class="text-sm font-semibold text-slate-700 bg-slate-50 rounded-lg px-3 py-2">
                                                {{ $payment->wallet->getTypeLabel() }} — {{ $payment->wallet->name }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    {{-- Notes --}}
                                    @if($payment->notes)
                                    <div class="border-t border-slate-200 pt-4">
                                        <p class="text-xs font-bold text-slate-500 mb-2">ملاحظات</p>
                                        <p class="text-sm text-slate-600 bg-slate-50 rounded-lg px-3 py-2">
                                            {{ $payment->notes }}
                                        </p>
                                    </div>
                                    @endif

                                    {{-- Attachments --}}
                                    @if($payment->attachments && count($payment->attachments) > 0)
                                    @php $pPaths = json_decode($payment->attachment_paths, true) ?? []; @endphp
                                    <div class="border-t border-slate-200 pt-4">
                                        <p class="text-xs font-bold text-slate-500 mb-3">المرفقات ({{ count($payment->attachments) }})</p>
                                        <div class="space-y-2">
                                            @foreach($payment->attachments as $index => $filename)
                                            @php
                                                $fp  = $pPaths[$index] ?? null;
                                                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                                $ico = in_array($ext, ['jpg','jpeg','png','gif']) ? '🖼' : '📄';
                                                $sz  = $fp && file_exists(storage_path('app/public/'.$fp)) ? round(filesize(storage_path('app/public/'.$fp))/1024,1).' KB' : '';
                                            @endphp
                                            @if($fp)
                                            <button type="button"
                                                    onclick="gzLightboxOpen('{{ asset('storage/'.$fp) }}','{{ addslashes($filename) }}')"
                                                    class="w-full flex items-center gap-3 p-2.5 rounded-xl border border-slate-100 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 transition-colors text-right group">
                                                <span class="text-xl flex-shrink-0">{{ $ico }}</span>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-slate-700 truncate group-hover:text-indigo-700 m-0">{{ $filename }}</p>
                                                    @if($sz)<p class="text-xs text-slate-400 m-0">{{ $sz }}</p>@endif
                                                </div>
                                                <span class="text-xs text-slate-300 group-hover:text-indigo-400 flex-shrink-0">👁</span>
                                            </button>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                {{-- Modal Footer --}}
                                <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 flex gap-3 justify-end">
                                    <button type="button" onclick="closePaymentModal('payment-modal-{{ $payment->id }}')"
                                            class="px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 font-bold hover:bg-slate-100 transition-colors">
                                        إغلاق
                                    </button>
                                    <a href="{{ route('udhiya.payments.print', $payment) }}" target="_blank"
                                       class="px-4 py-2.5 rounded-lg bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                        🖨️ طباعة الإيصال
                                    </a>
                                </div>
                            </div>
                        </div>
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

{{-- Item Edit Modal --}}
<div id="itemModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-lg max-w-md w-full overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-amber-50 to-white">
            <h6 class="text-lg font-black text-amber-900 m-0">تعديل العنصر</h6>
        </div>
        <form id="itemEditForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">سعر الوحدة <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="number" id="itemUnitPrice" name="unit_price" step="0.01" min="0.01" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">ج.م</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">عدد الأنصبة <span class="text-rose-500">*</span></label>
                <input type="number" id="itemSharesCount" name="shares_count" min="1" step="1" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
            </div>

            <div class="flex gap-3 pt-3">
                <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl bg-amber-600 text-white hover:bg-amber-700 shadow-md shadow-amber-200/60 transition-all">
                    ✅ حفظ التغييرات
                </button>
                <button type="button" onclick="closeItemModal()" class="flex-1 inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">
                    ✕ إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Customer Edit Modal --}}
<div id="customerModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-lg max-w-md w-full overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-indigo-50 to-white">
            <h6 class="text-lg font-black text-indigo-900 m-0">تعديل بيانات العميل</h6>
        </div>
        <form action="{{ route('udhiya.customers.update', $contract->customer) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">اسم العميل <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ $contract->customer->name }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">الهاتف <span class="text-rose-500">*</span></label>
                <input type="tel" name="phone" value="{{ $contract->customer->phone }}" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">العنوان</label>
                <input type="text" name="address" value="{{ $contract->customer->address ?? '' }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">ملاحظات</label>
                <textarea name="notes" rows="2"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none">{{ $contract->customer->notes ?? '' }}</textarea>
            </div>

            <div class="flex gap-3 pt-3">
                <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200/60 transition-all">
                    ✅ حفظ التغييرات
                </button>
                <button type="button" onclick="closeCustomerModal()" class="flex-1 inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">
                    ✕ إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Transfer to Group Modal --}}
<div id="transferGroupModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-lg max-w-md w-full overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-blue-50 to-white">
            <h6 class="text-lg font-black text-blue-900 m-0">نقل إلى مجموعة أخرى</h6>
        </div>
        <form action="{{ route('udhiya.contracts.transfer-group', $contract) }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">المجموعة الجديدة <span class="text-rose-500">*</span></label>
                <select name="group_id" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    <option value="">-- اختر مجموعة --</option>
                    @php
                        $currentGroupId = $contract->items->first()?->group_id;
                        $groups = \App\Models\SlaughterGroup::with('animal')->get();
                    @endphp
                    @foreach($groups as $group)
                        @if($group->id !== $currentGroupId)
                        <option value="{{ $group->id }}">
                            {{ $group->name }} ({{ $group->shareLabel() }}) —
                            متاح: {{ $group->remainingSlots() }}/{{ $group->totalSlots() }}
                        </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <p class="text-xs text-slate-500 bg-blue-50 rounded-lg p-3 border border-blue-100">
                ℹ️ سيتم إلغاء الصك الحالي ونقل العميل إلى المجموعة الجديدة مع الاحتفاظ بالدفعات السابقة.
            </p>

            <div class="flex gap-3 pt-3">
                <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl bg-blue-600 text-white hover:bg-blue-700 shadow-md shadow-blue-200/60 transition-all">
                    ✅ نقل العميل
                </button>
                <button type="button" onclick="closeTransferGroupModal()" class="flex-1 inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">
                    ✕ إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePaymentForm() {
    const body = document.getElementById('paymentFormBody');
    const btn  = document.getElementById('togglePaymentBtn');
    if (!body) return;
    const isHidden = body.classList.contains('hidden');
    body.classList.toggle('hidden');
    btn.textContent = isHidden ? '✕ إخفاء' : '＋ إضافة دفعة';
    btn.className   = isHidden
        ? 'text-xs font-bold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors'
        : 'text-xs font-bold px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition-colors';
}

function openItemEditModal(itemId, unitPrice, sharesCount) {
    const form = document.getElementById('itemEditForm');
    form.action = `/udhiya/contract-items/${itemId}`;
    document.getElementById('itemUnitPrice').value = unitPrice;
    document.getElementById('itemSharesCount').value = sharesCount;
    document.getElementById('itemModal').classList.remove('hidden');
    document.getElementById('itemModal').addEventListener('click', function(e) {
        if (e.target === this) closeItemModal();
    });
}

function closeItemModal() {
    document.getElementById('itemModal').classList.add('hidden');
}

function openCustomerModal() {
    document.getElementById('customerModal').classList.remove('hidden');
    document.getElementById('customerModal').addEventListener('click', function(e) {
        if (e.target === this) closeCustomerModal();
    });
}

function closeCustomerModal() {
    document.getElementById('customerModal').classList.add('hidden');
}

// Real-time total calculation in item edit modal
// Payment Modal Functions
function openPaymentModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closePaymentModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('[id^="payment-modal-"]:not(.hidden)');
        modals.forEach(modal => {
            const modalId = modal.id;
            closePaymentModal(modalId);
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const unitPriceInput = document.getElementById('itemUnitPrice');
    const sharesCountInput = document.getElementById('itemSharesCount');

    if (unitPriceInput && sharesCountInput) {
        const updateTotal = () => {
            const unitPrice = parseFloat(unitPriceInput.value) || 0;
            const sharesCount = parseInt(sharesCountInput.value) || 1;
            const total = unitPrice * sharesCount;
            // Visual feedback (optional: add a span to show total)
            console.log('Total: ' + total.toFixed(2) + ' ج.م');
        };

        unitPriceInput.addEventListener('input', updateTotal);
        sharesCountInput.addEventListener('input', updateTotal);
    }
});

function openTransferGroupModal() {
    document.getElementById('transferGroupModal').classList.remove('hidden');
}

function closeTransferGroupModal() {
    document.getElementById('transferGroupModal').classList.add('hidden');
}

document.getElementById('transferGroupModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeTransferGroupModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTransferGroupModal();
    }
});
</script>

@endsection
