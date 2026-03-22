@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">صك جديد</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.contracts.index') }}">الصكوك</a></li>
            <li class="breadcrumb-item active">جديد</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<script>const allGroups = @json($groupsJson);</script>

<form action="{{ route('udhiya.contracts.store') }}" method="POST" id="contractForm">
    @csrf
    <div class="row">

        {{-- ========== ITEMS TABLE ========== --}}
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header"><h6 class="card-title mb-0">الحيوانات والأنصبة</h6></div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0" id="itemsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>الحيوان</th>
                                    <th style="width:145px">المجموعة</th>
                                    <th style="width:130px">نوع الحصة</th>
                                    <th style="width:80px">الأنصبة</th>
                                    <th style="width:115px">سعر الوحدة</th>
                                    <th style="width:115px">الإجمالي</th>
                                    <th style="width:36px"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][animal_id]" class="form-control form-control-sm animal-select" required>
                                            <option value="">-- اختر الحيوان --</option>
                                            @foreach($animals as $animal)
                                            <option value="{{ $animal->id }}"
                                                data-grouped="{{ $animal->is_grouped ? 1 : 0 }}"
                                                data-share-type="{{ $animal->shareSetting->share_type ?? '' }}"
                                                data-remaining="{{ $animal->shareSetting->remaining_shares ?? 1 }}"
                                                data-price-full="{{ $animal->price_full ?? 0 }}"
                                                data-price-seven="{{ $animal->price_seven ?? 0 }}"
                                                data-price-five="{{ $animal->price_five ?? 0 }}"
                                                data-price-quarter="{{ $animal->price_quarter ?? 0 }}"
                                                data-price-half="{{ $animal->price_half ?? 0 }}">
                                                {{ $animal->code }} — {{ $animal->product->name }}
                                                @if($animal->is_grouped)({{ $animal->shareSetting->remaining_shares ?? 0 }} نصيب متبقي)@else(كامل)@endif
                                            </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="items[0][group_id]" class="form-control form-control-sm group-select">
                                            <option value="">— بلا مجموعة —</option>
                                        </select>
                                        {{-- Group progress badge --}}
                                        <div class="group-info mt-1" style="display:none;">
                                            <div class="progress" style="height:6px;">
                                                <div class="group-progress-bar progress-bar bg-success" style="width:0%"></div>
                                            </div>
                                            <small class="group-slots-label text-muted"></small>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="items[0][share_type]" class="form-control form-control-sm share-type-select" required>
                                            <option value="full">كامل</option>
                                            <option value="seven">سُبع (7)</option>
                                            <option value="five">خُمس (5)</option>
                                            <option value="quarter">ربع (4)</option>
                                            <option value="half">نصف (2)</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][shares_count]"
                                               class="form-control form-control-sm shares-count"
                                               min="1" max="7" value="1" required>
                                        <small class="shares-limit-label text-muted" style="font-size:.7rem;"></small>
                                    </td>
                                    <td><input type="number" name="items[0][unit_price]"  class="form-control form-control-sm item-price"  step="0.01" min="0" readonly></td>
                                    <td><input type="number" name="items[0][total_price]" class="form-control form-control-sm item-total"  step="0.01" readonly></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-times"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="addRow" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-plus ml-1"></i> إضافة حيوان
                    </button>
                </div>
                <div class="card-footer text-left">
                    <strong>الإجمالي: <span id="grandTotal">0.00</span> ج.م</strong>
                </div>
            </div>
        </div>

        {{-- ========== CONTRACT META ========== --}}
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header"><h6 class="card-title mb-0">بيانات الصك</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>العميل <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-control" required>
                            <option value="">-- اختر العميل --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>يوم الذبح</label>
                        <input type="date" name="slaughter_day" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>ترتيب الذبح</label>
                        <input type="number" name="slaughter_order" class="form-control" min="1">
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save ml-1"></i> إنشاء الصك
                    </button>
                    <a href="{{ route('udhiya.contracts.index') }}"
                       class="btn btn-outline-secondary btn-block mt-2">إلغاء</a>
                </div>
            </div>
        </div>

    </div>
</form>
@endsection

@section('js')
<script>
let rowIndex = 1;

// Max shares per share-type
const SHARE_MAX = { full: 1, seven: 7, five: 5, quarter: 4, half: 2 };
const SHARE_LABEL_AR = { full: 'كامل (1)', seven: 'سُبع (7)', five: 'خُمس (5)', quarter: 'ربع (4)', half: 'نصف (2)' };

/* ─── Rebuild group dropdown for a row ─── */
function rebuildGroupSelect(row, animalId, shareType) {
    const groupSel   = row.querySelector('.group-select');
    const prevVal    = groupSel.value;
    groupSel.innerHTML = '<option value="">— بلا مجموعة —</option>';

    allGroups.forEach(function(g) {
        if (String(g.animal_id) !== String(animalId)) return;
        if (g.share_type !== shareType) return;
        if (g.remaining <= 0) return;          // full groups hidden

        const opt = document.createElement('option');
        opt.value       = g.id;
        opt.textContent = g.name + '  —  ' + g.used + '/' + g.total + ' نصيب';
        opt.dataset.gid = g.id;
        if (String(g.id) === String(prevVal)) opt.selected = true;
        groupSel.appendChild(opt);
    });

    updateGroupInfo(row);
}

/* ─── Show/hide progress bar under group dropdown ─── */
function updateGroupInfo(row) {
    const groupSel   = row.querySelector('.group-select');
    const infoDiv    = row.querySelector('.group-info');
    const bar        = row.querySelector('.group-progress-bar');
    const label      = row.querySelector('.group-slots-label');
    const sharesInput= row.querySelector('.shares-count');

    const gid = groupSel.value;
    if (!gid) {
        infoDiv.style.display = 'none';
        return;
    }

    const g = allGroups.find(x => String(x.id) === String(gid));
    if (!g) { infoDiv.style.display = 'none'; return; }

    const pct = g.total > 0 ? Math.round((g.used / g.total) * 100) : 0;
    bar.style.width      = pct + '%';
    bar.className        = 'group-progress-bar progress-bar ' + (g.remaining === 0 ? 'bg-danger' : (pct > 60 ? 'bg-warning' : 'bg-success'));
    label.textContent    = g.used + ' مشغول / ' + g.total + ' نصيب  —  متبقي: ' + g.remaining;
    infoDiv.style.display = '';

    // Clamp shares_count to group remaining
    const maxAllowed = Math.min(SHARE_MAX[row.querySelector('.share-type-select').value] || 7, g.remaining);
    sharesInput.max = maxAllowed;
    sharesInput.title = 'الحد الأقصى: ' + maxAllowed + ' نصيب';
    row.querySelector('.shares-limit-label').textContent = 'الحد: ' + maxAllowed;
    if (parseInt(sharesInput.value) > maxAllowed) {
        sharesInput.value = maxAllowed;
    }
}

/* ─── Main row update (price + group + limit) ─── */
function updateRow(row) {
    const animalSel  = row.querySelector('.animal-select');
    const opt        = animalSel.selectedOptions[0];
    const shareType  = row.querySelector('.share-type-select').value;
    const sharesInput= row.querySelector('.shares-count');

    if (!opt || !opt.value) return;

    // Price
    const priceKey = { full:'priceFull', seven:'priceSeven', five:'priceFive', quarter:'priceQuarter', half:'priceHalf' };
    const unitPrice = parseFloat(opt.dataset[priceKey[shareType]]) || 0;
    const sharesCount = parseInt(sharesInput.value) || 1;

    row.querySelector('.item-price').value = unitPrice.toFixed(2);
    row.querySelector('.item-total').value = (unitPrice * sharesCount).toFixed(2);

    // Enforce max based on share type (before group)
    const typeMax = SHARE_MAX[shareType] || 7;
    sharesInput.max = typeMax;
    row.querySelector('.shares-limit-label').textContent = 'الحد: ' + typeMax;

    // Rebuild groups filtered by animal + share_type
    rebuildGroupSelect(row, opt.value, shareType);

    calcGrand();
}

/* ─── When a group is chosen: sync share_type + enforce max ─── */
function onGroupChange(row) {
    const groupSel  = row.querySelector('.group-select');
    const gid       = groupSel.value;

    if (gid) {
        const g = allGroups.find(x => String(x.id) === String(gid));
        if (g) {
            // Force share type to match group
            const shareTypeSel = row.querySelector('.share-type-select');
            if (shareTypeSel.value !== g.share_type) {
                shareTypeSel.value = g.share_type;
            }
            // Trigger price + max update
            updateRow(row);
        }
    }

    updateGroupInfo(row);
    calcGrand();
}

function calcGrand() {
    let grand = 0;
    document.querySelectorAll('.item-total').forEach(el => grand += parseFloat(el.value) || 0);
    document.getElementById('grandTotal').textContent = grand.toFixed(2);
}

/* ─── Event delegation ─── */
document.getElementById('itemsBody').addEventListener('change', function(e) {
    const row = e.target.closest('.item-row');
    if (!row) return;
    if (e.target.classList.contains('group-select')) {
        onGroupChange(row);
    } else {
        updateRow(row);
    }
});

document.getElementById('itemsBody').addEventListener('input', function(e) {
    const row = e.target.closest('.item-row');
    if (row && e.target.classList.contains('shares-count')) {
        // Clamp to max
        const max = parseInt(e.target.max) || 7;
        if (parseInt(e.target.value) > max) e.target.value = max;
        updateRow(row);
    }
});

/* ─── Add Row ─── */
document.getElementById('addRow').addEventListener('click', function() {
    const tpl = document.querySelector('.item-row').cloneNode(true);

    tpl.querySelectorAll('input').forEach(i => {
        i.value = (i.classList.contains('shares-count')) ? 1 : '';
    });
    tpl.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    tpl.querySelector('.group-select').innerHTML = '<option value="">— بلا مجموعة —</option>';
    tpl.querySelector('.group-info').style.display = 'none';
    tpl.querySelector('.shares-limit-label').textContent = '';

    tpl.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/items\[\d+\]/, 'items[' + rowIndex + ']');
    });

    document.getElementById('itemsBody').appendChild(tpl);
    rowIndex++;
});

/* ─── Remove Row ─── */
document.getElementById('itemsBody').addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        if (document.querySelectorAll('.item-row').length > 1) {
            e.target.closest('.item-row').remove();
            calcGrand();
        }
    }
});
</script>
@endsection
