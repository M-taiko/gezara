@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-cyan-600 text-4xl">🧊</span> مخزن اللحوم
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.dashboard') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الرئيسية</a>
            / مخزن اللحوم
        </p>
    </div>
    <form method="GET" action="{{ route('udhiya.meat-inventory.index') }}" class="flex items-center gap-2">
        <select name="status" onchange="this.form.submit()"
                class="rounded-xl border border-slate-200 bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm">
            <option value="">كل الدفعات</option>
            <option value="available" {{ $status === 'available' ? 'selected' : '' }}>متاح للبيع</option>
            <option value="sold_out"  {{ $status === 'sold_out'  ? 'selected' : '' }}>نفد</option>
        </select>
    </form>
</div>
@endsection

@section('content')

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-cyan-100 p-4 flex flex-col gap-1">
        <span class="text-2xl mb-1">🧊</span>
        <p class="text-xs font-bold text-slate-400">إجمالي المخزن</p>
        <p class="text-xl font-black text-cyan-700">{{ number_format($totalStock, 1) }} <span class="text-xs text-cyan-400">كجم</span></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 p-4 flex flex-col gap-1">
        <span class="text-2xl mb-1">✅</span>
        <p class="text-xs font-bold text-slate-400">تم بيعه</p>
        <p class="text-xl font-black text-emerald-700">{{ number_format($totalSoldKg, 1) }} <span class="text-xs text-emerald-400">كجم</span></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-amber-100 p-4 flex flex-col gap-1">
        <span class="text-2xl mb-1">📦</span>
        <p class="text-xs font-bold text-slate-400">المتبقي للبيع</p>
        <p class="text-xl font-black text-amber-700">{{ number_format($totalRemaining, 1) }} <span class="text-xs text-amber-400">كجم</span></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 p-4 flex flex-col gap-1">
        <span class="text-2xl mb-1">💰</span>
        <p class="text-xs font-bold text-slate-400">إجمالي الإيراد</p>
        <p class="text-xl font-black text-indigo-700">{{ number_format($totalRevenue, 0) }} <span class="text-xs text-indigo-400">ج.م</span></p>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-6 pb-16">

    {{-- ═══ RIGHT SIDEBAR: Sell Form ═══ --}}
    <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-5">

        <div class="bg-white rounded-3xl shadow-sm border border-emerald-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-emerald-100 bg-emerald-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">🛒 تسجيل بيع جديد</h6>
            </div>
            <div class="p-5">
                @php $availableBatches = $items->filter(fn($i) => !$i->isSoldOut()); @endphp

                @if($availableBatches->isEmpty())
                <div class="text-center py-8">
                    <div class="text-5xl mb-3">📦</div>
                    <p class="text-slate-400 font-bold text-sm">لا يوجد مخزون متاح للبيع</p>
                </div>
                @else
                <form action="{{ route('udhiya.meat-sales.store') }}" method="POST" id="saleForm" class="space-y-4">
                    @csrf

                    {{-- Batch selector --}}
                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-1.5">الدفعة <span class="text-rose-500">*</span></label>
                        <select name="meat_inventory_id" id="batchSelect" required onchange="updateMaxWeight(this)"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="">— اختر الدفعة —</option>
                            @foreach($availableBatches as $batch)
                            <option value="{{ $batch->id }}"
                                    data-remaining="{{ $batch->remainingWeight() }}"
                                    data-animal="{{ $batch->animal->code }}">
                                {{ $batch->animal->code }} — {{ $batch->animal->product?->name }}
                                (متاح: {{ number_format($batch->remainingWeight(), 1) }} كجم)
                            </option>
                            @endforeach
                        </select>
                        <p id="remainingHint" class="text-xs text-slate-400 font-semibold mt-1 hidden">
                            المتاح: <span id="remainingVal" class="font-black text-cyan-700"></span> كجم
                        </p>
                    </div>

                    {{-- Customer --}}
                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-1.5">اسم العميل <span class="text-rose-500">*</span></label>
                        <input type="text" name="customer_name" required
                               placeholder="اسم المشتري"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-1.5">رقم الهاتف</label>
                        <input type="text" name="customer_phone"
                               placeholder="اختياري"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>

                    {{-- Weight + Price --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black text-slate-600 mb-1.5">الوزن (كجم) <span class="text-rose-500">*</span></label>
                            <input type="number" name="weight_kg" id="weightInput" required
                                   step="0.1" min="0.1"
                                   placeholder="0.0"
                                   oninput="calcTotal()"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors text-center">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 mb-1.5">سعر الكجم (ج.م) <span class="text-rose-500">*</span></label>
                            <input type="number" name="price_per_kg" id="priceInput" required
                                   step="0.5" min="0"
                                   placeholder="0.00"
                                   oninput="calcTotal()"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors text-center">
                        </div>
                    </div>

                    {{-- Auto total --}}
                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl px-4 py-3 flex items-center justify-between">
                        <span class="text-xs font-black text-emerald-700">الإجمالي</span>
                        <span id="totalDisplay" class="text-xl font-black text-emerald-700">0.00 <span class="text-xs font-normal">ج.م</span></span>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-1.5">تاريخ البيع <span class="text-rose-500">*</span></label>
                        <input type="date" name="sale_date" required
                               value="{{ date('Y-m-d') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-1.5">ملاحظات</label>
                        <textarea name="notes" rows="2" placeholder="اختياري..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2 px-3 text-sm font-semibold text-slate-800 resize-none transition-colors"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 text-sm font-black rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-200">
                        ✅ تسجيل البيع
                    </button>
                </form>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══ LEFT: Main Content ═══ --}}
    <div class="flex-1 min-w-0 flex flex-col gap-6">

        {{-- Inventory Batches --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">🧊 دفعات المخزن</h6>
                <span class="text-xs font-bold text-slate-400">{{ $items->count() }} دفعة</span>
            </div>
            <div class="overflow-x-auto">
                @if($items->isEmpty())
                <div class="py-16 text-center">
                    <div class="text-5xl mb-4">🧊</div>
                    <p class="text-slate-400 font-bold">المخزن فارغ</p>
                    <p class="text-slate-300 text-xs mt-1">يتم تعبئة المخزن تلقائياً عند ذبح ذبيحة بأنصبة غير مبيعة</p>
                </div>
                @else
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">الذبيحة</th>
                            <th class="px-5 py-3 text-center">الإجمالي</th>
                            <th class="px-5 py-3 text-center">المباع</th>
                            <th class="px-5 py-3 text-center">المتبقي</th>
                            <th class="px-5 py-3 text-center hidden sm:table-cell">عمليات البيع</th>
                            <th class="px-5 py-3 text-center">الحالة</th>
                            <th class="px-5 py-3 w-10 no-print"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($items as $batch)
                        @php
                            $remaining = $batch->remainingWeight();
                            $pct       = $batch->weight_kg > 0 ? min(100, ($batch->sold_weight_kg / $batch->weight_kg) * 100) : 0;
                            $soldOut   = $batch->isSoldOut();
                        @endphp
                        <tr class="hover:bg-slate-50/40 transition-colors {{ $soldOut ? 'opacity-60' : '' }}">
                            <td class="px-5 py-4">
                                <a href="{{ route('udhiya.animals.show', $batch->animal) }}"
                                   class="font-black text-indigo-600 hover:underline text-sm">
                                    {{ $batch->animal->code }}
                                </a>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $batch->animal->product?->name }}</div>
                                @if($batch->notes)
                                <div class="text-xs text-slate-300 mt-0.5 truncate max-w-[120px]">{{ $batch->notes }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="font-black text-slate-700">{{ number_format($batch->weight_kg, 1) }}</span>
                                <span class="text-xs text-slate-400"> كجم</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="font-black text-emerald-600">{{ number_format($batch->sold_weight_kg, 1) }}</span>
                                <span class="text-xs text-slate-400"> كجم</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($soldOut)
                                    <span class="text-xs font-black text-slate-400">نفد</span>
                                @else
                                    <span class="font-black text-cyan-700">{{ number_format($remaining, 1) }}</span>
                                    <span class="text-xs text-slate-400"> كجم</span>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full bg-cyan-400 transition-all" style="width:{{ 100 - $pct }}%"></div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center text-sm font-bold text-slate-700 hidden sm:table-cell">
                                {{ $batch->sales->count() }} عملية
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($soldOut)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-slate-100 text-slate-500 border border-slate-200">
                                        🏷 نفد
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-cyan-50 text-cyan-700 border border-cyan-200">
                                        🧊 متاح
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center no-print">
                                <form action="{{ route('udhiya.meat-inventory.destroy', $batch) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('حذف هذه الدفعة وجميع مبيعاتها؟')"
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white inline-flex items-center justify-center transition-colors text-xs">
                                        🗑
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        {{-- Sales History --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">🛒 سجل المبيعات</h6>
                <span class="text-xs font-bold text-slate-400">{{ $sales->count() }} عملية</span>
            </div>
            <div class="overflow-x-auto">
                @if($sales->isEmpty())
                <div class="py-12 text-center">
                    <div class="text-4xl mb-3">🛒</div>
                    <p class="text-slate-400 font-bold text-sm">لا توجد مبيعات بعد</p>
                </div>
                @else
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">التاريخ</th>
                            <th class="px-5 py-3">العميل</th>
                            <th class="px-5 py-3 hidden sm:table-cell">الذبيحة</th>
                            <th class="px-5 py-3 text-center">الوزن</th>
                            <th class="px-5 py-3 text-center hidden md:table-cell">سعر الكجم</th>
                            <th class="px-5 py-3 text-left">الإجمالي</th>
                            <th class="px-5 py-3 w-10 no-print"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($sales as $sale)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <td class="px-5 py-3 text-xs text-slate-400 font-semibold whitespace-nowrap">
                                {{ $sale->sale_date->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="text-sm font-black text-slate-800">{{ $sale->customer_name }}</div>
                                @if($sale->customer_phone)
                                @php
                                    $phone = $sale->customer_phone;
                                    $wa = '2' . ltrim(preg_replace('/\D/', '', $phone), '2');
                                    if (!str_starts_with($wa, '20')) $wa = '20' . ltrim($wa, '20');
                                @endphp
                                <a href="https://wa.me/{{ $wa }}" target="_blank"
                                   class="text-xs text-emerald-600 hover:underline">{{ $phone }}</a>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-500 font-semibold hidden sm:table-cell">
                                {{ $sale->inventory->animal->code ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-center font-black text-cyan-700">
                                {{ number_format($sale->weight_kg, 1) }}
                                <span class="text-xs text-slate-400 font-normal"> كجم</span>
                            </td>
                            <td class="px-5 py-3 text-center text-sm font-semibold text-slate-600 hidden md:table-cell">
                                {{ number_format($sale->price_per_kg, 2) }} ج.م
                            </td>
                            <td class="px-5 py-3 text-left font-black text-emerald-700 text-base">
                                {{ number_format($sale->total_amount, 0) }}
                                <span class="text-xs text-emerald-400 font-normal"> ج.م</span>
                            </td>
                            <td class="px-5 py-3 text-center no-print">
                                <form action="{{ route('udhiya.meat-sales.destroy', $sale) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('حذف هذه العملية؟')"
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white inline-flex items-center justify-center transition-colors text-xs">
                                        🗑
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-emerald-50/60 border-t-2 border-emerald-100">
                            <td colspan="3" class="px-5 py-3 text-xs font-black text-slate-600">الإجمالي</td>
                            <td class="px-5 py-3 text-center font-black text-cyan-700">
                                {{ number_format($sales->sum('weight_kg'), 1) }} كجم
                            </td>
                            <td class="hidden md:table-cell"></td>
                            <td class="px-5 py-3 text-left font-black text-emerald-700 text-base">
                                {{ number_format($sales->sum('total_amount'), 0) }}
                                <span class="text-xs text-emerald-400 font-normal"> ج.م</span>
                            </td>
                            <td class="no-print"></td>
                        </tr>
                    </tfoot>
                </table>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection

@push('js')
<script>
function updateMaxWeight(sel) {
    const opt = sel.options[sel.selectedIndex];
    const remaining = opt.dataset.remaining ?? 0;
    const hint      = document.getElementById('remainingHint');
    const val       = document.getElementById('remainingVal');
    const weightIn  = document.getElementById('weightInput');

    if (remaining > 0) {
        val.textContent = parseFloat(remaining).toFixed(1);
        hint.classList.remove('hidden');
        weightIn.max = remaining;
    } else {
        hint.classList.add('hidden');
        weightIn.removeAttribute('max');
    }
    calcTotal();
}

function calcTotal() {
    const w = parseFloat(document.getElementById('weightInput').value) || 0;
    const p = parseFloat(document.getElementById('priceInput').value)  || 0;
    document.getElementById('totalDisplay').innerHTML =
        (w * p).toFixed(2) + ' <span class="text-xs font-normal">ج.م</span>';
}
</script>
@endpush
