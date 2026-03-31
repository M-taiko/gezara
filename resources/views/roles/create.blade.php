@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-purple-600 text-4xl">➕</span> دور جديد
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('admin.roles.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الأدوار</a>
            / إضافة
        </p>
    </div>
    <a href="{{ route('admin.roles.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
        ← العودة
    </a>
</div>
@endsection

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h6 class="text-base font-black text-slate-800 m-0">🔑 بيانات الدور</h6>
        </div>
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">
                        الاسم البرمجي <span class="text-rose-500">*</span>
                        <span class="text-slate-400 font-normal mr-1">(بالإنجليزية، بدون مسافات)</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="مثال: supervisor"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-purple-400 focus:ring-2 focus:ring-purple-100 py-2.5 px-3 text-sm font-mono text-slate-800 transition-colors"
                           dir="ltr">
                    @error('name')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">الاسم للعرض <span class="text-rose-500">*</span></label>
                    <input type="text" name="display_name" value="{{ old('display_name') }}" required
                           placeholder="مثال: مشرف"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-purple-400 focus:ring-2 focus:ring-purple-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    @error('display_name')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1.5">الوصف <span class="text-slate-400 font-normal">(اختياري)</span></label>
                    <textarea name="description" rows="3" placeholder="وصف مختصر لصلاحيات هذا الدور..."
                              class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-purple-400 focus:ring-2 focus:ring-purple-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 text-sm font-black rounded-xl bg-purple-600 text-white hover:bg-purple-700 shadow-md shadow-purple-200/60 transition-all">
                        ✅ إنشاء الدور
                    </button>
                    <a href="{{ route('admin.roles.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
