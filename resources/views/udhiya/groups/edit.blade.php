@extends('layouts.master')
@section('title', 'تعديل مجموعة ذبح')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">✏️</span> تعديل مجموعة ذبح
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.groups.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المجموعات</a> /
            <a href="{{ route('udhiya.groups.show', $group) }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">{{ $group->name }}</a> /
            تعديل
        </p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('udhiya.groups.update', $group) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="space-y-8 pb-16">

        {{-- ===== SECTION 1: Main Fields ===== --}}
        <div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm">1</div>
                    <h6 class="text-lg font-black text-slate-800 m-0">بيانات المجموعة</h6>
                </div>

                <div class="p-8 flex flex-col gap-6">

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            اسم المجموعة <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name"
                               value="{{ old('name', $group->name) }}"
                               placeholder="مثال: مجموعة عجل 1"
                               required
                               class="w-full rounded-xl border @error('name') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50 @enderror focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                        @error('name')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Share Type (Read-Only) --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            نوع التقسيم
                            <span class="text-slate-400 font-normal text-xs">(لا يمكن تغييره بعد الإنشاء)</span>
                        </label>
                        <div class="w-full rounded-xl border border-slate-300 bg-slate-100 py-3 px-4 text-sm font-semibold text-slate-700 shadow-inner">
                            {{ $group->shareLabel() }}
                        </div>
                    </div>

                    {{-- Animal --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            الحيوان
                            <span class="text-slate-400 font-normal text-xs">(اختياري)</span>
                            @if($group->isSlaughtered())
                                <span class="block text-amber-600 text-xs font-bold mt-1">⚠️ لا يمكن تغيير الحيوان بعد الذبح</span>
                            @endif
                        </label>
                        <select name="animal_id" id="animalSelect"
                                @if($group->isSlaughtered()) disabled @endif
                                class="w-full @error('animal_id') border-rose-400 @enderror">
                            <option value="">— بدون حيوان —</option>
                            @foreach($animals as $a)
                            @php
                                $aCat  = $a->product?->mainCategory?->name ?? '';
                                $aType = $a->product?->name ?? '';
                                $aEm   = match($a->product?->mainCategory?->code ?? '') {
                                    'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾'
                                };
                            @endphp
                            <option value="{{ $a->id }}" {{ $group->animal_id == $a->id ? 'selected' : '' }}>
                                {{ $aEm }} {{ $a->code }}{{ $aCat ? ' — ' . $aCat : '' }}{{ $aType ? ' / ' . $aType : '' }}{{ $a->status === 'slaughtered' ? ' (مذبوح)' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('animal_id')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Animal Type Label --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            نوع الذبيحة <span class="text-slate-400 font-normal text-xs">(اختياري)</span>
                        </label>
                        <select name="animal_type_label"
                                class="w-full rounded-xl border @error('animal_type_label') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50 @enderror focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                            <option value="">— اختر نوع الذبيحة —</option>
                            @if(isset($products))
                                @foreach($products as $product)
                                <option value="{{ $product->name }}" {{ old('animal_type_label', $group->animal_type_label) === $product->name ? 'selected' : '' }}>
                                    {{ $product->mainCategory?->name ?? '' }}{{ $product->mainCategory?->name ? ' / ' : '' }}{{ $product->name }}
                                </option>
                                @endforeach
                            @endif
                        </select>
                        @error('animal_type_label')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ===== SECTION 2: Members Management ===== --}}
        @if($group->members->count() > 0)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">👥</div>
                <h6 class="text-lg font-black text-slate-800 m-0">أعضاء المجموعة</h6>
                <span class="text-sm font-bold bg-blue-100 text-blue-700 px-3 py-1 rounded-full mr-auto">{{ $group->members->count() }} عضو</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">العميل</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">نوع النصيب</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">عدد الأنصبة</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">سعر النصيب</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">الإجمالي</th>
                            <th class="px-6 py-3 text-xs font-bold text-slate-600 uppercase">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($group->members as $member)
                        @php
                            $contract = $member->contractItem?->contract;
                            $payment = $contract?->paid_amount ?? 0;
                            $total = $contract?->total_amount ?? 0;
                            $isPaid = $total > 0 && $payment >= $total;
                        @endphp
                        <tr class="hover:bg-slate-50/40">
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $member->customer?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                @php
                                    $shareLabels = ['full' => 'كامل', 'seven' => 'سُبع', 'six' => 'سُدس', 'five' => 'خُمس', 'quarter' => 'ربع', 'third' => 'ثُلث', 'half' => 'نصف'];
                                @endphp
                                {{ $shareLabels[$member->share_type] ?? $member->share_type }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $member->shares_count ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ number_format($member->unit_price ?? 0, 2) }}</td>
                            <td class="px-6 py-4 font-bold text-indigo-600">{{ number_format($member->total_price ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($isPaid)
                                    <span class="inline-block bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full text-xs font-bold">✅ مدفوع</span>
                                @else
                                    <span class="inline-block bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full text-xs font-bold">⏳ متبقي</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">إجمالي الأنصبة</p>
                        <p class="text-lg font-black text-slate-800">{{ $group->members->sum('shares_count') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">إجمالي الأسعار</p>
                        <p class="text-lg font-black text-slate-800">{{ number_format($group->members->sum('unit_price'), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">الإجمالي</p>
                        <p class="text-lg font-black text-indigo-600">{{ number_format($group->members->sum('total_price'), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-600 mb-1">عدد الأعضاء</p>
                        <p class="text-lg font-black text-slate-800">{{ $group->members->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ===== SECTION 3: Extra Details + Submit ===== --}}
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="flex-1">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">2</div>
                    <h6 class="text-lg font-black text-slate-800 m-0">تفاصيل إضافية</h6>
                </div>

                <div class="p-8 flex flex-col gap-6">

                    {{-- Slaughter Day --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            يوم الذبح
                            <span class="text-slate-400 font-normal text-xs">(اختياري)</span>
                            @if($group->isSlaughtered())
                                <span class="block text-amber-600 text-xs font-bold mt-1">⚠️ لا يمكن تغيير التاريخ بعد الذبح</span>
                            @endif
                        </label>
                        <input type="date" name="slaughter_day"
                               value="{{ old('slaughter_day', $group->slaughter_day?->format('Y-m-d')) }}"
                               @if($group->isSlaughtered()) disabled @endif
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner @if($group->isSlaughtered()) opacity-60 @endif">
                        @error('slaughter_day')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="3"
                                  placeholder="أي ملاحظات إضافية..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner resize-none">{{ old('notes', $group->notes) }}</textarea>
                    </div>

                </div>

                <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/80 flex flex-col gap-3">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-6 py-3.5 text-base font-black rounded-xl transition-all bg-emerald-600 text-white hover:bg-emerald-700 shadow-lg shadow-emerald-200 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                        💾 حفظ التعديلات
                    </button>
                    <a href="{{ route('udhiya.groups.show', $group) }}"
                       class="w-full inline-flex justify-center items-center px-6 py-3 text-sm font-bold rounded-xl transition-all bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-slate-200 shadow-sm">
                        إلغاء والعودة
                    </a>
                </div>
                </div>
            </div>

            {{-- Sidebar Submit --}}
            <div class="w-full lg:w-80">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
                    <div class="px-8 py-5 border-b border-slate-100 bg-emerald-50/50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">✓</div>
                        <h6 class="text-lg font-black text-slate-800 m-0">حفظ التغييرات</h6>
                    </div>

                    <div class="p-8 flex flex-col gap-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-xs font-bold text-blue-700 mb-1">⚠️ ملاحظة مهمة</p>
                            <p class="text-xs text-blue-600">يمكنك تعديل بيانات المجموعة طالما لم يتم الذبح بعد</p>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-6 py-3.5 text-base font-black rounded-xl transition-all bg-emerald-600 text-white hover:bg-emerald-700 shadow-lg shadow-emerald-200 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            💾 حفظ التعديلات
                        </button>
                        <a href="{{ route('udhiya.groups.show', $group) }}"
                           class="w-full inline-flex justify-center items-center px-6 py-3 text-sm font-bold rounded-xl transition-all bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-slate-200 shadow-sm">
                            إلغاء والعودة
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>
@endsection

@push('js')
<script>
$(function () {
    @if(!$group->isSlaughtered())
    $('#animalSelect').select2({
        dir: 'rtl',
        placeholder: 'ابحث بالكود أو الفئة أو النوع...',
        allowClear: true,
        width: '100%',
        language: { noResults: function() { return 'لا توجد نتائج'; } },
    });
    @endif
});
</script>
@endpush
