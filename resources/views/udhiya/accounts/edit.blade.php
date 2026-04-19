@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">✏️</span> تعديل الحساب
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">تحديث بيانات الحساب المحاسبي</p>
    </div>
    <a href="{{ route('udhiya.accounts.index') }}"
       class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all bg-slate-200 text-slate-700 hover:bg-slate-300">
        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
        </svg>
        العودة
    </a>
</div>
@endsection

@section('content')

<div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 lg:col-span-8 lg:col-start-3">
        {{-- Error Alert --}}
        @if($account->is_system)
        <div class="bg-orange-50 border-2 border-orange-200 rounded-3xl p-6 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-orange-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-black text-orange-800">حساب نظامي</h3>
                    <p class="text-orange-700 text-xs font-semibold mt-1">هذا حساب نظامي ولا يمكن تعديل اسمه أو نوعه</p>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-8">
                <form method="POST" action="{{ route('udhiya.accounts.update', $account) }}" class="space-y-6">
                    @csrf @method('PUT')

                    {{-- Code (Read-only) --}}
                    <div>
                        <label class="block text-sm font-black text-slate-800 mb-2">الكود</label>
                        <input type="text" value="{{ $account->code }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 font-semibold transition-colors cursor-not-allowed"
                               disabled>
                        <p class="text-slate-500 text-xs font-semibold mt-1">الكود لا يمكن تعديله بعد الإنشاء</p>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-black text-slate-800 mb-2">اسم الحساب</label>
                        <input type="text" name="name" value="{{ old('name', $account->name) }}"
                               placeholder="مثال: الخزينة"
                               class="w-full px-4 py-3 rounded-xl border @error('name') border-red-500 @else border-slate-200 @enderror focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 text-slate-800 font-semibold transition-colors"
                               required>
                        @error('name')
                        <p class="text-red-600 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-black text-slate-800 mb-2">نوع الحساب</label>
                        <select name="type"
                                class="w-full px-4 py-3 rounded-xl border @error('type') border-red-500 @else border-slate-200 @enderror focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 text-slate-800 font-semibold transition-colors bg-white"
                                required>
                            @foreach($types as $key => $label)
                            <option value="{{ $key }}" @selected(old('type', $account->type) == $key)>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('type')
                        <p class="text-red-600 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Current Balance Info --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
                        <p class="text-xs font-black text-blue-800 mb-1">الرصيد الحالي</p>
                        <p class="text-2xl font-black text-blue-600">
                            {{ number_format($account->balance, 2) }}
                        </p>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-between gap-4 pt-6 border-t border-slate-100">
                        <a href="{{ route('udhiya.accounts.index') }}"
                           class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all bg-slate-100 text-slate-700 hover:bg-slate-200">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            إلغاء
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
