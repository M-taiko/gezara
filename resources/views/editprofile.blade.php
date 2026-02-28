@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">الحساب الشخصي</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تعديل الملف الشخصي</span>
			</div>
		</div>
		<div class="d-flex my-xl-auto right-content">
			<div class="pr-1 mb-3 mb-xl-0">
				<a href="{{ route('profile.show') }}" class="btn btn-secondary btn-icon ml-2"><i class="mdi mdi-arrow-right"></i> العودة للملف</a>
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
					<h5 class="card-title mb-4">تعديل الملف الشخصي</h5>

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

					<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
						@csrf

						<!-- Avatar Upload Section -->
						<div class="form-group mg-b-20">
							<label>الصورة الشخصية</label>
							<div class="card mb-3">
								<div class="card-body text-center">
									@if($profile && $profile->avatar)
										<img id="avatarPreview" src="{{ asset($profile->avatar) }}" alt="Avatar" style="max-height: 150px; max-width: 100%;" class="mb-3">
									@else
										<div id="avatarPlaceholder" class="p-5">
											<i class="fas fa-user fa-5x text-muted"></i>
											<p class="text-muted mt-3">لم يتم رفع صورة بعد</p>
										</div>
										<img id="avatarPreview" src="#" alt="Avatar" style="max-height: 150px; max-width: 100%; display: none;" class="mb-3">
									@endif
								</div>
							</div>
							<input type="file" class="form-control @error('avatar') is-invalid @enderror"
								name="avatar" id="avatarInput" accept="image/*">
							<small class="form-text text-muted">الأنواع المسموحة: JPEG, PNG, JPG, GIF. الحد الأقصى: 2 ميجابايت</small>
							@error('avatar')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<!-- Personal Information Section -->
						<div class="form-group mg-b-20">
							<label>الاسم الكامل</label>
							<input type="text" class="form-control @error('name') is-invalid @enderror"
								name="name" value="{{ old('name', $user->name) }}"
								placeholder="أدخل اسمك الكامل">
							@error('name')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<div class="form-group mg-b-20">
							<label>البريد الإلكتروني</label>
							<input type="email" class="form-control @error('email') is-invalid @enderror"
								name="email" value="{{ old('email', $user->email) }}"
								placeholder="أدخل بريدك الإلكتروني">
							@error('email')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<div class="row mg-b-20">
							<div class="col-md-6">
								<div class="form-group">
									<label>رقم الهاتف</label>
									<input type="text" class="form-control @error('phone') is-invalid @enderror"
										name="phone" value="{{ old('phone', $profile && $profile->phone ? $profile->phone : '') }}"
										placeholder="أدخل رقم هاتفك">
									@error('phone')
										<span class="invalid-feedback d-block">{{ $message }}</span>
									@enderror
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>المسمى الوظيفي</label>
									<input type="text" class="form-control @error('job_title') is-invalid @enderror"
										name="job_title" value="{{ old('job_title', $profile && $profile->job_title ? $profile->job_title : '') }}"
										placeholder="أدخل مسماك الوظيفي">
									@error('job_title')
										<span class="invalid-feedback d-block">{{ $message }}</span>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-group mg-b-20">
							<label>نبذة شخصية</label>
							<textarea class="form-control @error('bio') is-invalid @enderror"
								name="bio" rows="4"
								placeholder="اكتب نبذة عن نفسك">{{ old('bio', $profile && $profile->bio ? $profile->bio : '') }}</textarea>
							@error('bio')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<!-- معلومات الموقع Section -->
						<h5 class="card-title mg-t-30 mg-b-20">معلومات الموقع</h5>

						<div class="row mg-b-20">
							<div class="col-md-6">
								<div class="form-group">
									<label>العنوان</label>
									<input type="text" class="form-control @error('address') is-invalid @enderror"
										name="address" value="{{ old('address', $profile && $profile->address ? $profile->address : '') }}"
										placeholder="أدخل عنوانك">
									@error('address')
										<span class="invalid-feedback d-block">{{ $message }}</span>
									@enderror
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>المدينة</label>
									<input type="text" class="form-control @error('city') is-invalid @enderror"
										name="city" value="{{ old('city', $profile && $profile->city ? $profile->city : '') }}"
										placeholder="أدخل مدينتك">
									@error('city')
										<span class="invalid-feedback d-block">{{ $message }}</span>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-group mg-b-20">
							<label>الدولة</label>
							<input type="text" class="form-control @error('country') is-invalid @enderror"
								name="country" value="{{ old('country', $profile && $profile->country ? $profile->country : '') }}"
								placeholder="أدخل دولتك">
							@error('country')
								<span class="invalid-feedback d-block">{{ $message }}</span>
							@enderror
						</div>

						<!-- Action Buttons -->
						<div class="form-group mg-t-30">
							<button type="submit" class="btn btn-primary">Save Changes</button>
							<a href="{{ route('profile.show') }}" class="btn btn-secondary">Cancel</a>
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
