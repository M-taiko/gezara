@extends('layouts.master')

@section('css')
@endsection

@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">Roles</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Add New Role</span>
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
		<div class="col-lg-8 mx-auto">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title mb-4">Create New Role</h5>

					@if ($errors->any())
						<div class="alert alert-danger">
							<ul class="mb-0">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form action="{{ route('admin.roles.store') }}" method="POST">
						@csrf

						<div class="form-group mg-b-20">
							<label>Role Name *</label>
							<input type="text" class="form-control @error('name') is-invalid @enderror"
								name="name" value="{{ old('name') }}"
								placeholder="Enter role name (e.g., editor, supervisor)" required>
							<small class="form-text text-muted">Lowercase, no spaces (e.g., 'editor')</small>
							@error('name')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group mg-b-20">
							<label>Display Name *</label>
							<input type="text" class="form-control @error('display_name') is-invalid @enderror"
								name="display_name" value="{{ old('display_name') }}"
								placeholder="Enter display name (e.g., Content Editor)" required>
							@error('display_name')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group mg-b-20">
							<label>Description</label>
							<textarea class="form-control @error('description') is-invalid @enderror"
								name="description" rows="4"
								placeholder="Enter role description">{{ old('description') }}</textarea>
							@error('description')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<!-- Action Buttons -->
						<div class="form-group mg-t-30">
							<button type="submit" class="btn btn-primary">Create Role</button>
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
