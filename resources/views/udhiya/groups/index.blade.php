@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">👥</span> عُصب ومجموعات الذبح
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">تتبع التشارك في الرؤوس ومتابعة اكتمال الحصص</p>
    </div>
    <div class="flex h-full items-center gap-3">
        @php
            $standaloneCount = \App\Models\Contract::whereHas('items', fn($q) => $q->whereNull('group_id'))->count();
        @endphp
        <a href="{{ route('udhiya.contracts.index', ['type' => 'standalone']) }}"
           class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 shadow-sm">
            📄 الصكوك المنفردة
            @if($standaloneCount > 0)
            <span class="mr-2 inline-flex items-center justify-center min-w-[1.4rem] h-6 px-1.5 rounded-full text-xs font-black bg-slate-100 text-slate-600">{{ $standaloneCount }}</span>
            @endif
        </a>
        <a href="{{ route('udhiya.groups.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold rounded-xl transition-all bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            تكوين مجموعة مقفولة
        </a>
    </div>
</div>
@endsection

@section('content')

@php
    // حساب الإحصائيات
    $allGroups = \App\Models\SlaughterGroup::with('animal.product.mainCategory', 'members')->get();
    $allAnimals = \App\Models\Animal::with('product.mainCategory', 'product')->get();

    // الذبائح المرتبطة بمجموعات
    $groupedAnimals = $allGroups->pluck('animal')->filter();

    // الذبائح بالمخزون (لم تذبح بعد)
    $stockAnimals = $allAnimals->where('status', '!=', 'slaughtered')->where('status', '!=', 'sold');

    // تجميع حسب الفئة الرئيسية ثم حسب المنتج
    $animalsByCategory = $groupedAnimals->groupBy(function($a) {
        return $a->product?->mainCategory?->code ?? 'unknown';
    })->map(function($items) {
        return $items->groupBy(function($a) {
            return $a->product?->id ?? 'unknown';
        });
    });

    $stockByCategory = $stockAnimals->groupBy(function($a) {
        return $a->product?->mainCategory?->code ?? 'unknown';
    })->map(function($items) {
        return $items->groupBy(function($a) {
            return $a->product?->id ?? 'unknown';
        });
    });

    // معلومات المنتجات
    $products = \App\Models\Product::with('mainCategory')->get();
    $productMap = $products->keyBy('id');
@endphp

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    {{-- إجمالي المجموعات --}}
    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-indigo-600 font-bold text-sm">إجمالي المجموعات</p>
                <h3 class="text-3xl font-black text-indigo-900 mt-2">{{ $allGroups->count() }}</h3>
            </div>
            <div class="w-12 h-12 bg-indigo-200 rounded-xl flex items-center justify-center text-2xl">👥</div>
        </div>
    </div>

    {{-- الذبائح المرتبطة --}}
    <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-amber-600 font-bold text-sm">ذبائح مرتبطة</p>
                <h3 class="text-3xl font-black text-amber-900 mt-2">{{ $groupedAnimals->count() }}</h3>
            </div>
            <div class="w-12 h-12 bg-amber-200 rounded-xl flex items-center justify-center text-2xl">🔗</div>
        </div>
    </div>

    {{-- الذبائح في المخزون --}}
    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-emerald-600 font-bold text-sm">في المخزون</p>
                <h3 class="text-3xl font-black text-emerald-900 mt-2">{{ $stockAnimals->count() }}</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-200 rounded-xl flex items-center justify-center text-2xl">📦</div>
        </div>
    </div>

    {{-- المبيعة --}}
    <div class="bg-gradient-to-br from-rose-50 to-rose-100 border border-rose-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-rose-600 font-bold text-sm">مبيعة/مذبوحة</p>
                <h3 class="text-3xl font-black text-rose-900 mt-2">{{ $allAnimals->whereIn('status', ['slaughtered', 'sold'])->count() }}</h3>
            </div>
            <div class="w-12 h-12 bg-rose-200 rounded-xl flex items-center justify-center text-2xl">✅</div>
        </div>
    </div>

    {{-- المجموعات المكتملة --}}
    <div class="bg-gradient-to-br from-violet-50 to-violet-100 border border-violet-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-violet-600 font-bold text-sm">مجموعات مكتملة</p>
                <h3 class="text-3xl font-black text-violet-900 mt-2">{{ $allGroups->filter(fn($g) => $g->remainingSlots() === 0)->count() }}</h3>
            </div>
            <div class="w-12 h-12 bg-violet-200 rounded-xl flex items-center justify-center text-2xl">🎯</div>
        </div>
    </div>
</div>

{{-- Animals by Product with Inventory Analysis --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-8">
    {{-- Grouped Animals --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 bg-indigo-50/50">
            <h6 class="text-sm font-bold text-indigo-900 m-0 flex items-center gap-2">
                <span>🔗</span> الذبائح المرتبطة
            </h6>
        </div>
        <div class="p-4 max-h-72 overflow-y-auto">
            @php
                $categories = [
                    'BQR' => ['اسم' => 'عجول', 'emoji' => '🐄'],
                    'GHN' => ['اسم' => 'أغنام', 'emoji' => '🐑'],
                    'JDN' => ['اسم' => 'ماعز', 'emoji' => '🐐'],
                    'JML' => ['اسم' => 'جمال', 'emoji' => '🐪'],
                ];
                $hasAnyGrouped = false;
            @endphp

            @forelse($animalsByCategory as $catCode => $productGroups)
            @php $hasAnyGrouped = true; @endphp
            <div class="mb-4 pb-3 border-b border-slate-200 last:mb-0 last:pb-0 last:border-b-0">
                <p class="text-xs font-bold text-slate-500 uppercase mb-2 flex items-center gap-1.5">
                    <span class="text-base">{{ $categories[$catCode]['emoji'] ?? '🐾' }}</span>
                    {{ $categories[$catCode]['اسم'] ?? $catCode }}
                </p>

                @forelse($productGroups as $productId => $items)
                @php
                    $product = $productMap[$productId] ?? null;
                    $productName = $product?->name ?? 'بدون اسم';
                    $stockForProduct = $stockAnimals->where('product_id', $productId)->count();
                    $deficit = max(0, $items->count() - $stockForProduct);
                    $hasDeficit = $deficit > 0;
                @endphp
                <div class="flex items-center justify-between p-2 mb-1.5 rounded-md border {{ $hasDeficit ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-white' }} last:mb-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold {{ $hasDeficit ? 'text-rose-700' : 'text-slate-700' }} truncate">
                            {{ $productName }}
                        </p>
                        @if($hasDeficit)
                        <p class="text-xs text-rose-600 font-bold mt-0.5">⚠️ نقص {{ $deficit }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1 ml-2">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold whitespace-nowrap {{ $hasDeficit ? 'bg-rose-200 text-rose-700' : 'bg-indigo-100 text-indigo-700' }}">
                            {{ $items->count() }}
                        </span>
                        @if($hasDeficit)
                        <button onclick="openPurchaseModal('{{ $catCode }}', '{{ $productName }}', {{ $deficit }}, '{{ $productId }}')"
                                class="px-1 py-0.5 text-xs font-bold rounded bg-rose-600 text-white hover:bg-rose-700 transition-all whitespace-nowrap">
                            طلب
                        </button>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400">لا توجد ذبائح</p>
                @endforelse
            </div>
            @empty
            <div class="flex items-center justify-center py-6 text-slate-400">
                <p class="text-xs">لا توجد ذبائح مرتبطة</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Stock Animals --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 bg-emerald-50/50">
            <h6 class="text-sm font-bold text-emerald-900 m-0 flex items-center gap-2">
                <span>📦</span> المخزون المتاح
            </h6>
        </div>
        <div class="p-4 max-h-72 overflow-y-auto">
            @forelse($stockByCategory as $catCode => $productGroups)
            <div class="mb-4 pb-3 border-b border-slate-200 last:mb-0 last:pb-0 last:border-b-0">
                <p class="text-xs font-bold text-slate-500 uppercase mb-2 flex items-center gap-1.5">
                    <span class="text-base">{{ $categories[$catCode]['emoji'] ?? '🐾' }}</span>
                    {{ $categories[$catCode]['اسم'] ?? $catCode }}
                </p>

                @forelse($productGroups as $productId => $items)
                @php
                    $product = $productMap[$productId] ?? null;
                    $productName = $product?->name ?? 'بدون اسم';
                @endphp
                <div class="flex items-center justify-between p-2 mb-1.5 rounded-md border border-slate-200 bg-white last:mb-0">
                    <p class="text-xs font-bold text-slate-700 truncate flex-1">{{ $productName }}</p>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold whitespace-nowrap bg-emerald-100 text-emerald-700 ml-2">
                        {{ $items->count() }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400">لا توجد ذبائح</p>
                @endforelse
            </div>
            @empty
            <div class="flex items-center justify-center py-6 text-slate-400">
                <p class="text-xs">لا توجد ذبائح في المخزون</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

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
            {{-- Animal Code and Category --}}
            <div class="flex justify-between items-center mb-2 text-slate-500 font-bold text-sm bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="flex items-center gap-1 text-indigo-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    {{ $group->animal?->code ?? 'غير مرتبط' }}
                </span>
                <span>{{ $cat?->name ?? 'مجموعة فارغة' }}</span>
            </div>

            {{-- Animal Type Label --}}
            @if($group->animal_type_label)
            <div class="mb-4 px-3 py-2 rounded-lg border border-amber-100 bg-amber-50 text-right">
                <p class="text-xs font-bold text-amber-700">{{ $group->animal_type_label }}</p>
            </div>
            @endif

            {{-- Status Badge --}}
            @php
                $isSlaughtered = $group->animal?->status === 'slaughtered';
                $allDelivered = $isSlaughtered && $group->members->every(fn($m) => $m->contractItem?->delivered_at);
            @endphp
            <div class="mb-4">
                @if($isSlaughtered && $allDelivered)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-black bg-emerald-100 text-emerald-700 border border-emerald-300">
                        ✅ تم التسليم
                    </span>
                @elseif($isSlaughtered)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-black bg-rose-100 text-rose-700 border border-rose-300">
                        🔪 تم الذبح
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-black bg-sky-100 text-sky-700 border border-sky-300">
                        ⏳ لم يتم الذبح
                    </span>
                @endif
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

{{-- Purchase Order Modal --}}
<div id="purchaseModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
        <div class="sticky top-0 px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-lg font-black text-slate-800">📦 طلب شراء ذبائح ناقصة من مورد</h3>
        </div>

        <form id="purchaseForm" method="POST" action="{{ route('udhiya.purchases.store') }}" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">نوع الذبيحة</label>
                    <input type="text" id="animalType" disabled class="w-full rounded-xl border border-slate-200 bg-slate-100 py-2 px-3 text-sm font-semibold text-slate-600">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">العدد المطلوب <span class="text-rose-500">*</span></label>
                    <input type="number" id="quantity" name="quantity" min="1" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-sm font-semibold text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">اختر المورد <span class="text-rose-500">*</span></label>
                <select id="supplierId" name="supplier_id" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-sm font-semibold text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    <option value="">-- اختر مورد --</option>
                    @foreach(\App\Models\Supplier::orderBy('name')->get() as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }} (رصيد: {{ number_format($supplier->balance, 0) }} ر.س)</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-1">اختيار المورد يسمح لك بتتبع الديون والمدفوعات</p>
            </div>

            {{-- Items Container --}}
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <h5 class="text-sm font-bold text-slate-700 mb-3">🔍 عناصر الطلب</h5>

                <div id="itemsContainer" class="space-y-3">
                    <div class="grid grid-cols-3 gap-3 item-row">
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">المنتج</label>
                            <select name="items[0][product_id]" required class="w-full rounded-lg border border-slate-300 bg-white py-1.5 px-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                                <option value="">-- اختر --</option>
                                @foreach(\App\Models\Product::where('is_active', true)->orderBy('name')->get() as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">الكمية</label>
                            <input type="number" name="items[0][quantity]" min="1" value="1" required class="w-full rounded-lg border border-slate-300 bg-white py-1.5 px-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-600 block mb-1">سعر الوحدة</label>
                            <input type="number" name="items[0][cost_per_unit]" step="0.01" min="0" required class="w-full rounded-lg border border-slate-300 bg-white py-1.5 px-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <button type="button" onclick="addItem()" class="mt-2 text-xs font-bold text-indigo-600 hover:text-indigo-700">+ إضافة منتج آخر</button>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
                <textarea name="notes" rows="2" placeholder="مثل: ذبائح عالية الجودة أو متطلبات خاصة"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-sm font-semibold text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 resize-none"></textarea>
            </div>

            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-3">
                <p class="text-xs text-indigo-700"><strong>💡 ملاحظة مهمة:</strong> تأكد من تحديد التاريخ والسعر بشكل صحيح. سيتم ربط هذا الطلب بمجموعتك.</p>
            </div>

            {{-- Store category code --}}
            <input type="hidden" id="categoryCode" name="category_code">
            <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">

            <div class="flex gap-3 pt-4 border-t border-slate-100">
                <button type="button" onclick="closePurchaseModal()"
                        class="flex-1 px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
                    إلغاء
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2.5 text-sm font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all">
                    ✅ إنشاء الطلب
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemCount = 0;

function openPurchaseModal(categoryCode, productName, deficit, productId) {
    itemCount = 0;
    document.getElementById('animalType').value = productName;
    document.getElementById('quantity').value = deficit;
    document.getElementById('categoryCode').value = categoryCode;
    document.getElementById('purchaseForm').reset();
    resetItems(productId);
    document.getElementById('purchaseModal').classList.remove('hidden');
}

function closePurchaseModal() {
    document.getElementById('purchaseModal').classList.add('hidden');
    document.getElementById('purchaseForm').reset();
}

function resetItems(productId = null) {
    itemCount = 1;
    const container = document.getElementById('itemsContainer');
    const selectedProduct = productId ? 'selected' : '';
    container.innerHTML = `
        <div class="grid grid-cols-3 gap-3 item-row">
            <div>
                <label class="text-xs font-bold text-slate-600 block mb-1">المنتج</label>
                <select name="items[0][product_id]" required class="w-full rounded-lg border border-slate-300 bg-white py-1.5 px-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                    <option value="">-- اختر --</option>
                    @foreach(\App\Models\Product::where('is_active', true)->orderBy('name')->get() as $product)
                    <option value="{{ $product->id }}" ${productId === {{ $product->id }} ? 'selected' : ''}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-600 block mb-1">الكمية</label>
                <input type="number" name="items[0][quantity]" min="1" value="1" required class="w-full rounded-lg border border-slate-300 bg-white py-1.5 px-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
            </div>
            <div>
                <label class="text-xs font-bold text-slate-600 block mb-1">سعر الوحدة</label>
                <input type="number" name="items[0][cost_per_unit]" step="0.01" min="0" required class="w-full rounded-lg border border-slate-300 bg-white py-1.5 px-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" placeholder="0.00">
            </div>
        </div>
    `;

    // Set the selected product if provided
    if (productId) {
        const select = container.querySelector('select[name="items[0][product_id]"]');
        if (select) {
            select.value = productId;
        }
    }
}

function addItem() {
    itemCount++;
    const container = document.getElementById('itemsContainer');
    const newItem = document.createElement('div');
    newItem.className = 'grid grid-cols-3 gap-3 item-row';
    newItem.innerHTML = `
        <div>
            <select name="items[${itemCount}][product_id]" required class="w-full rounded-lg border border-slate-300 bg-white py-1.5 px-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                <option value="">-- اختر --</option>
                @foreach(\App\Models\Product::where('is_active', true)->orderBy('name')->get() as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <input type="number" name="items[${itemCount}][quantity]" min="1" value="1" required class="w-full rounded-lg border border-slate-300 bg-white py-1.5 px-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
        </div>
        <div class="flex gap-1 items-end">
            <input type="number" name="items[${itemCount}][cost_per_unit]" step="0.01" min="0" required class="flex-1 rounded-lg border border-slate-300 bg-white py-1.5 px-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" placeholder="0.00">
            <button type="button" onclick="removeItem(this)" class="px-2 py-1.5 bg-rose-100 text-rose-600 rounded-lg font-bold text-xs hover:bg-rose-200">✕</button>
        </div>
    `;
    container.appendChild(newItem);
}

function removeItem(btn) {
    btn.closest('.item-row').remove();
}

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePurchaseModal();
    }
});

// Close modal when clicking outside
document.getElementById('purchaseModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePurchaseModal();
    }
});
</script>

@endsection
