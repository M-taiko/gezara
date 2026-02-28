@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">الموردون</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="#">الأضاحي</a></li><li class="breadcrumb-item active">الموردون</li></ol>
    </div>
    <div class="page-rightheader">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addSupplierModal">
            <i class="fas fa-plus ml-1"></i> إضافة مورد
        </button>
    </div>
</div>
@endsection
@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr><th>#</th><th>الاسم</th><th>الهاتف</th><th>العنوان</th><th>الرصيد</th><th>المشتريات</th><th>الإجراءات</th></tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->phone ?? '—' }}</td>
                    <td>{{ $supplier->address ?? '—' }}</td>
                    <td class="{{ $supplier->balance > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($supplier->balance, 2) }} ج.م
                    </td>
                    <td><span class="badge badge-info">{{ $supplier->purchases_count }}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editSupplierModal{{ $supplier->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="{{ route('udhiya.reports.supplier', $supplier) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-chart-line"></i>
                        </a>
                        <form action="{{ route('udhiya.suppliers.destroy', $supplier) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                {{-- Edit Modal --}}
                <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header"><h5 class="modal-title">تعديل مورد</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                            <form action="{{ route('udhiya.suppliers.update', $supplier) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>الاسم <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>الهاتف</label>
                                        <input type="text" name="phone" class="form-control" value="{{ $supplier->phone }}">
                                    </div>
                                    <div class="form-group">
                                        <label>العنوان</label>
                                        <input type="text" name="address" class="form-control" value="{{ $supplier->address }}">
                                    </div>
                                    <div class="form-group">
                                        <label>ملاحظات</label>
                                        <textarea name="notes" class="form-control" rows="3">{{ $supplier->notes }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer"><button type="submit" class="btn btn-primary">حفظ التعديلات</button></div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">لا يوجد موردون بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer">{{ $suppliers->links() }}</div>
    @endif
</div>

{{-- Add Supplier Modal --}}
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">إضافة مورد جديد</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <form action="{{ route('udhiya.suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>الهاتف</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">إضافة</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
