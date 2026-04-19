@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">💰</span> إدارة السلف
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.dashboard') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">لوحة التحكم</a>
            / السلف
        </p>
    </div>
    <a href="{{ route('udhiya.advances.create') }}"
       class="inline-flex items-center gap-2 px-6 py-3 text-base font-black rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200/60 transition-all transform hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة سلفة جديدة
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Filters --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
        <form method="GET" action="{{ route('udhiya.advances.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">بحث</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="رقم السلفة أو الاسم"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">النوع</label>
                    <select name="type" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">-- الكل --</option>
                        <option value="customer" {{ $type === 'customer' ? 'selected' : '' }}>سلف عملاء</option>
                        <option value="supplier" {{ $type === 'supplier' ? 'selected' : '' }}>سلف موردين</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">-- الكل --</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="settled" {{ $status === 'settled' ? 'selected' : '' }}>مغلقة</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-xl bg-indigo-600 text-white font-black py-2.5 px-3 hover:bg-indigo-700 transition-colors">
                        🔍 بحث
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                        <th class="px-6 py-4">رقم السلفة</th>
                        <th class="px-6 py-4">النوع</th>
                        <th class="px-6 py-4">الاسم</th>
                        <th class="px-6 py-4">المبلغ الأصلي</th>
                        <th class="px-6 py-4">المتبقي</th>
                        <th class="px-6 py-4">الحالة</th>
                        <th class="px-6 py-4">التاريخ</th>
                        <th class="px-6 py-4 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($advances as $advance)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-black text-indigo-600">{{ $advance->advance_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black
                                {{ $advance->type === 'customer' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $advance->getTypeLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-700">{{ $advance->getName() }}</td>
                        <td class="px-6 py-4 font-black text-slate-800">{{ number_format($advance->amount, 2) }} ج.م</td>
                        <td class="px-6 py-4 font-black {{ $advance->remaining > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ number_format($advance->remaining, 2) }} ج.م
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black
                                {{ $advance->status === 'active' ? 'bg-amber-100 text-amber-700' : ($advance->status === 'settled' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700') }}">
                                {{ $advance->getStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $advance->date->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('udhiya.advances.show', $advance) }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white transition-colors">
                                👁️
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-4xl mb-3">💤</div>
                            <p class="text-slate-400 font-semibold">لا توجد سلف</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $advances->links() }}
    </div>
</div>
@endsection
