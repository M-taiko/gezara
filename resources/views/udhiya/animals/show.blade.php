@extends('layouts.master')
@section('title', 'حيوان: ' . $animal->code)

@section('page-header')
@php
    $cat   = $animal->product?->mainCategory;
    $emoji = match($cat?->code) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
    $statusColors = [
        'available'           => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'partially_allocated' => 'bg-amber-50 text-amber-700 border-amber-200',
        'fully_allocated'     => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'slaughtered'         => 'bg-slate-100 text-slate-600 border-slate-200',
    ];
    $statusColor = $statusColors[$animal->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
    $statusLabel = \App\Models\Animal::STATUS_LABELS[$animal->status] ?? $animal->status;
    $isLarge = in_array($cat?->code, ['BQR', 'JML']);
@endphp
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-4xl">{{ $emoji }}</span> {{ $animal->code }}
            <span class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-black border {{ $statusColor }}">{{ $statusLabel }}</span>
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.animals.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الحيوانات</a>
            / {{ $animal->product?->name }} / {{ $animal->code }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 shadow-sm no-print">
            🖨️ طباعة
        </button>
    </div>
</div>
@endsection

@section('content')
<div class="flex flex-col lg:flex-row gap-6 pb-16">

    {{-- ═══════════ RIGHT SIDEBAR ═══════════ --}}
    <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-5">

        {{-- Basic Info --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">📋 البيانات الأساسية</h6>
            </div>
            <div class="p-5 space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-semibold">الكود</span>
                    <span class="font-black text-slate-800">{{ $animal->code }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-semibold">النوع</span>
                    <span class="font-bold text-slate-700">{{ $animal->product?->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-semibold">الفئة</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-xs font-bold">{{ $cat?->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-semibold">المخزن</span>
                    <span class="font-bold text-slate-700">{{ $animal->warehouse?->name ?? '—' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-semibold">المورد</span>
                    <span class="font-bold text-slate-700">{{ $animal->supplier?->name ?? '—' }}</span>
                </div>

                @if($animal->weight)
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <span class="text-slate-400 font-semibold">الوزن</span>
                    <span class="font-black text-slate-800">{{ $animal->weight }} كجم</span>
                </div>
                @endif
                @if($animal->price_per_kg)
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-semibold">سعر الكجم</span>
                    <span class="font-bold text-slate-700">{{ number_format($animal->price_per_kg, 2) }} ج.م</span>
                </div>
                @endif
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <span class="text-slate-400 font-semibold">التكلفة</span>
                    <span class="font-black text-rose-700">{{ number_format($animal->cost, 2) }} ج.م</span>
                </div>
                @if($animal->price_full)
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-semibold">سعر البيع</span>
                    <span class="font-black text-emerald-700">{{ number_format($animal->price_full, 2) }} ج.م</span>
                </div>
                @if($animal->cost > 0 && $animal->price_full > 0)
                @php $margin = $animal->price_full - $animal->cost; $marginPct = round(($margin / $animal->cost) * 100, 1); @endphp
                <div class="flex justify-between items-center">
                    <span class="text-slate-400 font-semibold">هامش الربح</span>
                    <span class="font-black {{ $margin >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ number_format($margin, 0) }} ج.م ({{ $marginPct }}%)
                    </span>
                </div>
                @endif
                @endif

                @if($animal->notes)
                <div class="pt-2 border-t border-slate-100 text-xs text-slate-500 italic">{{ $animal->notes }}</div>
                @endif
            </div>
        </div>

        {{-- Edit Code --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">✏️ تعديل الكود</h6>
            </div>
            <div class="p-5">
                <form action="{{ route('udhiya.animals.update-code', $animal) }}" method="POST" class="flex gap-2">
                    @csrf @method('PATCH')
                    <input type="text" name="code" value="{{ $animal->code }}" required
                           class="flex-1 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors">
                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl text-sm font-bold bg-orange-500 text-white hover:bg-orange-600 transition-all shadow-sm">
                        حفظ
                    </button>
                </form>
                @error('code')
                <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Share System --}}
        @if($animal->status === 'available' || $animal->is_grouped)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">🔀 نظام الأنصبة</h6>
            </div>
            <div class="p-5">
                @if($animal->is_grouped && $animal->shareSetting)
                    @php
                        $ss  = $animal->shareSetting;
                        $pct = $ss->total_shares > 0 ? round(($ss->sold_shares / $ss->total_shares) * 100) : 0;
                    @endphp
                    <div class="mb-3 bg-indigo-50 border border-indigo-100 rounded-2xl p-4 text-sm">
                        <div class="font-black text-indigo-800 mb-2">{{ \App\Models\AnimalShareSetting::SHARE_TYPE_LABELS[$ss->share_type] ?? $ss->share_type }}</div>
                        <div class="flex justify-between text-xs text-indigo-600 font-semibold mb-2">
                            <span>{{ $ss->sold_shares }} / {{ $ss->total_shares }} مباع</span>
                            <span>{{ $ss->remaining_shares }} متبقي</span>
                        </div>
                        <div class="w-full bg-indigo-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $pct >= 100 ? 'bg-emerald-500' : 'bg-indigo-500' }} transition-all"
                                 style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    @if($ss->sold_shares === 0)
                    <form action="{{ route('udhiya.animals.unset-grouped', $animal) }}" method="POST">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('إلغاء نظام الأنصبة؟')"
                                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-200 transition-all">
                            إلغاء نظام الأنصبة
                        </button>
                    </form>
                    @endif
                @else
                    <form action="{{ route('udhiya.animals.set-grouped', $animal) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">نوع الأنصبة</label>
                            <select name="share_type" required
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                                @foreach(\App\Models\AnimalShareSetting::SHARE_TYPE_LABELS as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-sm">
                            تفعيل نظام الأنصبة
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endif

        {{-- Transfer --}}
        @if($warehouses->count() > 0)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">🚚 نقل إلى مخزن</h6>
            </div>
            <div class="p-5 space-y-3">
                <form action="{{ route('udhiya.animals.transfer', $animal) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">المخزن الجديد</label>
                        <select name="to_warehouse_id" required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">ملاحظات النقل</label>
                        <textarea name="notes" rows="2" placeholder="سبب النقل..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2 px-3 text-sm font-semibold text-slate-800 resize-none transition-colors"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-amber-500 text-white hover:bg-amber-600 transition-all shadow-sm">
                        تأكيد النقل
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>

    {{-- ═══════════ LEFT MAIN ═══════════ --}}
    <div class="flex-1 min-w-0 flex flex-col gap-5">

        {{-- Purchase Origin --}}
        @if($animal->purchase)
        <div class="bg-white rounded-3xl shadow-sm border border-indigo-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-indigo-100 bg-indigo-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">🛒 مصدر الشراء</h6>
                <a href="{{ route('udhiya.purchases.show', $animal->purchase) }}"
                   class="text-xs font-black text-indigo-600 hover:text-indigo-800 hover:underline">
                    عرض فاتورة الشراء ←
                </a>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4 text-sm">
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-slate-400 font-bold">المورد</span>
                    <span class="font-black text-slate-800">{{ $animal->purchase->supplier->name }}</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-slate-400 font-bold">تاريخ الشراء</span>
                    <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($animal->purchase->date)->format('d/m/Y') }}</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-xs text-slate-400 font-bold">تكلفة الحيوان</span>
                    <span class="font-black text-rose-600">{{ number_format($animal->cost, 0) }} ج.م</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Prices Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-emerald-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">💰 أسعار البيع</h6>
                <span class="text-xs text-slate-400 font-semibold">تحديث مباشر</span>
            </div>
            <form action="{{ route('udhiya.animals.update-prices', $animal) }}" method="POST">
                @csrf @method('PATCH')
                <div class="p-5">
                    {{-- Full price + per kg --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div class="bg-emerald-50 rounded-2xl border border-emerald-100 p-4">
                            <label class="block text-xs font-black text-emerald-700 mb-2">سعر الكجم (بيع)</label>
                            <div class="relative">
                                <input type="number" name="price_per_kg" step="0.01" min="0"
                                       value="{{ $animal->price_per_kg }}"
                                       placeholder="0.00"
                                       class="w-full rounded-xl border border-emerald-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 pl-12 text-sm font-bold text-slate-800 transition-colors">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-400 text-xs font-bold">ج.م</span>
                            </div>
                        </div>
                        <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-4">
                            <label class="block text-xs font-black text-emerald-800 mb-2">سعر الحيوان كاملاً ⭐</label>
                            <div class="relative">
                                <input type="number" name="price_full" step="0.01" min="0"
                                       value="{{ $animal->price_full }}"
                                       placeholder="0.00"
                                       class="w-full rounded-xl border border-emerald-300 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 pl-12 text-sm font-black text-emerald-800 transition-colors">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-500 text-xs font-bold">ج.م</span>
                            </div>
                        </div>
                    </div>

                    {{-- Share prices — only for large animals --}}
                    @if($isLarge)
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
                        <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3">أسعار الأنصبة</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach([
                                'price_half'    => 'نصف (÷2)',
                                'price_third'   => 'ثُلث (÷3)',
                                'price_quarter' => 'ربع (÷4)',
                                'price_five'    => 'خُمس (÷5)',
                                'price_six'     => 'سُدس (÷6)',
                                'price_seven'   => 'سُبع (÷7)',
                            ] as $field => $lbl)
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1.5">{{ $lbl }}</label>
                                <div class="relative">
                                    <input type="number" name="{{ $field }}" step="0.01" min="0"
                                           value="{{ $animal->$field }}" placeholder="0.00"
                                           class="w-full rounded-xl border border-slate-200 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-2 pl-10 text-sm font-bold text-slate-700 transition-colors">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-slate-300 text-xs">ج.م</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                <div class="px-5 pb-5">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 text-sm font-black rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        حفظ الأسعار
                    </button>
                </div>
            </form>
        </div>

        {{-- Contracts Table --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">
                    📄 الصكوك المرتبطة
                    <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-bold bg-indigo-100 text-indigo-700 mr-2">
                        {{ $animal->contractItems->count() }}
                    </span>
                </h6>
                <a href="{{ route('udhiya.contracts.create') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-black rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-100 transition-all">
                    ＋ صك جديد
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">رقم الصك</th>
                            <th class="px-5 py-3">العميل</th>
                            <th class="px-5 py-3 text-center">نوع الحصة</th>
                            <th class="px-5 py-3 text-center">الأنصبة</th>
                            <th class="px-5 py-3 text-center">السعر</th>
                            <th class="px-5 py-3 w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($animal->contractItems as $item)
                        @php
                            $paid      = $item->contract->paid_amount ?? 0;
                            $total     = $item->contract->total_amount ?? 0;
                            $remaining = $total - $paid;
                            $isPaid    = $remaining <= 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3 font-black text-slate-800">{{ $item->contract->contract_number }}</td>
                            <td class="px-5 py-3">
                                <div class="font-semibold text-slate-700">{{ $item->contract->customer->name }}</div>
                                @if($item->contract->customer->phone)
                                <div class="text-xs text-slate-400">{{ $item->contract->customer->phone }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ \App\Models\Animal::SHARE_LABELS[$item->share_type] ?? $item->share_type ?? 'كامل' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center font-bold text-slate-700">{{ $item->shares_count }}</td>
                            <td class="px-5 py-3 text-center">
                                <div class="font-black text-emerald-700">{{ number_format($item->total_price, 0) }} ج.م</div>
                                @if($isPaid)
                                    <span class="text-xs font-bold text-emerald-600">✅ مسدد</span>
                                @else
                                    <span class="text-xs font-bold text-amber-600">متبقي {{ number_format($remaining, 0) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <a href="{{ route('udhiya.contracts.show', $item->contract) }}"
                                   class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition-colors mx-auto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400 text-sm font-semibold">لا توجد صكوك مرتبطة بهذا الحيوان</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Expenses --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-orange-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">
                    💸 المصاريف المرتبطة
                    <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-bold bg-orange-100 text-orange-700 mr-2">
                        {{ $animal->expenses->count() }}
                    </span>
                </h6>
                @php $totalExpenses = $animal->expenses->sum('amount'); @endphp
                @if($totalExpenses > 0)
                <span class="text-sm font-black text-orange-700">{{ number_format($totalExpenses, 0) }} ج.م</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">التاريخ</th>
                            <th class="px-5 py-3">الفئة</th>
                            <th class="px-5 py-3">الوصف</th>
                            <th class="px-5 py-3 text-left">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($animal->expenses->sortByDesc('date') as $expense)
                        @php
                            $cat = \App\Models\Expense::CATEGORIES[$expense->category] ?? ['label'=>$expense->category,'emoji'=>'📦'];
                        @endphp
                        <tr class="hover:bg-orange-50/30 transition-colors">
                            <td class="px-5 py-3 text-xs text-slate-500 font-semibold">
                                {{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-orange-50 text-orange-700 border border-orange-100">
                                    {{ $cat['emoji'] }} {{ $cat['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 font-semibold text-slate-700">{{ $expense->description }}</td>
                            <td class="px-5 py-3 text-left font-black text-orange-700">
                                {{ number_format($expense->amount, 0) }}
                                <span class="text-xs text-orange-400 font-normal">ج.م</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-slate-400 text-sm font-semibold">لا توجد مصاريف مرتبطة بهذا الحيوان</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($totalExpenses > 0)
                    <tfoot>
                        <tr class="bg-orange-50/60 border-t border-orange-100">
                            <td colspan="3" class="px-5 py-3 text-xs font-black text-slate-600">إجمالي المصاريف</td>
                            <td class="px-5 py-3 text-left text-sm font-black text-orange-700">
                                {{ number_format($totalExpenses, 0) }}
                                <span class="text-xs text-orange-400 font-normal">ج.م</span>
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- P&L Summary --}}
        @php
            $purchaseCost  = $animal->cost ?? 0;
            $contractRevenue = $animal->contractItems->sum('total_price');
            $netProfit     = $contractRevenue - $purchaseCost - $totalExpenses;
        @endphp
        @if($contractRevenue > 0 || $totalExpenses > 0)
        <div class="bg-white rounded-3xl shadow-sm border {{ $netProfit >= 0 ? 'border-emerald-100' : 'border-rose-100' }} overflow-hidden">
            <div class="px-6 py-5 border-b {{ $netProfit >= 0 ? 'bg-emerald-50/50 border-emerald-100' : 'bg-rose-50/50 border-rose-100' }}">
                <h6 class="text-base font-black text-slate-800 m-0">📊 ملخص الأرباح والخسائر</h6>
            </div>
            <div class="px-6 py-5 space-y-3 text-sm">
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-slate-500 font-semibold">تكلفة الشراء</span>
                    <span class="font-black text-rose-600">{{ number_format($purchaseCost, 0) }} ج.م</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-slate-500 font-semibold">المصاريف الإضافية</span>
                    <span class="font-black text-orange-600">{{ number_format($totalExpenses, 0) }} ج.م</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-slate-500 font-semibold">إجمالي التكلفة</span>
                    <span class="font-black text-slate-800">{{ number_format($purchaseCost + $totalExpenses, 0) }} ج.م</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-50">
                    <span class="text-slate-500 font-semibold">الإيرادات (الصكوك)</span>
                    <span class="font-black text-emerald-600">{{ number_format($contractRevenue, 0) }} ج.م</span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="font-black text-slate-800">صافي الربح</span>
                    <span class="text-lg font-black {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $netProfit >= 0 ? '+' : '' }}{{ number_format($netProfit, 0) }} ج.م
                    </span>
                </div>
            </div>
        </div>
        @endif

        {{-- Transfer History --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">
                    🚚 سجل النقل
                    <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-bold bg-amber-100 text-amber-700 mr-2">
                        {{ $animal->transfers->count() }}
                    </span>
                </h6>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">من</th>
                            <th class="px-5 py-3">إلى</th>
                            <th class="px-5 py-3">بواسطة</th>
                            <th class="px-5 py-3">التاريخ</th>
                            <th class="px-5 py-3">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($animal->transfers as $transfer)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <span class="px-2 py-1 rounded-md bg-rose-50 text-rose-700 text-xs font-bold">{{ $transfer->fromWarehouse?->name ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-bold">{{ $transfer->toWarehouse?->name ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3 font-semibold text-slate-700">{{ $transfer->transferredBy?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-500 text-xs font-semibold">{{ $transfer->transferred_at->format('Y/m/d H:i') }}</td>
                            <td class="px-5 py-3 text-slate-400 text-xs">{{ $transfer->notes ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-slate-400 text-sm font-semibold">لا توجد عمليات نقل</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<style>
@media print { .no-print { display: none !important; } }
</style>
@endsection
