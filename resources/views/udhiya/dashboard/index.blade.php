@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 mb-16">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-4"><span class="page-title-emoji">📊</span> لوحة التحكم</h1>
        <ol class="breadcrumb">
            <span>برنامج الأضاحي 🐄</span>
        </ol>
    </div>
    <div class="flex h-full items-center">
        <span style="font-size:.82rem;color:var(--text-slate-500);">📅 {{ now()->format('Y/m/d') }}</span>
    </div>
</div>
@endsection

@section('content')

{{-- Stats Row 1 --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 mb-12">
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-purple-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">🐄</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ $stats['animals_total'] }}</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">إجمالي الحيوانات</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-emerald-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">✅</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ $stats['animals_available'] }}</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">متاح للبيع</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-orange-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">📋</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ $stats['contracts_active'] }}</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">صكوك نشطة</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-pink-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">🙋</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ $stats['customers_total'] }}</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">إجمالي العملاء</div>
        </div>
    </div>
</div>

{{-- Stats Row 2 --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 mb-12">
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-blue-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">💰</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ number_format($stats['revenue_total']/1000,1) }}k</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">إجمالي المبيعات ج.م</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-teal-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">💵</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ number_format($stats['collected_total']/1000,1) }}k</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">المحصّل ج.م</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-orange-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">⏳</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ number_format($stats['remaining_total']/1000,1) }}k</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">المتبقي ج.م</div>
        </div>
    </div>
    <div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 border-b-4 border-b-emerald-500 hover:shadow-md transition-all bg-white rounded-2xl shadow-sm border-b-4 h-full p-10 hover:-translate-y-1 transition-transform duration-300">
            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-2xl mb-4 shadow-sm">🏦</div>
            <div class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ number_format($stats['treasury_balance']/1000,1) }}k</div>
            <div class="text-sm font-semibold text-slate-500 uppercase">رصيد الخزينة ج.م</div>
        </div>
    </div>
</div>

{{-- Collection Progress --}}
@if($stats['revenue_total'] > 0)
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
    <div class="p-12 flex-1">
        <div class="flex justify-between items-center mb-4">
            <span style="font-weight:700;font-size:.9rem;">💹 نسبة التحصيل</span>
            <span style="font-weight:700;color:var(--primary);">
                {{ number_format(($stats['collected_total']/$stats['revenue_total'])*100,1) }}%
            </span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
            <div class="bg-indigo-600 h-2.5 rounded-full" " style="width:{{ ($stats['collected_total']/$stats['revenue_total'])*100 }}%"></div>
        </div>
        <div class="flex justify-between mt-4" style="font-size:.78rem;color:var(--text-slate-500);">
            <span>محصّل: {{ number_format($stats['collected_total']) }} ج.م</span>
            <span>إجمالي: {{ number_format($stats['revenue_total']) }} ج.م</span>
        </div>
    </div>
</div>
@endif

{{-- Recent Tables --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 mb-12">
    <div class="col-span-1 lg:col-span-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 text-slate-800 font-bold">
                <span>📋 آخر الصكوك</span>
                <a href="{{ route('udhiya.contracts.index') }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-all bg-indigo-600 text-white hover:bg-indigo-700 ring-indigo-600 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm">عرض الكل</a>
            </div>
            <div class="p-12 flex-1 p-0">
                <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
                    <table class="min-w-full text-end text-sm text-slate-500">
                        <thead class="text-xs text-slate-600 uppercase bg-slate-100/80 font-bold border-b border-slate-200"><tr><th class="px-6 py-4 font-bold tracking-wider">رقم الصك</th><th class="px-6 py-4 font-bold tracking-wider">العميل</th><th class="px-6 py-4 font-bold tracking-wider">المبلغ</th><th class="px-6 py-4 font-bold tracking-wider">الحالة</th></tr></thead>
                        <tbody>
                            @forelse($recentContracts as $c)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><strong style="color:var(--primary);">{{ $c->contract_number }}</strong></td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $c->customer->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($c->total_amount) }} <small class="text-slate-500">ج.م</small></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($c->status==='active') <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 ring-1 ring-inset ring-orange-600/20">🟡 نشط</span>
                                    @elseif($c->status==='completed') <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">✅ مكتمل</span>
                                    @else <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-600/20">{{ $c->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors"><td colspan="10" class="text-center py-12"><div class="flex flex-col items-center justify-center text-slate-400"><span class="text-4xl mb-3">📭</span><p class="text-lg font-medium">لا توجد بيانات</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-span-1 lg:col-span-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 text-slate-800 font-bold">
                <span>🛒 آخر المشتريات</span>
                <a href="{{ route('udhiya.purchases.index') }}" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg transition-all bg-indigo-600 text-white hover:bg-indigo-700 ring-indigo-600 focus:ring-offset-2 focus:ring-2 border border-transparent shadow-sm">عرض الكل</a>
            </div>
            <div class="p-12 flex-1 p-0">
                <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
                    <table class="min-w-full text-end text-sm text-slate-500">
                        <thead class="text-xs text-slate-600 uppercase bg-slate-100/80 font-bold border-b border-slate-200"><tr><th class="px-6 py-4 font-bold tracking-wider">المورد</th><th class="px-6 py-4 font-bold tracking-wider">التاريخ</th><th class="px-6 py-4 font-bold tracking-wider">الإجمالي</th><th class="px-6 py-4 font-bold tracking-wider">الحالة</th></tr></thead>
                        <tbody>
                            @forelse($recentPurchases as $p)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><strong>{{ $p->supplier->name }}</strong></td>
                                <td style="font-size:.82rem;color:var(--text-slate-500);">{{ $p->date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($p->total) }} <small class="text-slate-500">ج.م</small></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($p->status==='confirmed') <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/20">✅ مؤكدة</span>
                                    @elseif($p->status==='pending') <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-orange-100 text-orange-800 ring-1 ring-inset ring-orange-600/20">⏳ معلقة</span>
                                    @else <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-slate-100 text-slate-800 ring-1 ring-inset ring-slate-600/20">{{ $p->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors"><td colspan="10" class="text-center py-12"><div class="flex flex-col items-center justify-center text-slate-400"><span class="text-4xl mb-3">📭</span><p class="text-lg font-medium">لا توجد بيانات</p></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
