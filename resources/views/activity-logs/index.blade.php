@extends('layouts.master')

@section('css')
@endsection

@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">سجل النشاطات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ نشاط النظام</span>
			</div>
		</div>
	</div>
	<!-- breadcrumb -->
@endsection

@section('content')
	<!-- row -->
	<div class="row row-sm">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title mb-4">سجل النشاطات</h5>

					<!-- Filter Section -->
					<form method="GET" action="{{ route('admin.activity-logs.index') }}" class="mb-4">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label>تصفية حسب المستخدم</label>
									<select name="user_id" class="form-control">
										<option value="">جميع المستخدمين</option>
										@foreach($users as $user)
											<option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
												{{ $user->name }}
											</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>تصفية حسب الإجراء</label>
									<select name="action" class="form-control">
										<option value="">جميع الإجراءات</option>
										<option value="viewed" {{ request('action') === 'viewed' ? 'selected' : '' }}>عرض</option>
										<option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>إنشاء</option>
										<option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>تعديل</option>
										<option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>حذف</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>&nbsp;</label>
									<button type="submit" class="btn btn-primary btn-block"><i class="las la-filter"></i> تطبيق الفلتر</button>
								</div>
							</div>
						</div>
					</form>

					<!-- Activity Table -->
					<div class="table-responsive">
						<table class="table text-md-nowrap table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>المستخدم</th>
									<th>الإجراء</th>
									<th>الوصف</th>
									<th>النوع</th>
									<th>عنوان IP</th>
									<th>التاريخ والوقت</th>
									<th>العمليات</th>
								</tr>
							</thead>
							<tbody>
								@forelse($logs as $log)
									<tr>
										<td>{{ $log->id }}</td>
										<td>
											@if($log->user)
												<a href="{{ route('admin.activity-logs.user', $log->user->id) }}">
													{{ $log->user->name }}
												</a>
											@else
												<span class="text-muted">مستخدم محذوف</span>
											@endif
										</td>
										<td>
											@if($log->action === 'created')
												<span class="badge badge-success">{{ ucfirst($log->action) }}</span>
											@elseif($log->action === 'updated')
												<span class="badge badge-info">{{ ucfirst($log->action) }}</span>
											@elseif($log->action === 'deleted')
												<span class="badge badge-danger">{{ ucfirst($log->action) }}</span>
											@else
												<span class="badge badge-secondary">{{ ucfirst($log->action) }}</span>
											@endif
										</td>
										<td>{{ $log->description }}</td>
										<td><small>{{ class_basename($log->model_type) }}</small></td>
										<td><small>{{ $log->ip_address }}</small></td>
										<td>{{ $log->created_at->format('M d, Y H:i A') }}</td>
										<td>
											<a href="{{ route('admin.activity-logs.show', $log->id) }}" class="btn btn-sm btn-info" title="عرض التفاصيل"><i class="las la-eye"></i></a>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="8" class="text-center text-muted py-5">لا توجد سجلات نشاط</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					<!-- Pagination -->
					<div class="d-flex justify-content-center mt-4">
						{{ $logs->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- row closed -->
@endsection

@section('js')
@endsection
