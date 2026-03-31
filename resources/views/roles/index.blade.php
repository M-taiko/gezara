@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-purple-600 text-4xl">🔑</span> إدارة الأدوار والصلاحيات
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('admin.users.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المستخدمون</a>
            / الأدوار
        </p>
    </div>
    <a href="{{ route('admin.roles.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl bg-purple-600 text-white hover:bg-purple-700 shadow-md shadow-purple-200/60 transition-all">
        ＋ دور جديد
    </a>
</div>
@endsection

@section('content')

{{-- Stats --}}
@php
$roleIcons = ['owner'=>'👑','accountant'=>'💼','seller'=>'🏪','admin'=>'🛡','manager'=>'⚙️','user'=>'👤'];
$roleColors = [
    'owner'      => 'border-indigo-200 text-indigo-700',
    'accountant' => 'border-emerald-200 text-emerald-700',
    'seller'     => 'border-amber-200 text-amber-700',
    'admin'      => 'border-rose-200 text-rose-700',
    'manager'    => 'border-purple-200 text-purple-700',
    'user'       => 'border-slate-200 text-slate-600',
];
$protected = ['admin', 'manager', 'user', 'owner', 'accountant', 'seller'];
@endphp

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
    @foreach($roles as $role)
    <div class="bg-white rounded-2xl shadow-sm border {{ $roleColors[$role->name] ?? 'border-slate-200 text-slate-600' }} p-5 flex flex-col gap-2">
        <div class="flex items-center justify-between">
            <span class="text-2xl">{{ $roleIcons[$role->name] ?? '🔑' }}</span>
            <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">{{ $role->users_count }} مستخدم</span>
        </div>
        <div class="font-black text-slate-800 text-sm mt-1">{{ $role->display_name }}</div>
        <div class="text-xs text-slate-400 font-mono">{{ $role->name }}</div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <h6 class="text-base font-black text-slate-800 m-0">قائمة الأدوار</h6>
        <span class="text-xs font-bold text-slate-400">{{ $roles->total() }} دور</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                    <th class="px-5 py-3">الدور</th>
                    <th class="px-5 py-3">الاسم البرمجي</th>
                    <th class="px-5 py-3 hidden md:table-cell">الوصف</th>
                    <th class="px-5 py-3 text-center">المستخدمون</th>
                    <th class="px-5 py-3 hidden sm:table-cell">تاريخ الإنشاء</th>
                    <th class="px-5 py-3 w-24 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($roles as $role)
                <tr class="hover:bg-slate-50/40 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-xl">{{ $roleIcons[$role->name] ?? '🔑' }}</span>
                            <span class="font-black text-slate-800 text-sm">{{ $role->display_name }}</span>
                            @if(in_array($role->name, $protected))
                            <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-slate-100 text-slate-400">محمي</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <code class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-1 rounded-lg">{{ $role->name }}</code>
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-500 font-semibold hidden md:table-cell">
                        {{ $role->description ?? '—' }}
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-sm font-black
                                     {{ $role->users_count > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-400' }}">
                            {{ $role->users_count }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-400 font-semibold hidden sm:table-cell">
                        {{ $role->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.roles.edit', $role->id) }}"
                               class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white inline-flex items-center justify-center transition-colors text-sm"
                               title="تعديل">✏️</a>
                            @if(!in_array($role->name, $protected) && $role->users_count == 0)
                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                  onsubmit="return confirm('حذف دور {{ addslashes($role->display_name) }}؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white inline-flex items-center justify-center transition-colors text-sm"
                                        title="حذف">🗑</button>
                            </form>
                            @else
                            <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-300 inline-flex items-center justify-center text-sm cursor-not-allowed"
                                  title="{{ in_array($role->name, $protected) ? 'دور محمي' : 'يوجد مستخدمون' }}">🔒</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="py-16 text-center">
                            <div class="text-5xl mb-4">🔑</div>
                            <p class="text-slate-400 font-bold">لا توجد أدوار</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($roles->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $roles->links() }}
    </div>
    @endif
</div>

@endsection
