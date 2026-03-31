@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">👥</span> إدارة المستخدمين
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.dashboard') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الرئيسية</a>
            / المستخدمون
        </p>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200/60 transition-all">
        ＋ مستخدم جديد
    </a>
</div>
@endsection

@section('content')

{{-- Stats --}}
@php
$roleColors = [
    'owner'      => ['bg-indigo-50','text-indigo-700','border-indigo-200','👑'],
    'accountant' => ['bg-emerald-50','text-emerald-700','border-emerald-200','💼'],
    'seller'     => ['bg-amber-50','text-amber-700','border-amber-200','🏪'],
    'admin'      => ['bg-rose-50','text-rose-700','border-rose-200','🛡'],
    'manager'    => ['bg-purple-50','text-purple-700','border-purple-200','⚙️'],
    'user'       => ['bg-slate-50','text-slate-600','border-slate-200','👤'],
];
@endphp

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col items-center gap-1 text-center">
        <span class="text-3xl">👥</span>
        <span class="text-2xl font-black text-slate-700">{{ $users->total() }}</span>
        <span class="text-xs font-bold text-slate-400">إجمالي المستخدمين</span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-emerald-200 p-5 flex flex-col items-center gap-1 text-center">
        <span class="text-3xl">✅</span>
        <span class="text-2xl font-black text-emerald-700">{{ $users->getCollection()->where('status','active')->count() }}</span>
        <span class="text-xs font-bold text-slate-400">نشط</span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-5 flex flex-col items-center gap-1 text-center">
        <span class="text-3xl">⏸</span>
        <span class="text-2xl font-black text-amber-700">{{ $users->getCollection()->where('status','inactive')->count() }}</span>
        <span class="text-xs font-bold text-slate-400">غير نشط</span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-rose-200 p-5 flex flex-col items-center gap-1 text-center">
        <span class="text-3xl">🚫</span>
        <span class="text-2xl font-black text-rose-700">{{ $users->getCollection()->where('status','banned')->count() }}</span>
        <span class="text-xs font-bold text-slate-400">محظور</span>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <h6 class="text-base font-black text-slate-800 m-0">قائمة المستخدمين</h6>
        <span class="text-xs font-bold text-slate-400">{{ $users->total() }} مستخدم</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                    <th class="px-5 py-3">المستخدم</th>
                    <th class="px-5 py-3 hidden sm:table-cell">البريد</th>
                    <th class="px-5 py-3 text-center">الدور</th>
                    <th class="px-5 py-3 text-center">الحالة</th>
                    <th class="px-5 py-3 text-center hidden md:table-cell">تاريخ الإضافة</th>
                    <th class="px-5 py-3 w-28 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($users as $user)
                @php
                    $roleName  = $user->roles->first()?->name ?? 'user';
                    [$rbg,$rtxt,$rborder,$remoji] = $roleColors[$roleName] ?? $roleColors['user'];
                @endphp
                <tr class="hover:bg-slate-50/40 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            @if($user->profile?->avatar)
                                <img src="{{ asset($user->profile->avatar) }}" alt=""
                                     class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-base border border-indigo-200">
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <div class="font-black text-slate-800 text-sm">{{ $user->name }}</div>
                                @if($user->id === Auth::id())
                                <span class="text-xs font-bold text-indigo-500">أنت</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-500 font-semibold hidden sm:table-cell">
                        {{ $user->email }}
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black border {{ $rbg }} {{ $rtxt }} {{ $rborder }}">
                            {{ $remoji }} {{ $user->roles->first()?->display_name ?? 'بدون دور' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($user->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200">✅ نشط</span>
                        @elseif($user->status === 'banned')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-rose-50 text-rose-700 border border-rose-200">🚫 محظور</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-amber-50 text-amber-700 border border-amber-200">⏸ غير نشط</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center text-xs text-slate-400 font-semibold hidden md:table-cell">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                               class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white inline-flex items-center justify-center transition-colors text-sm"
                               title="تعديل">✏️</a>
                            @if($user->id !== Auth::id())
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                  onsubmit="return confirm('حذف {{ addslashes($user->name) }}؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white inline-flex items-center justify-center transition-colors text-sm"
                                        title="حذف">🗑</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="py-16 text-center">
                            <div class="text-5xl mb-4">👥</div>
                            <p class="text-slate-400 font-bold">لا يوجد مستخدمون</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- Roles reference card --}}
<div class="mt-6 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
        <h6 class="text-base font-black text-slate-800 m-0">🔑 الأدوار والصلاحيات</h6>
    </div>
    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @php
        $roleDescriptions = [
            'owner'      => ['👑', 'صاحب المحل',  'صلاحيات كاملة على جميع أقسام النظام', 'bg-indigo-50 border-indigo-100'],
            'accountant' => ['💼', 'محاسب',        'الصكوك والمدفوعات والتقارير المالية', 'bg-emerald-50 border-emerald-100'],
            'seller'     => ['🏪', 'بياع',          'إدارة الصكوك والعملاء والمجموعات', 'bg-amber-50 border-amber-100'],
            'admin'      => ['🛡',  'مدير النظام',  'وصول كامل للنظام وإعدادات المستخدمين', 'bg-rose-50 border-rose-100'],
            'manager'    => ['⚙️', 'مشرف',         'إدارة المستخدمين وعرض التقارير', 'bg-purple-50 border-purple-100'],
        ];
        @endphp
        @foreach($roleDescriptions as $key => [$icon, $title, $desc, $bg])
        <div class="flex items-start gap-3 p-4 rounded-2xl border {{ $bg }}">
            <span class="text-2xl mt-0.5">{{ $icon }}</span>
            <div>
                <p class="font-black text-slate-800 text-sm m-0">{{ $title }}</p>
                <p class="text-xs text-slate-500 font-semibold mt-0.5 m-0">{{ $desc }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection
