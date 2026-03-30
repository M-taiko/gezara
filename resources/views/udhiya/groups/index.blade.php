@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">👥</span> عُصب ومجموعات الذبح
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">تتبع التشارك في الرؤوس ومتابعة اكتمال الحصص</p>
    </div>
    <div class="flex h-full items-center">
        <a href="{{ route('udhiya.groups.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            تكوين مجموعة مقفولة
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- Search --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8 overflow-hidden p-6 max-w-3xl">
    <form method="GET" action="{{ route('udhiya.groups.index') }}" class="flex gap-4">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-3 pr-12 text-sm font-semibold text-slate-700"
                   placeholder="بحث بالرمز أو باسم أحد الشركاء..."
                   value="{{ $search }}">
        </div>
        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
            البحث
        </button>
        @if($search)
        <a href="{{ route('udhiya.groups.index') }}" class="inline-flex items-center justify-center px-4 py-3 text-sm font-bold rounded-xl transition-all bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-100" title="إلغاء التصفية">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </a>
        @endif
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
    @forelse($groups as $group)
    @php
        $used      = $group->usedSlots();
        $total     = $group->totalSlots();
        $remaining = $group->remainingSlots();
        $pct       = $total > 0 ? round(($used / $total) * 100) : 0;
        $cat       = $group->animal?->product?->mainCategory;
        $emoji     = match($cat?->code) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
    @endphp
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center gap-2">
            <h6 class="font-black text-slate-800 text-lg flex items-center gap-2 truncate text-right">
                <span class="text-2xl">{{ $emoji }}</span> {{ $group->name }}
            </h6>
            @if($remaining > 0)
                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    متاح {{ $remaining }}
                </span>
            @else
                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    مكتمل
                </span>
            @endif
        </div>
        
        <div class="p-6 flex-1 flex flex-col">
            <div class="flex justify-between items-center mb-4 text-slate-500 font-bold text-sm bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="flex items-center gap-1 text-indigo-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    {{ $group->animal?->code ?? 'غير مرتبط' }}
                </span>
                <span>{{ $cat?->name ?? 'مجموعة فارغة' }}</span>
            </div>
            
            <div class="flex justify-between items-center mb-6 text-slate-600 font-medium text-xs">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                    {{ $group->shareLabel() }}
                </span>
                @if($group->slaughter_day)
                <span class="flex items-center gap-1 text-amber-600 bg-amber-50 px-2 py-1 rounded-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ $group->slaughter_day->format('Y/m/d') }}
                </span>
                @endif
            </div>

            {{-- Progress --}}
            <div class="mb-6 mb-auto">
                <div class="flex justify-between mb-2 text-xs font-bold">
                    <span class="text-slate-500">حالة الاكتمال</span>
                    <span class="text-slate-800">{{ $used }} / {{ $total }} حجز</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 mb-1 overflow-hidden ring-1 ring-inset ring-slate-200">
                    <div class="h-2.5 rounded-full transition-all duration-500 {{ $remaining === 0 ? 'bg-emerald-500' : ($pct > 50 ? 'bg-amber-500' : 'bg-indigo-500') }}"
                         style="width:{{ $pct }}%"></div>
                </div>
            </div>

            {{-- Members preview --}}
            <div class="pt-4 border-t border-slate-100 mt-2">
                @if($group->members->count())
                <h6 class="text-xs font-bold text-slate-400 mb-3">المشتركون (العُصبة):</h6>
                <div class="space-y-2">
                    @foreach($group->members->take(3) as $member)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2 truncate">
                            <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xs font-bold border border-slate-200">
                                {{ mb_substr($member->customer?->name, 0, 1) }}
                            </div>
                            <span class="text-slate-700 font-bold truncate max-w-[100px]" title="{{ $member->customer?->name }}">{{ $member->customer?->name }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">{{ $member->shares_count }} سهم</span>
                            @if($member->contractItem)
                            <a href="{{ route('udhiya.contracts.show', $member->contractItem->contract_id) }}" class="text-indigo-500 hover:text-indigo-700 mx-1" title="رقم الصك">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    @if($group->members->count() > 3)
                    <div class="text-xs text-slate-400 font-bold italic text-center mt-2">
                        + {{ $group->members->count() - 3 }} شركاء آخرين
                    </div>
                    @endif
                </div>
                @else
                <div class="flex items-center justify-center py-4 bg-slate-50 rounded-xl border border-slate-100 border-dashed">
                    <p class="text-slate-400 font-medium text-xs">لم يكتتب أحد في هذه المجموعة بعد</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 text-center">
            <a href="{{ route('udhiya.groups.show', $group) }}" class="inline-flex w-full items-center justify-center px-4 py-2.5 text-sm font-bold rounded-xl transition-all bg-white text-indigo-600 border border-slate-200 hover:bg-indigo-50 hover:border-indigo-200 shadow-sm">
                مراجعة بيانات العصبة بالكامل
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-12 flex flex-col items-center justify-center py-20 text-center">
            <div class="text-7xl mb-6 text-slate-300">👥</div>
            <h5 class="text-2xl font-black text-slate-700 mb-3">{{ $search ? 'لا توجد نتائج للبحث المطابق' : 'لا توجد مجموعات وعُصب مسجلة' }}</h5>
            <p class="text-slate-500 max-w-lg mb-8">نظام التشارك يتيح للعملاء تقسيم الأنعام الكبيرة شرعياً (كالأبقار والإبل) إلى أسباع وخمسة وبناء عُصب لها.</p>
            @unless($search)
            <a href="{{ route('udhiya.groups.create') }}" class="inline-flex items-center justify-center px-6 py-3 text-base font-bold rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-md transform hover:-translate-y-0.5">بدء إنشاء أول مجموعة</a>
            @endunless
        </div>
    </div>
    @endforelse
</div>
@endsection
