@extends('layouts.master')
@section('title', 'مجموعة: ' . $group->name)

@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-4xl font-black text-slate-800 tracking-tight flex items-center gap-4">
            <span class="text-5xl">👥</span> {{ $group->name }}
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-2">
            <a href="{{ route('udhiya.groups.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">المجموعات</a>
            / {{ $group->name }}
        </p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        @if($group->animal && $group->animal->status !== 'slaughtered')
            @php $allContracted = $group->remainingSlots() === 0; @endphp
            <form action="{{ route('udhiya.groups.slaughter', $group) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" onclick="return confirm('{{ $allContracted ? 'تأكيد ذبح الذبيحة ' . $group->animal->code . '؟' : 'لم تكتمل جميع الصكوك. هل تريد الذبح على أي حال؟' }}')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-lg transition-all
                               {{ $allContracted
                                  ? 'bg-rose-600 text-white hover:bg-rose-700 shadow-lg'
                                  : 'bg-amber-500 text-white hover:bg-amber-600 shadow-lg' }}">
                    🔪 {{ $allContracted ? 'ذبح' : 'ذبح!' }}
                </button>
            </form>
        @elseif($group->animal && $group->animal->status === 'slaughtered')
            <span class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-lg bg-emerald-100 text-emerald-700">
                ✅ مذبوح
            </span>
        @endif

        <a href="{{ route('udhiya.groups.edit', $group) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-lg">
            ✏️ تعديل
        </a>

        @if(!$group->isSlaughtered())
            <form action="{{ route('udhiya.groups.destroy', $group) }}" method="POST" style="display: inline;"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-lg bg-rose-600 text-white hover:bg-rose-700 transition-all shadow-lg">
                    🗑️
                </button>
            </form>
        @endif

        <a href="{{ route('udhiya.groups.print', $group) }}"
           class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold rounded-lg transition-all bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 shadow-sm">
            🖨️
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
    $isSlaughtered = $group->animal?->status === 'slaughtered';
    $allDelivered = $isSlaughtered && $group->members->every(fn($m) => $m->contractItem?->delivered_at);
@endphp

<div class="space-y-8 pb-16">

    {{-- ===== GROUP DETAILS SECTION ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-purple-50 to-pink-50 flex items-center justify-between">
            <h6 class="text-lg font-black text-slate-800 m-0">📋 تفاصيل المجموعة</h6>
            <button type="button" onclick="document.getElementById('addMemberModal').showModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all">
                ➕ إضافة عضو
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Category --}}
                @if($group->animal?->product?->mainCategory)
                <div class="flex flex-col">
                    <p class="text-xs font-bold text-slate-500 mb-1">الفئة</p>
                    <p class="font-bold text-slate-800">{{ $group->animal->product->mainCategory->name }}</p>
                </div>
                @endif

                {{-- Product --}}
                @if($group->animal?->product)
                <div class="flex flex-col">
                    <p class="text-xs font-bold text-slate-500 mb-1">النوع</p>
                    <p class="font-bold text-slate-800">{{ $group->animal->product->name }}</p>
                </div>
                @endif

                {{-- Share Type --}}
                <div class="flex flex-col">
                    <p class="text-xs font-bold text-slate-500 mb-1">نوع الحصة</p>
                    <p class="font-bold text-indigo-700">{{ $group->shareLabel() }}</p>
                </div>

                {{-- Slaughter Date --}}
                @if($group->slaughter_day)
                <div class="flex flex-col">
                    <p class="text-xs font-bold text-slate-500 mb-1">موعد الذبح</p>
                    <p class="font-bold text-slate-800">{{ $group->slaughter_day->format('d/m/Y') }}</p>
                </div>
                @endif

                {{-- Created At --}}
                <div class="flex flex-col">
                    <p class="text-xs font-bold text-slate-500 mb-1">تاريخ الإنشاء</p>
                    <p class="text-xs text-slate-600">{{ $group->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            {{-- Notes --}}
            @if($group->notes)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs font-bold text-slate-500 mb-2">ملاحظات</p>
                <p class="text-sm text-slate-700 bg-slate-50 p-3 rounded">{{ $group->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== KEY METRICS ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Status --}}
        <div class="bg-gradient-to-br rounded-2xl p-6 border-2 shadow-sm
            {{ $isSlaughtered && $allDelivered ? 'from-emerald-50 to-emerald-100 border-emerald-200' : ($isSlaughtered ? 'from-blue-50 to-blue-100 border-blue-200' : 'from-amber-50 to-amber-100 border-amber-200') }}">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-600 mb-1">الحالة</p>
                    <p class="text-2xl font-black {{ $isSlaughtered && $allDelivered ? 'text-emerald-900' : ($isSlaughtered ? 'text-blue-900' : 'text-amber-900') }}">
                        @if($isSlaughtered && $allDelivered)
                            مكتملة
                        @elseif($isSlaughtered)
                            مذبوحة
                        @else
                            نشطة
                        @endif
                    </p>
                </div>
                <div class="text-4xl">
                    @if($isSlaughtered && $allDelivered) ✅ @elseif($isSlaughtered) 🔪 @else ⏳ @endif
                </div>
            </div>
        </div>

        {{-- Animal --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-bold text-slate-600 mb-2">الحيوان</p>
            @if($group->animal)
                <div class="flex items-center gap-3 mb-1">
                    <span class="text-3xl">{{ $emoji }}</span>
                    <p class="text-2xl font-black text-slate-800">{{ $group->animal->code }}</p>
                </div>
                @if($group->animal_type_label)
                    <p class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded inline-block">{{ $group->animal_type_label }}</p>
                @endif
            @else
                <p class="text-lg font-bold text-slate-400">غير محدد</p>
            @endif
        </div>

        {{-- Share Type & Date --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-bold text-slate-600 mb-2">نوع التقسيم</p>
            <p class="text-2xl font-black text-indigo-700 mb-3">{{ $group->shareLabel() }}</p>
            @if($group->slaughter_day)
                <p class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-1.5 rounded">📅 {{ $group->slaughter_day->format('d/m/Y') }}</p>
            @endif
        </div>

        {{-- Share Progress --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-bold text-slate-600 mb-3">تقدم الأنصبة</p>
            <div class="mb-3">
                <p class="text-2xl font-black text-slate-800 mb-2">{{ $used }}<span class="text-lg text-slate-500">/{{ $total }}</span></p>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="h-3 rounded-full transition-all {{ $remaining === 0 ? 'bg-emerald-500' : ($pct > 60 ? 'bg-amber-500' : 'bg-indigo-500') }}"
                         style="width:{{ $pct }}%"></div>
                </div>
            </div>
            <p class="text-xs font-bold text-slate-500">{{ $remaining }} متبقي</p>
        </div>
    </div>

    {{-- ===== MEMBERS SECTION ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-indigo-50 via-blue-50 to-purple-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center text-xl">👥</div>
                <div>
                    <h6 class="text-lg font-black text-slate-800 m-0">أعضاء المجموعة</h6>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $group->members->count() }} عضو</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-lg font-black text-indigo-700">{{ number_format($group->members->sum('shares_count'), 0) }} نصيب</p>
            </div>
        </div>

        @if($group->members->count())
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">العميل</th>
                            <th class="px-6 py-3 text-center">الأنصبة</th>
                            @if($pricePerShare > 0)
                                <th class="px-6 py-3 text-center">المستحق</th>
                                <th class="px-6 py-3 text-center">المدفوع</th>
                                <th class="px-6 py-3 text-center">المتبقي</th>
                            @endif
                            <th class="px-6 py-3">الصك</th>
                            @if($isSlaughtered)
                                <th class="px-6 py-3 text-center">التسليم</th>
                            @endif
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($group->members as $i => $member)
                        @php
                            $memberTotal     = $pricePerShare * $member->shares_count;
                            $memberPaid      = $member->contractItem?->contract?->paid_amount ?? 0;
                            $memberRemaining = $memberTotal - $memberPaid;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-400">{{ $i + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xs flex-shrink-0">
                                        {{ mb_substr($member->customer?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $member->customer?->name ?? '—' }}</p>
                                        @if($member->customer?->phone)
                                            <p class="text-xs text-slate-400">{{ $member->customer->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                    {{ $member->contractItem ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $member->contractItem?->shares_count ?? $member->shares_count }}
                                </span>
                            </td>
                            @if($pricePerShare > 0)
                                <td class="px-6 py-4 text-center font-black text-slate-800">{{ number_format($memberTotal, 0) }}</td>
                                <td class="px-6 py-4 text-center font-black text-emerald-600">
                                    {{ $memberPaid > 0 ? number_format($memberPaid, 0) : '—' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($memberRemaining > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700">
                                            {{ number_format($memberRemaining, 0) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">✓</span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4">
                                @if($member->contractItem)
                                    <a href="{{ route('udhiya.contracts.show', $member->contractItem->contract_id) }}"
                                       class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline text-sm">
                                        📄 {{ $member->contractItem->contract?->contract_number }}
                                    </a>
                                @else
                                    <a href="{{ route('udhiya.contracts.create') }}?group_id={{ $group->id }}&customer_id={{ $member->customer_id }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-all border border-indigo-100">
                                        ➕ صك
                                    </a>
                                @endif
                            </td>
                            @if($isSlaughtered)
                                <td class="px-6 py-4 text-center">
                                    @if($member->contractItem)
                                        @if($member->contractItem->delivered_at)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-700">
                                                ✅ {{ $member->contractItem->delivered_at->format('d/m') }}
                                            </span>
                                        @else
                                            <form action="{{ route('udhiya.groups.members.deliver', [$group, $member]) }}" method="POST" style="display: inline;">
                                                @csrf @method('PATCH')
                                                <button type="submit" onclick="return confirm('تسليم {{ $member->customer?->name }}؟')"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all">
                                                    📦
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4 flex gap-1.5">
                                <div class="flex gap-1.5">
                                    {{-- Edit Member Shares --}}
                                    @if(!$member->contract_item_id)
                                        <button type="button" onclick="openEditMemberModal({{ $member->id }}, '{{ addslashes($member->customer?->name) }}', {{ $member->shares_count }}, '{{ addslashes($member->notes ?? '') }}')"
                                                class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-500 hover:bg-indigo-500 hover:text-white flex items-center justify-center transition-all text-sm"
                                                title="تعديل عدد الأنصبة">
                                            ✏️
                                        </button>
                                    @endif

                                    {{-- Edit Customer --}}
                                    @if($member->customer)
                                        <button type="button" onclick="openEditCustomerModal({{ $member->customer->id }}, '{{ addslashes($member->customer->name) }}', '{{ addslashes($member->customer->phone ?? '') }}', '{{ addslashes($member->customer->address ?? '') }}', '{{ addslashes($member->customer->notes ?? '') }}')"
                                                class="w-7 h-7 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all text-sm"
                                                title="تعديل بيانات العميل">
                                            👤
                                        </button>
                                    @endif

                                    {{-- Delete Member --}}
                                    @if(!$member->contract_item_id)
                                        <form action="{{ route('udhiya.groups.members.remove', [$group, $member]) }}" method="POST" onsubmit="return confirm('حذف العضو؟')" style="display: inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all text-sm"
                                                    title="حذف العضو">
                                                🗑
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-300 text-sm" title="مرتبط بصك">🔒</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gradient-to-b from-slate-50 to-slate-100 border-t-2 border-slate-200 font-bold">
                        <tr class="text-sm">
                            <td colspan="2" class="px-6 py-4 text-slate-700">الإجمالي</td>
                            <td class="px-6 py-4 text-center text-slate-800">{{ $group->members->sum('shares_count') }}</td>
                            @if($pricePerShare > 0)
                                <td class="px-6 py-4 text-center text-slate-800">{{ number_format($pricePerShare * $group->members->sum('shares_count'), 0) }}</td>
                                <td class="px-6 py-4 text-center text-emerald-700">{{ number_format($group->members->sum(fn($m) => $m->contractItem?->contract?->paid_amount ?? 0), 0) }}</td>
                                <td class="px-6 py-4 text-center text-rose-700">{{ number_format($group->members->sum(fn($m) => ($pricePerShare * $m->shares_count) - ($m->contractItem?->contract?->paid_amount ?? 0)), 0) }}</td>
                            @endif
                            <td colspan="{{ $isSlaughtered ? 3 : 2 }}"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="py-16 text-center">
                <p class="text-5xl mb-4">👥</p>
                <p class="text-slate-500 font-bold mb-1">لا يوجد أعضاء بعد</p>
                <p class="text-slate-400 text-sm">ابدأ بإضافة أعضاء للمجموعة</p>
            </div>
        @endif
    </div>

    {{-- ===== EDIT HISTORY ===== --}}
    @if($group->edit_history && count($group->edit_history) > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-orange-50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-xl">📝</div>
            <h6 class="text-lg font-black text-slate-800 m-0">سجل التعديلات</h6>
        </div>
        <div class="p-6 max-h-96 overflow-y-auto space-y-4">
            @foreach(array_reverse($group->edit_history) as $edit)
            <div class="flex gap-4 pb-4 border-b border-slate-100 last:border-b-0">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-sm flex-shrink-0">✏️</div>
                </div>
                <div class="flex-grow text-sm">
                    <p class="font-bold text-slate-800">{{ $edit['action'] }}</p>
                    @if($edit['details'])
                        <p class="text-slate-600 mt-1 text-xs">{{ $edit['details'] }}</p>
                    @endif
                    <p class="text-slate-500 text-xs mt-2">{{ $edit['user_name'] }} • {{ \Carbon\Carbon::parse($edit['timestamp'])->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- ===== EDIT MEMBER SHARES MODAL ===== --}}
<dialog id="editMemberModal" class="rounded-2xl shadow-2xl max-w-2xl w-full">
    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-8 py-6 border-b border-slate-200">
        <h3 class="text-xl font-black text-slate-800 m-0">✏️ تعديل بيانات العضو</h3>
        <p id="memberNameDisplay" class="text-sm text-slate-600 mt-1"></p>
    </div>

    <form id="editMemberForm" method="POST" class="p-8 space-y-6">
        @csrf @method('PATCH')

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">عدد الأنصبة</label>
            <input type="number" name="shares_count" id="editSharesCount" min="1" max="{{ $total }}" required
                   class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
            <p class="text-xs text-slate-500 mt-1">الحد الأقصى: {{ $remaining }} أنصبة متاحة</p>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
            <textarea name="notes" id="editNotes" rows="2" class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"></textarea>
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-200">
            <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-all">
                ✓ حفظ التعديلات
            </button>
            <button type="button" onclick="document.getElementById('editMemberModal').close()" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition-all">
                إلغاء
            </button>
        </div>
    </form>
</dialog>

{{-- ===== EDIT CUSTOMER MODAL ===== --}}
<dialog id="editCustomerModal" class="rounded-2xl shadow-2xl max-w-2xl w-full">
    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-8 py-6 border-b border-slate-200">
        <h3 class="text-xl font-black text-slate-800 m-0">👤 تعديل بيانات العميل</h3>
        <p id="customerNameDisplay" class="text-sm text-slate-600 mt-1"></p>
    </div>

    <form id="editCustomerForm" method="POST" class="p-8 space-y-6">
        @csrf @method('PATCH')

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">الاسم *</label>
            <input type="text" name="name" id="editCustomerName" required
                   class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">الهاتف</label>
            <input type="text" name="phone" id="editCustomerPhone"
                   class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                   placeholder="رقم الهاتف (اختياري)">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">العنوان</label>
            <input type="text" name="address" id="editCustomerAddress"
                   class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                   placeholder="العنوان (اختياري)">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
            <textarea name="notes" id="editCustomerNotes" rows="2"
                   class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                   placeholder="ملاحظات عن العميل (اختيارية)"></textarea>
        </div>

        <div class="flex gap-3 pt-4 border-t border-slate-200">
            <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-all">
                ✓ حفظ التعديلات
            </button>
            <button type="button" onclick="document.getElementById('editCustomerModal').close()" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition-all">
                إلغاء
            </button>
        </div>
    </form>
</dialog>

{{-- ===== ADD MEMBER MODAL ===== --}}
<dialog id="addMemberModal" class="rounded-2xl shadow-2xl max-w-2xl w-full">
    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 px-8 py-6 border-b border-slate-200">
        <h3 class="text-xl font-black text-slate-800 m-0">➕ إضافة عضو للمجموعة</h3>
        <p class="text-sm text-slate-600 mt-1">{{ $remaining }} أنصبة متاحة</p>
    </div>

    <form action="{{ route('udhiya.groups.members.add', $group) }}" method="POST" class="p-8 space-y-6">
        @csrf

        {{-- Tabs: Select Existing or Create New --}}
        <div class="flex gap-2 border-b border-slate-200">
            <button type="button" onclick="showTab('existing')" class="px-4 py-2 font-bold text-sm border-b-2 border-emerald-600 text-emerald-700">
                👤 عميل موجود
            </button>
            <button type="button" onclick="showTab('new')" class="px-4 py-2 font-bold text-sm text-slate-600 hover:text-slate-800 border-b-2 border-transparent">
                ➕ عميل جديد
            </button>
        </div>

        {{-- Tab 1: Existing Customer --}}
        <div id="tab-existing" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">العميل</label>
                <select name="customer_id" class="w-full rounded-lg border border-slate-300 bg-white text-slate-800 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                    <option value="">-- اختر عميلاً --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}
                            @if($customer->phone)
                                ({{ $customer->phone }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Tab 2: New Customer --}}
        <div id="tab-new" class="space-y-4" style="display: none;">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">اسم العميل *</label>
                <input type="text" name="new_customer_name" class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="أدخل اسم العميل">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">رقم الهاتف</label>
                <input type="text" name="new_customer_phone" class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="رقم الهاتف (اختياري)">
            </div>
        </div>

        {{-- Shares Count --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">عدد الأنصبة</label>
            <input type="number" name="shares_count" min="1" max="{{ $remaining }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="1">
            <p class="text-xs text-slate-500 mt-1">الحد الأقصى: {{ $remaining }} أنصبة</p>
        </div>

        {{-- Contract Number (Optional) --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">رقم الصك (اختياري)</label>
            <input type="text" name="contract_number" class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="إذا تُركت فارغة، سيتم إنشاء الصك لاحقاً">
        </div>

        {{-- Notes --}}
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">ملاحظات</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-slate-300 p-2.5 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" placeholder="ملاحظات إضافية (اختيارية)"></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 pt-4 border-t border-slate-200">
            <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition-all">
                ✓ إضافة العضو
            </button>
            <button type="button" onclick="document.getElementById('addMemberModal').close()" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition-all">
                إلغاء
            </button>
        </div>
    </form>
</dialog>

<script>
function showTab(tab) {
    document.getElementById('tab-existing').style.display = tab === 'existing' ? 'block' : 'none';
    document.getElementById('tab-new').style.display = tab === 'new' ? 'block' : 'none';
    document.querySelectorAll('[onclick*="showTab"]').forEach(btn => {
        btn.classList.remove('border-b-2', 'border-emerald-600', 'text-emerald-700');
        btn.classList.add('border-b-2', 'border-transparent', 'text-slate-600', 'hover:text-slate-800');
    });
    event.target.classList.add('border-b-2', 'border-emerald-600', 'text-emerald-700');
    event.target.classList.remove('border-b-2', 'border-transparent', 'text-slate-600', 'hover:text-slate-800');
}

function openEditMemberModal(memberId, memberName, sharesCount, notes) {
    document.getElementById('memberNameDisplay').textContent = memberName;
    document.getElementById('editSharesCount').value = sharesCount;
    document.getElementById('editNotes').value = notes;

    const form = document.getElementById('editMemberForm');
    form.action = `{{ route('udhiya.groups.members.update', [$group, ':memberId']) }}`.replace(':memberId', memberId);

    document.getElementById('editMemberModal').showModal();
}

function openEditCustomerModal(customerId, customerName, customerPhone, customerAddress, customerNotes) {
    document.getElementById('customerNameDisplay').textContent = customerName;
    document.getElementById('editCustomerName').value = customerName;
    document.getElementById('editCustomerPhone').value = customerPhone;
    document.getElementById('editCustomerAddress').value = customerAddress;
    document.getElementById('editCustomerNotes').value = customerNotes;

    const form = document.getElementById('editCustomerForm');
    form.action = `{{ route('udhiya.customers.update', ':customerId') }}`.replace(':customerId', customerId);

    document.getElementById('editCustomerModal').showModal();
}
</script>

@endsection

