@extends('layouts.master')

@section('css')
@endsection

@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">Roles</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Edit Role</span>
			</div>
		</div>
		<div class="d-flex my-xl-auto right-content">
			<div class="pr-1 mb-3 mb-xl-0">
				<a href="{{ route('admin.roles.index') }}" class="btn btn-secondary ml-2"><i class="las la-arrow-left"></i> Back to Roles</a>
			</div>
		</div>
	</div>
	<!-- breadcrumb -->
@endsection

@section('content')
	<!-- row -->
	<div class="row row-sm">
		<div class="col-lg-6 mx-auto">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title mb-4">Edit Role: {{ $role->display_name }}</h5>

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

					<form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
						@csrf
						@method('PUT')

						<div class="form-group mg-b-20">
							<label>Role Name *</label>
							<input type="text" class="form-control @error('name') is-invalid @enderror"
								name="name" value="{{ old('name', $role->name) }}"
								placeholder="Enter role name" required>
							@error('name')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group mg-b-20">
							<label>Display Name *</label>
							<input type="text" class="form-control @error('display_name') is-invalid @enderror"
								name="display_name" value="{{ old('display_name', $role->display_name) }}"
								placeholder="Enter display name" required>
							@error('display_name')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group mg-b-20">
							<label>Description</label>
							<textarea class="form-control @error('description') is-invalid @enderror"
								name="description" rows="4"
								placeholder="Enter role description">{{ old('description', $role->description) }}</textarea>
							@error('description')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<!-- Users with this role -->
						@if($role->users()->count() > 0)
							<div class="card mb-4">
								<div class="card-header pb-0">
									<h6 class="card-title">Users with this role ({{ $role->users()->count() }})</h6>
								</div>
								<div class="card-body">
									<ul class="list-unstyled">
										@foreach($role->users as $user)
											<li class="mb-2">
												<span class="badge badge-primary">{{ $user->name }}</span>
											</li>
										@endforeach
									</ul>
								</div>
							</div>
						@endif

						<!-- Action Buttons -->
						<div class="form-group mg-t-30">
							<button type="submit" class="btn btn-primary">Update Role</button>
							<a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- row closed -->
@endsection

@section('js')
@endsection
