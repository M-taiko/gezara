@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🛒</span> تسجيل مشترى جديد
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.purchases.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المشتريات</a>
            / إضافة
        </p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('udhiya.purchases.store') }}" method="POST" id="purchaseForm" class="flex flex-col lg:flex-row-reverse gap-6 pb-16">
    @csrf

    {{-- ═══ RIGHT SIDEBAR: Meta ═══ --}}
    <div class="w-full lg:w-80 flex-shrink-0">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
            <div class="px-6 py-5 border-b border-slate-100 bg-indigo-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-sm">2</div>
                <h6 class="text-base font-black text-slate-800 m-0">بيانات الفاتورة</h6>
            </div>
            <div class="p-5 space-y-4">

                {{-- Supplier --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">
                        المورد <span class="text-rose-500">*</span>
                    </label>
                    <select name="supplier_id" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">— اختر المورد —</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">
                        تاريخ الفاتورة <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                </div>

                {{-- Paid --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">الدفعة المقدمة (ج.م)</label>
                    <input type="number" name="paid" min="0" step="0.01" value="{{ old('paid', 0) }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-black text-emerald-700 transition-colors">
                    <p class="text-xs text-slate-400 font-semibold mt-1">المتبقي يُسجَّل في رصيد المورد تلقائياً</p>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">ملاحظات</label>
                    <textarea name="notes" rows="3"
                              placeholder="أرقام سيارات النقل، ملاحظات صحية..."
                              class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-3 text-sm font-semibold text-slate-800 resize-none transition-colors">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Grand Total display --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-900 flex justify-between items-center">
                <span class="text-xs font-bold text-slate-400">إجمالي الفاتورة</span>
                <span class="text-2xl font-black text-white">
                    <span id="grandTotal">0.00</span>
                    <span class="text-sm text-indigo-400 font-normal"> ج.م</span>
                </span>
            </div>

            <div class="p-5 space-y-3">
                <button type="submit"
                        class="w-full inline-flex justify-center items-center gap-2 px-6 py-3.5 text-sm font-black rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200/60 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    حفظ وإنشاء الحيوانات
                </button>
                <a href="{{ route('udhiya.purchases.index') }}"
                   class="w-full inline-flex justify-center items-center px-6 py-2.5 text-sm font-bold rounded-xl bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 transition-all">
                    إلغاء
                </a>
            </div>
        </div>
    </div>

    {{-- ═══ LEFT: Items Table ═══ --}}
    <div class="flex-1 min-w-0">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-sm">1</div>
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
                            <tr class="item-row bg-white hover:bg-indigo-50/10 transition-colors">
                                <td class="px-4 py-3">
                                    <select name="items[0][product_id]" required
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                                        <option value="">— اختر المنتج —</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->mainCategory->name }} — {{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[0][quantity]" min="1" value="1" required
                                           class="item-qty w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-2 text-sm font-bold text-center text-slate-800 transition-colors">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[0][weight]" step="0.01" min="0" placeholder="—"
                                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-2 text-sm font-bold text-center text-slate-800 transition-colors">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[0][cost_per_unit]" step="0.01" min="0" required placeholder="0.00"
                                           class="item-price w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-2 text-sm font-bold text-center text-slate-800 transition-colors">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[0][total]" step="0.01" readonly placeholder="0.00"
                                           class="item-total w-full border-0 bg-transparent py-2 px-1 text-sm font-black text-indigo-600 text-center">
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <button type="button"
                                            class="remove-row w-8 h-8 rounded-lg bg-rose-50 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors mx-auto">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" id="addRow"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-100 transition-all">
                    ＋ إضافة صنف آخر
                </button>
            </div>

            {{-- Info note --}}
            <div class="mx-5 mb-5 bg-amber-50 border border-amber-100 rounded-2xl px-4 py-3 flex items-start gap-3">
                <span class="text-xl mt-0.5">💡</span>
                <p class="text-xs font-bold text-amber-700 leading-relaxed m-0">
                    بعد حفظ الفاتورة سيتم إنشاء الحيوانات تلقائياً بناءً على العدد المدخل لكل صنف،
                    ويمكنك تعديل بيانات كل حيوان من صفحة الحيوانات.
                </p>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
let rowIndex = 1;

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
    const template = document.querySelector('.item-row').cloneNode(true);
    template.querySelectorAll('input').forEach(i => {
        i.value = i.classList.contains('item-qty') ? 1 : '';
    });
    template.querySelectorAll('select').forEach(s => s.value = '');
    template.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/items\[\d+\]/, 'items[' + rowIndex + ']');
    });
    document.getElementById('itemsBody').appendChild(template);
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
