@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">✏️</span> تعديل الحيوان {{ $animal->code }}
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.animals.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الحيوانات</a>
            / {{ $animal->code }}
            / تعديل
        </p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('udhiya.animals.update', $animal) }}" method="POST" class="flex flex-col lg:flex-row gap-6 pb-16">
    @csrf
    @method('PUT')

    {{-- Right Sidebar --}}
    <div class="w-full lg:w-80">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden lg:sticky lg:top-24 space-y-6">
            {{-- Kود Editing Section --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <h6 class="text-sm font-black text-slate-800 mb-4">🏷️ الكود</h6>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">كود الحيوان <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $animal->code) }}" required
                           class="w-full rounded-xl border @error('code') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50 @enderror focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-black text-slate-800 transition-colors">
                    @error('code')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                    <p class="text-xs text-slate-500 mt-1">أدخل الكود يدويا</p>
                </div>
            </div>

            {{-- Product & Type --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <h6 class="text-sm font-black text-slate-800 mb-4">🐄 النوع</h6>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">نوع الحيوان <span class="text-rose-500">*</span></label>
                    <select name="product_id" required
                            class="w-full rounded-xl border @error('product_id') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50 @enderror focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">-- اختر النوع --</option>
                        @foreach($products->groupBy('main_category.name') as $category => $items)
                        <optgroup label="{{ $category }}">
                            @foreach($items as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $animal->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                    @error('product_id')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Warehouse & Supplier --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <h6 class="text-sm font-black text-slate-800 mb-4">📦 المخزن والمورد</h6>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">المخزن <span class="text-rose-500">*</span></label>
                        <select name="warehouse_id" required
                                class="w-full rounded-xl border @error('warehouse_id') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50 @enderror focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="">-- اختر المخزن --</option>
                            @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ old('warehouse_id', $animal->warehouse_id) == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('warehouse_id')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">المورد</label>
                        <select name="supplier_id"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="">-- بدون مورد --</option>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id', $animal->supplier_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="px-6 py-5 bg-slate-50/80 space-y-3">
                <button type="submit" class="w-full rounded-xl bg-indigo-600 text-white font-black py-3 hover:bg-indigo-700 shadow-lg shadow-indigo-200/60 transition-all">
                    ✅ حفظ التعديلات
                </button>
                <a href="{{ route('udhiya.animals.show', $animal) }}" class="w-full block text-center rounded-xl bg-white text-slate-600 font-bold py-2.5 border border-slate-200 hover:bg-slate-50 transition-colors">
                    إلغاء
                </a>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 min-w-0 space-y-6">
        {{-- Physical Properties --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-lg font-black text-slate-800 m-0">⚖️ المواصفات الفيزيائية</h6>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">الوزن (كجم)</label>
                        <input type="number" name="weight" step="0.01" min="0" value="{{ old('weight', $animal->weight) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        @error('weight')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">سعر الكجم (ج.م)</label>
                        <input type="number" name="price_per_kg" step="0.01" min="0" value="{{ old('price_per_kg', $animal->price_per_kg) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        @error('price_per_kg')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">تكلفة الشراء (ج.م)</label>
                    <input type="number" name="cost" step="0.01" min="0" value="{{ old('cost', $animal->cost) }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    @error('cost')<p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Prices by Share Type --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-lg font-black text-slate-800 m-0">💰 الأسعار حسب نوع الحصة</h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">السعر الكامل</label>
                        <input type="number" name="price_full" step="0.01" min="0" value="{{ old('price_full', $animal->price_full) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">سعر السُبع</label>
                        <input type="number" name="price_seven" step="0.01" min="0" value="{{ old('price_seven', $animal->price_seven) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">سعر السُدس</label>
                        <input type="number" name="price_six" step="0.01" min="0" value="{{ old('price_six', $animal->price_six) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">سعر الخُمس</label>
                        <input type="number" name="price_five" step="0.01" min="0" value="{{ old('price_five', $animal->price_five) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">سعر الربع</label>
                        <input type="number" name="price_quarter" step="0.01" min="0" value="{{ old('price_quarter', $animal->price_quarter) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">سعر الثُلث</label>
                        <input type="number" name="price_third" step="0.01" min="0" value="{{ old('price_third', $animal->price_third) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2">سعر النصف</label>
                        <input type="number" name="price_half" step="0.01" min="0" value="{{ old('price_half', $animal->price_half) }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-lg font-black text-slate-800 m-0">📝 ملاحظات</h6>
            </div>
            <div class="p-6">
                <textarea name="notes" rows="4" placeholder="ملاحظات إضافية عن الحيوان..."
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none">{{ old('notes', $animal->notes) }}</textarea>
            </div>
        </div>
    </div>
</form>
@endsection
