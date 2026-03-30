@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">📊</span> مركز التقارير والإحصائيات
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">عرض الأداء العام والتقارير المالية والتشغيلية المتقدمة</p>
    </div>
</div>
@endsection

@section('content')

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
    {{-- Animal Report --}}
    <a href="{{ route('udhiya.reports.animals') }}" class="group relative bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 block focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
        <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-emerald-400 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="p-10 flex flex-col items-center text-center">
            <div class="w-24 h-24 mb-6 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-5xl shadow-inner border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white group-hover:shadow-indigo-200 transition-colors">
                🐄
            </div>
            <h5 class="text-xl font-black text-slate-800 mb-2">جرد المخزون والحيوانات</h5>
            <p class="text-sm font-medium text-slate-500">متابعة نسب البيع، المتلقي، المنصرف والمحجوز بشكل تفصيلي يوماً بيوم.</p>
        </div>
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 text-sm font-bold text-indigo-600 flex justify-center items-center gap-2 group-hover:bg-indigo-50 transition-colors">
            استعراض التقرير <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </div>
    </a>

    {{-- Profit Report --}}
    <a href="{{ route('udhiya.reports.profit') }}" class="group relative bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 block focus:outline-none focus:ring-4 focus:ring-amber-500/20">
        <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-amber-400 to-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="p-10 flex flex-col items-center text-center">
            <div class="w-24 h-24 mb-6 rounded-3xl bg-amber-50 text-amber-600 flex items-center justify-center text-5xl shadow-inner border border-amber-100 group-hover:bg-amber-500 group-hover:text-white group-hover:shadow-amber-200 transition-colors">
                📈
            </div>
            <h5 class="text-xl font-black text-slate-800 mb-2">الماليات وحساب الأرباح</h5>
            <p class="text-sm font-medium text-slate-500">حصر إجمالي الواردات من الصكوك، مطروحة من تكلفة الحيوانات لحساب الربح الصافي.</p>
        </div>
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 text-sm font-bold text-amber-600 flex justify-center items-center gap-2 group-hover:bg-amber-50 transition-colors">
            استعراض التقرير <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </div>
    </a>

    {{-- Slaughter Schedule --}}
    <a href="{{ route('udhiya.reports.slaughter') }}" class="group relative bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 block focus:outline-none focus:ring-4 focus:ring-rose-500/20">
        <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-rose-400 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="p-10 flex flex-col items-center text-center">
            <div class="w-24 h-24 mb-6 rounded-3xl bg-rose-50 text-rose-600 flex items-center justify-center text-5xl shadow-inner border border-rose-100 group-hover:bg-rose-500 group-hover:text-white group-hover:shadow-rose-200 transition-colors">
                🧾
            </div>
            <h5 class="text-xl font-black text-slate-800 mb-2">جدول الذبح والتسليم</h5>
            <p class="text-sm font-medium text-slate-500">كشوفات تنفيذية لأيام العيد مرتبة حسب مواعيد الذبح وطلبات العملاء لتسهيل عمل المجزر.</p>
        </div>
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 text-sm font-bold text-rose-600 flex justify-center items-center gap-2 group-hover:bg-rose-50 transition-colors">
            استعراض التقرير <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </div>
    </a>
</div>

@endsection
