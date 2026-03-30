
<!-- Sidebar backdrop (mobile only) -->
<div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-40 lg:hidden lg:z-auto transition-opacity duration-200" 
     :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" 
     aria-hidden="true" x-cloak></div>

<!-- Sidebar -->
<div id="sidebar" class="flex flex-col absolute z-40 start-0 top-0 lg:static lg:start-auto lg:top-auto lg:translate-x-0 h-screen overflow-y-scroll lg:overflow-y-auto no-scrollbar w-64 lg:w-72 bg-white border-e border-slate-200 transition-all duration-200 ease-in-out shadow-sm" 
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
    <div class="space-y-8 flex-1 py-6">
        <div class="px-3">
            <ul class="space-y-1">
                @php
                    $links = [
                        ['route' => 'udhiya.dashboard', 'icon' => '📊', 'label' => 'الرئيسية', 'pattern' => 'udhiya.dashboard'],
                        ['route' => 'udhiya.animals.index', 'icon' => '🐄', 'label' => 'الحيوانات', 'pattern' => 'udhiya.animals.*'],
                        ['route' => 'udhiya.customers.index', 'icon' => '👥', 'label' => 'العملاء', 'pattern' => 'udhiya.customers.*'],
                        ['route' => 'udhiya.contracts.index', 'icon' => '📑', 'label' => 'الصكوك', 'pattern' => 'udhiya.contracts.*'],
                        ['route' => 'udhiya.purchases.index', 'icon' => '🛒', 'label' => 'المشتريات', 'pattern' => 'udhiya.purchases.*'],
                        ['route' => 'udhiya.suppliers.index', 'icon' => '🚚', 'label' => 'الموردين', 'pattern' => 'udhiya.suppliers.*'],
                        ['route' => 'udhiya.groups.index', 'icon' => '🔪', 'label' => 'الذبح', 'pattern' => 'udhiya.groups.*'],
                        ['route' => 'udhiya.reports.index', 'icon' => '📈', 'label' => 'التقارير', 'pattern' => 'udhiya.reports.*'],
                    ];
                @endphp
                
                @foreach($links as $link)
                <li>
                    <a href="{{ route($link['route']) }}" class="flex items-center space-x-3 space-x-reverse px-4 py-2.5 rounded-xl text-slate-700 hover:bg-slate-50 transition-colors {{ request()->routeIs($link['pattern']) ? 'bg-indigo-50/80 text-indigo-600 font-semibold shadow-sm ring-1 ring-indigo-100/50' : '' }}">
                        <span class="text-xl shrink-0 drop-shadow-sm">{{ $link['icon'] }}</span>
                        <span>{{ $link['label'] }}</span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
