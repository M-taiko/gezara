<!-- main-header opened -->
			<div class="main-header sticky side-header nav nav-item">
				<div class="container-fluid">
					<div class="main-header-left ">
						<div class="responsive-logo">
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-1" alt="logo"></a>
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/logo-white.png')}}" class="dark-logo-1" alt="logo"></a>
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="logo-2" alt="logo"></a>
							<a href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="dark-logo-2" alt="logo"></a>
						</div>
						<div class="app-sidebar__toggle" data-toggle="sidebar">
							<a class="open-toggle" href="#"><i class="header-icon fe fe-align-left" ></i></a>
							<a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
						</div>
						<div class="main-header-center mr-3 d-sm-none d-md-none d-lg-block">
							<input class="form-control" placeholder="ابحث عن أي شيء..." type="search"> <button class="btn"><i class="fas fa-search d-none d-md-block"></i></button>
						</div>
					</div>
					<div class="main-header-right">
						<ul class="nav">
							<li class="">
							<div class="nav-itemd-none d-md-flex my-auto px-2">
								<span class="tx-13 text-muted font-weight-bold">&#x1F1EA;&#x1F1EC; عربي</span>
							</div>
						</li>
							@auth
							@endauth
						</ul>
						<div class="nav nav-item  navbar-nav-right ml-auto">
							<div class="nav-link" id="bs-example-navbar-collapse-1">
								<form class="navbar-form" role="search">
									<div class="input-group">
										<input type="text" class="form-control" placeholder="ابحث...">
										<span class="input-group-btn">
											<button type="reset" class="btn btn-default">
												<i class="fas fa-times"></i>
											</button>
											<button type="submit" class="btn btn-default nav-link resp-btn">
												<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
											</button>
										</span>
									</div>
								</form>
							</div>
							{{-- Messages icon hidden: feature disabled --}}
							<div class="dropdown nav-item main-header-notification">
								<a class="new nav-link" href="#">
								<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span class=" pulse"></span></a>
								<div class="dropdown-menu">
									<div class="menu-header-content bg-primary text-right">
										<div class="d-flex">
											<h6 class="dropdown-title mb-1 tx-15 text-white font-weight-semibold">الإشعارات</h6>
											<span class="badge badge-pill badge-warning mr-auto my-auto float-left">تعليم الكل كمقروء</span>
										</div>
										<p class="dropdown-title-text subtext mb-0 text-white op-6 pb-0 tx-12 ">لديك إشعارات جديدة</p>
									</div>
									<div class="main-notification-list Notification-scroll">
										<a class="d-flex p-3 border-bottom" href="#">
											<div class="notifyimg bg-pink">
												<i class="la la-file-alt text-white"></i>
											</div>
											<div class="mr-3">
												<h5 class="notification-label mb-1">ملفات جديدة متاحة</h5>
												<div class="notification-subtext">منذ 10 ساعات</div>
											</div>
											<div class="mr-auto" >
												<i class="las la-angle-left text-left text-muted"></i>
											</div>
										</a>
										<a class="d-flex p-3" href="#">
											<div class="notifyimg bg-purple">
												<i class="la la-gem text-white"></i>
											</div>
											<div class="mr-3">
												<h5 class="notification-label mb-1">تحديثات متاحة</h5>
												<div class="notification-subtext">منذ يومين</div>
											</div>
											<div class="mr-auto" >
												<i class="las la-angle-left text-left text-muted"></i>
											</div>
										</a>
										<a class="d-flex p-3 border-bottom" href="#">
											<div class="notifyimg bg-success">
												<i class="la la-shopping-basket text-white"></i>
											</div>
											<div class="mr-3">
												<h5 class="notification-label mb-1">طلب جديد وصل</h5>
												<div class="notification-subtext">منذ ساعة</div>
											</div>
											<div class="mr-auto" >
												<i class="las la-angle-left text-left text-muted"></i>
											</div>
										</a>
										<a class="d-flex p-3 border-bottom" href="#">
											<div class="notifyimg bg-warning">
												<i class="la la-envelope-open text-white"></i>
											</div>
											<div class="mr-3">
												<h5 class="notification-label mb-1">مراجعة جديدة</h5>
												<div class="notification-subtext">منذ يوم</div>
											</div>
											<div class="mr-auto" >
												<i class="las la-angle-left text-left text-muted"></i>
											</div>
										</a>
										<a class="d-flex p-3 border-bottom" href="#">
											<div class="notifyimg bg-danger">
												<i class="la la-user-check text-white"></i>
											</div>
											<div class="mr-3">
												<h5 class="notification-label mb-1">٢٢ تسجيل موثق</h5>
												<div class="notification-subtext">منذ ساعتين</div>
											</div>
											<div class="mr-auto" >
												<i class="las la-angle-left text-left text-muted"></i>
											</div>
										</a>
										<a class="d-flex p-3 border-bottom" href="#">
											<div class="notifyimg bg-primary">
												<i class="la la-check-circle text-white"></i>
											</div>
											<div class="mr-3">
												<h5 class="notification-label mb-1">تمت الموافقة على المشروع</h5>
												<div class="notification-subtext">منذ 4 ساعات</div>
											</div>
											<div class="mr-auto" >
												<i class="las la-angle-left text-left text-muted"></i>
											</div>
										</a>
									</div>
									<div class="dropdown-footer">
										<a href="">عرض الكل</a>
									</div>
								</div>
							</div>
							<!-- Dark Mode Toggle -->
							@auth
								<div class="nav-item  nav-item main-header-notification">
									<a class="new nav-link dark-mode-toggle" href="#" data-mode="{{ Auth::user()->dark_mode === 'dark' ? 'light' : 'dark' }}" title="Toggle Dark Mode">
										<span class="light-layout">
											<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" width="24" height="24" viewBox="0 0 24 24"><path d="M6.993 12c0 2.761 2.246 5.007 5.007 5.007s5.007-2.246 5.007-5.007S14.761 6.993 12 6.993 6.993 9.239 6.993 12zM12 8.993c1.658 0 3.007 1.349 3.007 3.007S13.658 15.007 12 15.007 8.993 13.658 8.993 12 10.342 8.993 12 8.993zM10.998 19h2v3h-2zm0-17h2v3h-2zm-9 9h3v2h-3zm17 0h3v2h-3zM4.219 18.363l2.12-2.122 1.415 1.414-2.12 2.122zM16.24 6.344l2.122-2.122 1.414 1.414-2.122 2.122zM6.342 7.759 4.22 5.637l1.415-1.414 2.12 2.122zm13.434 10.605-1.414 1.414-2.122-2.122 1.414-1.414z"></path></svg>
										</span>
										<span class="dark-layout" style="display:none;">
											<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" width="24" height="24" viewBox="0 0 24 24"><path d="M12 18c-3.3 0-6-2.7-6-6s2.7-6 6-6 6 2.7 6 6-2.7 6-6 6m0-10c-2.2 0-4 1.8-4 4s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4zM13 2h-2v3h2V2zm0 15h-2v3h2v-3zM5 11H2v2h3v-2zm15 0h-3v2h3v-2zM6.3 5.3L4.2 3.2 2.8 4.6l2.1 2.1 1.4-1.4zm11.4 11.4l-1.4 1.4 2.1 2.1 1.4-1.4-2.1-2.1zM19.8 4.6l-2.1-2.1-1.4 1.4 2.1 2.1 1.4-1.4zM7.7 17.7l-2.1 2.1 1.4 1.4 2.1-2.1-1.4-1.4z"></path></svg>
										</span>
									</a>
								</div>
							@endauth
							<div class="nav-item full-screen fullscreen-button">
								<a class="new nav-link full-screen-link" href="#"><svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-maximize"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg></a>
							</div>
							@if(Auth::check())
								<?php
									$user = Auth::user();
									$profile = $user->profile;
									$avatarUrl = ($profile && $profile->avatar) ? asset($profile->avatar) : URL::asset('assets/img/faces/6.jpg');
								?>
								<div class="dropdown main-profile-menu nav nav-item nav-link">
									<a class="profile-user d-flex" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img alt="" src="{{ $avatarUrl }}"></a>
									<div class="dropdown-menu dropdown-menu-right" data-popper-placement="bottom-end">
										<div class="main-header-profile bg-primary p-3">
											<div class="d-flex wd-100p">
												<div class="main-img-user"><img alt="" src="{{ $avatarUrl }}" class=""></div>
												<div class="mr-3 my-auto">
													<h6>{{ $user->name }}</h6><span>{{ $profile && $profile->job_title ? $profile->job_title : 'عضو' }}</span>
												</div>
											</div>
										</div>
										<a class="dropdown-item" href="{{ route('udhiya.dashboard') }}"><i class="bx bx-tachometer"></i> لوحة التحكم</a>
										<a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bx bx-user-circle"></i> ملفي الشخصي</a>
										<a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bx bx-edit"></i> تعديل الملف الشخصي</a>
										<a class="dropdown-item" href="{{ route('company.settings') }}"><i class="bx bx-cog"></i> إعدادات الشركة</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="{{ url('/') }}"><i class="bx bx-home"></i> الصفحة الرئيسية</a>
										<div class="dropdown-divider"></div>
										<form action="{{ route('logout') }}" method="POST">
											@csrf
											<button type="submit" class="dropdown-item text-danger">
												<i class="bx bx-log-out"></i> تسجيل الخروج
											</button>
										</form>
									</div>
								</div>
							@endif
							<div class="dropdown main-header-message right-toggle">
								<a class="nav-link pr-0" data-toggle="sidebar-left" data-target=".sidebar-left">
									<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
<!-- /main-header -->
