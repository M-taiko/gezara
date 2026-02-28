@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">العملاء</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="#">الأضاحي</a></li><li class="breadcrumb-item active">العملاء</li></ol>
    </div>
    <div class="page-rightheader">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal">
            <i class="fas fa-plus ml-1"></i> إضافة عميل
        </button>
    </div>
</div>
@endsection
@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr><th>#</th><th>الاسم</th><th>الهاتف</th><th>العنوان</th><th>الصكوك</th><th>الإجراءات</th></tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->address ?? '—' }}</td>
                    <td><span class="badge badge-info">{{ $customer->contracts_count }}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editCustomerModal{{ $customer->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="{{ route('udhiya.reports.customer', $customer) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-chart-line"></i>
                        </a>
                        <form action="{{ route('udhiya.customers.destroy', $customer) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <div class="modal fade" id="editCustomerModal{{ $customer->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header"><h5 class="modal-title">تعديل عميل</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                            <form action="{{ route('udhiya.customers.update', $customer) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>الاسم <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>الهاتف <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>العنوان</label>
                                        <input type="text" name="address" class="form-control" value="{{ $customer->address }}">
                                    </div>
                                    <div class="form-group">
                                        <label>ملاحظات</label>
                                        <textarea name="notes" class="form-control" rows="3">{{ $customer->notes }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer"><button type="submit" class="btn btn-primary">حفظ</button></div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">لا يوجد عملاء بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="card-footer">{{ $customers->links() }}</div>
    @endif
</div>

<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">إضافة عميل جديد</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <form action="{{ route('udhiya.customers.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>الاسم <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>الهاتف <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" required>
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
