<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">

    {{-- ── Brand ── --}}
    <div class="main-sidebar-header active">
        <a href="{{ route('udhiya.dashboard') }}" class="sidebar-brand-text text-decoration-none">
            <span style="font-size:1.6rem">🐄</span>
            <span>برنامج الأضاحي</span>
        </a>
    </div>

    <div class="main-sidemenu">

        {{-- ── User Info ── --}}
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body text-center">
                @auth
                    @php
                        $user    = Auth::user();
                        $profile = $user->profile;
                        $avatar  = ($profile && $profile->avatar) ? asset($profile->avatar) : URL::asset('assets/img/faces/6.jpg');
                    @endphp
                    <img alt="user" class="avatar avatar-xl brround" src="{{ $avatar }}">
                    <span class="avatar-status profile-status bg-green"></span>
                    <div class="user-info">
                        <h4>{{ $user->name }}</h4>
                        <span>{{ $profile && $profile->job_title ? $profile->job_title : 'مستخدم' }}</span>
                    </div>
                @endauth
            </div>
        </div>

        {{-- ── Navigation ── --}}
        <ul class="side-menu">

            @if(Auth::check())
            <li class="side-item side-item-category">إدارة الأضاحي</li>

            <li class="slide">
                <a class="side-menu__item {{ request()->routeIs('udhiya.dashboard') ? 'active' : '' }}"
                   href="{{ route('udhiya.dashboard') }}">
                    <span class="emoji-icon">📊</span>
                    <span class="side-menu__label">لوحة التحكم</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item {{ request()->routeIs('udhiya.animals.*') ? 'active' : '' }}"
                   href="{{ route('udhiya.animals.index') }}">
                    <span class="emoji-icon">🐄</span>
                    <span class="side-menu__label">الحيوانات</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item {{ request()->routeIs('udhiya.groups.*') ? 'active' : '' }}"
                   href="{{ route('udhiya.groups.index') }}">
                    <span class="emoji-icon">👥</span>
                    <span class="side-menu__label">المجموعات</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item {{ request()->routeIs('udhiya.contracts.*') ? 'active' : '' }}"
                   href="{{ route('udhiya.contracts.index') }}">
                    <span class="emoji-icon">📋</span>
                    <span class="side-menu__label">الصكوك</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item {{ request()->routeIs('udhiya.customers.*') ? 'active' : '' }}"
                   href="{{ route('udhiya.customers.index') }}">
                    <span class="emoji-icon">🙋</span>
                    <span class="side-menu__label">العملاء</span>
                </a>
            </li>

            <li class="side-item side-item-category">المشتريات</li>

            <li class="slide">
                <a class="side-menu__item {{ request()->routeIs('udhiya.suppliers.*') ? 'active' : '' }}"
                   href="{{ route('udhiya.suppliers.index') }}">
                    <span class="emoji-icon">🚚</span>
                    <span class="side-menu__label">الموردون</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item {{ request()->routeIs('udhiya.purchases.*') ? 'active' : '' }}"
                   href="{{ route('udhiya.purchases.index') }}">
                    <span class="emoji-icon">🛒</span>
                    <span class="side-menu__label">المشتريات</span>
                </a>
            </li>

            <li class="side-item side-item-category">التحليلات</li>

            <li class="slide">
                <a class="side-menu__item {{ request()->routeIs('udhiya.reports.*') ? 'active' : '' }}"
                   href="{{ route('udhiya.reports.index') }}">
                    <span class="emoji-icon">📈</span>
                    <span class="side-menu__label">التقارير</span>
                </a>
            </li>

            @if(Auth::user()->isAdmin())
            <li class="side-item side-item-category">الإدارة</li>
            <li class="slide">
                <a class="side-menu__item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                   href="{{ route('admin.users.index') }}">
                    <span class="emoji-icon">👤</span>
                    <span class="side-menu__label">المستخدمون</span>
                </a>
            </li>
            @endif

            @endif

        </ul>
    </div>
</aside>
<!-- main-sidebar closed -->
