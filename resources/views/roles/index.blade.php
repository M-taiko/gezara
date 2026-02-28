@extends('layouts.master')

@section('css')
@endsection

@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">Roles</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Manage Roles</span>
			</div>
		</div>
		<div class="d-flex my-xl-auto right-content">
			<div class="pr-1 mb-3 mb-xl-0">
				<a href="{{ route('admin.roles.create') }}" class="btn btn-primary ml-2"><i class="las la-plus"></i> Add New Role</a>
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
					<h5 class="card-title mb-4">Roles List</h5>

					<div class="table-responsive">
						<table class="table text-md-nowrap table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Name</th>
									<th>Display Name</th>
									<th>Description</th>
									<th>Users Count</th>
									<th>Created</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								@forelse($roles as $role)
									<tr>
										<td>{{ $role->id }}</td>
										<td><strong>{{ $role->name }}</strong></td>
										<td>{{ $role->display_name }}</td>
										<td>{{ $role->description ?? 'N/A' }}</td>
										<td><span class="badge badge-info">{{ $role->users_count }}</span></td>
										<td>{{ $role->created_at->format('M d, Y') }}</td>
										<td>
											<a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="las la-edit"></i></a>
											@if(!in_array($role->name, ['admin', 'manager', 'user']) && $role->users_count == 0)
												<form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
													@csrf
													@method('DELETE')
													<button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="las la-trash"></i></button>
												</form>
											@endif
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="7" class="text-center text-muted py-5">No roles found</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					<!-- Pagination -->
					<div class="d-flex justify-content-center mt-4">
						{{ $roles->links() }}
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- row closed -->
@endsection

@section('js')
@endsection
