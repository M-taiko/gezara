@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-amber-500 text-4xl">✏️</span> تعديل دور
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('admin.roles.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الأدوار</a>
            / {{ $role->display_name }}
        </p>
    </div>
    <a href="{{ route('admin.roles.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
        ← العودة
    </a>
</div>
@endsection

@section('content')
@php
$protected = ['admin', 'manager', 'user', 'owner', 'accountant', 'seller'];
$isProtected = in_array($role->name, $protected);
@endphp

<div class="flex flex-col lg:flex-row gap-6 max-w-3xl">

    {{-- Form --}}
    <div class="flex-1">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-base font-black text-slate-800 m-0">🔑 بيانات الدور</h6>
                @if($isProtected)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black bg-slate-100 text-slate-500">🔒 دور محمي</span>
                @endif
            </div>
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-5">

                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-1.5">
                            الاسم البرمجي <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" required
                               @if($isProtected) readonly @endif
                               class="w-full rounded-xl border border-slate-200 py-2.5 px-3 text-sm font-mono text-slate-800 transition-colors
                                      {{ $isProtected ? 'bg-slate-100 cursor-not-allowed' : 'bg-slate-50 focus:bg-white focus:border-purple-400 focus:ring-2 focus:ring-purple-100' }}"
                               dir="ltr">
                        @error('name')
                        <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-1.5">الاسم للعرض <span class="text-rose-500">*</span></label>
                        <input type="text" name="display_name" value="{{ old('display_name', $role->display_name) }}" required
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-purple-400 focus:ring-2 focus:ring-purple-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        @error('display_name')
                        <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-600 mb-1.5">الوصف <span class="text-slate-400 font-normal">(اختياري)</span></label>
                        <textarea name="description" rows="3"
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-purple-400 focus:ring-2 focus:ring-purple-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                        <p class="text-rose-500 text-xs font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 text-sm font-black rounded-xl bg-amber-500 text-white hover:bg-amber-600 shadow-md shadow-amber-200/60 transition-all">
                            💾 حفظ التعديلات
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

    {{-- Users with this role --}}
    @if($role->users->count() > 0)
    <div class="w-full lg:w-64 flex-shrink-0">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">👥 المستخدمون</h6>
            </div>
            <div class="p-4 space-y-2">
                @foreach($role->users as $user)
                <a href="{{ route('admin.users.edit', $user->id) }}"
                   class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
                    @if($user->profile?->avatar)
                        <img src="{{ asset($user->profile->avatar) }}" alt=""
                             class="w-8 h-8 rounded-lg object-cover border border-slate-200">
                    @else
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xs border border-indigo-200">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-slate-800 truncate group-hover:text-indigo-600">{{ $user->name }}</div>
                        <div class="text-xs text-slate-400 truncate">{{ $user->email }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
