@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">مشترى جديد</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.purchases.index') }}">المشتريات</a></li>
            <li class="breadcrumb-item active">جديد</li>
        </ol>
    </div>
</div>
@endsection
@section('content')
<form action="{{ route('udhiya.purchases.store') }}" method="POST" id="purchaseForm">
    @csrf
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header"><h6 class="card-title mb-0">أصناف المشترى</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>المنتج</th>
                                    <th style="width:80px">الكمية</th>
                                    <th style="width:100px">الوزن (كجم)</th>
                                    <th style="width:130px">سعر الوحدة (ج.م)</th>
                                    <th style="width:130px">الإجمالي</th>
                                    <th style="width:40px"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td>
                                        <select name="items[0][product_id]" class="form-control form-control-sm" required>
                                            <option value="">-- اختر المنتج --</option>
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->mainCategory->name }} — {{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm item-qty" min="1" value="1" required></td>
                                    <td><input type="number" name="items[0][weight]" class="form-control form-control-sm" step="0.01" min="0"></td>
                                    <td><input type="number" name="items[0][cost_per_unit]" class="form-control form-control-sm item-price" step="0.01" min="0" required></td>
                                    <td><input type="number" name="items[0][total]" class="form-control form-control-sm item-total" step="0.01" readonly></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-times"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="addRow" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus ml-1"></i> إضافة صنف
                    </button>
                </div>
                <div class="card-footer text-left">
                    <strong>الإجمالي: <span id="grandTotal">0.00</span> ج.م</strong>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header"><h6 class="card-title mb-0">بيانات المشترى</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>المورد <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="form-control" required>
                            <option value="">-- اختر المورد --</option>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>التاريخ <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>المدفوع (ج.م)</label>
                        <input type="number" name="paid" class="form-control" min="0" step="0.01" value="0">
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save ml-1"></i> حفظ المشترى
                    </button>
                    <a href="{{ route('udhiya.purchases.index') }}" class="btn btn-outline-secondary btn-block mt-2">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section('js')
<script>
let rowIndex = 1;
const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->mainCategory->name . ' — ' . $p->name]));

function calcRow(row) {
    const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const total = qty * price;
    row.querySelector('.item-total').value = total.toFixed(2);
    calcGrand();
}

function calcGrand() {
    let grand = 0;
    document.querySelectorAll('.item-total').forEach(el => grand += parseFloat(el.value) || 0);
    document.getElementById('grandTotal').textContent = grand.toFixed(2);
}

document.getElementById('itemsBody').addEventListener('input', function(e) {
    const row = e.target.closest('.item-row');
    if (row) calcRow(row);
});

document.getElementById('addRow').addEventListener('click', function() {
    const template = document.querySelector('.item-row').cloneNode(true);
    template.querySelectorAll('input').forEach(i => i.value = i.type === 'number' ? (i.name.includes('quantity') ? 1 : '') : '');
    template.querySelectorAll('select').forEach(s => s.value = '');
    template.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/items\[\d+\]/, 'items[' + rowIndex + ']');
    });
    document.getElementById('itemsBody').appendChild(template);
    rowIndex++;
});

document.getElementById('itemsBody').addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) { e.target.closest('.item-row').remove(); calcGrand(); }
    }
});
</script>
@endsection
