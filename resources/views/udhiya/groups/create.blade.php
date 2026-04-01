@extends('layouts.master')
@section('title', 'إنشاء مجموعة جديدة')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🐄</span> إنشاء مجموعة ذبح جديدة
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.groups.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المجموعات</a> / إضافة جديدة
        </p>
    </div>
</div>
@endsection

@section('content')
<form action="{{ route('udhiya.groups.store') }}" method="POST">
    @csrf

    <div class="flex flex-col lg:flex-row gap-8 pb-16">

        {{-- ===== LEFT: Main Fields ===== --}}
        <div class="flex-1">
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
                               value="{{ old('name') }}"
                               placeholder="مثال: مجموعة عجل 1"
                               required
                               class="w-full rounded-xl border @error('name') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50 @enderror focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                        @error('name')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Share Type --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            نوع التقسيم <span class="text-rose-500">*</span>
                        </label>
                        <select name="share_type"
                                required
                                class="w-full rounded-xl border @error('share_type') border-rose-400 bg-rose-50 @else border-slate-200 bg-slate-50 @enderror focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                            <option value="">-- اختر نوع التقسيم --</option>
                            @foreach($shareLabels as $val => $label)
                            <option value="{{ $val }}" {{ old('share_type') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('share_type')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Animal --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">
                            الحيوان
                            <span class="text-slate-400 font-normal text-xs">(اختياري — يمكن تحديده لاحقاً)</span>
                        </label>
                        <select name="animal_id" id="animalSelect"
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
                            <option value="{{ $a->id }}" {{ old('animal_id') == $a->id ? 'selected' : '' }}>
                                {{ $aEm }} {{ $a->code }}{{ $aCat ? ' — ' . $aCat : '' }}{{ $aType ? ' / ' . $aType : '' }}{{ $a->status === 'slaughtered' ? ' (مذبوح)' : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('animal_id')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ===== RIGHT: Extra Details + Submit ===== --}}
        <div class="w-full lg:w-80">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
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
                        </label>
                        <input type="date" name="slaughter_day"
                               value="{{ old('slaughter_day') }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner">
                        @error('slaughter_day')
                            <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="3"
                                  placeholder="أي ملاحظات إضافية..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 px-4 text-sm font-semibold text-slate-800 shadow-inner resize-none">{{ old('notes') }}</textarea>
                    </div>

                </div>

                <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/80 flex flex-col gap-3">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-6 py-3.5 text-base font-black rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        إنشاء المجموعة
                    </button>
                    <a href="{{ route('udhiya.groups.index') }}"
                       class="w-full inline-flex justify-center items-center px-6 py-3 text-sm font-bold rounded-xl transition-all bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-slate-200 shadow-sm">
                        إلغاء والعودة
                    </a>
                </div>
            </div>
        </div>

    </div>
</form>
@endsection

@push('js')
<script>
$(function () {
    $('#animalSelect').select2({
        dir: 'rtl',
        placeholder: 'ابحث بالكود أو الفئة أو النوع...',
        allowClear: true,
        width: '100%',
        language: { noResults: function() { return 'لا توجد نتائج'; } },
    });
});
</script>
@endpush
