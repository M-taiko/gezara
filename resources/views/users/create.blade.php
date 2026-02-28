@extends('layouts.master')

@section('css')
@endsection

@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">Users</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Add New User</span>
			</div>
		</div>
		<div class="d-flex my-xl-auto right-content">
			<div class="pr-1 mb-3 mb-xl-0">
				<a href="{{ route('admin.users.index') }}" class="btn btn-secondary ml-2"><i class="las la-arrow-left"></i> Back to Users</a>
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
					<h5 class="card-title mb-4">Create New User</h5>

					@if ($errors->any())
						<div class="alert alert-danger">
							<ul class="mb-0">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif

					<form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
						@csrf

						<!-- Avatar Upload Section -->
						<div class="form-group mg-b-20">
							<label>Profile Photo</label>
							<div class="card mb-3">
								<div class="card-body text-center">
									<div id="avatarPlaceholder" class="p-5">
										<i class="fas fa-user fa-5x text-muted"></i>
										<p class="text-muted mt-3">No photo uploaded yet</p>
									</div>
									<img id="avatarPreview" src="#" alt="Avatar" style="max-height: 150px; max-width: 100%; display: none;" class="mb-3">
								</div>
							</div>
							<input type="file" class="form-control @error('avatar') is-invalid @enderror"
								name="avatar" id="avatarInput" accept="image/*">
							<small class="form-text text-muted">Allowed formats: JPEG, PNG, JPG, GIF. Max size: 2MB</small>
							@error('avatar')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<!-- Personal Information Section -->
						<h5 class="card-title mg-t-30 mg-b-20">Personal Information</h5>

						<div class="form-group mg-b-20">
							<label>Full Name *</label>
							<input type="text" class="form-control @error('name') is-invalid @enderror"
								name="name" value="{{ old('name') }}"
								placeholder="Enter full name" required>
							@error('name')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group mg-b-20">
							<label>Email Address *</label>
							<input type="email" class="form-control @error('email') is-invalid @enderror"
								name="email" value="{{ old('email') }}"
								placeholder="Enter email address" required>
							@error('email')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<!-- Password Section -->
						<h5 class="card-title mg-t-30 mg-b-20">Login Credentials</h5>

						<div class="row mg-b-20">
							<div class="col-md-6">
								<div class="form-group">
									<label>Password *</label>
									<input type="password" class="form-control @error('password') is-invalid @enderror"
										name="password" placeholder="Enter password" required>
									@error('password')
										<span class="invalid-feedback d-block">{{ $message }}</span>
									@enderror
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Confirm Password *</label>
									<input type="password" class="form-control"
										name="password_confirmation" placeholder="Confirm password" required>
								</div>
							</div>
						</div>

						<!-- Role and Status Section -->
						<h5 class="card-title mg-t-30 mg-b-20">Permissions & Status</h5>

						<div class="row mg-b-20">
							<div class="col-md-6">
								<div class="form-group">
									<label>Role *</label>
									<select class="form-control @error('role_id') is-invalid @enderror" name="role_id" required>
										<option value="">Select a role</option>
										@foreach($roles as $role)
											<option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
												{{ $role->display_name }}
											</option>
										@endforeach
									</select>
									@error('role_id')
										<span class="invalid-feedback d-block">{{ $message }}</span>
									@enderror
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Status *</label>
									<select class="form-control @error('status') is-invalid @enderror" name="status" required>
										<option value="">Select status</option>
										<option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
										<option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
										<option value="banned" {{ old('status') === 'banned' ? 'selected' : '' }}>Banned</option>
									</select>
									@error('status')
										<span class="invalid-feedback d-block">{{ $message }}</span>
									@enderror
								</div>
							</div>
						</div>

						<!-- Action Buttons -->
						<div class="form-group mg-t-30">
							<button type="submit" class="btn btn-primary">Create User</button>
							<a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- row closed -->
@endsection

@section('js')
<script>
	// Preview avatar on file select
	document.getElementById('avatarInput').addEventListener('change', function(e) {
		const file = e.target.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = function(event) {
				document.getElementById('avatarPreview').src = event.target.result;
				document.getElementById('avatarPreview').style.display = 'block';
				const placeholder = document.getElementById('avatarPlaceholder');
				if (placeholder) {
					placeholder.style.display = 'none';
				}
			};
			reader.readAsDataURL(file);
		}
	});
</script>
@endsection
