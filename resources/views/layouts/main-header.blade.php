
<header class="sticky top-0 bg-white/80 backdrop-blur-md border-b border-slate-200 z-30 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 -mb-px">
            {{-- Mobile menu button --}}
            <div class="flex">
                <button class="text-slate-500 hover:text-slate-600 lg:hidden p-2 rounded-lg hover:bg-slate-100 transition" @click.stop="sidebarOpen = !sidebarOpen" aria-controls="sidebar" :aria-expanded="sidebarOpen">
                    <span class="sr-only">فتح القائمة</span>
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="5" width="16" height="2" rx="1" />
                        <rect x="4" y="11" width="16" height="2" rx="1" />
                        <rect x="4" y="17" width="16" height="2" rx="1" />
                    </svg>
                </button>
            </div>
            
            {{-- Header Right --}}
            <div class="flex items-center space-x-3 space-x-reverse">
                <div class="relative" x-data="{ open: false }">
                    <button class="inline-flex justify-center items-center group p-1.5 rounded-full hover:bg-slate-50 transition" aria-haspopup="true" @click.prevent="open = !open" :aria-expanded="open">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold shadow-sm">
                            م
                        </div>
                        <div class="hidden md:flex items-center truncate">
                            <span class="truncate ms-2 text-sm font-semibold text-slate-700 group-hover:text-slate-900 transition">المدير العام</span>
                            <svg class="w-3 h-3 shrink-0 ms-1 fill-current text-slate-400 group-hover:text-slate-600" viewBox="0 0 12 12">
                                <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                            </svg>
                        </div>
                    </button>
                    <div class="origin-top-left z-10 absolute top-full start-0 min-w-44 bg-white border border-slate-200 py-1.5 rounded-xl shadow-lg overflow-hidden mt-1" 
                         @click.outside="open = false" 
                         @keydown.escape.window="open = false" 
                         x-show="open" 
                         x-transition:enter="transition ease-out duration-200 transform" 
                         x-transition:enter-start="opacity-0 -translate-y-2" 
                         x-transition:enter-end="opacity-100 translate-y-0" 
                         x-transition:leave="transition ease-out duration-200" 
                         x-transition:leave-start="opacity-100" 
                         x-transition:leave-end="opacity-0" x-cloak>
                        <div class="pt-2 pb-2 px-4 mb-1 border-b border-slate-100">
                            <div class="font-bold text-slate-800">المدير العام</div>
                            <div class="text-xs text-slate-500">administrator</div>
                        </div>
                        <ul>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="w-full text-start font-medium text-sm text-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-colors flex items-center py-2 px-4" type="submit">
                                        تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
