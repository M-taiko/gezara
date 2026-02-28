@extends('layouts.master')
@section('page-header')
<div class="page-header">
    <div class="page-leftheader">
        <h4 class="page-title mb-0">المشتريات</h4>
        <ol class="breadcrumb"><li class="breadcrumb-item"><a href="#">الأضاحي</a></li><li class="breadcrumb-item active">المشتريات</li></ol>
    </div>
    <div class="page-rightheader">
        <a href="{{ route('udhiya.purchases.create') }}" class="btn btn-primary">
            <i class="fas fa-plus ml-1"></i> مشترى جديد
        </a>
    </div>
</div>
@endsection
@section('content')
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr><th>#</th><th>المورد</th><th>التاريخ</th><th>الإجمالي</th><th>المدفوع</th><th>المتبقي</th><th>الحالة</th><th>الإجراءات</th></tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->id }}</td>
                    <td>{{ $purchase->supplier->name }}</td>
                    <td>{{ $purchase->date }}</td>
                    <td>{{ number_format($purchase->total, 2) }} ج.م</td>
                    <td>{{ number_format($purchase->paid, 2) }} ج.م</td>
                    <td class="{{ ($purchase->total - $purchase->paid) > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($purchase->total - $purchase->paid, 2) }} ج.م
                    </td>
                    <td>
                        @if($purchase->status === 'confirmed')
                            <span class="badge badge-success">مؤكد</span>
                        @else
                            <span class="badge badge-secondary">مسودة</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('udhiya.purchases.show', $purchase) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">لا توجد مشتريات بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($purchases->hasPages())
    <div class="card-footer">{{ $purchases->links() }}</div>
    @endif
</div>
@endsection
