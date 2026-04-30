@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🧾</span> تعديل الصك #{{ $contract->contract_number }}
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.contracts.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">إدارة الصكوك</a> / إضافة
        </p>
    </div>
</div>
@endsection

@section('content')
<script>const allGroups = @json($groupsJson);</script>

<form action="{{ route('udhiya.contracts.update', $contract) }}" method="POST" id="contractForm"
      class="flex flex-col lg:flex-row gap-6 pb-16">
    @csrf
    @method('PUT')

    {{-- ═══════════ RIGHT SIDEBAR ═══════════ --}}
    <div class="w-full lg:w-80 flex-shrink-0">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden lg:sticky lg:top-24">

            {{-- Step 1: Group --}}
            <div class="px-6 py-5 border-b border-purple-100 bg-gradient-to-b from-purple-50 to-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-black text-sm flex-shrink-0">1</div>
                    <h6 class="text-base font-black text-purple-900 m-0">اختر المجموعة</h6>
                </div>
                <div class="flex gap-2 mb-3">
                    <select id="groupFilter"
                            class="flex-1 rounded-xl border border-purple-200 bg-white focus:bg-white focus:border-purple-400 focus:ring-2 focus:ring-purple-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                        <option value="">— اختر مجموعة —</option>
                        @foreach(json_decode(json_encode($groupsJson), false) as $g)
                        <option value="{{ $g->id }}"
                                data-animal="{{ $g->animal_id }}"
                                data-share="{{ $g->share_type }}"
                                data-price="{{ $g->price_per_share }}">
                            {{ $g->name }}
                            @if($g->animal_id) — {{ $g->animal_code }} @else — (بدون حيوان) @endif
                            ({{ $g->remaining }} متبقي)
                        </option>
                        @endforeach
                    </select>
                    <a href="{{ route('udhiya.groups.create') }}" target="_blank"
                       class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 hover:bg-purple-200 flex items-center justify-center transition-colors flex-shrink-0"
                       title="إنشاء مجموعة جديدة">
                        ➕
                    </a>
                </div>

                {{-- Members panel --}}
                <div id="groupMembersPanel" style="display:none;" class="mt-3 bg-white rounded-xl border border-purple-100 overflow-hidden shadow-sm">
                    <div class="px-3 py-2 bg-purple-50 border-b border-purple-100">
                        <p class="text-xs font-black text-purple-700 m-0">أعضاء المجموعة</p>
                    </div>
                    <div id="groupMembersList" class="divide-y divide-slate-50 max-h-48 overflow-y-auto"></div>
                </div>

                {{-- Standalone toggle --}}
                <div id="standaloneRow" class="mt-3 pt-3 border-t border-purple-100 flex items-center justify-between">
                    <span class="text-xs text-purple-300 font-semibold">أو</span>
                    <button type="button" onclick="enableStandaloneMode()"
                            class="text-xs font-black text-purple-600 hover:text-purple-900 underline underline-offset-2 transition-colors">
                        إصدار صك منفرد ←
                    </button>
                </div>
            </div>

            {{-- Step 2: Customer --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-sm flex-shrink-0">2</div>
                    <h6 class="text-base font-black text-slate-800 m-0 flex items-center gap-2 flex-wrap">
                        العميل <span class="text-rose-500 font-black">*</span>
                        <span id="standaloneBadge" style="display:none"
                              class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                            منفرد
                            <button type="button" onclick="disableStandaloneMode()"
                                    class="text-amber-900 hover:text-rose-600 font-black leading-none ml-0.5">✕</button>
                        </span>
                    </h6>
                </div>

                {{-- Customer Info Display --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-3">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-start">
                            <span class="text-blue-700 font-semibold">الاسم:</span>
                            <span class="text-blue-900 font-black" id="customerNameDisplay">{{ $contract->customer->name }}</span>
                        </div>
                        @if($contract->customer->phone)
                        <div class="flex justify-between items-start">
                            <span class="text-blue-700 font-semibold">الهاتف:</span>
                            <span class="text-blue-900 font-mono" dir="ltr" id="customerPhoneDisplay">{{ $contract->customer->phone }}</span>
                        </div>
                        @endif
                        @if($contract->customer->address)
                        <div class="flex justify-between items-start">
                            <span class="text-blue-700 font-semibold">العنوان:</span>
                            <span class="text-blue-900 text-xs" id="customerAddressDisplay">{{ $contract->customer->address }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Hidden input carries customer_id when select is disabled --}}
                <input type="hidden" id="customerIdHidden" name="customer_id" value="">
                <div class="flex gap-2">
                    <button type="button" id="editCustomerBtn" onclick="openEditCustomerModal()"
                            class="flex-1 w-10 h-10 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 flex items-center justify-center transition-colors font-bold text-sm"
                            title="تعديل بيانات العميل">
                        ✏️ تعديل العميل
                    </button>
                </div>
            </div>

            {{-- Financial Summary --}}
            <div class="px-6 py-5 border-b border-emerald-100 bg-gradient-to-b from-emerald-50 to-white"
                 data-current-total="{{ $contract->total_amount }}"
                 data-current-paid="{{ $contract->paid_amount }}">
                <h6 class="text-base font-black text-emerald-900 m-0 mb-4">الملخص المالي</h6>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-semibold">إجمالي الصك</span>
                        <span class="font-black text-slate-800">{{ number_format($contract->total_amount, 2) }} <span class="text-xs text-slate-400">ج.م</span></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-y border-emerald-100">
                        <span class="text-slate-500 font-semibold">المحصّل</span>
                        <span class="font-black text-emerald-600">{{ number_format($contract->paid_amount, 2) }} <span class="text-xs text-emerald-400">ج.م</span></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-semibold">المتبقي</span>
                        <span class="font-black {{ $contract->remaining_amount > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ number_format($contract->remaining_amount, 2) }} <span class="text-xs {{ $contract->remaining_amount > 0 ? 'text-rose-400' : 'text-emerald-400' }}">ج.م</span>
                        </span>
                    </div>
                    <div class="pt-2">
                        <div class="flex justify-between text-xs font-bold text-slate-400 mb-1.5">
                            <span>الحالة</span>
                            <span id="contractStatusBadge" class="px-2.5 py-1 rounded-full text-xs font-black
                                {{ $contract->status === 'active' ? 'bg-amber-100 text-amber-700' : ($contract->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700') }}">
                                {{ ['active' => 'نشط', 'completed' => 'مكتمل', 'cancelled' => 'ملغى'][$contract->status] ?? $contract->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payments --}}
            @if($contract->payments && count($contract->payments) > 0)
            <div class="px-6 py-5 border-b border-cyan-100 bg-gradient-to-b from-cyan-50 to-white">
                <h6 class="text-base font-black text-cyan-900 m-0 mb-4">الدفعات المسجلة</h6>
                <div class="space-y-3">
                    @foreach($contract->payments as $payment)
                    <div class="bg-white border border-cyan-200 rounded-xl p-3 flex items-center justify-between hover:shadow-sm transition-all">
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-bold text-cyan-700 mb-1">
                                {{ \Carbon\Carbon::parse($payment->date)->format('Y-m-d') }} — {{ number_format($payment->amount, 2) }} ج.م
                            </div>
                            @if($payment->receipt_number)
                            <div class="text-xs text-cyan-600 font-mono">رقم الايصال: {{ $payment->receipt_number }}</div>
                            @endif
                            @if($payment->reference_number)
                            <div class="text-xs text-slate-500">المرجع: {{ $payment->reference_number }}</div>
                            @endif
                        </div>
                        <a href="{{ route('udhiya.collections.edit', $payment) }}"
                           class="text-xs font-black text-cyan-600 hover:text-cyan-800 whitespace-nowrap mr-3"
                           title="تعديل الدفعة">
                            ✏️
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Step 3: Details --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <h6 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">تفاصيل إضافية</h6>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">رقم الصك <span class="text-slate-400 font-normal">(اختياري)</span></label>
                        <input type="text" name="contract_number" placeholder="سيتم إنشاء رقم تلقائي إن تركته فارغاً" value="{{ $contract->contract_number }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">تاريخ الذبح</label>
                        <input type="date" name="slaughter_day" value="{{ $contract->slaughter_day ? \Carbon\Carbon::parse($contract->slaughter_day)->format('Y-m-d') : '' }}"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">ترتيب الذبح</label>
                        <input type="number" name="slaughter_order" min="1" placeholder="رقم الترتيب"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors" value="{{ $contract->slaughter_order }}">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">ملاحظات ووصايا التسليم</label>
                        <textarea name="notes" rows="3" placeholder="مثال: يود استلام الثلث..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none">{{ $contract->notes }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">📎 مرفقات الصك <span class="text-slate-400 font-normal">(اختياري)</span></label>
                        <input type="file" name="attachments[]" multiple
                               accept="image/*,.pdf"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm text-slate-800 transition-colors">
                        <p class="text-xs text-slate-500 mt-1">صورة الإيصال أو المستند — JPG, PNG, PDF (اختياري)</p>
                    </div>
                </div>
            </div>

            {{-- Current Attachments (if any) --}}
            @if($contract->attachments && count($contract->attachments) > 0)
            <div class="px-6 py-5 border-t border-slate-100 bg-slate-50/40">
                <h6 class="text-xs font-bold text-slate-600 mb-3">📎 المرفقات الحالية</h6>
                <div class="space-y-2">
                    @foreach($contract->attachments as $idx => $attachment)
                    <div class="flex items-center justify-between bg-white p-2.5 rounded-lg border border-slate-200">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            <span class="text-sm text-indigo-600 flex-shrink-0">📄</span>
                            <a href="{{ asset('storage/' . ($contract->attachment_paths ? json_decode($contract->attachment_paths)[$idx] : '')) }}"
                               target="_blank"
                               class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 hover:underline truncate">
                                {{ $attachment }}
                            </a>
                        </div>
                        <label class="flex items-center gap-1.5 cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="remove_attachments[]" value="{{ $idx }}" class="w-4 h-4">
                            <span class="text-xs text-slate-400">حذف</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Submit --}}
<div class="px-6 py-5 bg-slate-50/80 space-y-3">
    <button type="submit"
            class="w-full inline-flex justify-center items-center gap-2 px-6 py-3.5 text-base font-black rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200/60 transition-all transform hover:-translate-y-0.5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        حفظ التعديلات
    </button>
    <a href="{{ route('udhiya.contracts.show', $contract) }}"
       class="w-full inline-flex justify-center items-center px-6 py-2.5 text-sm font-bold rounded-xl bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 transition-all">
        إلغاء والعودة
    </a>
</div>
        </div>
    </div>

    {{-- ═══════════ LEFT: ITEMS ═══════════ --}}
    <div class="flex-1 min-w-0 flex flex-col gap-5">

        {{-- Group lock banner --}}
        <div id="groupLockBanner" style="display:none;"
             class="flex items-center gap-4 bg-purple-50 border border-purple-200 rounded-2xl px-5 py-3.5">
            <div class="text-2xl flex-shrink-0">👥</div>
            <div class="flex-1 min-w-0">
                <div class="font-black text-purple-900 text-sm truncate" id="groupLockTitle">—</div>
                <div class="text-xs text-purple-400 font-semibold mt-0.5" id="groupLockDetails">—</div>
            </div>
            <div class="flex-shrink-0 flex items-center gap-2">
                <span class="text-xs text-purple-500 font-semibold">نوع الحصة:</span>
                <span id="shareTypeBadge" class="px-3 py-1 rounded-full text-xs font-black bg-purple-100 text-purple-700 border border-purple-200"></span>
            </div>
        </div>

        {{-- Items card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h6 class="text-lg font-black text-slate-800 m-0 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-sm">3</div>
                    الأضاحي والأنصبة
                </h6>
            </div>

            <div class="p-6 flex-1">
                <div class="overflow-x-auto rounded-2xl ring-1 ring-slate-100 mb-5">
                    <table class="w-full text-right" id="itemsTable">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold">
                                <th class="px-4 py-3 min-w-[200px]">الذبيحة <span class="text-slate-400 font-normal">(اختياري)</span></th>
                                <th class="px-4 py-3 w-36">نوع الحصة</th>
                                <th class="px-4 py-3 w-24">العدد</th>
                                <th class="px-4 py-3 w-28 text-center">سعر الوحدة</th>
                                <th class="px-4 py-3 w-28 text-center">الإجمالي (ج.م)</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-slate-50">
    @foreach($contract->items as $i => $cItem)
    <tr class="item-row bg-white hover:bg-slate-50/30 transition-colors">
        <td class="px-4 py-3">
            <select name="items[{{ $i }}][animal_id]"
                    class="animal-select w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-3 text-sm font-bold text-slate-800 transition-colors">
                <option value="">-- لم يحدد ذبيحة بعد --</option>
                @foreach($animals as $animal)
                <option value="{{ $animal->id }}"
                    {{ $cItem->animal_id == $animal->id ? 'selected' : '' }}
                    data-grouped="{{ $animal->is_grouped ? 1 : 0 }}"
                    data-share-type="{{ $animal->shareSetting->share_type ?? '' }}"
                    data-remaining="{{ $animal->shareSetting->remaining_shares ?? 1 }}"
                    data-price-full="{{ $animal->price_full ?? 0 }}"
                    data-price-seven="{{ $animal->price_seven ?? 0 }}"
                    data-price-six="{{ $animal->price_six ?? 0 }}"
                    data-price-five="{{ $animal->price_five ?? 0 }}"
                    data-price-quarter="{{ $animal->price_quarter ?? 0 }}"
                    data-price-third="{{ $animal->price_third ?? 0 }}"
                    data-price-half="{{ $animal->price_half ?? 0 }}">
                    🐄 [{{ $animal->code }}] — {{ $animal->product->name }}
                    @if($animal->is_grouped)({{ $animal->shareSetting->remaining_shares ?? 0 }} متبقي)@else(كامل)@endif
                </option>
                @endforeach
            </select>
            <input type="hidden" name="items[{{ $i }}][group_id]" class="group-id-input" value="{{ $cItem->group_id }}">
            <div class="group-info mt-2" style="display:none;">
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="group-progress-bar bg-emerald-500 h-1.5 rounded-full transition-all duration-300" style="width:0%"></div>
                </div>
                <div class="group-slots-label text-xs text-slate-400 mt-1 font-semibold"></div>
            </div>
        </td>
        <td class="px-4 py-3">
            <select name="items[{{ $i }}][share_type]"
                    class="share-type-select w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-3 text-sm font-bold text-slate-800 transition-colors"
                    required>
                <option value="full" {{ $cItem->share_type == 'full' ? 'selected' : '' }}>كامل</option>
                <option value="seven" {{ $cItem->share_type == 'seven' ? 'selected' : '' }}>سُبع (7)</option>
                <option value="six" {{ $cItem->share_type == 'six' ? 'selected' : '' }}>سُدس (6)</option>
                <option value="five" {{ $cItem->share_type == 'five' ? 'selected' : '' }}>خُمس (5)</option>
                <option value="quarter" {{ $cItem->share_type == 'quarter' ? 'selected' : '' }}>ربع (4)</option>
                <option value="third" {{ $cItem->share_type == 'third' ? 'selected' : '' }}>ثُلث (3)</option>
                <option value="half" {{ $cItem->share_type == 'half' ? 'selected' : '' }}>نصف (2)</option>
            </select>
            <input type="hidden" class="share-type-hidden" name="" value="{{ $cItem->share_type }}">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[{{ $i }}][shares_count]"
                   class="shares-count w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-2 text-sm font-bold text-center text-slate-800 transition-colors"
                   min="1" max="{{ max(7, $cItem->shares_count ?? 1) }}" value="{{ $cItem->shares_count ?? 1 }}" required>
            <div class="shares-limit-label text-xs font-bold text-slate-400 mt-1 text-center"></div>
        </td>
        <td class="px-4 py-3 text-center">
            <input type="number" name="items[{{ $i }}][unit_price]"
                    class="item-price w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-2 text-sm font-black text-slate-800 text-center transition-colors"
                    step="0.01" placeholder="0.00" value="{{ $cItem->unit_price }}" required>
        </td>
        <td class="px-4 py-3 text-center">
            <input type="number" name="items[{{ $i }}][total_price]"
                   class="item-total w-full border-0 bg-transparent py-1 text-sm font-black text-indigo-600 text-center"
                   step="0.01" readonly placeholder="—" value="{{ $cItem->total_price }}">
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button"
                    class="remove-row w-8 h-8 rounded-lg bg-rose-50 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors mx-auto">
                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </td>
    </tr>
    @endforeach
</tbody>
                    </table>
                </div>

                <button type="button" id="addRow"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-100 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    إضافة حيوان آخر
                </button>
            </div>

            {{-- Grand total --}}
            <div class="px-6 py-5 border-t border-slate-100 bg-slate-900 text-white flex justify-between items-center">
                <div class="text-sm font-bold text-slate-400">إجمالي قيمة الصك</div>
                <div class="text-3xl font-black">
                    <span id="grandTotal">0.00</span>
                    <span class="text-lg text-indigo-400 mr-1">ج.م</span>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
let rowIndex = {{ count($contract->items) }};

const SHARE_MAX = { full: 1, seven: 7, six: 6, five: 5, quarter: 4, third: 3, half: 2 };
const SHARE_LABEL_AR = { full: 'كامل', seven: 'سُبع', six: 'سُدس', five: 'خُمس', quarter: 'ربع', third: 'ثُلث', half: 'نصف' };

/* ══════════════════════════════════
   ROW HELPERS
══════════════════════════════════ */

function _getShareType(row) {
    const hidden = row.querySelector('.share-type-hidden');
    const sel    = row.querySelector('.share-type-select');
    return (hidden && hidden.value) ? hidden.value : sel.value;
}

function _lockShareType(row, shareType) {
    const sel    = row.querySelector('.share-type-select');
    const hidden = row.querySelector('.share-type-hidden');
    sel.value    = shareType;
    if (sel.name) { hidden.name = sel.name; sel.name = ''; }
    hidden.value = shareType;
    sel.disabled = true;
    sel.classList.add('bg-purple-50', 'text-purple-700', 'cursor-not-allowed', 'opacity-80');
}

function _unlockShareType(row) {
    const sel    = row.querySelector('.share-type-select');
    const hidden = row.querySelector('.share-type-hidden');
    sel.disabled = false;
    if (!sel.name && hidden.name) { sel.name = hidden.name; }
    hidden.name  = '';
    hidden.value = '';
    sel.classList.remove('bg-purple-50', 'text-purple-700', 'cursor-not-allowed', 'opacity-80');
}

function updateGroupInfo(row) {
    const gid       = row.querySelector('.group-id-input').value;
    const infoDiv   = row.querySelector('.group-info');
    const bar       = row.querySelector('.group-progress-bar');
    const label     = row.querySelector('.group-slots-label');
    const sharesIn  = row.querySelector('.shares-count');
    const animalSel = row.querySelector('.animal-select');

    if (!gid) { infoDiv.style.display = 'none'; return; }

    const g = allGroups.find(x => String(x.id) === String(gid));
    if (!g)  { infoDiv.style.display = 'none'; return; }

    // Validate that animal matches group's animal
    const selectedAnimalId = animalSel.value;
    if (selectedAnimalId && g.animal_id && String(selectedAnimalId) !== String(g.animal_id)) {
        infoDiv.style.display = 'none';
        console.warn('⚠️ الحيوان المختار غير مطابق للمجموعة');
        return;
    }

    const pct = g.total > 0 ? Math.round((g.used / g.total) * 100) : 0;
    bar.style.width  = pct + '%';
    bar.className    = 'group-progress-bar h-1.5 rounded-full transition-all duration-300 '
                     + (g.remaining === 0 ? 'bg-rose-500' : pct > 60 ? 'bg-amber-500' : 'bg-emerald-500');
    label.textContent = g.used + ' محجوز / ' + g.total + ' (متبقي: ' + g.remaining + ')';
    infoDiv.style.display = '';

    if (!sharesIn.readOnly) {
        const maxAllowed = Math.min(SHARE_MAX[_getShareType(row)] || 7, g.remaining);
        sharesIn.max = maxAllowed;
        row.querySelector('.shares-limit-label').textContent = 'الحد: ' + maxAllowed;
        if (parseInt(sharesIn.value) > maxAllowed) {
            sharesIn.value = maxAllowed > 0 ? maxAllowed : parseInt(sharesIn.value);
        }
    } else {
        // For locked shares, preserve the current value and set max to current value
        sharesIn.max = Math.max(7, parseInt(sharesIn.value) || 1);
    }
}

function updateRow(row) {
    const animalSel = row.querySelector('.animal-select');
    const opt       = animalSel.selectedOptions[0];
    const shareType = _getShareType(row);
    const sharesIn  = row.querySelector('.shares-count');
    const weightIn  = row.querySelector('.animal-weight');
    const priceIn   = row.querySelector('.item-price');
    const totalIn   = row.querySelector('.item-total');

    let unitPrice = 0;

    // Check if user has manually entered a price
    const manualPrice = parseFloat(priceIn.value) || 0;

    if (opt && opt.value && manualPrice === 0) {
        // Animal is selected AND no manual price - get price from animal data
        const priceKey  = { full:'priceFull', seven:'priceSeven', six:'priceSix', five:'priceFive', quarter:'priceQuarter', third:'priceThird', half:'priceHalf' };
        unitPrice = parseFloat(opt.dataset[priceKey[shareType]]) || 0;
        priceIn.value = unitPrice > 0 ? unitPrice.toFixed(2) : '';

        // In standalone mode, populate weight from animal
        if (weightIn && !weightIn.parentElement.parentElement.style.display || weightIn.parentElement.parentElement.style.display === '') {
            weightIn.value = opt.dataset.weight || '';
        }
    } else {
        // User entered manual price OR no animal - use the entered price
        unitPrice = manualPrice;
    }

    // Calculate total based on mode (shares count or weight)
    const isStandaloneMode = weightIn && (weightIn.parentElement.parentElement.style.display === '' || !weightIn.parentElement.parentElement.style.display);
    let quantity = 1;

    if (isStandaloneMode) {
        // Standalone mode: quantity is weight, default to 1 if not specified
        quantity = parseFloat(weightIn.value) || 1;
    } else {
        // Group mode: quantity is shares count
        quantity = parseInt(sharesIn.value) || 1;
    }

    // Calculate and display total
    totalIn.value = (unitPrice * quantity).toFixed(2);

    if (sharesIn.parentElement.parentElement.style.display !== 'none') {
        if (!sharesIn.readOnly) {
            // Only unlock shares - can change max
            const typeMax = SHARE_MAX[shareType] || 7;
            sharesIn.max  = typeMax;
            row.querySelector('.shares-limit-label').textContent = 'أقصاها: ' + typeMax;
        } else {
            // Locked shares - keep current value as max (don't change it)
            sharesIn.max = Math.max(7, parseInt(sharesIn.value) || 1);
            row.querySelector('.shares-limit-label').textContent = '🔒 محدد من المجموعة';
        }
    }

    updateGroupInfo(row);
    calcGrand();
    updateFinancialSummary();
}

function calcGrand() {
    let grand = 0;
    document.querySelectorAll('.item-total').forEach(el => grand += parseFloat(el.value) || 0);
    document.getElementById('grandTotal').textContent =
        Number(grand).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    updatePaymentSummary(grand);
    updateFinancialSummary();
}

function updateFinancialSummary() {
    // Get current contract data from attributes
    const summaryEl = document.querySelector('[data-current-paid]');
    if (!summaryEl) return;

    const currentPaid = parseFloat(summaryEl.getAttribute('data-current-paid') || 0);
    const currentTotal = parseFloat(summaryEl.getAttribute('data-current-total') || 0);

    // Calculate remaining
    const remaining = currentTotal - currentPaid;

    // Update all financial summary elements
    const totalEl = summaryEl.querySelector('div:nth-child(1) span:last-child') ||
                   document.querySelector('[data-current-total] + div .space-y-3 div:nth-child(1) span:last-child');
    const paidEl = summaryEl.querySelector('div:nth-child(2) span:last-child') ||
                  document.querySelector('[data-current-total] + div .space-y-3 div:nth-child(2) span:last-child');
    const remainingEl = summaryEl.querySelector('div:nth-child(3) span:last-child') ||
                       document.querySelector('[data-current-total] + div .space-y-3 div:nth-child(3) span:last-child');
    const statusEl = document.getElementById('contractStatusBadge');

    // Format numbers with thousand separators
    const formatNumber = (num) => Number(num).toLocaleString('ar-EG', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    // Update display values
    if (totalEl) {
        totalEl.innerHTML = `${formatNumber(currentTotal)} <span class="text-xs text-slate-400">ج.م</span>`;
    }
    if (paidEl) {
        paidEl.innerHTML = `${formatNumber(currentPaid)} <span class="text-xs text-emerald-400">ج.م</span>`;
    }
    if (remainingEl) {
        remainingEl.innerHTML = `${formatNumber(remaining)} <span class="text-xs ${remaining > 0 ? 'text-rose-400' : 'text-emerald-400'}">ج.م</span>`;
        remainingEl.parentElement.className = remaining > 0 ? 'flex justify-between items-center' : 'flex justify-between items-center';
        remainingEl.classList = remaining > 0 ? 'font-black text-rose-600' : 'font-black text-emerald-600';
    }

    // Update status badge
    if (statusEl) {
        if (remaining <= 0) {
            statusEl.className = 'px-2.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700';
            statusEl.textContent = 'مكتمل';
        } else {
            statusEl.className = 'px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-700';
            statusEl.textContent = 'نشط';
        }
    }
}

function updatePaymentSummary(total) {
    const paymentAmountEl = document.getElementById('paymentAmount');
    if (!paymentAmountEl) return; // Payment summary only on create page

    const paid      = parseFloat(paymentAmountEl.value) || 0;
    const remaining = total - paid;
    const summary   = document.getElementById('paymentSummary');
    if (!summary) return;

    if (total <= 0 && paid <= 0) { summary.style.display = 'none'; return; }
    summary.style.display = '';

    const summaryTotal = document.getElementById('summaryTotal');
    const summaryPaid = document.getElementById('summaryPaid');
    const summaryRemaining = document.getElementById('summaryRemaining');

    if (summaryTotal) summaryTotal.textContent = Number(total).toLocaleString(undefined, {minimumFractionDigits: 0}) + ' ج.م';
    if (summaryPaid) summaryPaid.textContent = Number(paid).toLocaleString(undefined, {minimumFractionDigits: 0}) + ' ج.م';
    if (summaryRemaining) summaryRemaining.textContent = Number(remaining).toLocaleString(undefined, {minimumFractionDigits: 0}) + ' ج.م';
}

function attachPaymentAmountListener() {
    const paymentAmountEl = document.getElementById('paymentAmount');
    if (paymentAmountEl) {
        paymentAmountEl.addEventListener('input', function () {
            const grand = parseFloat(document.getElementById('grandTotal').textContent.replace(/,/g, '')) || 0;
            updatePaymentSummary(grand);
        });
    }
}

/* ══════════════════════════════════
   GROUP LOCK BANNER
══════════════════════════════════ */

function showGroupBanner(group) {
    document.getElementById('groupLockTitle').textContent  = group.name;
    document.getElementById('groupLockDetails').textContent =
        (group.animal_code ? 'الحيوان: ' + group.animal_code + ' — ' : '') +
        group.used + '/' + group.total + ' نصيب محجوز';
    document.getElementById('shareTypeBadge').textContent  = SHARE_LABEL_AR[group.share_type] || group.share_type;
    document.getElementById('groupLockBanner').style.display = '';
}

function hideGroupBanner() {
    document.getElementById('groupLockBanner').style.display = 'none';
}

/* ══════════════════════════════════
   GROUP FILTER LOGIC
══════════════════════════════════ */

function attachGroupFilterListener() {
    const groupFilter = document.getElementById('groupFilter');
    if (groupFilter) {
        groupFilter.addEventListener('change', function () {
            const gid = this.value;
            filterCustomersByGroup(gid);
            if (gid) autoFillFromGroup(gid);
            else     clearGroupLock();
        });
    }
}

function attachCustomerSelectListener() {
    const customerSelect = document.getElementById('customerSelect');
    if (customerSelect) {
        customerSelect.addEventListener('change', function () {
            // Always mirror value to hidden input (select may be disabled)
            document.getElementById('customerIdHidden').value = this.value;

            // Update edit button state
            const btn = document.getElementById('editCustomerBtn');
            if (btn) {
                if (this.value) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    btn.classList.add('bg-indigo-100', 'text-indigo-600', 'hover:bg-indigo-200');
                } else {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                    btn.classList.remove('bg-indigo-100', 'text-indigo-600', 'hover:bg-indigo-200');
                }
            }

            const gid = document.getElementById('groupFilter').value;
            if (!gid) return;
            const group  = allGroups.find(g => String(g.id) === String(gid));
            if (!group)  return;
            const member = group.members.find(m => String(m.customer_id) === String(this.value));
            if (!member) return;
            lockRowToMember(document.querySelector('.item-row'), group, member);
        });
    }
}

// Snapshot before any manipulation
function captureCustomerOptions() {
    const customerSelect = document.getElementById('customerSelect');
    if (!customerSelect) return [];
    return Array.from(customerSelect.options)
        .filter(o => o.value !== '')
        .map(o => ({ value: o.value, text: o.text, phone: o.dataset.phone || '' }));
}

let allCustomerOptions = [];

function _enableCustSelect(sel) {
    if (!sel) return;
    sel.disabled = false;
    sel.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed', 'opacity-60');
    sel.classList.add('bg-white', 'text-slate-800');
}
function _disableCustSelect(sel) {
    if (!sel) return;
    sel.disabled = true;
    sel.classList.remove('bg-white', 'text-slate-800');
    sel.classList.add('bg-slate-100', 'text-slate-400', 'cursor-not-allowed', 'opacity-60');
}

function filterCustomersByGroup(gid) {
    const sel = document.getElementById('customerSelect');
    // On edit page, customerSelect doesn't exist - just handle members panel
    if (!sel) {
        // Edit page only shows members panel
        const panel = document.getElementById('groupMembersPanel');
        const list  = document.getElementById('groupMembersList');

        if (!gid || !panel || !list) return;

        const group = allGroups.find(g => String(g.id) === String(gid));
        if (!group) return;

        panel.style.display = '';
        const freeMembers = group.members.filter(m => !m.has_contract);
        list.innerHTML = freeMembers.length
            ? freeMembers.map(m =>
                `<div class="flex items-center justify-between px-3 py-2 text-xs cursor-pointer hover:bg-purple-50 transition-colors" data-cid="${m.customer_id}">
                    <span class="font-bold text-slate-700">${m.customer_name}${m.customer_phone ? '<span class="font-normal text-slate-400 mr-1"> ' + m.customer_phone + '</span>' : ''}</span>
                    <span class="font-black text-purple-600">${m.shares_count} نصيب</span>
                </div>`
              ).join('')
            : `<div class="px-3 py-4 text-xs text-center text-slate-400 font-semibold">✅ تم إصدار صكوك لجميع أعضاء المجموعة</div>`;
        return;
    }

    // Create page logic (with customer select)
    sel.innerHTML = '';

    if (!gid) {
        sel.appendChild(new Option('— اختر مجموعة أولاً —', ''));
        _disableCustSelect(sel);
        document.getElementById('groupMembersPanel').style.display = 'none';
        document.getElementById('standaloneRow').style.display     = '';
        document.getElementById('standaloneBadge').style.display   = 'none';
        return;
    }

    const group = allGroups.find(g => String(g.id) === String(gid));
    if (!group) return;

    _enableCustSelect(sel);
    document.getElementById('standaloneRow').style.display   = 'none';
    document.getElementById('standaloneBadge').style.display = 'none';

    sel.appendChild(new Option('-- اختر العميل من المجموعة --', ''));
    group.members.forEach(m => {
        if (m.has_contract) return; // skip already-contracted members entirely
        const label = m.customer_name + (m.customer_phone ? ' (' + m.customer_phone + ')' : '');
        const opt = new Option(label, m.customer_id);
        opt.dataset.sharesCount = m.shares_count;
        sel.appendChild(opt);
    });

    // Members panel
    const panel = document.getElementById('groupMembersPanel');
    const list  = document.getElementById('groupMembersList');
    panel.style.display = '';
    const freeMembers = group.members.filter(m => !m.has_contract);
    list.innerHTML = freeMembers.length
        ? freeMembers.map(m =>
            `<div class="flex items-center justify-between px-3 py-2 text-xs cursor-pointer hover:bg-purple-50 transition-colors member-pick" data-cid="${m.customer_id}">
                <span class="font-bold text-slate-700">${m.customer_name}${m.customer_phone ? '<span class="font-normal text-slate-400 mr-1"> ' + m.customer_phone + '</span>' : ''}</span>
                <span class="font-black text-purple-600">${m.shares_count} نصيب</span>
            </div>`
          ).join('')
        : `<div class="px-3 py-4 text-xs text-center text-slate-400 font-semibold">✅ تم إصدار صكوك لجميع أعضاء المجموعة</div>`;

    list.querySelectorAll('.member-pick').forEach(div => {
        div.addEventListener('click', function () {
            sel.value = this.dataset.cid;
            sel.dispatchEvent(new Event('change'));
        });
    });

    // Auto-select if single free member
    const free = group.members.filter(m => !m.has_contract);
    if (free.length === 1) {
        sel.value = free[0].customer_id;
        sel.dispatchEvent(new Event('change'));
    }
}

function autoFillFromGroup(gid) {
    const group = allGroups.find(g => String(g.id) === String(gid));
    if (!group) return;
    const row = document.querySelector('.item-row');

    // Lock share type immediately
    _lockShareType(row, group.share_type);

    // Set animal if group has one
    if (group.animal_id) {
        row.querySelector('.animal-select').value = group.animal_id;
    }

    // Set group id
    row.querySelector('.group-id-input').value = group.id;

    updateRow(row);
    updateGroupInfo(row);
    showGroupBanner(group);
}

function lockRowToMember(row, group, member) {
    _lockShareType(row, group.share_type);

    const sharesIn  = row.querySelector('.shares-count');
    sharesIn.value    = member.shares_count;
    sharesIn.max      = member.shares_count;
    sharesIn.readOnly = true;
    sharesIn.classList.add('bg-purple-50', 'text-purple-700');
    row.querySelector('.shares-limit-label').textContent = '🔒 محدد من المجموعة';

    updateRow(row);
}

function clearGroupLock() {
    const row = document.querySelector('.item-row');
    if (!row) return;

    _unlockShareType(row);

    const sharesIn  = row.querySelector('.shares-count');
    if (sharesIn) {
        sharesIn.readOnly = false;
        sharesIn.classList.remove('bg-purple-50', 'text-purple-700');
    }

    const limitsLabel = row.querySelector('.shares-limit-label');
    if (limitsLabel) limitsLabel.textContent = '';

    const groupInput = row.querySelector('.group-id-input');
    if (groupInput) groupInput.value = '';

    const groupInfo = row.querySelector('.group-info');
    if (groupInfo) groupInfo.style.display = 'none';

    hideGroupBanner();

    const gmp = document.getElementById('groupMembersPanel');
    if (gmp) gmp.style.display = 'none';
}

/* ══════════════════════════════════
   STANDALONE MODE
══════════════════════════════════ */

function enableStandaloneMode() {
    const sel = document.getElementById('customerSelect');
    if (!sel) return;

    sel.innerHTML = '';
    sel.appendChild(new Option('-- اختر العميل --', ''));

    if (allCustomerOptions && allCustomerOptions.length > 0) {
        allCustomerOptions.forEach(o => {
            const opt = new Option(o.text, o.value);
            opt.dataset.phone = o.phone;
            sel.appendChild(opt);
        });
    }
    _enableCustSelect(sel);

    const gf = document.getElementById('groupFilter');
    if (gf) gf.value = '';

    const gmp = document.getElementById('groupMembersPanel');
    if (gmp) gmp.style.display = 'none';

    const sr = document.getElementById('standaloneRow');
    if (sr) sr.style.display = 'none';

    const sb = document.getElementById('standaloneBadge');
    if (sb) sb.style.display = '';

    clearGroupLock();
}

function disableStandaloneMode() {
    const sel = document.getElementById('customerSelect');
    if (!sel) return;

    sel.innerHTML = '';
    sel.appendChild(new Option('— اختر مجموعة أولاً —', ''));
    _disableCustSelect(sel);

    document.getElementById('standaloneRow').style.display   = '';
    document.getElementById('standaloneBadge').style.display = 'none';
    document.getElementById('groupMembersPanel').style.display = 'none';
}

/* ══════════════════════════════════
   EVENT DELEGATION
══════════════════════════════════ */

function attachItemsBodyListeners() {
    const itemsBody = document.getElementById('itemsBody');
    if (!itemsBody) return;

    itemsBody.addEventListener('change', function (e) {
        const row = e.target.closest('.item-row');
        if (row) updateRow(row);
    });

    itemsBody.addEventListener('input', function (e) {
        const row = e.target.closest('.item-row');
        if (row && (e.target.classList.contains('shares-count') || e.target.classList.contains('animal-weight') || e.target.classList.contains('item-price'))) {
            if (e.target.classList.contains('shares-count')) {
                const max = parseInt(e.target.max) || 7;
                if (parseInt(e.target.value) > max) e.target.value = max;
            }
            updateRow(row);
        }
    });

    itemsBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row')) {
            if (document.querySelectorAll('.item-row').length > 1) {
                const row = e.target.closest('.item-row');
                row.style.opacity    = '0';
                row.style.transition = 'opacity .3s';
                setTimeout(() => { row.remove(); calcGrand(); }, 300);
            } else {
                alert('يجب أن يحتوي الصك على حيوان واحد على الأقل.');
            }
        }
    });
}

// Initialize calculation on page load
function initializeCalculations() {
    const itemRows = document.querySelectorAll('.item-row');
    if (itemRows.length > 0) {
        itemRows.forEach(row => {
            updateRow(row);
        });
    }
}

function initializeAllListeners() {
    attachPaymentAmountListener();
    attachGroupFilterListener();
    attachCustomerSelectListener();
    attachItemsBodyListeners();
    attachAddRowListener();
    attachFormSubmitListener();
    allCustomerOptions = captureCustomerOptions();
    initializeCalculations();

    // Initialize page data after listeners are attached
    initFromUrl();
    initEditView();

    // Attach edit customer form listener
    attachEditCustomerFormListener();
}

function attachEditCustomerFormListener() {
    const editCustomerForm = document.getElementById('editCustomerFormInline');
    if (editCustomerForm) {
        editCustomerForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!currentEditCustomerId) {
                alert('حدث خطأ: لم يتم تحديد العميل');
                return;
            }

            const nameInput = document.getElementById('editCustomerName');
            const phoneInput = document.getElementById('editCustomerPhone');

            if (!nameInput.value || !phoneInput.value) {
                alert('يجب إدخال الاسم والهاتف');
                return;
            }

            // Submit the form to update customer
            const url = `/udhiya/customers/${currentEditCustomerId}`;
            const formData = new FormData();
            formData.append('_method', 'PATCH');
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('name', nameInput.value);
            formData.append('phone', phoneInput.value);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json().catch(() => ({})))
            .then(data => {
                // Update customer select display
                const sel = document.getElementById('customerSelect');
                const name = nameInput.value;
                const phone = phoneInput.value;
                const option = sel.options[sel.selectedIndex];
                if (option) {
                    option.textContent = name + (phone ? ' (' + phone + ')' : '');
                    option.setAttribute('data-phone', phone);
                }

                closeEditCustomerModal();
                alert('تم تحديث بيانات العميل بنجاح');
            })
            .catch(err => {
                console.error('Error:', err);
                alert('حدث خطأ في الاتصال');
            });
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAllListeners);
} else {
    // Page already loaded
    initializeAllListeners();
}

/* ══════════════════════════════════
   ADD / REMOVE ROW
══════════════════════════════════ */

function attachAddRowListener() {
    const addRowBtn = document.getElementById('addRow');
    if (addRowBtn) {
        addRowBtn.addEventListener('click', function () {
            const tpl = document.querySelector('.item-row').cloneNode(true);

            // Reset inputs
            tpl.querySelectorAll('input:not([type=hidden])').forEach(i => {
                i.value = i.classList.contains('shares-count') ? 1 : '';
                i.readOnly = false;
                i.classList.remove('bg-purple-50', 'text-purple-700');
            });
            tpl.querySelectorAll('select').forEach(s => {
                s.selectedIndex = 0;
                s.disabled = false;
                s.classList.remove('bg-purple-50', 'text-purple-700', 'cursor-not-allowed', 'opacity-80');
            });

            // Reset hidden group + share_type
            const groupIn  = tpl.querySelector('.group-id-input');
            groupIn.value  = '';

            const shareSel    = tpl.querySelector('.share-type-select');
            const shareHidden = tpl.querySelector('.share-type-hidden');
            if (!shareSel.name && shareHidden.name) { shareSel.name = shareHidden.name; }
            shareHidden.name  = '';
            shareHidden.value = '';

            tpl.querySelector('.group-info').style.display = 'none';
            tpl.querySelector('.shares-limit-label').textContent = '';

            // Re-index names
            tpl.querySelectorAll('[name]').forEach(el => {
                el.name = el.name.replace(/items\[\d+\]/, 'items[' + rowIndex + ']');
            });
            // Also fix group-id-input name
            groupIn.name = groupIn.name.replace(/items\[\d+\]/, 'items[' + rowIndex + ']');

            tpl.classList.add('animate-fade-in');
            const itemsBody = document.getElementById('itemsBody');
            if (itemsBody) itemsBody.appendChild(tpl);
            rowIndex++;
        });
    }
}

/* ══════════════════════════════════
   AUTO-FILL FROM URL PARAMS
══════════════════════════════════ */

function initFromUrl() {
    const params      = new URLSearchParams(window.location.search);
    const initGroupId = params.get('group_id');
    const initCustId  = params.get('customer_id');
    if (!initGroupId) return;

    const groupFilter = document.getElementById('groupFilter');
    if (groupFilter) {
        groupFilter.value = initGroupId;
        filterCustomersByGroup(initGroupId);
        autoFillFromGroup(initGroupId);

        if (initCustId) {
            setTimeout(function () {
                const sel = document.getElementById('customerSelect');
                if (sel) {
                    sel.value = initCustId;
                    sel.dispatchEvent(new Event('change'));
                }
            }, 150);
        }
    }
}

function attachFormSubmitListener() {
    const contractForm = document.getElementById('contractForm');
    if (contractForm) {
        contractForm.addEventListener('submit', function () {
            var sel = document.getElementById('customerSelect');
            var hidden = document.getElementById('customerIdHidden');

            if (hidden) {
                if (sel && sel.value) {
                    // Create page: get from dropdown
                    hidden.value = sel.value;
                } else {
                    // Edit page: get from existing contract
                    const contractData = @json($contract);
                    if (contractData && contractData.customer_id) {
                        hidden.value = contractData.customer_id;
                    }
                }
            }
        });
    }
}

function initEditView() {
    const existingContract = @json($contract);

    // Check if it's standalone or grouped based on the first item
    const firstItem = existingContract.items[0];
    const groupFilter = document.getElementById('groupFilter');

    if (groupFilter) {
        if (firstItem && firstItem.group_id) {
            groupFilter.value = firstItem.group_id;
            filterCustomersByGroup(firstItem.group_id);
        } else {
            enableStandaloneMode();
        }

        // Set customer with a longer delay to ensure select is populated
        setTimeout(() => {
            const sel = document.getElementById('customerSelect');
            if (sel) {
                sel.value = existingContract.customer_id;
                document.getElementById('customerIdHidden').value = existingContract.customer_id;

                // Trigger change event to update button state and other listeners
                sel.dispatchEvent(new Event('change', { bubbles: true }));

                // initialize rows visually
                document.querySelectorAll('.item-row').forEach((row, idx) => {
                    const itemData = existingContract.items[idx];
                    if (itemData && itemData.group_id) {
                        // Find the group and member for this item
                        const g = allGroups.find(x => String(x.id) === String(itemData.group_id));
                        if (g) {
                            const member = g.members.find(m =>
                                String(m.customer_id) === String(existingContract.customer_id) &&
                                m.has_contract
                            );
                            if (member) {
                                lockRowToMember(row, g, member);
                            }
                        }
                    }
                    updateRow(row);
                });
                calcGrand();

                // Initialize financial summary display
                updateFinancialSummary();
            }
        }, 150);
    }
}

/* ══════════════════════════════════
   EDIT CUSTOMER MODAL
══════════════════════════════════ */
let currentEditCustomerId = null;

function openEditCustomerModal() {
    const sel = document.getElementById('customerSelect');
    let customerId, customerName, customerPhone;

    if (sel) {
        // Create page: get from dropdown
        customerId = sel.value;
        if (!customerId) {
            alert('اختر عميلاً أولاً');
            return;
        }
        const selectedOption = sel.options[sel.selectedIndex];
        const fullText = selectedOption.textContent;
        customerName = fullText.split('(')[0].trim();
        customerPhone = selectedOption.getAttribute('data-phone') || '';
    } else {
        // Edit page: get from display
        const nameEl = document.getElementById('customerNameDisplay');
        const phoneEl = document.getElementById('customerPhoneDisplay');
        if (!nameEl) return;
        customerId = @json($contract->customer_id ?? null);
        customerName = nameEl.textContent;
        customerPhone = phoneEl ? phoneEl.textContent : '';
    }

    currentEditCustomerId = customerId;

    // Open modal with customer data
    const modal = document.getElementById('editCustomerModal');
    if (modal) {
        document.getElementById('editCustomerName').value = customerName;
        document.getElementById('editCustomerPhone').value = customerPhone;
        modal.classList.remove('hidden');
    }
}

function closeEditCustomerModal() {
    const modal = document.getElementById('editCustomerModal');
    if (modal) modal.classList.add('hidden');
    currentEditCustomerId = null;
}

</script>

{{-- Edit Customer Modal --}}
<div id="editCustomerModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-lg max-w-md w-full overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-b from-indigo-50 to-white">
            <h6 class="text-lg font-black text-indigo-900 m-0">تعديل بيانات العميل</h6>
        </div>
        <form id="editCustomerFormInline" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">اسم العميل <span class="text-rose-500">*</span></label>
                <input type="text" id="editCustomerName" name="name" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">الهاتف <span class="text-rose-500">*</span></label>
                <input type="tel" id="editCustomerPhone" name="phone" required
                       class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
            </div>

            <div class="flex gap-3 pt-3">
                <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200/60 transition-all">
                    ✅ حفظ
                </button>
                <button type="button" onclick="closeEditCustomerModal()" class="flex-1 inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-black rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all">
                    ✕ إلغاء
                </button>
            </div>
        </form>
    </div>
</div>


<style>
    @keyframes fadeIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
    .animate-fade-in { animation: fadeIn .35s ease-out forwards; }
</style>
@endpush
