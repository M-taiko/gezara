@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">📥</span> تسجيل مشتريات جديدة
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.purchases.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المشتريات</a> / إضافة
        </p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('udhiya.purchases.store') }}" method="POST" id="purchaseForm" class="flex flex-col lg:flex-row gap-8 pb-16">
    @csrf

    {{-- ========== LEFT COLUMN: ITEMS TABLE ========== --}}
    <div class="flex-1 lg:w-2/3">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col h-full hover:shadow-md transition-shadow duration-300">
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">1</div>
                <h6 class="text-lg font-black text-slate-800 m-0">أصناف الفاتورة</h6>
            </div>
            
            <div class="p-8 flex-1">
                <div class="overflow-x-auto ring-1 ring-slate-100 rounded-2xl shadow-inner bg-slate-50/20 mb-6">
                    <table class="w-full text-right" id="itemsTable">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-sm font-bold">
                                <th class="px-5 py-3 min-w-[200px]">المنتج</th>
                                <th class="px-5 py-3 w-28">العدد المتوقع</th>
                                <th class="px-5 py-3 w-32">الوزن التقديري</th>
                                <th class="px-5 py-3 w-32">سعر الرأس (ج.م)</th>
                                <th class="px-5 py-3 w-32">إجمالي الصنف</th>
                                <th class="px-4 py-3 w-16"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-slate-100">
                            <!-- ITEM ROW TEMPLATE -->
                            <tr class="item-row hover:bg-white transition-colors">
                                <td class="px-5 py-4">
                                    <select name="items[0][product_id]" class="w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-2.5 px-3 text-sm font-bold text-slate-800 shadow-sm" required>
                                        <option value="">-- اختر المنتج --</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">🐄 {{ $product->mainCategory->name }} — {{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-5 py-4">
                                    <input type="number" name="items[0][quantity]" class="item-qty w-full rounded-xl border border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-bold text-center text-slate-800 shadow-sm" min="1" value="1" required>
                                </td>
                                <td class="px-5 py-4 relative">
                                    <div class="flex items-center">
                                        <input type="number" name="items[0][weight]" class="w-full rounded-xl border border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 pr-8 text-sm font-bold text-slate-800 shadow-sm" step="0.01" min="0">
                                        <span class="absolute right-8 text-slate-400 font-bold text-xs pointer-events-none">كجم</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <input type="number" name="items[0][cost_per_unit]" class="item-price w-full rounded-xl border border-slate-200 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-bold text-slate-800 shadow-sm" step="0.01" min="0" required placeholder="0.00">
                                </td>
                                <td class="px-5 py-4">
                                    <input type="number" name="items[0][total]" class="item-total w-full border-0 bg-transparent py-2 px-1 text-sm font-black text-indigo-600 text-center" step="0.01" readonly placeholder="0.00">
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button type="button" class="remove-row w-8 h-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors shadow-sm mx-auto" title="إزالة">
                                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" id="addRow" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold rounded-xl transition-all bg-indigo-50 text-indigo-700 hover:bg-indigo-100 shadow-sm border border-indigo-100">
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    إضافة صنف آخر للفاتورة
                </button>
            </div>
            
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-900 text-white flex justify-between items-center">
                <div class="text-sm font-bold text-slate-400">إجمالي فاتورة المشتريات</div>
                <div class="text-3xl font-black"><span id="grandTotal">0.00</span> <span class="text-lg text-indigo-400">ج.م</span></div>
            </div>
        </div>
    </div>

    {{-- ========== RIGHT COLUMN: INVOICE META ========== --}}
    <div class="w-full lg:w-1/3">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">2</div>
                <h6 class="text-lg font-black text-slate-800 m-0">بيانات التوريد والعصبة</h6>
            </div>
            
            <div class="p-8">
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">جهة التوريد (المورد) <span class="text-rose-500">*</span></label>
                    <select name="supplier_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                        <option value="">-- حدد المورد من القائمة --</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">تاريخ فاتورة الشراء <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                </div>
                
                <div class="mb-6 relative">
                    <label class="block text-sm font-bold text-slate-700 mb-2">الدفعة المقدمة (ج.م)</label>
                    <div class="flex items-center">
                        <input type="number" name="paid" min="0" step="0.01" value="0" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-colors py-3 px-4 text-sm font-black text-emerald-700 shadow-inner">
                        <div class="absolute right-4 pointer-events-none text-emerald-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-slate-500 mt-2">إن كان هناك متبقي سينزل آلياً في رصيد المورد.</p>
                </div>
                
                <div class="mb-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات الفاتورة</label>
                    <textarea name="notes" rows="3" placeholder="أرقام سيارات النقل، الملاحظات الصحية..." class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner resize-none"></textarea>
                </div>
            </div>
            
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/80 flex flex-col gap-3">
                <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3.5 text-base font-black rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    مسرحة وفهرسة المشترى
                </button>
                <a href="{{ route('udhiya.purchases.index') }}" class="w-full inline-flex justify-center items-center px-6 py-3 text-sm font-bold rounded-xl transition-all bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-slate-200 shadow-sm">
                    إلغاء والعودة
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
let rowIndex = 1;

function calcRow(row) {
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const total = qty * price;
    row.querySelector('.item-total').value = total.toFixed(2);
    calcGrand();
}

function calcGrand() {
    let grand = 0;
    document.querySelectorAll('.item-total').forEach(el => grand += parseFloat(el.value) || 0);
    
    // Animate grand total
    const grandEl = document.getElementById('grandTotal');
    grandEl.textContent = Number(grand).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

document.getElementById('itemsBody').addEventListener('input', function(e) {
    const row = e.target.closest('.item-row');
    if (row && (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price'))) {
        calcRow(row);
    }
});

document.getElementById('addRow').addEventListener('click', function() {
    const template = document.querySelector('.item-row').cloneNode(true);
    
    // Clear values
    template.querySelectorAll('input').forEach(i => {
        if(i.classList.contains('item-qty')) i.value = 1;
        else i.value = '';
    });
    template.querySelectorAll('select').forEach(s => s.value = '');
    
    // Update names for the multidimensional array indices
    template.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/items\[\d+\]/, 'items[' + rowIndex + ']');
    });
    
    template.classList.add('animate-fade-in');
    
    document.getElementById('itemsBody').appendChild(template);
    rowIndex++;
});

document.getElementById('itemsBody').addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) { 
            const row = e.target.closest('.item-row');
            row.style.opacity = '0';
            setTimeout(() => {
                row.remove();
                calcGrand();
            }, 300);
        } else {
            alert('الفاتورة يجب أن تحوي صنفاً واحداً على الأقل.');
        }
    }
});
</script>
<style>
    @keyframes fadeIn { from {opacity: 0; transform: translateY(-10px);} to {opacity: 1; transform: translateY(0);} }
    .animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>
@endpush
