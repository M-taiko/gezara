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
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold rounded-xl transition-all bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 shadow-sm no-print">
            🖨️ طباعة القائمة
        </button>
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
                    <select name="animal_id"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors py-2.5 px-3 text-sm font-semibold text-slate-800">
                        <option value="">— بدون حيوان —</option>
                        @foreach($animals as $animal)
                        <option value="{{ $animal->id }}" {{ $group->animal_id == $animal->id ? 'selected' : '' }}>
                            {{ $animal->code }} — {{ $animal->product?->name }}
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
                @if($remaining > 0)
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
                        <select name="customer_id" id="customerSelect"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-semibold text-slate-800">
                            <option value="">-- اختر --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}{{ $c->phone ? ' — '.$c->phone : '' }}</option>
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
                            <span class="text-slate-400 font-normal">(متاح: {{ $remaining }})</span>
                        </label>
                        <input type="number" name="shares_count"
                               value="1" min="1" max="{{ $remaining }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 py-2.5 px-3 text-sm font-bold text-slate-800 text-center">
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
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $member->shares_count }} نصيب
                                </span>
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
                            <td colspan="2"></td>
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

</div>

<style>
@media print {
    .no-print { display: none !important; }
}
</style>
@endsection

@push('js')
<script>
function toggleMode(mode) {
    const secExisting = document.getElementById('sectionExisting');
    const secNew      = document.getElementById('sectionNew');
    const btnE        = document.getElementById('btnExisting');
    const btnN        = document.getElementById('btnNew');
    const sel         = document.getElementById('customerSelect');

    if (mode === 'existing') {
        secExisting.style.display = 'block';
        secNew.style.display      = 'none';
        btnE.style.background     = '#4f46e5';
        btnE.style.color          = '#fff';
        btnN.style.background     = '#f1f5f9';
        btnN.style.color          = '#475569';
        sel.name = 'customer_id';
    } else {
        secExisting.style.display = 'none';
        secNew.style.display      = 'flex';
        btnN.style.background     = '#4f46e5';
        btnN.style.color          = '#fff';
        btnE.style.background     = '#f1f5f9';
        btnE.style.color          = '#475569';
        sel.name  = '_customer_id_disabled';
        sel.value = '';
    }
}
</script>
@endpush
