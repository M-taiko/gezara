@extends('layouts.master')

@section('css')
@endsection

@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">Users</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Manage Users</span>
			</div>
		</div>
		<div class="d-flex my-xl-auto right-content">
			<div class="pr-1 mb-3 mb-xl-0">
				<a href="{{ route('admin.users.create') }}" class="btn btn-primary ml-2"><i class="las la-plus"></i> Add New User</a>
			</div>
		</div>
	</div>
	<!-- breadcrumb -->
@endsection

@section('content')
	<!-- row -->
	<div class="row row-sm">
		<div class="col-lg-12">
			@if ($errors->any())
				<div class="alert alert-danger">
					<ul class="mb-0">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			@if (session('success'))
				<div class="alert alert-success">
					{{ session('success') }}
				</div>
			@endif

			@if (session('error'))
				<div class="alert alert-danger">
					{{ session('error') }}
				</div>
			@endif

			<div class="card">
				<div class="card-body">
					<h5 class="card-title mb-4">Users List</h5>

					<div class="table-responsive">
						<table class="table text-md-nowrap table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Avatar</th>
									<th>Name</th>
									<th>Email</th>
									<th>Role</th>
									<th>Status</th>
									<th>Created</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@forelse($users as $user)
									<tr>
										<td>{{ $user->id }}</td>
										<td>
											@if($user->profile && $user->profile->avatar)
												<img src="{{ asset($user->profile->avatar) }}" alt="{{ $user->name }}" class="avatar avatar-md brround">
											@else
												<img src="{{ URL::asset('assets/img/faces/6.jpg') }}" alt="{{ $user->name }}" class="avatar avatar-md brround">
											@endif
										</td>
										<td><strong>{{ $user->name }}</strong></td>
										<td>{{ $user->email }}</td>
										<td>
											@forelse($user->roles as $role)
												<span class="badge badge-primary">{{ $role->display_name }}</span>
											@empty
												<span class="badge badge-secondary">No Role</span>
											@endforelse
										</td>
										<td>
											@if($user->status === 'active')
												<span class="label text-success d-flex">
													<div class="dot-label bg-success ml-1"></div>
													Active
												</span>
											@elseif($user->status === 'banned')
												<span class="label text-danger d-flex">
													<div class="dot-label bg-danger ml-1"></div>
													Banned
												</span>
											@else
												<span class="label text-warning d-flex">
													<div class="dot-label bg-warning ml-1"></div>
													Inactive
												</span>
											@endif
										</td>
										<td>{{ $user->created_at->format('M d, Y') }}</td>
										<td>
											<a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info" title="View"><i class="las la-eye"></i></a>
											<a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="las la-edit"></i></a>
											@if($user->id !== Auth::id())
												<form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
													@csrf
													@method('DELETE')
													<button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="las la-trash"></i></button>
												</form>
											@endif
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="8" class="text-center text-muted py-5">No users found</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					<!-- Pagination -->
					<div class="d-flex justify-content-center mt-4">
						{{ $users->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- row closed -->
@endsection

@section('js')
@endsection
