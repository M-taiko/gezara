<!-- main-sidebar -->
	<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
	<aside class="app-sidebar sidebar-scroll">
		<div class="main-sidebar-header active">
			<?php $company = \App\Models\Company::getInstance(); ?>
			@if($company && $company->sidebar_logo_expanded)
				<!-- Expanded Sidebar Logo -->
				<a class="desktop-logo logo-light active" href="{{ url('/') }}"><img src="{{ asset($company->sidebar_logo_expanded) }}" class="main-logo" alt="{{ $company->name ?? 'logo' }}" style="max-height: 40px;"></a>
				<a class="desktop-logo logo-dark active" href="{{ url('/') }}"><img src="{{ asset($company->sidebar_logo_expanded) }}" class="main-logo dark-theme" alt="{{ $company->name ?? 'logo' }}" style="max-height: 40px;"></a>
				<!-- Collapsed Sidebar Logo -->
				<a class="logo-icon mobile-logo icon-light active" href="{{ url('/') }}"><img src="{{ asset($company->sidebar_logo_collapsed ?? $company->sidebar_logo_expanded) }}" class="logo-icon" alt="{{ $company->name ?? 'logo' }}" style="max-height: 40px;"></a>
				<a class="logo-icon mobile-logo icon-dark active" href="{{ url('/') }}"><img src="{{ asset($company->sidebar_logo_collapsed ?? $company->sidebar_logo_expanded) }}" class="logo-icon dark-theme" alt="{{ $company->name ?? 'logo' }}" style="max-height: 40px;"></a>
			@elseif($company && $company->logo)
				<!-- Fallback to main company logo -->
				<a class="desktop-logo logo-light active" href="{{ url('/') }}"><img src="{{ asset($company->logo) }}" class="main-logo" alt="{{ $company->name ?? 'logo' }}" style="max-height: 40px;"></a>
				<a class="desktop-logo logo-dark active" href="{{ url('/') }}"><img src="{{ asset($company->logo) }}" class="main-logo dark-theme" alt="{{ $company->name ?? 'logo' }}" style="max-height: 40px;"></a>
				<a class="logo-icon mobile-logo icon-light active" href="{{ url('/') }}"><img src="{{ asset($company->logo) }}" class="logo-icon" alt="{{ $company->name ?? 'logo' }}" style="max-height: 40px;"></a>
				<a class="logo-icon mobile-logo icon-dark active" href="{{ url('/') }}"><img src="{{ asset($company->logo) }}" class="logo-icon dark-theme" alt="{{ $company->name ?? 'logo' }}" style="max-height: 40px;"></a>
			@else
				<!-- Default logos -->
				<a class="desktop-logo logo-light active" href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo" alt="logo"></a>
				<a class="desktop-logo logo-dark active" href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/logo-white.png')}}" class="main-logo dark-theme" alt="logo"></a>
				<a class="logo-icon mobile-logo icon-light active" href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="logo-icon" alt="logo"></a>
				<a class="logo-icon mobile-logo icon-dark active" href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/favicon-white.png')}}" class="logo-icon dark-theme" alt="logo"></a>
			@endif
		</div>
		<div class="main-sidemenu">
			<div class="app-sidebar__user clearfix">
				<div class="dropdown user-pro-body">
					<div class="">
						@if(Auth::check())
							<?php
								$user = Auth::user();
								$profile = $user->profile;
								$avatarUrl = ($profile && $profile->avatar) ? asset($profile->avatar) : URL::asset('assets/img/faces/6.jpg');
							?>
							<img alt="user-img" class="avatar avatar-xl brround" src="{{ $avatarUrl }}"><span class="avatar-status profile-status bg-green"></span>
						@else
							<img alt="user-img" class="avatar avatar-xl brround" src="{{URL::asset('assets/img/faces/1.jpg')}}">
						@endif
					</div>
					<div class="user-info">
						@if(Auth::check())
							<h4 class="font-weight-semibold mt-3 mb-0">{{ Auth::user()->name }}</h4>
							<span class="mb-0 text-muted">User</span>
						@else
							<h4 class="font-weight-semibold mt-3 mb-0">Guest</h4>
							<span class="mb-0 text-muted">Not Logged In</span>
						@endif
					</div>
				</div>
			</div>
			<ul class="side-menu">

			{{-- ============================================ --}}
			{{-- UDHIYA MANAGEMENT SECTION                    --}}
			{{-- ============================================ --}}
			@if(Auth::check())
			<li class="side-item side-item-category">إدارة الأضاحي</li>
			<li class="slide">
				<a class="side-menu__item {{ request()->routeIs('udhiya.dashboard') ? 'active' : '' }}"
				   href="{{ route('udhiya.dashboard') }}">
					<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg>
					<span class="side-menu__label">لوحة التحكم</span>
				</a>
			</li>
			<li class="slide">
				<a class="side-menu__item {{ request()->routeIs('udhiya.suppliers.*') ? 'active' : '' }}"
				   href="{{ route('udhiya.suppliers.index') }}">
					<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4z" fill="currentColor"/></svg>
					<span class="side-menu__label">الموردون</span>
				</a>
			</li>
			<li class="slide">
				<a class="side-menu__item {{ request()->routeIs('udhiya.purchases.*') ? 'active' : '' }}"
				   href="{{ route('udhiya.purchases.index') }}">
					<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14l-5-5 1.41-1.41L12 14.17l7.59-7.59L21 8l-9 9z" fill="currentColor"/></svg>
					<span class="side-menu__label">المشتريات</span>
				</a>
			</li>
			<li class="slide">
				<a class="side-menu__item {{ request()->routeIs('udhiya.animals.*') ? 'active' : '' }}"
				   href="{{ route('udhiya.animals.index') }}">
					<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z" fill="currentColor"/></svg>
					<span class="side-menu__label">الحيوانات</span>
				</a>
			</li>
			<li class="slide">
				<a class="side-menu__item {{ request()->routeIs('udhiya.customers.*') ? 'active' : '' }}"
				   href="{{ route('udhiya.customers.index') }}">
					<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" fill="currentColor"/></svg>
					<span class="side-menu__label">العملاء</span>
				</a>
			</li>
			<li class="slide">
				<a class="side-menu__item {{ request()->routeIs('udhiya.contracts.*') ? 'active' : '' }}"
				   href="{{ route('udhiya.contracts.index') }}">
					<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill="currentColor"/></svg>
					<span class="side-menu__label">الصكوك</span>
				</a>
			</li>
			<li class="slide">
				<a class="side-menu__item {{ request()->routeIs('udhiya.groups.*') ? 'active' : '' }}"
				   href="{{ route('udhiya.groups.index') }}">
					<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" fill="currentColor"/></svg>
					<span class="side-menu__label">المجموعات</span>
				</a>
			</li>
			<li class="slide">
				<a class="side-menu__item {{ request()->routeIs('udhiya.reports.*') ? 'active' : '' }}"
				   href="{{ route('udhiya.reports.index') }}">
					<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" fill="currentColor"/></svg>
					<span class="side-menu__label">التقارير</span>
				</a>
			</li>
			@endif

				{{-- ============================================ --}}
				
			</ul>
		</div>
	</aside>
	<!-- main-sidebar closed -->
