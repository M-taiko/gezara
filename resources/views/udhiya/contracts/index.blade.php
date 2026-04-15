@extends('layouts.master')

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🧾</span> إدارة الصكوك
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.dashboard') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">الرئيسية</a> / الصكوك
        </p>
    </div>
    <a href="{{ route('udhiya.contracts.create') }}"
       class="inline-flex items-center gap-2 px-5 py-3 text-sm font-black rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200/60 transition-all transform hover:-translate-y-0.5">
        ＋ صك جديد
    </a>
</div>
@endsection

@section('content')

{{-- Search bar --}}
<form method="GET" action="{{ route('udhiya.contracts.index') }}" class="mb-5">
    <div class="flex gap-3">
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="ابحث باسم العميل أو رقم هاتفه..."
                   class="w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 pr-9 pl-4 text-sm font-semibold text-slate-800 transition-colors shadow-sm">
        </div>
        <select name="filter_type" class="rounded-xl border border-slate-200 bg-white py-2.5 px-3 text-sm font-semibold text-slate-800 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 shadow-sm transition-colors">
            <option value="">جميع الصكوك</option>
            <option value="standalone" {{ request('filter_type') === 'standalone' ? 'selected' : '' }}>صكوك منفردة (بلا مجموعة)</option>
            <option value="grouped" {{ request('filter_type') === 'grouped' ? 'selected' : '' }}>صكوك في مجموعات</option>
        </select>
        <select name="filter_share" class="rounded-xl border border-slate-200 bg-white py-2.5 px-3 text-sm font-semibold text-slate-800 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 shadow-sm transition-colors w-32">
            <option value="">جميع الأنصبة</option>
            @foreach(['full'=>'كامل', 'seven'=>'سُبع', 'six'=>'سُدس', 'five'=>'خُمس', 'quarter'=>'ربع', 'third'=>'ثُلث', 'half'=>'نصف'] as $val => $label)
                <option value="{{ $val }}" {{ request('filter_share') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-sm">
            تصفية
        </button>
        @if($search || request('filter_type') || request('filter_share'))
        <a href="{{ route('udhiya.contracts.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">
            ✕ مسح
        </a>
        @endif
    </div>
    @if($search)
    <p class="text-xs font-semibold text-indigo-600 mt-2 mr-1">
        نتائج البحث عن "<span class="font-black">{{ $search }}</span>" — {{ $contracts->total() }} نتيجة
    </p>
    @endif
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.contract-checkbox');
    const createBtn = document.getElementById('createGroupBtn');
    
    function toggleBtn() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (anyChecked) {
            createBtn.classList.remove('hidden');
            createBtn.classList.add('inline-flex');
        } else {
            createBtn.classList.add('hidden');
            createBtn.classList.remove('inline-flex');
        }
    }
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleBtn();
        });
    }
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleBtn);
    });
    
    if (createBtn) {
        createBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('createGroupModal').classList.remove('hidden');
        });
    }
});
</script>

{{-- Table card --}}
<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

    {{-- Header --}}
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <h6 class="text-base font-black text-slate-800 m-0 flex items-center gap-2">
            🧾 قائمة الصكوك
        </h6>
        <div class="flex gap-3">
            <button type="button" id="createGroupBtn" class="hidden items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-purple-600 text-white hover:bg-purple-700 transition-all shadow-sm">
                👥 تجميع في مجموعة
            </button>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-indigo-100 text-indigo-700 border border-indigo-100">
            {{ $contracts->total() }} صك
        </span>
        </div>
    </div>

    {{-- Table --}}
    <form action="{{ route('udhiya.groups.store-from-contracts') }}" method="POST" id="groupContractsForm">
        @csrf
        <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                    <th class="px-5 py-3 w-10"><input type="checkbox" id="selectAll" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"></th>
                    <th class="px-5 py-3">رقم الصك</th>
                    <th class="px-5 py-3">العميل</th>
                    <th class="px-5 py-3 hidden sm:table-cell">يوم الذبح</th>
                    <th class="px-5 py-3 text-center hidden sm:table-cell">الأنصبة</th>
                    <th class="px-5 py-3 text-left">الإجمالي</th>
                    <th class="px-5 py-3 text-left hidden md:table-cell">المحصّل</th>
                    <th class="px-5 py-3 text-left hidden md:table-cell">المتبقي</th>
                    <th class="px-5 py-3">الحالة</th>
                    <th class="px-5 py-3 text-center w-40">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($contracts as $contract)
                @php
                    $rawPhone = preg_replace('/\D/', '', $contract->customer->phone ?? '');
                    if (strlen($rawPhone) === 11 && str_starts_with($rawPhone, '0')) {
                        $waPhone = '2' . $rawPhone;
                    } elseif (strlen($rawPhone) >= 10) {
                        $waPhone = $rawPhone;
                    } else {
                        $waPhone = '';
                    }
                    $isFullyPaid = $contract->remaining_amount <= 0;
                    $shareLabels = ['full'=>'كامل','seven'=>'سُبع','six'=>'سُدس','five'=>'خُمس','quarter'=>'ربع','third'=>'ثُلث','half'=>'نصف'];

                    if ($waPhone) {
                        // Load items relation if not loaded
                        $animalLines = $contract->items->map(function($item) use ($shareLabels) {
                            $label = $shareLabels[$item->share_type] ?? $item->share_type;
                            $animalInfo = $item->animal ? ($item->animal->code . ' — ' . ($item->animal->product->name ?? '')) : '(بدون حيوان)';
                            return '🐄 ' . $animalInfo . ' (' . $label . ')';
                        })->implode("\n");

                        if ($isFullyPaid) {
                            $waMsg =
                                "السلام عليكم ورحمة الله وبركاته 🌙\n" .
                                "أخي / أختي *{$contract->customer->name}*،\n\n" .
                                "يسعدنا إبلاغكم بأن صك الأضحية قد اكتمل سداده ✅\n\n" .
                                "📋 *رقم الصك:* {$contract->contract_number}\n" .
                                "{$animalLines}\n" .
                                "💰 *إجمالي الصك:* " . number_format($contract->total_amount, 2) . " ج.م\n\n" .
                                "جزاكم الله خيراً وتقبّل الله منا ومنكم صالح الأعمال 🤲";
                        } else {
                            $waMsg =
                                "السلام عليكم ورحمة الله وبركاته 🌙\n" .
                                "أخي / أختي *{$contract->customer->name}*،\n\n" .
                                "نُذكّركم بأن لديكم صك أضحية لم يكتمل سداده بعد.\n\n" .
                                "📋 *رقم الصك:* {$contract->contract_number}\n" .
                                "{$animalLines}\n\n" .
                                "💰 *الإجمالي:* " . number_format($contract->total_amount, 2) . " ج.م\n" .
                                "✅ *المسدّد:* " . number_format($contract->paid_amount, 2) . " ج.م\n" .
                                "⏳ *المتبقي:* " . number_format($contract->remaining_amount, 2) . " ج.م\n\n" .
                                "نرجو التكرم بسداد المبلغ المتبقي في أقرب وقت ممكن 🙏\n" .
                                "جزاكم الله خيراً وبارك فيكم 🤲";
                        }
                        $waUrl = 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($waMsg);
                    } else {
                        $waUrl = '';
                    }
                @endphp
                <tr class="hover:bg-slate-50/40 transition-colors">
                    {{-- Checkbox --}}
                    @php
                        $canGroup = $contract->items->count() === 1 && !$contract->items->first()->group_id && $contract->items->first()->share_type !== 'full';
                        $contractGroups = $contract->items->map->group->filter()->unique('id');
                        $isGrouped = $contractGroups->isNotEmpty();
                    @endphp
                    <td class="px-5 py-4">
                        @if($canGroup)
                        <input type="checkbox" name="contract_ids[]" value="{{ $contract->id }}" class="contract-checkbox rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                        @endif
                    </td>
                    {{-- Contract number --}}
                    <td class="px-5 py-4">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('udhiya.contracts.show', $contract) }}"
                                   class="font-black text-indigo-600 hover:text-indigo-800 hover:underline text-sm">
                                    {{ $contract->contract_number }}
                                </a>
                                @if(!$isGrouped)
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-slate-100 text-slate-500" title="منفرد">👤</span>
                                @endif
                            </div>
                            @if($isGrouped)
                            <div class="flex flex-wrap gap-1">
                                @foreach($contractGroups as $group)
                                <a href="{{ route('udhiya.groups.show', $group->id) }}" class="inline-flex items-center px-1.5 py-0.5 rounded md:rounded-md text-[10px] md:text-xs font-black bg-purple-100 text-purple-700 hover:bg-purple-200 transition-colors">
                                    👥 {{ $group->name }}
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </td>
                    {{-- Customer --}}
                    <td class="px-5 py-4">
                        <div class="font-black text-slate-800 text-sm">{{ $contract->customer->name }}</div>
                        @if($contract->customer->phone)
                        <div class="text-xs text-slate-400 font-mono mt-0.5" dir="ltr">{{ $contract->customer->phone }}</div>
                        @endif
                    </td>
                    {{-- Slaughter day --}}
                    <td class="px-5 py-4 text-sm text-slate-500 hidden sm:table-cell">
                        {{ $contract->slaughter_day ? \Carbon\Carbon::parse($contract->slaughter_day)->format('d/m/Y') : '—' }}
                    </td>
                    {{-- Shares --}}
                    <td class="px-5 py-4 text-center hidden sm:table-cell">
                        @php $totalShares = $contract->items->sum('shares_count'); @endphp
                        @foreach($contract->items as $item)
                        <div class="text-xs font-bold text-slate-600">
                            <span class="text-slate-400">{{ $item->animal ? $item->animal->code : '—' }}</span>
                            — <span class="text-indigo-700">{{ $shareLabels[$item->share_type] ?? $item->share_type }}</span>
                            @if($item->share_type !== 'full')
                            <span class="font-black text-slate-800">×{{ $item->shares_count }}</span>
                            @endif
                        </div>
                        @endforeach
                    </td>
                    {{-- Total --}}
                    <td class="px-5 py-4 text-left text-sm font-black text-slate-800">
                        {{ number_format($contract->total_amount) }}
                        <span class="text-xs text-slate-400 font-normal">ج.م</span>
                    </td>
                    {{-- Paid --}}
                    <td class="px-5 py-4 text-left hidden md:table-cell">
                        <span class="text-sm font-bold text-emerald-600">
                            {{ number_format($contract->paid_amount) }}
                            <span class="text-xs text-emerald-400">ج.م</span>
                        </span>
                    </td>
                    {{-- Remaining --}}
                    <td class="px-5 py-4 text-left hidden md:table-cell">
                        @if($contract->remaining_amount > 0)
                            <span class="text-sm font-bold text-rose-600">
                                {{ number_format($contract->remaining_amount) }}
                                <span class="text-xs text-rose-400">ج.م</span>
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-black bg-emerald-100 text-emerald-700">مسدّد ✅</span>
                        @endif
                    </td>
                    {{-- Status --}}
                    <td class="px-5 py-4">
                        @if($contract->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-amber-100 text-amber-700">نشط</span>
                        @elseif($contract->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-100 text-emerald-700">✅ مكتمل</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-rose-100 text-rose-700">ملغى</span>
                        @endif
                    </td>
                    {{-- Actions --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-1.5">
                            {{-- View --}}
                            <a href="{{ route('udhiya.contracts.show', $contract) }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white transition-colors text-xs"
                               title="عرض">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            {{-- Print --}}
                            <a href="{{ route('udhiya.contracts.print', $contract) }}" target="_blank"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-500 hover:text-white transition-colors text-xs"
                               title="طباعة">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </a>
                            {{-- WhatsApp --}}
                            @if($waUrl)
                            <a href="{{ $waUrl }}" target="_blank"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors text-xs
                                      {{ $isFullyPaid ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-500 hover:text-white' : 'bg-amber-100 text-amber-700 hover:bg-amber-500 hover:text-white' }}"
                               title="{{ $isFullyPaid ? 'إرسال الفاتورة' : 'تذكير بالدفع' }}">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="py-16 text-center">
                            <div class="text-5xl mb-4">🧾</div>
                            <p class="text-slate-400 font-bold text-base">
                                {{ $search ? 'لا توجد نتائج للبحث عن "' . $search . '"' : 'لا توجد صكوك بعد' }}
                            </p>
                            @if(!$search)
                            <a href="{{ route('udhiya.contracts.create') }}"
                               class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 text-sm font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all">
                                ＋ أضف أول صك
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Group Modal --}}
    <div id="createGroupModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" onclick="this.parentElement.parentElement.classList.add('hidden')"></div>
            <div class="relative inline-block w-full max-w-sm p-6 overflow-hidden text-right align-middle transition-all transform bg-white shadow-xl rounded-3xl">
                <h3 class="text-xl font-black text-slate-800 mb-4">👥 إنشاء مجموعة صكوك</h3>
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">اسم المجموعة <span class="text-rose-500">*</span></label>
                    <input type="text" name="group_name" placeholder="مثال: مجموعة بقر 1" required
                           class="w-full px-4 py-3 text-sm font-semibold rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-purple-100 focus:border-purple-400">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-5 py-3 text-sm font-black text-white bg-purple-600 rounded-xl hover:bg-purple-700 transition-all">إنشاء وربط</button>
                    <button type="button" onclick="document.getElementById('createGroupModal').classList.add('hidden')" class="px-5 py-3 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition-all">إلغاء</button>
                </div>
            </div>
        </div>
    </div>
    
    </form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.contract-checkbox');
    const createBtn = document.getElementById('createGroupBtn');
    
    function toggleBtn() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (anyChecked) {
            createBtn.classList.remove('hidden');
            createBtn.classList.add('inline-flex');
        } else {
            createBtn.classList.add('hidden');
            createBtn.classList.remove('inline-flex');
        }
    }
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleBtn();
        });
    }
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleBtn);
    });
    
    if (createBtn) {
        createBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('createGroupModal').classList.remove('hidden');
        });
    }
});
</script>

    {{-- Pagination --}}
    @if($contracts->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $contracts->links() }}
    </div>
    @endif
</div>

@endsection
