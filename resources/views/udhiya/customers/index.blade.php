@extends('layouts.master')

@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="page-title-emoji">🙋</span> العملاء</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('udhiya.dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active">العملاء</li>
        </ol>
    </div>
    <div class="page-rightheader">
        <button class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal">
            ➕ إضافة عميل
        </button>
    </div>
</div>
@endsection

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>🙋 قائمة العملاء</span>
        <span class="badge badge-primary">{{ $customers->total() }} عميل</span>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الهاتف</th>
                    <th>العنوان</th>
                    <th>الصكوك</th>
                    <th class="text-center">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td style="color:var(--text-muted);font-size:.82rem;">{{ $loop->iteration }}</td>
                    <td><strong>{{ $customer->name }}</strong></td>
                    <td style="font-size:.85rem;color:var(--text-muted);">{{ $customer->phone }}</td>
                    <td style="font-size:.85rem;color:var(--text-muted);">{{ $customer->address ?? '—' }}</td>
                    <td><span class="badge badge-primary">{{ $customer->contracts_count }}</span></td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-sm btn-warning btn-action" data-toggle="modal"
                                    data-target="#editCustomerModal{{ $customer->id }}" title="تعديل">✏️</button>
                            <a href="{{ route('udhiya.reports.customer', $customer) }}"
                               class="btn btn-sm btn-info btn-action" title="تقرير">📊</a>
                            <form action="{{ route('udhiya.customers.destroy', $customer) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('هل تريد حذف هذا العميل؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-action" title="حذف">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <span class="empty-icon">🙋</span>
                            <p>لا يوجد عملاء بعد</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="card-footer">{{ $customers->links() }}</div>
    @endif
</div>

{{-- Edit Modals --}}
@foreach($customers as $customer)
<div class="modal fade" id="editCustomerModal{{ $customer->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">✏️ تعديل عميل</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">💾 حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Add Customer Modal --}}
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">➕ إضافة عميل جديد</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">➕ إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
