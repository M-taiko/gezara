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
            <span class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-black rounded-xl bg-emerald-100 text-emerald-700 border border-emerald-300">
                ✅ تم الذبح {{ $group->animal->slaughtered_at?->format('d/m/Y') }}
            </span>
        @endif

        <a href="{{ route('udhiya.groups.edit', $group) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl shadow-md transition-all
                  bg-indigo-600 text-white hover:bg-indigo-700 shadow-indigo-200/60">
            ✏️ تعديل
        </a>

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
            🖨️ طباعة
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

{{-- Top Status Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    {{-- Group Status --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-slate-500 font-bold text-xs mb-1">حالة المجموعة</p>
                <p class="text-lg font-black text-slate-800">
                    @if($isSlaughtered && $allDelivered)
                        ✅ مكتملة
                    @elseif($isSlaughtered)
                        🔪 مذبوحة
                    @else
                        ⏳ نشطة
                    @endif
                </p>
            </div>
            <div class="text-3xl">
                @if($isSlaughtered && $allDelivered)
                    ✅
                @elseif($isSlaughtered)
                    🔪
                @else
                    ⏳
                @endif
            </div>
        </div>
    </div>

    {{-- Animal Info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div>
            <p class="text-slate-500 font-bold text-xs mb-1">الحيوان</p>
            @if($group->animal)
                <p class="text-lg font-black text-slate-800 flex items-center gap-2">
                    <span class="text-2xl">{{ $emoji }}</span>
                    <span>{{ $group->animal->code }}</span>
                </p>
                @if($group->animal_type_label)
                    <p class="text-xs text-amber-700 font-bold mt-1">{{ $group->animal_type_label }}</p>
                @endif
            @else
                <p class="text-lg font-bold text-slate-400">غير محدد</p>
            @endif
        </div>
    </div>

    {{-- Share Progress --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <p class="text-slate-500 font-bold text-xs mb-2">تقدم الأنصبة</p>
        <p class="text-lg font-black text-slate-800 mb-2">{{ $used }}/{{ $total }}</p>
        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
            <div class="h-2 rounded-full transition-all duration-500 {{ $remaining === 0 ? 'bg-emerald-500' : ($pct > 60 ? 'bg-amber-500' : 'bg-indigo-500') }}"
                 style="width:{{ $pct }}%"></div>
        </div>
        <p class="text-xs text-slate-500 font-bold mt-1">{{ $remaining }} متبقي</p>
    </div>

    {{-- Share Type --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <p class="text-slate-500 font-bold text-xs mb-1">نوع التقسيم</p>
        <p class="text-lg font-black text-indigo-700">{{ $group->shareLabel() }}</p>
        @if($group->slaughter_day)
            <p class="text-xs text-amber-700 font-bold mt-2">📅 {{ $group->slaughter_day->format('d/m/Y') }}</p>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-16">

    {{-- Main Content (Left) --}}
    <div class="lg:col-span-2 flex flex-col gap-6">

        {{-- Members Table --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-blue-50 flex justify-between items-center">
                <h6 class="text-lg font-black text-slate-800 m-0 flex items-center gap-2">
                    👥 أعضاء المجموعة
                    <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold bg-indigo-100 text-indigo-700">
                        {{ $group->members->count() }}
                    </span>
                </h6>
            </div>

            @if($group->members->count())
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">العميل</th>
                            <th class="px-5 py-3 text-center">الأنصبة</th>
                            @if($pricePerShare > 0)
                            <th class="px-5 py-3 text-center">المستحق</th>
                            <th class="px-5 py-3 text-center">المدفوع</th>
                            <th class="px-5 py-3 text-center">المتبقي</th>
                            @endif
                            <th class="px-5 py-3">الصك</th>
                            @if($isSlaughtered)
                            <th class="px-5 py-3 text-center">التسليم</th>
                            @endif
                            <th class="px-5 py-3 no-print"></th>
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
                            <td class="px-5 py-3 text-slate-400 font-bold">{{ $i + 1 }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xs flex-shrink-0">
                                        {{ mb_substr($member->customer?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $member->customer?->name ?? '—' }}</div>
                                        @if($member->customer?->phone)
                                        <div class="text-xs text-slate-400">{{ $member->customer->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($member->contractItem)
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    ✅ {{ $member->contractItem->shares_count }}
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $member->shares_count }}
                                </span>
                                @endif
                            </td>

                            @if($pricePerShare > 0)
                            <td class="px-5 py-3 text-center font-black text-slate-800">
                                {{ number_format($memberTotal, 0) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($memberPaid > 0)
                                <span class="font-black text-emerald-600">{{ number_format($memberPaid, 0) }}</span>
                                @else
                                <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($memberRemaining > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-black bg-rose-50 text-rose-700 border border-rose-100">
                                    {{ number_format($memberRemaining, 0) }}
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    ✓
                                </span>
                                @endif
                            </td>
                            @endif

                            <td class="px-5 py-3">
                                @if($member->contractItem)
                                <a href="{{ route('udhiya.contracts.show', $member->contractItem->contract_id) }}"
                                   class="font-bold text-indigo-600 hover:underline text-xs">
                                    📄 {{ $member->contractItem->contract?->contract_number }}
                                </a>
                                @else
                                <a href="{{ route('udhiya.contracts.create') }}?group_id={{ $group->id }}&customer_id={{ $member->customer_id }}"
                                   class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-all border border-indigo-100">
                                    ➕
                                </a>
                                @endif
                            </td>

                            @if($isSlaughtered)
                            <td class="px-5 py-3 text-center">
                                @if($member->contractItem)
                                    @if($member->contractItem->delivered_at)
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        ✅ {{ $member->contractItem->delivered_at->format('d/m') }}
                                    </span>
                                    @else
                                    <form action="{{ route('udhiya.groups.members.deliver', [$group, $member]) }}" method="POST" style="display: inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" onclick="return confirm('تسليم {{ $member->customer?->name }}؟')"
                                                class="px-2 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all">
                                            📦
                                        </button>
                                    </form>
                                    @endif
                                @else
                                <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            @endif

                            <td class="px-5 py-3 no-print">
                                @if(!$member->contract_item_id)
                                <form action="{{ route('udhiya.groups.members.remove', [$group, $member]) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all text-xs">
                                        🗑
                                    </button>
                                </form>
                                @else
                                <span class="text-slate-400">🔒</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gradient-to-b from-slate-50 to-slate-100 border-t-2 border-slate-200 text-xs font-black">
                        <tr>
                            <td colspan="2" class="px-5 py-3 text-slate-600">إجمالي الأنصبة: {{ $group->members->sum('shares_count') }}</td>
                            @if($pricePerShare > 0)
                            <td class="px-5 py-3 text-center text-slate-800">{{ number_format($pricePerShare * $group->members->sum('shares_count'), 0) }}</td>
                            <td class="px-5 py-3 text-center text-emerald-700">{{ number_format($group->members->sum(fn($m) => $m->contractItem?->contract?->paid_amount ?? 0), 0) }}</td>
                            <td class="px-5 py-3 text-center text-rose-700">{{ number_format($group->members->sum(fn($m) => ($pricePerShare * $m->shares_count) - ($m->contractItem?->contract?->paid_amount ?? 0)), 0) }}</td>
                            @endif
                            <td colspan="{{ $isSlaughtered ? 3 : 2 }}"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="py-20 text-center">
                <div class="text-5xl mb-4">👥</div>
                <p class="text-slate-500 font-bold text-sm mb-1">لا يوجد أعضاء بعد</p>
                <p class="text-slate-400 text-xs">أضف عضواً من القائمة الجانبية</p>
            </div>
            @endif
        </div>

        {{-- Edit History --}}
        @if($group->edit_history && count($group->edit_history) > 0)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-5 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-orange-50">
                <h6 class="text-lg font-black text-slate-800 m-0">📝 سجل التعديلات</h6>
            </div>
            <div class="p-6 max-h-80 overflow-y-auto">
                <div class="space-y-4">
                    @foreach(array_reverse($group->edit_history) as $edit)
                    <div class="flex gap-3 pb-3 border-b border-slate-100 last:border-b-0">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">✏️</div>
                        </div>
                        <div class="flex-grow text-xs">
                            <p class="font-bold text-slate-800">{{ $edit['action'] }}</p>
                            @if($edit['details'])
                            <p class="text-slate-600 mt-1">{{ $edit['details'] }}</p>
                            @endif
                            <p class="text-slate-500 mt-1">{{ $edit['user_name'] }} • <span dir="ltr">{{ \Carbon\Carbon::parse($edit['timestamp'])->format('Y-m-d H:i') }}</span></p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Right Sidebar --}}
    <div class="flex flex-col gap-6">

        {{-- Details Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                <h6 class="text-base font-black text-slate-800 m-0">ℹ️ التفاصيل</h6>
            </div>
            <div class="p-6 space-y-4 text-sm">
                <div class="space-y-1">
                    <p class="text-slate-500 font-bold text-xs">الاسم</p>
                    <p class="font-black text-slate-800">{{ $group->name }}</p>
                </div>

                <div class="space-y-1">
                    <p class="text-slate-500 font-bold text-xs">نوع التقسيم</p>
                    <p class="font-bold text-indigo-700">{{ $group->shareLabel() }}</p>
                </div>

                @if($group->animal)
                <div class="space-y-1">
                    <p class="text-slate-500 font-bold text-xs">الحيوان</p>
                    <a href="{{ route('udhiya.animals.show', $group->animal) }}"
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 font-bold hover:bg-indigo-100 transition-colors">
                        <span class="text-lg">{{ $emoji }}</span> {{ $group->animal->code }}
                    </a>
                </div>

                @if($group->animal_type_label)
                <div class="space-y-1">
                    <p class="text-slate-500 font-bold text-xs">نوع الذبيحة</p>
                    <p class="font-bold text-amber-700 bg-amber-50 px-3 py-2 rounded-lg border border-amber-100">{{ $group->animal_type_label }}</p>
                </div>
                @endif
                @endif

                @if($group->slaughter_day)
                <div class="space-y-1">
                    <p class="text-slate-500 font-bold text-xs">يوم الذبح</p>
                    <p class="font-bold text-amber-700">📅 {{ $group->slaughter_day->format('d/m/Y') }}</p>
                </div>
                @endif

                @if($pricePerShare > 0)
                <div class="space-y-1">
                    <p class="text-slate-500 font-bold text-xs">سعر النصيب</p>
                    <p class="text-2xl font-black text-emerald-700">{{ number_format($pricePerShare, 0) }} <span class="text-xs">ج.م</span></p>
                </div>
                @endif

                @if($group->notes)
                <div class="space-y-1 pt-2 border-t border-slate-200">
                    <p class="text-slate-500 font-bold text-xs">ملاحظات</p>
                    <p class="text-slate-700 text-xs leading-relaxed">{{ $group->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Manage Group Card --}}
        @if(!$isSlaughtered)
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden sticky top-96">
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h6 class="text-base font-black text-slate-800 m-0">⚙️ إدارة</h6>
            </div>
            <div class="p-6 space-y-3">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-600">تغيير الحيوان</label>
                    <form action="{{ route('udhiya.groups.assign-animal', $group) }}" method="POST" class="space-y-2">
                        @csrf @method('PATCH')
                        <select name="animal_id" id="animalSelect" class="w-full">
                            <option value="">— بدون حيوان —</option>
                            @foreach($animals as $a)
                            @php
                                $aCat = $a->product?->mainCategory?->name ?? '';
                                $aType = $a->product?->name ?? '';
                            @endphp
                            <option value="{{ $a->id }}" {{ $group->animal_id == $a->id ? 'selected' : '' }}>
                                {{ $a->code }}{{ $aCat ? ' — ' . $aCat : '' }}{{ $aType ? ' / ' . $aType : '' }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full py-2 px-3 rounded-lg text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 transition-all">
                            حفظ
                        </button>
                    </form>
                </div>

                @if($remaining > 0)
                <div class="pt-2 border-t border-slate-200">
                    <label class="block text-xs font-bold text-slate-600 mb-2">إضافة عضو جديد</label>
                    <a href="#addMemberSection" class="w-full py-2 px-3 rounded-lg text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition-all block text-center">
                        ➕ إضافة عضو
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Add Member Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden" id="addMemberSection">
            <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-green-50">
                <h6 class="text-base font-black text-slate-800 m-0">➕ عضو جديد</h6>
            </div>
            <div class="p-6">
                @if($isSlaughtered)
                <div class="flex items-center gap-2 p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-700">
                    🔒 تم الذبح — لا يمكن إضافة أعضاء
                </div>
                @elseif($remaining > 0)
                <form action="{{ route('udhiya.groups.members.add', $group) }}" method="POST" class="space-y-3">
                    @csrf

                    <div class="flex rounded-lg overflow-hidden border border-slate-200 text-xs font-bold">
                        <button type="button" class="flex-1 py-2 bg-indigo-600 text-white" onclick="document.getElementById('secExisting').style.display='block'; document.getElementById('secNew').style.display='none'">
                            موجود
                        </button>
                        <button type="button" class="flex-1 py-2 bg-slate-100 text-slate-600" onclick="document.getElementById('secExisting').style.display='none'; document.getElementById('secNew').style.display='block'">
                            جديد
                        </button>
                    </div>

                    <div id="secExisting">
                        <label class="block text-xs font-bold text-slate-600 mb-1">العميل</label>
                        <select name="customer_id" id="customerSelect">
                            <option value="">— اختر —</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}{{ $c->phone ? ' — ' . $c->phone : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="secNew" style="display:none;" class="space-y-2">
                        <input type="text" name="new_customer_name" placeholder="الاسم *" class="w-full rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 py-2 px-3 text-xs font-bold" required>
                        <input type="text" name="new_customer_phone" placeholder="الهاتف" class="w-full rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 py-2 px-3 text-xs font-bold" dir="ltr">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">الأنصبة (متاح: {{ $remaining }})</label>
                        <input type="number" name="shares_count" value="1" min="1" max="{{ $remaining }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 px-3 text-center text-xs font-bold" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">رقم الصك (اختياري)</label>
                        <input type="text" name="contract_number" placeholder="مثال: 1001" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 px-3 text-xs font-bold" dir="ltr">
                    </div>

                    <button type="submit" class="w-full py-2.5 px-3 rounded-lg text-xs font-black bg-emerald-600 text-white hover:bg-emerald-700 transition-all">
                        ✅ إضافة
                    </button>
                </form>
                @else
                <div class="flex items-center gap-2 p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs font-bold text-amber-700">
                    ⚠️ المجموعة مكتملة
                </div>
                @endif
            </div>
        </div>

    </div>

</div>

<style>
@media print {
    .no-print { display: none !important; }
    .sticky { position: static !important; }
}
</style>
@endsection

@push('js')
<script>
$(function () {
    const s2opts = {
        dir: 'rtl',
        width: '100%',
        language: { noResults: () => 'لا توجد نتائج' }
    };

    $('#animalSelect').select2($.extend({}, s2opts, {
        placeholder: 'ابحث...',
        allowClear: true
    }));

    $('#customerSelect').select2($.extend({}, s2opts, {
        placeholder: 'ابحث...',
        allowClear: true
    }));
});
</script>
@endpush
