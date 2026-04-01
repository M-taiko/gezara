@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-orange-500 text-4xl">✏️</span> تعديل فاتورة #{{ $purchase->id }}
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.purchases.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المشتريات</a>
            / <a href="{{ route('udhiya.purchases.show', $purchase) }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">#{{ $purchase->id }}</a>
            / تعديل
        </p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('udhiya.purchases.update', $purchase) }}" method="POST"
      id="purchaseForm" class="flex flex-col lg:flex-row-reverse gap-6 pb-16">
    @csrf @method('PUT')

    {{-- ═══ RIGHT SIDEBAR ═══ --}}
    <div class="w-full lg:w-80 flex-shrink-0">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
            <div class="px-6 py-5 border-b border-slate-100 bg-orange-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-sm">2</div>
                <h6 class="text-base font-black text-slate-800 m-0">بيانات الفاتورة</h6>
            </div>
            <div class="p-5 space-y-4">

                {{-- Supplier --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">المورد <span class="text-rose-500">*</span></label>
                    <select name="supplier_id" required id="supplierSelect"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800">
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ $purchase->supplier_id == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">تاريخ الفاتورة <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" required
                           value="{{ \Carbon\Carbon::parse($purchase->date)->format('Y-m-d') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800">
                </div>

                {{-- Paid (readonly info) --}}
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                    <p class="text-xs font-black text-slate-500 mb-1">المدفوع حتى الآن</p>
                    <p class="text-lg font-black text-emerald-600">{{ number_format($purchase->paid, 0) }} ج.م</p>
                    <p class="text-xs text-slate-400 mt-0.5">لتعديل المدفوع استخدم زرار "تسجيل دفعة"</p>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">ملاحظات</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-3 text-sm font-semibold text-slate-800 resize-none">{{ $purchase->notes }}</textarea>
                </div>
            </div>

            {{-- Grand Total --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-900 flex justify-between items-center">
                <span class="text-xs font-bold text-slate-400">إجمالي الفاتورة</span>
                <span class="text-2xl font-black text-white">
                    <span id="grandTotal">{{ number_format($purchase->total, 2) }}</span>
                    <span class="text-sm text-indigo-400 font-normal"> ج.م</span>
                </span>
            </div>

            <div class="p-5 space-y-3">
                <button type="submit"
                        class="w-full inline-flex justify-center items-center gap-2 px-6 py-3.5 text-sm font-black rounded-xl bg-orange-500 text-white hover:bg-orange-600 shadow-md shadow-orange-200/60 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    حفظ التعديلات
                </button>
                <a href="{{ route('udhiya.purchases.show', $purchase) }}"
                   class="w-full inline-flex justify-center items-center px-6 py-2.5 text-sm font-bold rounded-xl bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 transition-all">
                    إلغاء
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ LEFT: Items ═══ --}}
    <div class="flex-1 min-w-0">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-black text-sm">1</div>
                <h6 class="text-base font-black text-slate-800 m-0">أصناف الفاتورة</h6>
            </div>

            <div class="p-5">
                <div class="overflow-x-auto rounded-2xl ring-1 ring-slate-100 bg-slate-50/30 mb-5">
                    <table class="w-full text-right" id="itemsTable">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                                <th class="px-4 py-3 min-w-[200px]">المنتج</th>
                                <th class="px-4 py-3 w-24 text-center">العدد</th>
                                <th class="px-4 py-3 w-28 text-center">الوزن (كجم)</th>
                                <th class="px-4 py-3 w-32 text-center">سعر الرأس (ج.م)</th>
                                <th class="px-4 py-3 w-32 text-center">الإجمالي</th>
                                <th class="px-3 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-slate-100">
                            @foreach($purchase->items as $idx => $item)
                            <tr class="item-row bg-white hover:bg-indigo-50/10 transition-colors">
                                <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                                <td class="px-4 py-3">
                                    <select name="items[{{ $idx }}][product_id]" required
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800">
                                        <option value="">— اختر المنتج —</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}" {{ $item->product_id == $p->id ? 'selected' : '' }}>
                                            {{ $p->mainCategory->name }} — {{ $p->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $idx }}][quantity]" min="1"
                                           value="{{ $item->quantity }}" required
                                           class="item-qty w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-2 text-sm font-bold text-center text-slate-800">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $idx }}][weight]" step="0.01" min="0"
                                           value="{{ $item->weight }}" placeholder="—"
                                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-2 text-sm font-bold text-center text-slate-800">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $idx }}][cost_per_unit]" step="0.01" min="0"
                                           value="{{ $item->cost_per_unit }}" required
                                           class="item-price w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-2 text-sm font-bold text-center text-slate-800">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $idx }}][total]" step="0.01" readonly
                                           value="{{ $item->total }}"
                                           class="item-total w-full border-0 bg-transparent py-2 px-1 text-sm font-black text-indigo-600 text-center">
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <button type="button"
                                            class="remove-row w-8 h-8 rounded-lg bg-rose-50 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors mx-auto">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="button" id="addRow"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-100 transition-all">
                    ＋ إضافة صنف آخر
                </button>
            </div>

            <div class="mx-5 mb-5 bg-amber-50 border border-amber-100 rounded-2xl px-4 py-3 flex items-start gap-3">
                <span class="text-xl mt-0.5">⚠️</span>
                <p class="text-xs font-bold text-amber-700 leading-relaxed m-0">
                    تعديل الكميات أو الأسعار يؤثر على إجمالي الفاتورة ورصيد المورد.
                    الحيوانات المنشأة مسبقاً لا تتأثر بالتعديل.
                </p>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
let rowIndex = {{ $purchase->items->count() }};

const productOptions = `@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->mainCategory->name }} — {{ $p->name }}</option>@endforeach`;

function calcRow(row) {
    const qty   = parseFloat(row.querySelector('.item-qty').value)   || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    row.querySelector('.item-total').value = (qty * price).toFixed(2);
    calcGrand();
}

function calcGrand() {
    let grand = 0;
    document.querySelectorAll('.item-total').forEach(el => grand += parseFloat(el.value) || 0);
    document.getElementById('grandTotal').textContent =
        grand.toLocaleString('ar-EG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

document.getElementById('itemsBody').addEventListener('input', function(e) {
    const row = e.target.closest('.item-row');
    if (row && (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price'))) {
        calcRow(row);
    }
});

document.getElementById('addRow').addEventListener('click', function() {
    const tr = document.createElement('tr');
    tr.className = 'item-row bg-white hover:bg-indigo-50/10 transition-colors';
    tr.innerHTML = `
        <td class="px-4 py-3">
            <select name="items[${rowIndex}][product_id]" required
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800">
                <option value="">— اختر المنتج —</option>
                ${productOptions}
            </select>
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[${rowIndex}][quantity]" min="1" value="1" required
                   class="item-qty w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-2 text-sm font-bold text-center text-slate-800">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[${rowIndex}][weight]" step="0.01" min="0" placeholder="—"
                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-2 text-sm font-bold text-center text-slate-800">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[${rowIndex}][cost_per_unit]" step="0.01" min="0" required placeholder="0.00"
                   class="item-price w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-2 text-sm font-bold text-center text-slate-800">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[${rowIndex}][total]" step="0.01" readonly placeholder="0.00"
                   class="item-total w-full border-0 bg-transparent py-2 px-1 text-sm font-black text-indigo-600 text-center">
        </td>
        <td class="px-3 py-3 text-center">
            <button type="button" class="remove-row w-8 h-8 rounded-lg bg-rose-50 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors mx-auto">✕</button>
        </td>`;
    document.getElementById('itemsBody').appendChild(tr);
    rowIndex++;
});

document.getElementById('itemsBody').addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            e.target.closest('.item-row').remove();
            calcGrand();
        }
    }
});
</script>
@endpush
