
<!-- Sidebar backdrop (mobile only) -->
<div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-20 lg:hidden lg:z-auto transition-opacity duration-200"
     :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'"
     aria-hidden="true" x-cloak></div>

<!-- Sidebar -->
<div id="sidebar" class="flex flex-col fixed lg:static z-30 start-0 top-16 lg:top-auto lg:start-auto lg:translate-x-0 h-[calc(100vh-64px)] lg:h-screen overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-72 bg-white border-e border-slate-200 transition-all duration-200 ease-in-out shadow-sm"
     :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
     @click.outside="sidebarOpen = false"
     @keydown.escape.window="sidebarOpen = false">

    <!-- Sidebar header -->
    <div class="flex justify-between items-center pe-3 sm:pe-6 lg:pe-8 py-5 px-4 border-b border-slate-100">
        <button class="lg:hidden text-slate-500 hover:text-slate-400" @click.stop="sidebarOpen = !sidebarOpen" aria-controls="sidebar" :aria-expanded="sidebarOpen">
            <span class="sr-only">إغلاق</span>
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
            </svg>
        </button>
        <a class="flex items-center gap-2 text-xl font-bold bg-clip-text text-transparent bg-gradient-to-l from-indigo-600 to-blue-500" href="{{ route('udhiya.dashboard') }}">
            <span>برنامج الأضاحي</span>
            <span>🐄</span>
        </a>
    </div>

    <!-- Links -->
    <div class="space-y-6 flex-1 py-6">

        {{-- Main navigation --}}
        <div class="px-3">
                {{-- Public site link --}}
        <div class="px-3 pb-2">
            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-500 hover:bg-emerald-50 hover:text-emerald-700 transition-colors border border-dashed border-slate-200 hover:border-emerald-200">
                <span class="text-lg shrink-0">🌐</span>
                <span class="text-sm font-semibold">الصفحة الرئيسية</span>
                <svg class="w-3.5 h-3.5 mr-auto opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
            <p class="px-4 mb-2 text-[10px] font-black tracking-widest text-slate-400 uppercase">القائمة الرئيسية</p>
            <ul class="space-y-0.5">
                @php
                    $links = [
                        ['route' => 'udhiya.dashboard',            'icon' => '📊', 'label' => 'الرئيسية',           'pattern' => 'udhiya.dashboard'],
                        ['route' => 'udhiya.animals.index',        'icon' => '🐑', 'label' => 'الأضاحي',            'pattern' => 'udhiya.animals.index'],
                        ['route' => 'udhiya.animals.by-warehouse', 'icon' => '🏪', 'label' => 'الأضاحي بالموقع',    'pattern' => 'udhiya.animals.by-warehouse'],
                        ['route' => 'udhiya.customers.index',      'icon' => '👥', 'label' => 'العملاء',            'pattern' => 'udhiya.customers.*'],
                        ['route' => 'udhiya.contracts.index',      'icon' => '📑', 'label' => 'الصكوك',             'pattern' => 'udhiya.contracts.*'],
                        ['route' => 'udhiya.contract-requests.index', 'icon' => '📋', 'label' => 'طلبات الاشتراك',  'pattern' => 'udhiya.contract-requests.*'],
                        ['route' => 'udhiya.purchases.index',      'icon' => '🛒', 'label' => 'المشتريات',          'pattern' => 'udhiya.purchases.*'],
                        ['route' => 'udhiya.wallets.index',        'icon' => '💰', 'label' => 'الخزائن',            'pattern' => 'udhiya.wallets.*'],
                        ['route' => 'udhiya.suppliers.index',      'icon' => '🚚', 'label' => 'الموردين',           'pattern' => 'udhiya.suppliers.*'],
                        ['route' => 'udhiya.advances.index',       'icon' => '💸', 'label' => 'السلف',              'pattern' => 'udhiya.advances.*'],
                        ['route' => 'udhiya.accounts.index',       'icon' => '📊', 'label' => 'دليل الحسابات',    'pattern' => 'udhiya.accounts.*'],
                        ['route' => 'udhiya.groups.index',         'icon' => '🔪', 'label' => 'الذبح',              'pattern' => 'udhiya.groups.*'],
                        ['route' => 'udhiya.expenses.index',       'icon' => '💳', 'label' => 'المصروفات',          'pattern' => 'udhiya.expenses.*'],
                        ['route' => 'udhiya.collections.index',    'icon' => '🎯', 'label' => 'تحصيل الدفعات',     'pattern' => 'udhiya.collections.*'],
                        ['route' => 'udhiya.meat-inventory.index', 'icon' => '🧊', 'label' => 'مخزن اللحوم',       'pattern' => 'udhiya.meat-inventory.*'],
                        ['route' => 'udhiya.reports.index',        'icon' => '📈', 'label' => 'التقارير',           'pattern' => 'udhiya.reports.*'],
                    ];
                @endphp
                @foreach($links as $link)
                <li>
                    <a href="{{ route($link['route']) }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors {{ request()->routeIs($link['pattern']) ? 'bg-indigo-50/80 text-indigo-600 font-semibold shadow-sm ring-1 ring-indigo-100/50' : '' }}">
                        <span class="text-lg shrink-0 drop-shadow-sm">{{ $link['icon'] }}</span>
                        <span class="text-sm">{{ $link['label'] }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- Admin section — only for admin/owner/manager --}}
        @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('owner') || Auth::user()->hasRole('manager'))
        <div class="px-3">
            <p class="px-4 mb-2 text-[10px] font-black tracking-widest text-slate-400 uppercase">الإدارة</p>
            <ul class="space-y-0.5">
                <li>
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-purple-50/80 text-purple-600 font-semibold shadow-sm ring-1 ring-purple-100/50' : '' }}">
                        <span class="text-lg shrink-0">👤</span>
                        <span class="text-sm">المستخدمون</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.roles.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-purple-50/80 text-purple-600 font-semibold shadow-sm ring-1 ring-purple-100/50' : '' }}">
                        <span class="text-lg shrink-0">🔑</span>
                        <span class="text-sm">الصلاحيات</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.activity-logs.index') }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors {{ request()->routeIs('admin.activity-logs.*') ? 'bg-purple-50/80 text-purple-600 font-semibold shadow-sm ring-1 ring-purple-100/50' : '' }}">
                        <span class="text-lg shrink-0">📋</span>
                        <span class="text-sm">سجل النشاط</span>
                    </a>
                </li>
            </ul>
        </div>
        @endif

    

    </div>
</div>
