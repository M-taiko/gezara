@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">الحيوانات — المخزون</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">الأضاحي</a></li>
            <li class="breadcrumb-item active">الحيوانات</li>
        </ol>
    </div>
    <div class="page-rightheader d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#manageProductsModal">
            <i class="las la-list mr-1"></i> إدارة النوعيات
        </button>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAnimalModal">
            <i class="las la-plus mr-1"></i> إضافة حيوان
        </button>
    </div>
</div>
@endsection

@section('content')

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row align-items-end">
            <div class="col-md-3">
                <label class="small">بحث بالكود</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}" placeholder="كود الحيوان">
            </div>
            <div class="col-md-3">
                <label class="small">الفئة</label>
                <select name="category" class="form-control form-control-sm">
                    <option value="">كل الفئات</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="small">المخزن</label>
                <select name="warehouse" class="form-control form-control-sm">
                    <option value="">الكل</option>
                    @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" @selected(request('warehouse') == $wh->id)>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="small">الحالة</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">الكل</option>
                    @foreach(\App\Models\Animal::STATUS_LABELS as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') == $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm btn-block">بحث</button>
                @if(request()->hasAny(['search','category','warehouse','status']))
                <a href="{{ route('udhiya.animals.index') }}" class="btn btn-light btn-sm">مسح</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th>الكود</th>
                    <th>النوع</th>
                    <th>الفئة</th>
                    <th>المخزن</th>
                    <th>الوزن</th>
                    <th>التكلفة</th>
                    <th>نظام</th>
                    <th>الحالة</th>
                    <th style="width:110px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($animals as $animal)
                <tr>
                    <td><strong>{{ $animal->code }}</strong></td>
                    <td>{{ $animal->product->name }}</td>
                    <td>{{ $animal->product->mainCategory->name }}</td>
                    <td>{{ $animal->warehouse->name }}</td>
                    <td>{{ $animal->weight ? $animal->weight . ' كجم' : '—' }}</td>
                    <td>{{ number_format($animal->cost, 2) }} ج.م</td>
                    <td>
                        @if($animal->is_grouped)
                            <span class="badge badge-info">{{ \App\Models\AnimalShareSetting::SHARE_TYPE_LABELS[$animal->shareSetting->share_type] ?? '' }}</span>
                        @else
                            <span class="badge badge-secondary">كامل</span>
                        @endif
                    </td>
                    <td>
                        @php $labels = \App\Models\Animal::STATUS_LABELS; @endphp
                        <span class="badge badge-{{ ['available'=>'success','partially_allocated'=>'warning','fully_allocated'=>'primary','slaughtered'=>'dark'][$animal->status] ?? 'secondary' }}">
                            {{ $labels[$animal->status] ?? $animal->status }}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-warning btn-transfer"
                                data-id="{{ $animal->id }}"
                                data-code="{{ $animal->code }}"
                                data-warehouse="{{ $animal->warehouse->name }}"
                                data-toggle="modal" data-target="#transferModal"
                                title="نقل">
                            <i class="las la-exchange-alt"></i>
                        </button>
                        <a href="{{ route('udhiya.animals.show', $animal) }}"
                           class="btn btn-sm btn-outline-info" title="تفاصيل">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">لا توجد حيوانات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($animals->hasPages())
    <div class="card-footer">{{ $animals->links() }}</div>
    @endif
</div>

{{-- ===================== ADD ANIMAL MODAL ===================== --}}
<div class="modal fade" id="addAnimalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('udhiya.animals.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-plus-circle mr-1"></i> إضافة حيوان جديد</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>كود الحيوان <span class="text-danger">*</span></label>
                                <input type="text" name="code"
                                       class="form-control @error('code') is-invalid @enderror"
                                       value="{{ old('code') }}"
                                       placeholder="مثال: 2026-BQR-0010" required>
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>النوعية <span class="text-danger">*</span></label>
                                <select name="product_id"
                                        class="form-control @error('product_id') is-invalid @enderror" required>
                                    <option value="">-- اختر النوعية --</option>
                                    @foreach($products->groupBy(fn($p) => $p->mainCategory->name) as $catName => $prods)
                                    <optgroup label="{{ $catName }}">
                                        @foreach($prods as $prod)
                                        <option value="{{ $prod->id }}" @selected(old('product_id') == $prod->id)>
                                            {{ $prod->name }}
                                        </option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>المخزن / المكان <span class="text-danger">*</span></label>
                                <select name="warehouse_id"
                                        class="form-control @error('warehouse_id') is-invalid @enderror" required>
                                    <option value="">-- اختر --</option>
                                    @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" @selected(old('warehouse_id') == $wh->id)>{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>المورد</label>
                                <select name="supplier_id" class="form-control">
                                    <option value="">— بلا مورد —</option>
                                    @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" @selected(old('supplier_id') == $sup->id)>{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>الوزن (كجم)</label>
                                <input type="number" name="weight" class="form-control"
                                       step="0.1" min="0" value="{{ old('weight') }}" placeholder="اختياري">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>التكلفة (ج.م)</label>
                                <input type="number" name="cost" class="form-control"
                                       step="0.01" min="0" value="{{ old('cost') }}" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-0">
                                <label>ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="las la-save mr-1"></i> حفظ الحيوان
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===================== TRANSFER MODAL ===================== --}}
<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form id="transferForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-exchange-alt mr-1"></i> نقل حيوان</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3 p-2 bg-light rounded">
                        <strong>الحيوان:</strong> <span id="transferAnimalCode" class="text-primary font-weight-bold">—</span><br>
                        <strong>المخزن الحالي:</strong> <span id="transferCurrentWarehouse">—</span>
                    </p>
                    <div class="form-group">
                        <label>نقل إلى <span class="text-danger">*</span></label>
                        <select name="to_warehouse_id" class="form-control" required>
                            <option value="">-- اختر المكان --</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="سبب النقل (اختياري)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="las la-exchange-alt mr-1"></i> تأكيد النقل
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ===================== MANAGE PRODUCTS MODAL ===================== --}}
<div class="modal fade" id="manageProductsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="las la-list mr-1"></i> إدارة نوعيات الأضاحي</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>النوعية</th>
                            <th>الفئة</th>
                            <th class="text-center">عدد الحيوانات</th>
                            <th style="width:120px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allProducts as $prod)
                        <tr>
                            <td><strong>{{ $prod->name }}</strong></td>
                            <td>{{ $prod->mainCategory->name ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $prod->animals_count > 0 ? 'info' : 'secondary' }}">
                                    {{ $prod->animals_count }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-product"
                                        data-id="{{ $prod->id }}"
                                        data-name="{{ $prod->name }}"
                                        data-category="{{ $prod->main_category_id }}"
                                        title="تعديل">
                                    <i class="las la-edit"></i>
                                </button>
                                @if($prod->animals_count == 0)
                                <form action="{{ route('udhiya.products.destroy', $prod) }}" method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('هل تريد حذف نوعية {{ addslashes($prod->name) }}؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="las la-trash"></i>
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-outline-danger" disabled title="لا يمكن الحذف: مرتبط بحيوانات">
                                    <i class="las la-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">لا توجد نوعيات</td></tr>
                        @endforelse
                    </tbody>
                    {{-- Add new product row --}}
                    <tfoot>
                        <tr class="bg-light">
                            <form action="{{ route('udhiya.products.store') }}" method="POST" class="d-contents">
                                @csrf
                                <td>
                                    <input type="text" name="name" class="form-control form-control-sm"
                                           placeholder="اسم النوعية" required>
                                </td>
                                <td>
                                    <select name="main_category_id" class="form-control form-control-sm" required>
                                        <option value="">-- الفئة --</option>
                                        @foreach($mainCategories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td></td>
                                <td>
                                    <button type="submit" class="btn btn-sm btn-primary btn-block">
                                        <i class="las la-plus"></i> إضافة
                                    </button>
                                </td>
                            </form>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Product Modal --}}
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form id="editProductForm" method="POST">
            @csrf @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="las la-edit mr-1"></i> تعديل النوعية</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>اسم النوعية <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editProductName" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label>الفئة <span class="text-danger">*</span></label>
                        <select name="main_category_id" id="editProductCategory" class="form-control" required>
                            <option value="">-- اختر --</option>
                            @foreach($mainCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="las la-save mr-1"></i> حفظ
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script>
// Re-open add modal if validation errors exist
@if($errors->any())
$(document).ready(function() { $('#addAnimalModal').modal('show'); });
@endif

// Populate edit product modal
document.querySelectorAll('.btn-edit-product').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('editProductName').value     = this.dataset.name;
        document.getElementById('editProductCategory').value = this.dataset.category;
        document.getElementById('editProductForm').action   =
            '{{ url("udhiya/products") }}/' + this.dataset.id;
        // Close manage modal, open edit modal
        $('#manageProductsModal').modal('hide');
        $('#editProductModal').modal('show');
    });
});

// Re-open manage modal when edit modal closes without submitting
var editProductSubmitted = false;
document.getElementById('editProductForm').addEventListener('submit', function() {
    editProductSubmitted = true;
});
$('#editProductModal').on('hidden.bs.modal', function() {
    if (!editProductSubmitted) {
        $('#manageProductsModal').modal('show');
    }
    editProductSubmitted = false;
});

// Populate transfer modal
document.querySelectorAll('.btn-transfer').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('transferAnimalCode').textContent      = this.dataset.code;
        document.getElementById('transferCurrentWarehouse').textContent = this.dataset.warehouse;
        document.getElementById('transferForm').action =
            '{{ url("udhiya/animals") }}/' + this.dataset.id + '/transfer';
        document.querySelector('#transferModal select').value         = '';
        document.querySelector('#transferModal textarea').value       = '';
    });
});
</script>
@endsection
