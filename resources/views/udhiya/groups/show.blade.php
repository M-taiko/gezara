@extends('layouts.master')
@section('title', 'مجموعة: ' . $group->name)

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">👥</span> {{ $group->name }}
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.groups.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المجموعات</a>
            / {{ $group->name }}
        </p>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        {{-- Slaughter button --}}
        @if($group->animal && $group->animal->status !== 'slaughtered')
            @php $allContracted = $group->remainingSlots() === 0; @endphp
            <form action="{{ route('udhiya.groups.slaughter', $group) }}" method="POST"
                  onsubmit="return confirm('{{ $allContracted ? 'تأكيد ذبح الذبيحة ' . $group->animal->code . '؟' : 'لم تكتمل جميع الصكوك. هل تريد الذبح على أي حال؟' }}')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl shadow-md transition-all
                               {{ $allContracted
                                  ? 'bg-rose-600 text-white hover:bg-rose-700 shadow-rose-200/60'
                                  : 'bg-amber-500 text-white hover:bg-amber-600 shadow-amber-200/60' }}">
                    🔪 {{ $allContracted ? 'ذبح الذبيحة' : 'ذبح على أي حال' }}
                </button>
            </form>
        @elseif($group->animal && $group->animal->status === 'slaughtered')
            <span class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-black rounded-xl bg-slate-100 text-slate-500 border border-slate-200">
                ✅ تم الذبح {{ $group->animal->slaughtered_at?->format('d/m/Y') }}
            </span>
        @endif

        {{-- Edit button (always available, but limited options if slaughtered) --}}
        <a href="{{ route('udhiya.groups.edit', $group) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl shadow-md transition-all
                  bg-indigo-600 text-white hover:bg-indigo-700 shadow-indigo-200/60">
            ✏️ تعديل
        </a>

        {{-- Delete button (only if not slaughtered) --}}
        @if(!$group->isSlaughtered())
            <form action="{{ route('udhiya.groups.destroy', $group) }}" method="POST" style="display: inline;"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl shadow-md transition-all
                               bg-rose-600 text-white hover:bg-rose-700 shadow-rose-200/60">
                    🗑️ حذف
                </button>
            </form>
        @endif

        <a href="{{ route('udhiya.groups.print', $group) }}"
           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold rounded-xl transition-all bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 shadow-sm">
            🖨️ طباعة القائمة
        </a>
    </div>
</div>
@endsection

@section('content')
@php
    $cat       = $group->animal?->product?->mainCategory;
    $emoji     = match($cat?->code) { 'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾' };
    $used      = $group->usedSlots();
    $total     = $group->totalSlots();
    $remaining = $group->remainingSlots();
    $pct       = $total > 0 ? round(($used / $total) * 100) : 0;
@endphp

<div class="flex flex-col lg:flex-row gap-8 pb-16">

    {{-- ===== RIGHT SIDEBAR ===== --}}
    <div class="w-full lg:w-80 flex flex-col gap-6">

        {{-- Group Info Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">📋 بيانات المجموعة</h6>
            </div>
            <div class="p-6 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">الاسم</span>
                    <span class="font-black text-slate-800">{{ $group->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">نوع التقسيم</span>
                    <span class="font-bold text-indigo-700">{{ $group->shareLabel() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-semibold">الحيوان</span>
                    @if($group->animal)
                    <a href="{{ route('udhiya.animals.show', $group->animal) }}"
                       class="font-bold text-indigo-600 hover:underline">
                        {{ $emoji }} {{ $group->animal->code }}
                    </a>
                    @else
                    <span class="text-slate-400 text-xs italic">غير محدد</span>
                    @endif
                </div>
                @if($group->slaughter_day)
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">يوم الذبح</span>
                    <span class="font-bold text-amber-700">{{ $group->slaughter_day->format('Y/m/d') }}</span>
                </div>
                @endif
                @if($pricePerShare > 0)
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">سعر النصيب</span>
                    <span class="font-black text-emerald-700">{{ number_format($pricePerShare, 0) }} ج.م</span>
                </div>
                @endif
                @if($group->notes)
                <div class="pt-2 border-t border-slate-100 text-slate-600 text-xs">{{ $group->notes }}</div>
                @endif
            </div>

            {{-- Slots Progress --}}
            <div class="px-6 pb-5">
                <div class="flex justify-between mb-2 text-xs font-bold">
                    <span class="text-slate-500">الأنصبة المحجوزة</span>
                    <span class="text-slate-800">{{ $used }} / {{ $total }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden ring-1 ring-inset ring-slate-200">
                    <div class="h-3 rounded-full transition-all duration-500
                        {{ $remaining === 0 ? 'bg-emerald-500' : ($pct > 60 ? 'bg-amber-500' : 'bg-indigo-500') }}"
                         style="width:{{ $pct }}%"></div>
                </div>
                <p class="text-xs text-slate-400 mt-1 font-semibold">{{ $remaining }} نصيب متبقي</p>
            </div>
        </div>

        {{-- Assign Animal Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">🐄 تعيين حيوان للمجموعة</h6>
            </div>
            <div class="p-6">
                <form action="{{ route('udhiya.groups.assign-animal', $group) }}" method="POST" class="flex flex-col gap-3">
                    @csrf @method('PATCH')

                    {{-- Current animal info --}}
                    @if($group->animal)
                    <div class="flex items-center gap-2 p-3 bg-indigo-50 rounded-xl border border-indigo-100 text-xs font-bold text-indigo-700">
                        <span class="text-base">{{ $emoji }}</span>
                        <div>
                            <div class="font-black">{{ $group->animal->code }}</div>
                            <div class="font-semibold text-indigo-500">{{ $group->animal->product?->mainCategory?->name }} — {{ $group->animal->product?->name }}</div>
                        </div>
                    </div>
                    @endif

                    <select name="animal_id" id="animalSelect">
                        <option value="">— بدون حيوان —</option>
                        @foreach($animals as $a)
                        @php
                            $aCat   = $a->product?->mainCategory?->name ?? '';
                            $aType  = $a->product?->name ?? '';
                            $aEmoji = match($a->product?->mainCategory?->code ?? '') {
                                'BQR' => '🐄', 'GHN' => '🐑', 'JDN' => '🐐', 'JML' => '🐪', default => '🐾'
                            };
                        @endphp
                        <option value="{{ $a->id }}" {{ $group->animal_id == $a->id ? 'selected' : '' }}>
                            {{ $aEmoji }} {{ $a->code }}{{ $aCat ? ' — ' . $aCat : '' }}{{ $aType ? ' / ' . $aType : '' }}{{ $a->status === 'slaughtered' ? ' (مذبوح)' : '' }}
                        </option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 text-sm font-bold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-sm">
                        حفظ التعيين
                    </button>
                </form>
            </div>
        </div>

        {{-- Add Member Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="text-base font-black text-slate-800 m-0">➕ إضافة عضو للمجموعة</h6>
            </div>
            <div class="p-6">
                @php $isSlaughtered = $group->animal?->status === 'slaughtered'; @endphp
                @if($isSlaughtered)
                <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 rounded-2xl p-4 text-sm font-bold text-rose-700">
                    🔒 تم الذبح — لا يمكن إضافة أعضاء جدد
                </div>
                @elseif($remaining > 0)

                <form action="{{ route('udhiya.groups.members.add', $group) }}" method="POST"
                      class="flex flex-col gap-4" id="addMemberForm">
                    @csrf

                    {{-- Toggle: existing / new --}}
                    <div class="flex rounded-xl overflow-hidden border border-slate-200 text-xs font-bold">
                        <button type="button" id="btnExisting"
                                onclick="toggleMode('existing')"
                                class="flex-1 py-2 bg-indigo-600 text-white transition-colors">
                            عميل موجود
                        </button>
                        <button type="button" id="btnNew"
                                onclick="toggleMode('new')"
                                class="flex-1 py-2 bg-slate-100 text-slate-600 transition-colors">
                            عميل جديد
                        </button>
                    </div>

                    {{-- Existing customer --}}
                    <div id="sectionExisting">
                        <label class="block text-xs font-bold text-slate-600 mb-1">اختر العميل</label>
                        <select name="customer_id" id="customerSelect">
                            <option value="">— اختر عميلاً —</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}" data-search="{{ strtolower($c->name . ' ' . $c->phone) }}">
                                {{ $c->name }}{{ $c->phone ? ' — ' . $c->phone : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- New customer --}}
                    <div id="sectionNew" style="display:none;" class="flex flex-col gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">الاسم <span class="text-rose-500">*</span></label>
                            <input type="text" name="new_customer_name"
                                   placeholder="اسم العميل الجديد"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">الهاتف</label>
                            <input type="text" name="new_customer_phone"
                                   placeholder="رقم الهاتف (اختياري)"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800">
                        </div>
                    </div>

                    {{-- Shares count --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">
                            عدد الأنصبة
                            @if(!$isSlaughtered)
                            <span class="text-slate-400 font-normal">(متاح: {{ $remaining }})</span>
                            @endif
                        </label>
                        <input type="number" name="shares_count"
                               value="1" min="1" @if(!$isSlaughtered) max="{{ $remaining }}" @endif
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-bold text-slate-800 text-center">
                    </div>

                    {{-- Contract number (manual) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">
                            رقم الصك
                            <span class="text-slate-400 font-normal">(يدوي — اختياري)</span>
                        </label>
                        <input type="text" name="contract_number"
                               placeholder="مثال: 1001 أو CNT-2026-0050"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-400 focus:ring-2 focus:ring-amber-100 py-2.5 px-3 text-sm font-bold text-slate-800 transition-colors"
                               dir="ltr">
                        <p class="text-xs text-slate-400 mt-1">إذا أدخلت رقماً سيُنشأ الصك فوراً</p>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">ملاحظات</label>
                        <textarea name="notes" rows="2"
                                  placeholder="ملاحظة اختيارية..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2 px-3 text-sm font-semibold text-slate-800 resize-none"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-3 text-sm font-black rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition-all shadow-sm shadow-emerald-200">
                        ✅ إضافة العضو
                    </button>
                </form>
                @else
                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm font-bold text-amber-700">
                    ⚠️ المجموعة مكتملة — لا توجد أنصبة متبقية
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ===== LEFT: Members Table ===== --}}
    <div class="flex-1">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
            <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h6 class="text-lg font-black text-slate-800 m-0">
                    أعضاء المجموعة
                    <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold bg-indigo-100 text-indigo-700 mr-2">
                        {{ $group->members->count() }}
                    </span>
                </h6>
            </div>

            @if($group->members->count())
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">اسم العميل</th>
                            <th class="px-4 py-3">الهاتف</th>
                            <th class="px-4 py-3 text-center">الأنصبة</th>
                            @if($pricePerShare > 0)
                            <th class="px-4 py-3 text-center">المستحق</th>
                            @endif
                            <th class="px-4 py-3 text-center">المدفوع</th>
                            @if($pricePerShare > 0)
                            <th class="px-4 py-3 text-center">المتبقي</th>
                            @endif
                            <th class="px-4 py-3">الصك</th>
                            @if($group->animal?->status === 'slaughtered')
                            <th class="px-4 py-3 text-center">التسليم</th>
                            @endif
                            <th class="px-4 py-3 no-print"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                        @foreach($group->members as $i => $member)
                        @php
                            $memberTotal     = $pricePerShare * $member->shares_count;
                            $memberPaid      = $member->contractItem?->contract?->paid_amount ?? 0;
                            $memberRemaining = $memberTotal - $memberPaid;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 text-slate-400 font-bold text-xs">{{ $i + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-sm border border-indigo-100 flex-shrink-0">
                                        {{ mb_substr($member->customer?->name ?? '?', 0, 1) }}
                                    </div>
                                    <strong class="text-slate-800 text-sm">{{ $member->customer?->name ?? '—' }}</strong>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $member->customer?->phone ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($member->contractItem)
                                @php $contractShares = $member->contractItem->shares_count; @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        ✅ {{ $contractShares }} نصيب
                                    </span>
                                    @if($contractShares != $member->shares_count)
                                    <span class="text-xs text-slate-400">(مسجّل: {{ $member->shares_count }})</span>
                                    @endif
                                </div>
                                @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $member->shares_count }} نصيب
                                </span>
                                @endif
                            </td>

                            @if($pricePerShare > 0)
                            {{-- Total due --}}
                            <td class="px-4 py-3 text-center">
                                <span class="font-black text-slate-800 text-sm">{{ number_format($memberTotal, 0) }}</span>
                                <span class="text-slate-400 text-xs"> ج.م</span>
                            </td>
                            @endif
                            {{-- Paid (always shown) --}}
                            <td class="px-4 py-3 text-center">
                                @if($memberPaid > 0)
                                <span class="font-black text-emerald-600 text-sm">{{ number_format($memberPaid, 0) }}</span>
                                <span class="text-slate-400 text-xs"> ج.م</span>
                                @else
                                <span class="text-slate-300 text-xs font-bold">—</span>
                                @endif
                            </td>
                            @if($pricePerShare > 0)
                            {{-- Remaining --}}
                            <td class="px-4 py-3 text-center">
                                @if($memberRemaining > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-black bg-rose-50 text-rose-700 border border-rose-100">
                                    {{ number_format($memberRemaining, 0) }} ج.م
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    مسدد ✓
                                </span>
                                @endif
                            </td>
                            @endif

                            {{-- Contract --}}
                            <td class="px-4 py-3">
                                @if($member->contractItem)
                                <a href="{{ route('udhiya.contracts.show', $member->contractItem->contract_id) }}"
                                   class="font-bold text-indigo-600 hover:underline text-xs">
                                    📄 {{ $member->contractItem->contract?->contract_number }}
                                </a>
                                @else
                                <a href="{{ route('udhiya.contracts.create') }}?group_id={{ $group->id }}&customer_id={{ $member->customer_id }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100" title="إصدار صك">
                                    ＋ صك
                                </a>
                                @endif
                            </td>
                            {{-- Delivery --}}
                            @if($group->animal?->status === 'slaughtered')
                            <td class="px-4 py-3 text-center">
                                @if($member->contractItem)
                                    @if($member->contractItem->delivered_at)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            ✅ {{ $member->contractItem->delivered_at->format('d/m') }}
                                        </span>
                                    @else
                                        <form action="{{ route('udhiya.groups.members.deliver', [$group, $member]) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    onclick="return confirm('تأكيد تسليم {{ $member->customer?->name }}؟')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-black bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-sm">
                                                🤝 تم التسليم
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                            @endif

                            {{-- Delete --}}
                            <td class="px-4 py-3 no-print">
                                @if(!$member->contract_item_id)
                                <form action="{{ route('udhiya.groups.members.remove', [$group, $member]) }}"
                                      method="POST" onsubmit="return confirm('تأكيد حذف العضو؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors text-xs">
                                        🗑
                                    </button>
                                </form>
                                @else
                                <span title="مربوط بصك" class="text-slate-400 text-lg">🔒</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-xs font-black">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-slate-600">
                                الإجمالي — {{ $group->members->sum('shares_count') }} نصيب
                            </td>
                            @if($pricePerShare > 0)
                            <td class="px-4 py-3 text-center text-slate-800">
                                {{ number_format($pricePerShare * $group->members->sum('shares_count'), 0) }} ج.م
                            </td>
                            @endif
                            <td class="px-4 py-3 text-center text-emerald-700">
                                {{ number_format($group->members->sum(fn($m) => $m->contractItem?->contract?->paid_amount ?? 0), 0) }} ج.م
                            </td>
                            @if($pricePerShare > 0)
                            <td class="px-4 py-3 text-center text-rose-700">
                                {{ number_format($group->members->sum(fn($m) => ($pricePerShare * $m->shares_count) - ($m->contractItem?->contract?->paid_amount ?? 0)), 0) }} ج.م
                            </td>
                            @endif
                            <td colspan="{{ $group->animal?->status === 'slaughtered' ? 3 : 2 }}"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="text-5xl mb-4">👥</div>
                <h5 class="text-lg font-black text-slate-600 mb-2">لا يوجد أعضاء بعد</h5>
                <p class="text-slate-400 text-sm">أضف عضواً من القائمة الجانبية</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Edit History Card --}}
    @if($group->edit_history && count($group->edit_history) > 0)
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
            <h6 class="text-base font-black text-slate-800 m-0">📝 سجل التعديلات</h6>
        </div>
        <div class="p-6">
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($group->edit_history as $edit)
                <div class="flex gap-3 pb-3 border-b border-slate-100 last:border-b-0 text-xs">
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-black">
                            ✏️
                        </div>
                    </div>
                    <div class="flex-grow">
                        <p class="font-bold text-slate-800">{{ $edit['action'] }}</p>
                        @if($edit['details'])
                        <p class="text-slate-600 mt-0.5">{{ $edit['details'] }}</p>
                        @endif
                        <p class="text-slate-500 text-xs mt-1">
                            بواسطة: <span class="font-bold">{{ $edit['user_name'] }}</span>
                            <br>
                            بتاريخ: <span class="font-bold" dir="ltr">{{ \Carbon\Carbon::parse($edit['timestamp'])->format('Y-m-d H:i') }}</span>
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

<style>
@media print {
    .no-print { display: none !important; }
}
</style>
@endsection

@push('js')
<script>
$(function () {
    var s2opts = {
        dir: 'rtl',
        allowClear: true,
        width: '100%',
        language: { noResults: function() { return 'لا توجد نتائج'; } },
    };

    $('#animalSelect').select2($.extend({}, s2opts, {
        placeholder: 'ابحث بالكود أو النوع...',
    }));

    $('#customerSelect').select2($.extend({}, s2opts, {
        placeholder: 'ابحث بالاسم أو الهاتف...',
    }));

    // ── Toggle existing / new customer ────────────────────
    window.toggleMode = function (mode) {
        var secE = document.getElementById('sectionExisting');
        var secN = document.getElementById('sectionNew');
        var btnE = document.getElementById('btnExisting');
        var btnN = document.getElementById('btnNew');

        if (mode === 'existing') {
            secE.style.display = 'block'; secN.style.display = 'none';
            btnE.className = 'flex-1 py-2 bg-indigo-600 text-white transition-colors';
            btnN.className = 'flex-1 py-2 bg-slate-100 text-slate-600 transition-colors';
        } else {
            secE.style.display = 'none'; secN.style.display = 'flex';
            btnN.className = 'flex-1 py-2 bg-indigo-600 text-white transition-colors';
            btnE.className = 'flex-1 py-2 bg-slate-100 text-slate-600 transition-colors';
            $('#customerSelect').val(null).trigger('change');
        }
    };
});
</script>
@endpush
