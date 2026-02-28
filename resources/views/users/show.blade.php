@extends('layouts.master')

@section('css')
@endsection

@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">Users</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ View User</span>
			</div>
		</div>
		<div class="d-flex my-xl-auto right-content">
			<div class="pr-1 mb-3 mb-xl-0">
				<a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary ml-2"><i class="las la-edit"></i> Edit</a>
			</div>
			<div class="pr-1 mb-3 mb-xl-0">
				<a href="{{ route('admin.users.index') }}" class="btn btn-secondary ml-2"><i class="las la-arrow-left"></i> Back</a>
			</div>
		</div>
	</div>
	<!-- breadcrumb -->
@endsection

@section('content')
	<!-- row -->
	<div class="row row-sm">
		<!-- User Info Card -->
		<div class="col-lg-4">
			<div class="card">
				<div class="card-body text-center">
					@if($user->profile && $user->profile->avatar)
						<img src="{{ asset($user->profile->avatar) }}" alt="{{ $user->name }}" class="avatar avatar-xxl brround mb-3">
					@else
						<img src="{{ URL::asset('assets/img/faces/6.jpg') }}" alt="{{ $user->name }}" class="avatar avatar-xxl brround mb-3">
					@endif

					<h5 class="card-title mb-1">{{ $user->name }}</h5>
					<p class="text-muted mb-3">{{ $user->email }}</p>

					<!-- Status Badge -->
					@if($user->status === 'active')
						<span class="label text-success d-inline-flex">
							<div class="dot-label bg-success ml-1"></div>
							Active
						</span>
					@elseif($user->status === 'banned')
						<span class="label text-danger d-inline-flex">
							<div class="dot-label bg-danger ml-1"></div>
							Banned
						</span>
					@else
						<span class="label text-warning d-inline-flex">
							<div class="dot-label bg-warning ml-1"></div>
							Inactive
						</span>
					@endif

					<!-- Role Badges -->
					<div class="mt-3">
						@forelse($user->roles as $role)
							<span class="badge badge-primary mr-2 mb-2">{{ $role->display_name }}</span>
						@empty
							<p class="text-muted">No roles assigned</p>
						@endforelse
					</div>

					<!-- User Details -->
					<hr>
					<div class="text-left">
						<div class="mb-3">
							<small class="text-muted">Email Address</small>
							<p class="mb-0">{{ $user->email }}</p>
						</div>
						@if($user->profile && $user->profile->phone)
							<div class="mb-3">
								<small class="text-muted">Phone</small>
								<p class="mb-0">{{ $user->profile->phone }}</p>
							</div>
						@endif
						@if($user->profile && $user->profile->address)
							<div class="mb-3">
								<small class="text-muted">Address</small>
								<p class="mb-0">{{ $user->profile->address }}</p>
							</div>
						@endif
						<div class="mb-3">
							<small class="text-muted">Registered</small>
							<p class="mb-0">{{ $user->created_at->format('M d, Y H:i A') }}</p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Activity Logs and Other Info -->
		<div class="col-lg-8">
			<!-- Recent Activity -->
			<div class="card">
				<div class="card-header pb-0">
					<div class="d-flex justify-content-between">
						<h4 class="card-title mg-b-0">Recent Activity</h4>
						<a href="{{ route('admin.activity-logs.user', $user->id) }}" class="btn btn-sm btn-info">View All</a>
					</div>
				</div>
				<div class="card-body">
					@forelse($activityLogs as $log)
						<div class="mb-3 pb-3 border-bottom">
							<div class="d-flex justify-content-between">
								<h6 class="mb-1">{{ ucfirst($log->action) }}: {{ $log->description }}</h6>
								<small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
							</div>
							<small class="text-muted">Model: {{ class_basename($log->model_type) }} | IP: {{ $log->ip_address }}</small>
							@if($log->changes)
								<div class="mt-2">
									<small class="text-muted d-block">{{ $log->getChangesDescription() }}</small>
								</div>
							@endif
						</div>
					@empty
						<p class="text-muted text-center py-5">No activity recorded yet</p>
					@endforelse

					<!-- Pagination -->
					<div class="d-flex justify-content-center mt-3">
						{{ $activityLogs->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- row closed -->
@endsection

@section('js')
@endsection
