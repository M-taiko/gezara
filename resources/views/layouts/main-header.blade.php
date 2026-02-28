<!-- main-header -->
<div class="main-header sticky side-header nav nav-item">
    <div class="container-fluid">
        <div class="main-header-left">
            {{-- Sidebar toggle --}}
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#">
                    <i class="fe fe-align-left" style="font-size:1.3rem;color:var(--primary)"></i>
                </a>
                <a class="close-toggle" href="#">
                    <i class="fe fe-x" style="font-size:1.3rem;color:var(--primary)"></i>
                </a>
            </div>
            {{-- Mobile brand --}}
            <a href="{{ route('udhiya.dashboard') }}" class="d-md-none text-decoration-none ms-2"
               style="font-weight:700;color:var(--primary);font-size:1rem;">
                🐄 الأضاحي
            </a>
        </div>

        <div class="main-header-right">
            <div class="nav nav-item navbar-nav-right ml-auto d-flex align-items-center gap-2">

                {{-- Dark Mode Toggle --}}
                @auth
                <div class="nav-item">
                    <a class="new nav-link dark-mode-toggle d-flex align-items-center justify-content-center"
                       href="#"
                       data-mode="{{ Auth::user()->dark_mode === 'dark' ? 'light' : 'dark' }}"
                       title="تبديل الوضع الليلي"
                       style="width:38px;height:38px;border-radius:10px;background:#F3F4F6;">
                        <span class="light-layout">🌙</span>
                        <span class="dark-layout" style="display:none;">☀️</span>
                    </a>
                </div>
                @endauth

                {{-- User Dropdown --}}
                @if(Auth::check())
                @php
                    $user    = Auth::user();
                    $profile = $user->profile;
                    $avatar  = ($profile && $profile->avatar) ? asset($profile->avatar) : URL::asset('assets/img/faces/6.jpg');
                @endphp
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle"
                       data-toggle="dropdown" aria-expanded="false">
                        <img src="{{ $avatar }}" alt="avatar"
                             style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--primary);">
                        <span class="d-none d-md-block" style="font-weight:600;font-size:.88rem;color:var(--text);">
                            {{ $user->name }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" style="border:none;border-radius:14px;box-shadow:0 8px 30px rgba(0,0,0,.15);min-width:210px;padding:8px;">
                        <div style="padding:14px 16px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:10px;margin-bottom:8px;">
                            <div style="color:#fff;font-weight:700;font-size:.9rem;">{{ $user->name }}</div>
                            <div style="color:rgba(255,255,255,.75);font-size:.78rem;">{{ $user->email }}</div>
                        </div>
                        <a class="dropdown-item rounded-sm" href="{{ route('udhiya.dashboard') }}" style="font-size:.87rem;padding:9px 12px;border-radius:8px;">
                            📊 لوحة التحكم
                        </a>
                        <a class="dropdown-item rounded-sm" href="{{ route('profile.show') }}" style="font-size:.87rem;padding:9px 12px;border-radius:8px;">
                            👤 ملفي الشخصي
                        </a>
                        <a class="dropdown-item rounded-sm" href="{{ route('company.settings') }}" style="font-size:.87rem;padding:9px 12px;border-radius:8px;">
                            ⚙️ إعدادات الشركة
                        </a>
                        <div class="dropdown-divider" style="margin:6px 0;"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item rounded-sm"
                                    style="font-size:.87rem;padding:9px 12px;border-radius:8px;color:#EF4444;width:100%;text-align:right;background:none;border:none;cursor:pointer;">
                                🚪 تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
<!-- /main-header -->
