@extends('layouts.master')

@section('css')
@endsection

@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">سجل النشاطات</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تفاصيل النشاط</span>
			</div>
		</div>
		<div class="d-flex my-xl-auto right-content">
			<div class="pr-1 mb-3 mb-xl-0">
				<a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary ml-2"><i class="las la-arrow-left"></i> العودة للسجلات</a>
			</div>
		</div>
	</div>
	<!-- breadcrumb -->
@endsection

@section('content')
	<!-- row -->
	<div class="row row-sm">
		<div class="col-lg-8 mx-auto">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title mb-4">تفاصيل النشاط</h5>

					<div class="row mg-b-20">
						<div class="col-md-6">
							<div class="mb-3">
								<small class="text-muted d-block">المستخدم</small>
								<strong>
									@if($log->user)
										<a href="{{ route('admin.activity-logs.user', $log->user->id) }}">{{ $log->user->name }}</a>
									@else
										<span class="text-muted">مستخدم محذوف</span>
									@endif
								</strong>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<small class="text-muted d-block">التاريخ والوقت</small>
								<strong>{{ $log->created_at->format('M d, Y H:i:s A') }}</strong>
							</div>
						</div>
					</div>

					<hr>

					<div class="row mg-b-20">
						<div class="col-md-6">
							<div class="mb-3">
								<small class="text-muted d-block">الإجراء</small>
								<strong>
									@if($log->action === 'created')
										<span class="badge badge-success">{{ ucfirst($log->action) }}</span>
									@elseif($log->action === 'updated')
										<span class="badge badge-info">{{ ucfirst($log->action) }}</span>
									@elseif($log->action === 'deleted')
										<span class="badge badge-danger">{{ ucfirst($log->action) }}</span>
									@else
										<span class="badge badge-secondary">{{ ucfirst($log->action) }}</span>
									@endif
								</strong>
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<small class="text-muted d-block">النوع</small>
								<strong>{{ class_basename($log->model_type) }} (ID: {{ $log->model_id }})</strong>
							</div>
						</div>
					</div>

					<hr>

					<div class="mb-3">
						<small class="text-muted d-block">الوصف</small>
						<strong>{{ $log->description }}</strong>
					</div>

					<div class="mb-3">
						<small class="text-muted d-block">عنوان IP</small>
						<strong>{{ $log->ip_address }}</strong>
					</div>

					<div class="mb-3">
						<small class="text-muted d-block">متصفح المستخدم</small>
						<small class="d-block">{{ $log->user_agent }}</small>
					</div>

					<!-- التغييرات Details -->
					@if($log->changes)
						<hr>
						<h6 class="card-title mb-3">التغييرات</h6>

						<div class="card bg-light">
							<div class="card-body">
								@if(isset($log->changes['before']) && isset($log->changes['after']))
									<div class="row">
										<div class="col-md-6">
											<h6 class="text-danger mb-3">قبل</h6>
											<pre class="bg-white p-3 rounded"><code>@json($log->changes['before'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)</code></pre>
										</div>
										<div class="col-md-6">
											<h6 class="text-success mb-3">بعد</h6>
											<pre class="bg-white p-3 rounded"><code>@json($log->changes['after'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)</code></pre>
										</div>
									</div>
								@else
									<pre class="bg-white p-3 rounded"><code>@json($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)</code></pre>
								@endif
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
	<!-- row closed -->
@endsection

@section('js')
@endsection
