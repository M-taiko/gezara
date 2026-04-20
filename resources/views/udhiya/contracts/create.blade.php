@extends('layouts.master')
@section('page-header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-8 mt-2">
    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
            <span class="text-indigo-600 text-4xl">🧾</span> إصدار صك جديد
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">
            <a href="{{ route('udhiya.contracts.index') }}" class="text-indigo-500 hover:text-indigo-700 hover:underline">إدارة الصكوك</a> / إضافة
        </p>
    </div>
</div>
@endsection

@section('content')
<script>const allGroups = @json($groupsJson);</script>

<form action="{{ route('udhiya.contracts.store') }}" method="POST" id="contractForm"
      class="flex flex-col lg:flex-row gap-6 pb-16">
    @csrf

    {{-- ═══════════ RIGHT SIDEBAR ═══════════ --}}
    <div class="w-full lg:w-80 flex-shrink-0">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden lg:sticky lg:top-24">

            {{-- Step 1: Group --}}
            <div class="px-6 py-5 border-b border-purple-100 bg-gradient-to-b from-purple-50 to-white">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-black text-sm flex-shrink-0">1</div>
                    <h6 class="text-base font-black text-purple-900 m-0">اختر المجموعة</h6>
                </div>
                <select id="groupFilter"
                        class="w-full rounded-xl border border-purple-200 bg-white focus:bg-white focus:border-purple-400 focus:ring-2 focus:ring-purple-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
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
                {{-- Hidden input carries customer_id when select is disabled --}}
                <input type="hidden" id="customerIdHidden" name="customer_id" value="">
                <select id="customerSelect" disabled
                        class="w-full rounded-xl border border-slate-200 bg-slate-100 py-2.5 px-3 text-sm font-semibold text-slate-400 appearance-none cursor-not-allowed opacity-60 transition-all">
                    <option value="">— اختر مجموعة أولاً —</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" data-phone="{{ $c->phone }}">{{ $c->name }}{{ $c->phone ? ' ('.$c->phone.')' : '' }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Step 3: Details --}}
            <div class="px-6 py-5 border-b border-slate-100">
                <h6 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">تفاصيل إضافية</h6>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">رقم الصك <span class="text-slate-400 font-normal">(اختياري)</span></label>
                        <input type="text" name="contract_number" placeholder="سيتم إنشاء رقم تلقائي إن تركته فارغاً"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">تاريخ الذبح</label>
                        <input type="date" name="slaughter_day"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">ترتيب الذبح</label>
                        <input type="number" name="slaughter_order" min="1" placeholder="رقم الترتيب"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">📎 مرفقات الصك</label>
                        <input type="file" name="attachments[]" multiple
                               accept="image/*,.pdf"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm text-slate-800 transition-colors">
                        <p class="text-xs text-slate-500 mt-1">صورة التحويل البنكي أو الإيصال (JPG, PNG, PDF)</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">ملاحظات ووصايا التسليم</label>
                        <textarea name="notes" rows="3" placeholder="مثال: يود استلام الثلث..."
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- Step 4: Payment --}}
            <div class="px-6 py-5 border-b border-slate-100 bg-emerald-50/40">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-sm flex-shrink-0">4</div>
                    <h6 class="text-base font-black text-slate-800 m-0">دفعة عند الاعتماد</h6>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">
                            المبلغ المدفوع
                            <span class="text-slate-400 font-normal">(اختياري — اتركه صفراً إن لم يدفع الآن)</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="payment_amount" id="paymentAmount"
                                   min="0" step="0.01" value="0" placeholder="0.00"
                                   class="w-full rounded-xl border border-emerald-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 pl-12 text-sm font-black text-emerald-800 transition-colors">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-400 text-xs font-bold">ج.م</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">طريقة الدفع</label>
                        <select name="payment_method"
                                class="w-full rounded-xl border border-slate-200 bg-white focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100 py-2.5 px-3 text-sm font-semibold text-slate-800 transition-colors">
                            <option value="cash">نقداً</option>
                            <option value="bank">تحويل بنكي</option>
                            <option value="check">شيك</option>
                        </select>
                    </div>
                    {{-- Live summary --}}
                    <div id="paymentSummary" style="display:none;"
                         class="bg-white rounded-xl border border-emerald-200 p-3 text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-semibold">إجمالي الصك</span>
                            <span id="summaryTotal" class="font-black text-slate-800">—</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-semibold">المدفوع الآن</span>
                            <span id="summaryPaid" class="font-black text-emerald-700">—</span>
                        </div>
                        <div class="flex justify-between border-t border-emerald-100 pt-1 mt-1">
                            <span class="text-slate-500 font-semibold">المتبقي</span>
                            <span id="summaryRemaining" class="font-black text-rose-600">—</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="px-6 py-5 bg-slate-50/80 space-y-3">
                <button type="submit" name="print_after" value="0"
                        class="w-full inline-flex justify-center items-center gap-2 px-6 py-3.5 text-base font-black rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 shadow-lg shadow-emerald-200/60 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    اعتماد الصك
                </button>
                <button type="submit" name="print_after" value="1"
                        class="w-full inline-flex justify-center items-center gap-2 px-6 py-3 text-sm font-black rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200/60 transition-all">
                    🖨️ اعتماد وطباعة الفاتورة
                </button>
                <a href="{{ route('udhiya.contracts.index') }}"
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
                                <th class="px-4 py-3 min-w-[200px]">الحيوان</th>
                                <th class="px-4 py-3 w-36 share-type-header">نوع الحصة</th>
                                <th class="px-4 py-3 w-24 count-header">العدد</th>
                                <th class="px-4 py-3 w-24 weight-header" style="display:none;">الوزن (كجم)</th>
                                <th class="px-4 py-3 w-28 text-center">سعر الوحدة</th>
                                <th class="px-4 py-3 w-28 text-center">الإجمالي (ج.م)</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-slate-50">
                            <tr class="item-row bg-white hover:bg-slate-50/30 transition-colors">
                                {{-- Animal --}}
                                <td class="px-4 py-3">
                                    <select name="items[0][animal_id]"
                                            class="animal-select w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-3 text-sm font-bold text-slate-800 transition-colors">
                                        <option value="">-- اختر الحيوان --</option>
                                        @foreach($animals as $animal)
                                        <option value="{{ $animal->id }}"
                                            data-grouped="{{ $animal->is_grouped ? 1 : 0 }}"
                                            data-share-type="{{ $animal->shareSetting->share_type ?? '' }}"
                                            data-remaining="{{ $animal->shareSetting->remaining_shares ?? 1 }}"
                                            data-weight="{{ $animal->weight ?? 0 }}"
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
                                    {{-- Hidden group_id (set by JS) --}}
                                    <input type="hidden" name="items[0][group_id]" class="group-id-input" value="">
                                    {{-- Group progress indicator --}}
                                    <div class="group-info mt-2" style="display:none;">
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="group-progress-bar bg-emerald-500 h-1.5 rounded-full transition-all duration-300" style="width:0%"></div>
                                        </div>
                                        <div class="group-slots-label text-xs text-slate-400 mt-1 font-semibold"></div>
                                    </div>
                                </td>
                                {{-- Share type --}}
                                <td class="px-4 py-3 share-type-cell">
                                    <select name="items[0][share_type]"
                                            class="share-type-select w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-3 text-sm font-bold text-slate-800 transition-colors">
                                        <option value="full">كامل</option>
                                        <option value="seven">سُبع (7)</option>
                                        <option value="six">سُدس (6)</option>
                                        <option value="five">خُمس (5)</option>
                                        <option value="quarter">ربع (4)</option>
                                        <option value="third">ثُلث (3)</option>
                                        <option value="half">نصف (2)</option>
                                    </select>
                                    {{-- Carries value when select is disabled --}}
                                    <input type="hidden" class="share-type-hidden" name="" value="">
                                </td>
                                {{-- Count (for group mode) --}}
                                <td class="px-4 py-3 count-cell">
                                    <input type="number" name="items[0][shares_count]"
                                           class="shares-count w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-2 text-sm font-bold text-center text-slate-800 transition-colors"
                                           min="1" max="7" value="1">
                                    <div class="shares-limit-label text-xs font-bold text-slate-400 mt-1 text-center"></div>
                                </td>
                                {{-- Weight (for standalone mode) --}}
                                <td class="px-4 py-3 weight-cell" style="display:none;">
                                    <input type="number" name="items[0][weight]"
                                           class="animal-weight w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-2 text-sm font-bold text-center text-slate-800 transition-colors"
                                           step="0.01" min="0.01" placeholder="الوزن">
                                    <div class="text-xs font-bold text-slate-400 mt-1 text-center">كجم</div>
                                </td>
                                {{-- Unit price --}}
                                <td class="px-4 py-3 text-center">
                                    <input type="number" name="items[0][unit_price]"
                                            class="item-price w-full rounded-xl border border-slate-200 bg-white focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 py-2 px-2 text-sm font-black text-slate-800 text-center transition-colors"
                                            step="0.01" placeholder="0.00" required>
                                </td>
                                {{-- Total --}}
                                <td class="px-4 py-3 text-center">
                                    <input type="number" name="items[0][total_price]"
                                           class="item-total w-full border-0 bg-transparent py-1 text-sm font-black text-indigo-600 text-center"
                                           step="0.01" readonly placeholder="—">
                                </td>
                                {{-- Delete --}}
                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                            class="remove-row w-8 h-8 rounded-lg bg-rose-50 text-rose-400 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-colors mx-auto">
                                        <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
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
let rowIndex = 1;

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

    if (!gid) { infoDiv.style.display = 'none'; return; }

    const g = allGroups.find(x => String(x.id) === String(gid));
    if (!g)  { infoDiv.style.display = 'none'; return; }

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
        if (parseInt(sharesIn.value) > maxAllowed) sharesIn.value = maxAllowed;
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

    if (!sharesIn.readOnly && sharesIn.parentElement.parentElement.style.display !== 'none') {
        const typeMax = SHARE_MAX[shareType] || 7;
        sharesIn.max  = typeMax;
        row.querySelector('.shares-limit-label').textContent = 'أقصاها: ' + typeMax;
    }

    updateGroupInfo(row);
    calcGrand();
}

function calcGrand() {
    let grand = 0;
    document.querySelectorAll('.item-total').forEach(el => grand += parseFloat(el.value) || 0);
    document.getElementById('grandTotal').textContent =
        Number(grand).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    updatePaymentSummary(grand);
}

function updatePaymentSummary(total) {
    const paid      = parseFloat(document.getElementById('paymentAmount').value) || 0;
    const remaining = total - paid;
    const summary   = document.getElementById('paymentSummary');
    if (total <= 0 && paid <= 0) { summary.style.display = 'none'; return; }
    summary.style.display = '';
    document.getElementById('summaryTotal').textContent     = Number(total).toLocaleString(undefined, {minimumFractionDigits: 0}) + ' ج.م';
    document.getElementById('summaryPaid').textContent      = Number(paid).toLocaleString(undefined, {minimumFractionDigits: 0}) + ' ج.م';
    document.getElementById('summaryRemaining').textContent = Number(remaining).toLocaleString(undefined, {minimumFractionDigits: 0}) + ' ج.م';
}

document.getElementById('paymentAmount').addEventListener('input', function () {
    const grand = parseFloat(document.getElementById('grandTotal').textContent.replace(/,/g, '')) || 0;
    updatePaymentSummary(grand);
});

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

document.getElementById('groupFilter').addEventListener('change', function () {
    const gid = this.value;
    filterCustomersByGroup(gid);
    if (gid) autoFillFromGroup(gid);
    else     clearGroupLock();
});

document.getElementById('customerSelect').addEventListener('change', function () {
    // Always mirror value to hidden input (select may be disabled)
    document.getElementById('customerIdHidden').value = this.value;

    const gid = document.getElementById('groupFilter').value;
    if (!gid) return;
    const group  = allGroups.find(g => String(g.id) === String(gid));
    if (!group)  return;
    const member = group.members.find(m => String(m.customer_id) === String(this.value));
    if (!member) return;
    lockRowToMember(document.querySelector('.item-row'), group, member);
});

// Snapshot before any manipulation
const allCustomerOptions = Array.from(document.getElementById('customerSelect').options)
    .filter(o => o.value !== '')
    .map(o => ({ value: o.value, text: o.text, phone: o.dataset.phone || '' }));

function _enableCustSelect(sel) {
    sel.disabled = false;
    sel.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed', 'opacity-60');
    sel.classList.add('bg-white', 'text-slate-800');
}
function _disableCustSelect(sel) {
    sel.disabled = true;
    sel.classList.remove('bg-white', 'text-slate-800');
    sel.classList.add('bg-slate-100', 'text-slate-400', 'cursor-not-allowed', 'opacity-60');
}

function filterCustomersByGroup(gid) {
    const sel = document.getElementById('customerSelect');
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

    // Set animal
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

    _unlockShareType(row);

    const sharesIn  = row.querySelector('.shares-count');
    sharesIn.readOnly = false;
    sharesIn.classList.remove('bg-purple-50', 'text-purple-700');
    row.querySelector('.shares-limit-label').textContent = '';

    row.querySelector('.group-id-input').value = '';
    row.querySelector('.group-info').style.display = 'none';

    hideGroupBanner();
    document.getElementById('groupMembersPanel').style.display = 'none';
}

/* ══════════════════════════════════
   STANDALONE MODE
══════════════════════════════════ */

function enableStandaloneMode() {
    const sel = document.getElementById('customerSelect');
    sel.innerHTML = '';
    sel.appendChild(new Option('-- اختر العميل --', ''));
    allCustomerOptions.forEach(o => {
        const opt = new Option(o.text, o.value);
        opt.dataset.phone = o.phone;
        sel.appendChild(opt);
    });
    _enableCustSelect(sel);

    document.getElementById('groupFilter').value              = '';
    document.getElementById('groupMembersPanel').style.display = 'none';
    document.getElementById('standaloneRow').style.display    = 'none';
    document.getElementById('standaloneBadge').style.display  = '';

    // Switch to weight/standalone mode
    switchToStandaloneMode();
    clearGroupLock();
}

function switchToStandaloneMode() {
    // Hide share type and count columns, show weight column
    document.querySelectorAll('.share-type-header, .count-header, .share-type-cell, .count-cell').forEach(el => {
        el.style.display = 'none';
    });
    document.querySelectorAll('.weight-header, .weight-cell').forEach(el => {
        el.style.display = '';
    });

    // Populate weight from animal if selected, make unit_price free input
    const row = document.querySelector('.item-row');
    if (row) {
        const animalSel = row.querySelector('.animal-select');
        if (animalSel.value) {
            const opt = animalSel.selectedOptions[0];
            row.querySelector('.animal-weight').value = opt.dataset.weight || '';
        }
    }
}

function switchToGroupMode() {
    // Show share type and count columns, hide weight column
    document.querySelectorAll('.share-type-header, .count-header, .share-type-cell, .count-cell').forEach(el => {
        el.style.display = '';
    });
    document.querySelectorAll('.weight-header, .weight-cell').forEach(el => {
        el.style.display = 'none';
    });
}

function disableStandaloneMode() {
    const sel = document.getElementById('customerSelect');
    sel.innerHTML = '';
    sel.appendChild(new Option('— اختر مجموعة أولاً —', ''));
    _disableCustSelect(sel);

    document.getElementById('standaloneRow').style.display   = '';
    document.getElementById('standaloneBadge').style.display = 'none';
    document.getElementById('groupMembersPanel').style.display = 'none';

    // Switch back to group mode
    switchToGroupMode();
}

/* ══════════════════════════════════
   EVENT DELEGATION
══════════════════════════════════ */

document.getElementById('itemsBody').addEventListener('change', function (e) {
    const row = e.target.closest('.item-row');
    if (row) updateRow(row);
});

document.getElementById('itemsBody').addEventListener('input', function (e) {
    const row = e.target.closest('.item-row');
    if (row && (e.target.classList.contains('shares-count') || e.target.classList.contains('animal-weight') || e.target.classList.contains('item-price'))) {
        if (e.target.classList.contains('shares-count')) {
            const max = parseInt(e.target.max) || 7;
            if (parseInt(e.target.value) > max) e.target.value = max;
        }
        updateRow(row);
    }
});

// Initialize calculation on page load
function initializeCalculations() {
    const itemRows = document.querySelectorAll('.item-row');
    if (itemRows.length > 0) {
        itemRows.forEach(row => {
            updateRow(row);
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCalculations);
} else {
    // Page already loaded
    initializeCalculations();
}

/* ══════════════════════════════════
   ADD / REMOVE ROW
══════════════════════════════════ */

document.getElementById('addRow').addEventListener('click', function () {
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

    // Apply current mode's visibility to new row
    const currentMode = document.querySelector('.weight-cell').style.display;
    if (currentMode === 'none') {
        // Group mode
        tpl.querySelectorAll('.share-type-cell, .count-cell').forEach(el => el.style.display = '');
        tpl.querySelectorAll('.weight-cell').forEach(el => el.style.display = 'none');
    } else {
        // Standalone mode
        tpl.querySelectorAll('.share-type-cell, .count-cell').forEach(el => el.style.display = 'none');
        tpl.querySelectorAll('.weight-cell').forEach(el => el.style.display = '');
    }

    // Re-index names
    tpl.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/items\[\d+\]/, 'items[' + rowIndex + ']');
    });
    // Also fix group-id-input name
    groupIn.name = groupIn.name.replace(/items\[\d+\]/, 'items[' + rowIndex + ']');

    tpl.classList.add('animate-fade-in');
    document.getElementById('itemsBody').appendChild(tpl);
    rowIndex++;
});

document.getElementById('itemsBody').addEventListener('click', function (e) {
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

/* ══════════════════════════════════
   AUTO-FILL FROM URL PARAMS
══════════════════════════════════ */

(function initFromUrl() {
    const params      = new URLSearchParams(window.location.search);
    const initGroupId = params.get('group_id');
    const initCustId  = params.get('customer_id');
    if (!initGroupId) return;

    const groupFilter = document.getElementById('groupFilter');
    groupFilter.value = initGroupId;
    filterCustomersByGroup(initGroupId);
    autoFillFromGroup(initGroupId);

    if (initCustId) {
        setTimeout(function () {
            const sel = document.getElementById('customerSelect');
            sel.value = initCustId;
            sel.dispatchEvent(new Event('change'));
        }, 150);
    }
})();

// Ensure hidden customer_id is always synced before submit
document.getElementById('contractForm').addEventListener('submit', function () {
    var sel = document.getElementById('customerSelect');
    var hidden = document.getElementById('customerIdHidden');
    if (sel && hidden && sel.value) {
        hidden.value = sel.value;
    }
});
</script>
<style>
    @keyframes fadeIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
    .animate-fade-in { animation: fadeIn .35s ease-out forwards; }
</style>
@endpush
