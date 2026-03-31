@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-slate-600 text-4xl">📋</span> سجل النشاط
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('admin.users.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الإدارة</a>
            / سجل النشاط
        </p>
    </div>
    <span class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 border border-slate-200">
        {{ $logs->total() }} سجل
    </span>
</div>
@endsection

@section('content')

{{-- Filters --}}
<form method="GET" action="{{ route('admin.activity-logs.index') }}"
      class="flex flex-wrap gap-3 mb-6">

    <select name="user_id" onchange="this.form.submit()"
            class="rounded-xl border border-slate-200 bg-white focus:border-indigo-400 py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm">
        <option value="">كل المستخدمين</option>
        @foreach($users as $user)
        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
            {{ $user->name }}
        </option>
        @endforeach
    </select>

    <select name="action" onchange="this.form.submit()"
            class="rounded-xl border border-slate-200 bg-white focus:border-indigo-400 py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm">
        <option value="">كل الإجراءات</option>
        <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>➕ إنشاء</option>
        <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>✏️ تعديل</option>
        <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>🗑 حذف</option>
        <option value="viewed"  {{ request('action') === 'viewed'  ? 'selected' : '' }}>👁 عرض</option>
    </select>

    <input type="date" name="start_date" value="{{ request('start_date') }}" onchange="this.form.submit()"
           class="rounded-xl border border-slate-200 bg-white py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm">
    <input type="date" name="end_date" value="{{ request('end_date') }}" onchange="this.form.submit()"
           class="rounded-xl border border-slate-200 bg-white py-2.5 px-3 text-sm font-semibold text-slate-800 shadow-sm">

    @if(request()->hasAny(['user_id','action','start_date','end_date']))
    <a href="{{ route('admin.activity-logs.index') }}"
       class="inline-flex items-center px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
        ✕ مسح
    </a>
    @endif
</form>

{{-- Table --}}
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                    <th class="px-5 py-3">المستخدم</th>
                    <th class="px-5 py-3 text-center">الإجراء</th>
                    <th class="px-5 py-3">الوصف</th>
                    <th class="px-5 py-3 hidden sm:table-cell">النوع</th>
                    <th class="px-5 py-3 hidden lg:table-cell text-left">IP</th>
                    <th class="px-5 py-3 hidden md:table-cell">التاريخ</th>
                    <th class="px-5 py-3 w-12"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($logs as $log)
                @php
                $actionCfg = [
                    'created' => ['bg-emerald-50 text-emerald-700 border-emerald-100', '➕', 'إنشاء'],
                    'updated' => ['bg-amber-50 text-amber-700 border-amber-100',       '✏️', 'تعديل'],
                    'deleted' => ['bg-rose-50 text-rose-700 border-rose-100',          '🗑', 'حذف'],
                    'viewed'  => ['bg-slate-50 text-slate-600 border-slate-100',       '👁', 'عرض'],
                ];
                [$aCls, $aEmoji, $aLbl] = $actionCfg[$log->action] ?? ['bg-slate-50 text-slate-600 border-slate-200', '•', $log->action];
                @endphp
                <tr class="hover:bg-slate-50/40 transition-colors">
                    <td class="px-5 py-3">
                        @if($log->user)
                        <a href="{{ route('admin.activity-logs.user', $log->user->id) }}"
                           class="flex items-center gap-2 group">
                            <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-black border border-indigo-200 flex-shrink-0">
                                {{ mb_substr($log->user->name, 0, 1) }}
                            </div>
                            <span class="text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">{{ $log->user->name }}</span>
                        </a>
                        @else
                        <span class="text-xs text-slate-400 font-semibold italic">محذوف</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black border {{ $aCls }}">
                            {{ $aEmoji }} {{ $aLbl }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm text-slate-700 max-w-xs">
                        <span class="line-clamp-2">{{ $log->description }}</span>
                    </td>
                    <td class="px-5 py-3 hidden sm:table-cell">
                        <code class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-lg">{{ class_basename($log->model_type) }}</code>
                    </td>
                    <td class="px-5 py-3 hidden lg:table-cell text-left">
                        <span class="text-xs font-mono text-slate-400">{{ $log->ip_address }}</span>
                    </td>
                    <td class="px-5 py-3 hidden md:table-cell">
                        <div class="text-xs font-semibold text-slate-600">{{ $log->created_at->format('d/m/Y') }}</div>
                        <div class="text-xs text-slate-400">{{ $log->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <a href="{{ route('admin.activity-logs.show', $log->id) }}"
                           class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 hover:bg-indigo-600 hover:text-white inline-flex items-center justify-center transition-colors text-sm border border-slate-200"
                           title="تفاصيل">👁</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="py-16 text-center">
                            <div class="text-5xl mb-4">📋</div>
                            <p class="text-slate-400 font-bold">لا توجد سجلات نشاط</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection
