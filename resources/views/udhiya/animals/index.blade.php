@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🐄</span> الحيوانات والمخزون
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">إدارة القطيع، الأوزان، الأنواع والمخازن</p>
    </div>
    <div class="flex gap-3">
        <button type="button" @click="$dispatch('open-products-modal')" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all bg-white text-slate-700 hover:bg-slate-50 hover:text-indigo-600 shadow-sm border border-slate-200 ring-1 ring-inset ring-slate-100">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            إدارة النوعيات
        </button>
        <button type="button" @click="$dispatch('open-add-animal-modal')" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            إضافة حيوان
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Filters --}}
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 mb-8 overflow-hidden">
    <div class="p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4 items-end">
            <div class="col-span-1 lg:col-span-1">
                <label class="block text-sm font-bold text-slate-700 mb-2">بحث بالكود</label>
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" class="w-full rounded-xl border-slate-200 bg-slate-50 border focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-2.5 pr-10 pl-3 text-sm font-semibold text-slate-700" value="{{ request('search') }}" placeholder="ابحث...">
                </div>
            </div>
            <div class="col-span-1 lg:col-span-1">
                <label class="block text-sm font-bold text-slate-700 mb-2">الفئة</label>
                <select name="category" class="w-full rounded-xl border-slate-200 bg-slate-50 border focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-2.5 px-3 text-sm font-semibold text-slate-700">
                    <option value="">كل الفئات</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 lg:col-span-1">
                <label class="block text-sm font-bold text-slate-700 mb-2">المخزن</label>
                <select name="warehouse" class="w-full rounded-xl border-slate-200 bg-slate-50 border focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-2.5 px-3 text-sm font-semibold text-slate-700">
                    <option value="">كل المخازن</option>
                    @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" @selected(request('warehouse') == $wh->id)>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 lg:col-span-1">
                <label class="block text-sm font-bold text-slate-700 mb-2">الحالة</label>
                <select name="status" class="w-full rounded-xl border-slate-200 bg-slate-50 border focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-2.5 px-3 text-sm font-semibold text-slate-700">
                    <option value="">كل الحالات</option>
                    @foreach(\App\Models\Animal::STATUS_LABELS as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') == $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-1 lg:col-span-1 flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold rounded-xl transition-all bg-slate-900 text-white hover:bg-slate-800 shadow-md">
                    تصفية
                </button>
                @if(request()->hasAny(['search','category','warehouse','status']))
                <a href="{{ route('udhiya.animals.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold rounded-xl transition-all bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-100" title="إلغاء التصفية">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-sm">
                    <th class="px-6 py-4 font-bold">الكود</th>
                    <th class="px-6 py-4 font-bold">النوع</th>
                    <th class="px-6 py-4 font-bold">الفئة</th>
                    <th class="px-6 py-4 font-bold">المخزن</th>
                    <th class="px-6 py-4 font-bold">الوزن</th>
                    <th class="px-6 py-4 font-bold">التكلفة</th>
                    <th class="px-6 py-4 font-bold">نظام</th>
                    <th class="px-6 py-4 font-bold">الحالة</th>
                    <th class="px-6 py-4 font-bold text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                @forelse($animals as $animal)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-bold border border-slate-200">
                            {{ $animal->code }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-slate-800 font-bold max-w-[150px] truncate">{{ $animal->product->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-slate-500">{{ $animal->product->mainCategory->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1 text-slate-600">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            {{ $animal->warehouse->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($animal->weight)
                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-100">{{ $animal->weight }} كجم</span>
                        @else<span class="text-slate-300">—</span>@endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-bold text-indigo-600">{{ number_format($animal->cost, 2) }} ج.م</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($animal->is_grouped)
                            <span class="px-2.5 py-1 bg-sky-50 text-sky-700 rounded-lg text-xs font-bold border border-sky-100">{{ \App\Models\AnimalShareSetting::SHARE_TYPE_LABELS[$animal->shareSetting->share_type] ?? 'مقسم' }}</span>
                        @else
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold border border-slate-200">كامل</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'available' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'partially_allocated' => 'bg-orange-50 text-orange-700 border-orange-200',
                                'fully_allocated' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'slaughtered' => 'bg-slate-100 text-slate-600 border-slate-200'
                            ];
                            $colorClass = $statusColors[$animal->status] ?? 'bg-slate-50 text-slate-700';
                            $labels = \App\Models\Animal::STATUS_LABELS;
                        @endphp
                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $colorClass }}">
                            {{ $labels[$animal->status] ?? $animal->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" @click="$dispatch('open-transfer-modal', { id: '{{ $animal->id }}', code: '{{ $animal->code }}', warehouse: '{{ $animal->warehouse->name }}' })"
                                    class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white flex items-center justify-center transition-colors" title="نقل إلى مخزن آخر">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            </button>
                            <a href="{{ route('udhiya.animals.show', $animal) }}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition-colors" title="تفاصيل وحصص">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            <a href="{{ route('udhiya.animals.edit', $animal) }}" class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white flex items-center justify-center transition-colors" title="تعديل">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('udhiya.animals.destroy', $animal) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد حذف هذا الحيوان؟');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-colors" title="حذف">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-slate-500 py-12 text-base">لا توجد حيوانات مسجلة</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($animals->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">{{ $animals->links() }}</div>
    @endif
</div>

{{-- ===================== ADD ANIMAL MODAL ===================== --}}
<div x-data="{
        open: false,
        weight: '{{ old('weight') }}',
        buyPriceKg: '',
        cost: '{{ old('cost') }}',
        sellPriceKg: '',
        priceFull:    '{{ old('price_full') }}',
        priceHalf:    '{{ old('price_half') }}',
        priceThird:   '{{ old('price_third') }}',
        priceQuarter: '{{ old('price_quarter') }}',
        priceFive:    '{{ old('price_five') }}',
        priceSix:     '{{ old('price_six') }}',
        priceSeven:   '{{ old('price_seven') }}',
        isLarge: false,
        calcBuyCost()   { if(this.weight && this.buyPriceKg) this.cost = (this.weight * this.buyPriceKg).toFixed(2); },
        calcSellFull()  { if(this.weight && this.sellPriceKg) this.priceFull = (this.weight * this.sellPriceKg).toFixed(2); },
        autoCalcShares() {
            const pf = parseFloat(this.priceFull) || 0;
            if (!pf) return;
            this.priceHalf    = (pf / 2).toFixed(2);
            this.priceThird   = (pf / 3).toFixed(2);
            this.priceQuarter = (pf / 4).toFixed(2);
            this.priceFive    = (pf / 5).toFixed(2);
            this.priceSix     = (pf / 6).toFixed(2);
            this.priceSeven   = (pf / 7).toFixed(2);
        },
        onProductChange(e) {
            const cat = e.target.selectedOptions[0]?.dataset.cat || '';
            this.isLarge = ['BQR', 'JML'].includes(cat);
        }
     }"
     @open-add-animal-modal.window="open = true"
     @close-modals.window="open = false"
     class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" @click.self="open = false">
        <form action="{{ route('udhiya.animals.store') }}" method="POST" class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            @csrf
            
            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-800">إضافة حيوان جديد</h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-rose-500 transition-colors bg-white hover:bg-rose-50 rounded-xl p-2 focus:outline-none">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-8 overflow-y-auto flex-1 custom-scrollbar">
                @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-bold">
                    يرجى ألتأكد من ملء جميع الحقول الإلزامية بشكل صحيح.
                </div>
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">كود الحيوان المعرف <span class="text-rose-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner" placeholder="P-001">
                        @error('code') <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">النوعية (المُنتج) <span class="text-rose-500">*</span></label>
                        <select name="product_id" required
                                @change="onProductChange($event)"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                            <option value="">-- اختر النوعية --</option>
                            @foreach($products->groupBy(fn($p) => $p->mainCategory->name) as $catName => $prods)
                                <optgroup label="{{ $catName }}">
                                    @foreach($prods as $prod)
                                    <option value="{{ $prod->id }}"
                                            data-cat="{{ $prod->mainCategory->code ?? '' }}"
                                            @selected(old('product_id') == $prod->id)>{{ $prod->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">مكان التواجد / المخزن <span class="text-rose-500">*</span></label>
                        <select name="warehouse_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                            <option value="">-- الموقع الحالي --</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" @selected(old('warehouse_id') == $wh->id)>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">المورّد (اختياري)</label>
                        <select name="supplier_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                            <option value="">— غير مرتبط بمورد —</option>
                            @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" @selected(old('supplier_id') == $sup->id)>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ── تكلفة الشراء ── --}}
                    <div class="sm:col-span-2">
                        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 space-y-3">
                            <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-1">تكلفة الشراء</p>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">الوزن (كجم)</label>
                                    <input type="number" step="0.1" name="weight"
                                           x-model="weight" @input="calcBuyCost()"
                                           class="w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800" placeholder="0.0">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">سعر الكجم (شراء)</label>
                                    <input type="number" step="0.01" name="buy_price_kg"
                                           x-model="buyPriceKg" @input="calcBuyCost()"
                                           class="w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800" placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">إجمالي التكلفة</label>
                                    <input type="number" step="0.01" name="cost"
                                           x-model="cost"
                                           class="w-full rounded-xl border border-indigo-200 bg-indigo-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-black text-indigo-700" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── أسعار البيع ── --}}
                    <div class="sm:col-span-2">
                        <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-4 space-y-3">
                            <p class="text-xs font-black text-emerald-700 uppercase tracking-widest mb-1">أسعار البيع</p>

                            {{-- سعر الكجم بيع + السعر الكامل --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">سعر الكجم (بيع)</label>
                                    <input type="number" step="0.01" name="price_per_kg"
                                           x-model="sellPriceKg"
                                           @input="calcSellFull(); if(priceFull) autoCalcShares();"
                                           class="w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800" placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1.5">
                                        سعر الحيوان كاملاً
                                        <button type="button" @click="autoCalcShares()"
                                                class="mr-1 text-xs text-emerald-600 hover:text-emerald-800 underline underline-offset-2 font-black">احسب الأنصبة ←</button>
                                    </label>
                                    <input type="number" step="0.01" name="price_full"
                                           x-model="priceFull"
                                           @change="autoCalcShares()"
                                           class="w-full rounded-xl border border-emerald-200 bg-white focus:bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-black text-emerald-700" placeholder="0.00">
                                </div>
                            </div>

                            {{-- أسعار الأنصبة — تظهر فقط للبقر والجمال --}}
                            <div x-show="isLarge" x-transition class="pt-2 border-t border-emerald-100">
                                <p class="text-xs text-emerald-600 font-bold mb-2">أسعار الأنصبة (تُحسب تلقائياً أو يمكن تعديلها)</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">نصف (÷2)</label>
                                        <input type="number" step="0.01" name="price_half" x-model="priceHalf"
                                               class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2 px-2 text-sm font-bold text-slate-700" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">ثُلث (÷3)</label>
                                        <input type="number" step="0.01" name="price_third" x-model="priceThird"
                                               class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2 px-2 text-sm font-bold text-slate-700" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">ربع (÷4)</label>
                                        <input type="number" step="0.01" name="price_quarter" x-model="priceQuarter"
                                               class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2 px-2 text-sm font-bold text-slate-700" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">خُمس (÷5)</label>
                                        <input type="number" step="0.01" name="price_five" x-model="priceFive"
                                               class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2 px-2 text-sm font-bold text-slate-700" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">سُدس (÷6)</label>
                                        <input type="number" step="0.01" name="price_six" x-model="priceSix"
                                               class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2 px-2 text-sm font-bold text-slate-700" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">سُبع (÷7)</label>
                                        <input type="number" step="0.01" name="price_seven" x-model="priceSeven"
                                               class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2 px-2 text-sm font-bold text-slate-700" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات وحالة صحية</label>
                        <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="px-8 py-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-shrink-0">
                <button type="button" @click="open = false" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-colors">إلغاء</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all transform hover:-translate-y-0.5">
                    حفظ وإضافة الحيوان
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== TRANSFER MODAL ===================== --}}
<div x-data="{ open: false, animalId: '', animalCode: '', currentWarehouse: '', 
               initTransfer(e) {
                   this.animalId = e.detail.id;
                   this.animalCode = e.detail.code;
                   this.currentWarehouse = e.detail.warehouse;
                   this.open = true;
               }
             }" 
     @open-transfer-modal.window="initTransfer($event)"
     @close-modals.window="open = false"
     class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" @click.self="open = false">
        <form :action="'{{ url('udhiya/animals') }}/' + animalId + '/transfer'" method="POST" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            @csrf
            
            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    نقل مخزني
                </h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-rose-500 transition-colors bg-white hover:bg-rose-50 rounded-xl p-2 focus:outline-none"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            
            <div class="p-8">
                <div class="mb-6 bg-orange-50 rounded-2xl p-5 border border-orange-100 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-white text-orange-600 flex items-center justify-center font-bold text-xl shadow-sm">🐄</div>
                    <div>
                        <div class="text-sm text-orange-700 font-medium">نقل الحيوان: <span class="font-bold" x-text="animalCode"></span></div>
                        <div class="text-xs text-orange-600/80 mt-1 font-bold">من الموقع: <span x-text="currentWarehouse"></span></div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">نقل إلى الوجهة الجديدة <span class="text-rose-500">*</span></label>
                    <select name="to_warehouse_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                        <option value="">-- اختر المخزن الجديد --</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">سبب النقل أو ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner resize-none"></textarea>
                </div>
            </div>
            
            <div class="px-8 py-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-shrink-0">
                <button type="button" @click="open = false" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-colors">إلغاء</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 shadow-md shadow-orange-200 transition-all transform hover:-translate-y-0.5">
                    تأكيد نقل العهدة
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MANAGE PRODUCTS MODAL ===================== --}}
<div x-data="{ open: false }" 
     @open-products-modal.window="open = true"
     @close-modals.window="open = false"
     class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" @click.self="open = false">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    دليل وفئات الأضاحي
                </h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-rose-500 transition-colors bg-white hover:bg-rose-50 rounded-xl p-2 focus:outline-none"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            
            <div class="p-0 overflow-y-auto flex-1 custom-scrollbar">
                <table class="w-full text-right border-collapse text-sm">
                    <thead class="sticky top-0 bg-white shadow-sm ring-1 ring-slate-100 z-10">
                        <tr class="text-slate-500">
                            <th class="px-6 py-4 font-bold">النوع (المنتج)</th>
                            <th class="px-6 py-4 font-bold">التصنيف الأساسي</th>
                            <th class="px-6 py-4 font-bold text-center">عدد الرؤوس المتوفرة</th>
                            <th class="px-6 py-4 font-bold text-center">المقترحات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($allProducts as $prod)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-slate-900 font-black">{{ $prod->name }}</td>
                            <td class="px-6 py-4"><span class="px-2.5 py-1 bg-slate-100 rounded-md text-xs font-bold">{{ $prod->mainCategory->name ?? 'غير محدد' }}</span></td>
                            <td class="px-6 py-4 text-center">
                                @if($prod->animals_count > 0)
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full font-black">{{ $prod->animals_count }}</span>
                                @else
                                    <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full font-bold">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="$dispatch('open-edit-product-modal', { id: '{{ $prod->id }}', name: '{{ $prod->name }}', cat: '{{ $prod->main_category_id }}' }); open = false;" class="text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white rounded-lg p-2 transition font-bold text-xs" title="تعديل المسمى">تعديل</button>
                                    @if($prod->animals_count == 0)
                                    <form action="{{ route('udhiya.products.destroy', $prod) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد مسح هذه النوعية؟')">
                                        @csrf @method('DELETE')
                                        <button class="text-rose-600 bg-rose-50 hover:bg-rose-600 hover:text-white rounded-lg p-2 transition font-bold text-xs" title="حذف نهائي">حذف</button>
                                    </form>
                                    @else
                                    <button disabled class="text-slate-400 bg-slate-50 rounded-lg p-2 cursor-not-allowed font-bold text-xs" title="لا يمكن مسح عنصر مرتبط برؤوس">مقفول</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-8 text-slate-500">القائمة فارغة تماماً</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-6 bg-slate-50/80 border-t border-slate-200">
                <form action="{{ route('udhiya.products.store') }}" method="POST" class="flex flex-col sm:flex-row gap-4 items-end sm:items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                    @csrf
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold text-slate-500 mb-2">تسجيل نوعية جديدة</label>
                        <input type="text" name="name" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-2.5 px-4 text-sm font-semibold text-slate-800" placeholder="مثال: خروف نعيمي">
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold text-slate-500 mb-2">الفصيل</label>
                        <select name="main_category_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-2.5 px-4 text-sm font-semibold text-slate-800">
                            <option value="">-- اختر التصنيف الأساسي --</option>
                            @foreach($mainCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all">
                        تسجيل إضافة
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===================== EDIT PRODUCT MODAL ===================== --}}
<div x-data="{ open: false, pId: '', pName: '', pCat: '', 
               initEdit(e) {
                   this.pId = e.detail.id;
                   this.pName = e.detail.name;
                   this.pCat = e.detail.cat;
                   this.open = true;
               }
             }" 
     @open-edit-product-modal.window="initEdit($event)"
     @close-modals.window="open = false"
     class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <form :action="'{{ url('udhiya/products') }}/' + pId" method="POST" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden relative" @click.stop>
            @csrf @method('PATCH')
            
            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-800">تعديل المسمى الضريبي</h3>
                <button type="button" @click="open = false; $dispatch('open-products-modal')" class="text-slate-400 hover:text-rose-500 transition-colors bg-white hover:bg-rose-50 rounded-xl p-2 focus:outline-none"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            
            <div class="p-8">
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">تعديل الاسم <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="pName" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">الفئة الأم <span class="text-rose-500">*</span></label>
                    <select name="main_category_id" x-model="pCat" required class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                        <option value="">-- اختر --</option>
                        @foreach($mainCategories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="px-8 py-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-shrink-0">
                <button type="button" @click="open = false; $dispatch('open-products-modal')" class="px-6 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-800 transition-colors">تراجع للملخص</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all transform hover:-translate-y-0.5">
                    حفظ واعتماد التعديل
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('js')
<script>
// If there are validation errors on init, reopen add form naturally
@if($errors->any() && !old('to_warehouse_id') && !old('_method'))
document.addEventListener('alpine:init', () => {
    setTimeout(() => {
        window.dispatchEvent(new CustomEvent('open-add-animal-modal'));
    }, 100);
});
@endif
</script>
@endpush
