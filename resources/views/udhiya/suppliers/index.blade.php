@extends('layouts.master')

@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="page-title-emoji">🏭</span> الموردون</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active">الموردون</li>
        </ol>
    </div>
    <div class="page-rightheader">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addSupplierModal">
            ➕ إضافة مورد
        </button>
    </div>
</div>
@endsection

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>🏭 قائمة الموردين</span>
        <span class="badge badge-primary">{{ $suppliers->total() }} مورد</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الهاتف</th>
                        <th>العنوان</th>
                        <th>الرصيد</th>
                        <th>المشتريات</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td style="color:var(--text-muted);font-size:.82rem;">{{ $loop->iteration }}</td>
                        <td><strong>{{ $supplier->name }}</strong></td>
                        <td style="font-size:.85rem;color:var(--text-muted);">{{ $supplier->phone ?? '—' }}</td>
                        <td style="font-size:.85rem;color:var(--text-muted);">{{ $supplier->address ?? '—' }}</td>
                        <td>
                            @if($supplier->balance > 0)
                                <span class="badge badge-danger">{{ number_format($supplier->balance, 2) }} ج.م</span>
                            @else
                                <span class="badge badge-success">{{ number_format($supplier->balance, 2) }} ج.م</span>
                            @endif
                        </td>
                        <td><span class="badge badge-primary">{{ $supplier->purchases_count }}</span></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm btn-warning btn-action" data-toggle="modal"
                                        data-target="#editSupplierModal{{ $supplier->id }}" title="تعديل">✏️</button>
                                <a href="{{ route('udhiya.reports.supplier', $supplier) }}"
                                   class="btn btn-sm btn-info btn-action" title="تقرير">📊</a>
                                <form action="{{ route('udhiya.suppliers.destroy', $supplier) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('هل تريد حذف هذا المورد؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger btn-action" title="حذف">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <span class="empty-icon">🏭</span>
                                <p>لا يوجد موردون بعد</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer">{{ $suppliers->links() }}</div>
    @endif
</div>

{{-- Edit Modals --}}
@foreach($suppliers as $supplier)
<div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">✏️ تعديل مورد</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">💾 حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Add Supplier Modal --}}
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">➕ إضافة مورد جديد</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">➕ إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
