@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-8 mb-16">
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-4">تقرير الأرباح</h1>
        <div class="mt-2 flex items-center text-sm text-slate-500"><li class="breadcrumb-item"><a href="{{ route('udhiya.reports.index') }}">التقارير</a></li><span>الأرباح</span></div>
    </div>
</div>
@endsection
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-12">
    <div><div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300"><div class="p-12 flex-1 text-center"><h4>{{ number_format($totalRevenue, 2) }}</h4><small class="text-slate-500">الإيرادات (ج.م)</small></div></div></div>
    <div><div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300"><div class="p-12 flex-1 text-center"><h4>{{ number_format($totalCost, 2) }}</h4><small class="text-slate-500">التكاليف (ج.م)</small></div></div></div>
    <div><div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300"><div class="p-12 flex-1 text-center"><h4 class="{{ $totalProfit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ number_format($totalProfit, 2) }}</h4><small class="text-slate-500">الربح الصافي (ج.م)</small></div></div></div>
    <div><div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300"><div class="p-12 flex-1 text-center"><h4 class="text-info">{{ number_format($totalCollected, 2) }}</h4><small class="text-slate-500">المحصّل (ج.م)</small></div></div></div>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col h-full hover:shadow-md transition-shadow duration-300">
    <div class="p-12 flex-1 p-0">
        <div class="overflow-x-auto ring-1 ring-slate-100 sm:rounded-lg">
            <table class="min-w-full text-end text-sm text-slate-500">
                <thead class="thead-light"><tr><th class="px-6 py-4 font-bold tracking-wider">رقم الصك</th><th class="px-6 py-4 font-bold tracking-wider">العميل</th><th class="px-6 py-4 font-bold tracking-wider">الإيراد</th><th class="px-6 py-4 font-bold tracking-wider">التكلفة</th><th class="px-6 py-4 font-bold tracking-wider">الربح</th><th class="px-6 py-4 font-bold tracking-wider">المحصّل</th><th class="px-6 py-4 font-bold tracking-wider">المتبقي</th></tr></thead>
                <tbody>
                    @foreach($contracts as $c)
                    @php $cost = $c->items->sum(fn($i) => $i->animal->cost); $profit = $c->total_amount - $cost; @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><a href="{{ route('udhiya.contracts.show', $c) }}">{{ $c->contract_number }}</a></td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $c->customer->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($c->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($cost, 2) }}</td>
                        <td class="{{ $profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}"><strong>{{ number_format($profit, 2) }}</strong></td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($c->paid_amount, 2) }}</td>
                        <td class="{{ $c->remaining_amount > 0 ? 'text-rose-600' : '' }}">{{ number_format($c->remaining_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
