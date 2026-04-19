@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">📊</span> دليل الحسابات
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">إدارة الحسابات المحاسبية والفئات ومتابعة الأرصدة الجارية</p>
    </div>
    <div class="flex h-full items-center">
        <a href="{{ route('udhiya.accounts.create') }}"
           class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            إنشاء حساب جديد
        </a>
    </div>
</div>
@endsection

@section('content')

@if($accounts->isNotEmpty())
<div class="grid grid-cols-1 gap-8 mb-8">
    @foreach(['asset' => ['icon' => '🏦', 'title' => 'أصول'], 'liability' => ['icon' => '📉', 'title' => 'خصوم'], 'revenue' => ['icon' => '💰', 'title' => 'إيرادات'], 'expense' => ['icon' => '💸', 'title' => 'مصروفات'], 'equity' => ['icon' => '📈', 'title' => 'حقوق الملكية']] as $type => $config)
        @if(isset($accounts[$type]))
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                    <span class="text-2xl">{{ $config['icon'] }}</span> {{ $config['title'] }}
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wide">
                            <th class="px-5 py-4">#</th>
                            <th class="px-5 py-4">الكود</th>
                            <th class="px-5 py-4">اسم الحساب</th>
                            <th class="px-5 py-4 text-center">الرصيد</th>
                            <th class="px-5 py-4 text-center">حالة</th>
                            <th class="px-5 py-4 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @foreach($accounts[$type] as $account)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-5 py-4 text-slate-400 font-bold text-xs">{{ $loop->iteration }}</td>

                            {{-- Code --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-bold">
                                    {{ $account->code }}
                                </span>
                            </td>

                            {{-- Name --}}
                            <td class="px-5 py-4">
                                <div>
                                    <strong class="text-slate-800 text-sm block">{{ $account->name }}</strong>
                                    @if($account->is_system)
                                    <span class="text-xs text-orange-600 font-semibold">🔒 حساب نظامي</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Balance --}}
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg {{ $account->balance >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }} font-bold text-sm">
                                    {{ number_format($account->balance, 2) }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    نشط
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if(!$account->is_system)
                                    <a href="{{ route('udhiya.accounts.edit', $account) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                       title="تعديل">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('udhiya.accounts.destroy', $account) }}"
                                          class="inline" onsubmit="return confirm('هل تريد حذف هذا الحساب؟');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-slate-400 text-xs font-semibold">نظامي</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach
</div>
@endif

{{-- Deleted Accounts Section --}}
@if($deleted->isNotEmpty())
<div class="bg-white rounded-3xl shadow-sm border border-red-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
    <div class="px-6 py-5 border-b border-red-200 bg-red-50/30">
        <h3 class="text-lg font-black text-red-800 flex items-center gap-2">
            <span class="text-2xl">🗑️</span> الحسابات المحذوفة
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-red-50 border-b border-red-200 text-red-600 text-xs font-bold uppercase tracking-wide">
                    <th class="px-5 py-4">#</th>
                    <th class="px-5 py-4">الكود</th>
                    <th class="px-5 py-4">اسم الحساب</th>
                    <th class="px-5 py-4">الفئة</th>
                    <th class="px-5 py-4 text-center">حالة</th>
                    <th class="px-5 py-4 text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-red-100 text-sm text-red-700">
                @foreach($deleted as $account)
                <tr class="hover:bg-red-50/30 transition-colors group opacity-75">
                    <td class="px-5 py-4 text-red-400 font-bold text-xs">{{ $loop->iteration }}</td>

                    {{-- Code --}}
                    <td class="px-5 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-100 text-red-700 font-mono text-xs font-bold">
                            {{ $account->code }}
                        </span>
                    </td>

                    {{-- Name --}}
                    <td class="px-5 py-4">
                        <strong class="text-red-800 text-sm">{{ $account->name }}</strong>
                    </td>

                    {{-- Type --}}
                    <td class="px-5 py-4 whitespace-nowrap">
                        <span class="text-xs font-semibold">{{ $account->typeLabel() }}</span>
                    </td>

                    {{-- Status --}}
                    <td class="px-5 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold bg-red-100 text-red-700">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            محذوف
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-4 text-center">
                        <form method="POST" action="{{ route('udhiya.accounts.restore', $account) }}"
                              class="inline" onsubmit="return confirm('هل تريد استرجاع هذا الحساب؟');">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors font-bold text-xs">
                                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/>
                                </svg>
                                استرجاع
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="bg-blue-50 border-2 border-blue-200 rounded-3xl p-8 text-center">
    <p class="text-blue-800 font-semibold">لا توجد حسابات محذوفة</p>
</div>
@endif

@endsection
