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

    {{-- ===== KEY METRICS ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Status --}}
        <div class="bg-gradient-to-br rounded-2xl p-6 border-2 shadow-sm"
             :class="$isSlaughtered && $allDelivered ? 'from-emerald-50 to-emerald-100 border-emerald-200' : ($isSlaughtered ? 'from-blue-50 to-blue-100 border-blue-200' : 'from-amber-50 to-amber-100 border-amber-200')">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-600 mb-1">الحالة</p>
                    <p class="text-2xl font-black" :class="$isSlaughtered && $allDelivered ? 'text-emerald-900' : ($isSlaughtered ? 'text-blue-900' : 'text-amber-900')">
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
                            <td class="px-6 py-4">
                                @if(!$member->contract_item_id)
                                    <form action="{{ route('udhiya.groups.members.remove', [$group, $member]) }}" method="POST" onsubmit="return confirm('حذف العضو؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all text-sm">
                                            🗑
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-300">🔒</span>
                                @endif
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
@endsection
