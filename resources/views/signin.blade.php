<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>تسجيل الدخول - برنامج الأضاحي</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-white min-h-screen flex selection:bg-indigo-500 selection:text-white">

    <!-- SPLIT LAYOUT -->
    <div class="flex w-full overflow-hidden">
        
        <!-- LEFT SCREEN: BRANDING & ART (Fixed on large screens) -->
        <div class="hidden lg:flex lg:w-1/2 relative bg-slate-900 overflow-hidden items-center justify-center">
            <!-- Animated gradient background layer -->
            <div class="absolute inset-0 bg-gradient-to-tr from-indigo-900 via-slate-900 to-indigo-900 z-0"></div>
            
            <!-- Floating orbs -->
            <div class="absolute top-[10%] start-[20%] w-96 h-96 bg-indigo-500/20 rounded-full blur-[100px] z-0 animate-pulse delay-75"></div>
            <div class="absolute bottom-[10%] end-[20%] w-96 h-96 bg-violet-500/20 rounded-full blur-[100px] z-0 animate-pulse"></div>
            
            <div class="relative z-10 w-full max-w-lg px-12 text-center text-white">
                <div class="w-24 h-24 bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl mx-auto flex items-center justify-center text-6xl shadow-2xl transform rotate-6 mb-12">
                    🐄
                </div>
                <h1 class="text-5xl font-black mb-6 leading-tight">مرحباً بك في <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-400">نظام الأضاحي السحابي</span></h1>
                <p class="text-xl text-slate-300 font-medium leading-relaxed">
                    منصتك الموحدة لإدارة القطعان، توزيع الحصص، متابعة العملاء والموردين بكل احترافية، وتوثيق سجلات الموسم من البداية حتى الختام.
                </p>
                
                <!-- Simple mock interface element to look like "SaaS" -->
                <div class="mt-16 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 shadow-2xl text-start transform -rotate-2 hover:rotate-0 transition duration-500">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400">✓</div>
                        <div>
                            <div class="text-sm font-bold">تم تخصيص الحصة بنجاح</div>
                            <div class="text-xs text-slate-400">رسالة تنبيهية تظهر للعملاء</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="absolute bottom-6 mx-auto text-slate-500 text-sm font-bold z-10">
                مدعوم بأحدث تقنيات الإدارة والمحاسبة الذكية © {{ date('Y') }}
            </div>
        </div>

        <!-- RIGHT SCREEN: LOGIN FORM -->
        <div class="w-full lg:w-1/2 flex items-center justify-center bg-white lg:rounded-s-[3rem] shadow-[0px_0px_50px_rgba(0,0,0,0.1)] z-20 relative px-6 py-12 lg:px-0">
            <!-- Mobile decorative orb -->
            <div class="absolute lg:hidden top-0 end-0 w-64 h-64 bg-indigo-50 rounded-full blur-[80px] -z-10"></div>

            <div class="w-full max-w-md mx-auto z-10">
                <!-- Mobile only brand header -->
                <div class="lg:hidden text-center mb-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-2xl mx-auto flex items-center justify-center text-3xl shadow-lg transform -rotate-3 mb-6">
                        🐄
                    </div>
                    <h2 class="text-3xl font-black text-slate-800">برنامج الأضاحي</h2>
                </div>

                <div class="mb-10 lg:text-start text-center">
                    <h2 class="text-4xl font-black text-slate-900 tracking-tight mb-2">تسجيل الدخول</h2>
                    <p class="text-slate-500 font-medium text-lg">أدخل بياناتك للوصول إلى لوحة التحكم الخاصة بك</p>
                </div>

                @if ($errors->any())
                    <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-8 rounded-e-xl shadow-sm">
                        <div class="flex items-start">
                            <div class="text-rose-500 text-xl font-bold ms-3">!</div>
                            <div>
                                <h3 class="text-sm font-bold text-rose-800 mb-1">واجهنا بعض المشاكل</h3>
                                <ul class="list-disc list-inside text-sm text-rose-700 font-medium">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label class="block text-sm font-bold text-slate-700">البريد الإلكتروني</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none transition-colors duration-200">
                                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="peer w-full rounded-xl border-slate-200 bg-slate-50 border shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300 py-3 px-4 ps-11 font-medium placeholder-slate-400 @error('email') border-rose-300 ring-rose-300 @enderror"
                                placeholder="name@company.com">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-sm font-bold text-slate-700">كلمة المرور</label>
                            <a href="#" tabindex="-1" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">هل نسيت كلمة المرور؟</a>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none transition-colors duration-200">
                                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input type="password" name="password" required
                                class="w-full rounded-xl border-slate-200 bg-slate-50 border shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all duration-300 py-3 px-4 ps-11 font-medium placeholder-slate-400 @error('password') border-rose-300 ring-rose-300 @enderror"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center pt-2">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center justify-center">
                                <input type="checkbox" name="remember" class="peer appearance-none w-5 h-5 border-2 border-slate-300 rounded-md bg-white checked:bg-indigo-600 checked:border-indigo-600 transition-colors duration-200 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 cursor-pointer">
                                <svg class="absolute w-3 h-3 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition">تذكرني على هذا الجهاز</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full relative group overflow-hidden inline-flex items-center justify-center px-6 py-4 text-base font-bold text-white rounded-xl bg-slate-900 hover:bg-slate-800 transition duration-300 shadow-xl shadow-slate-200 transform hover:-translate-y-0.5 mt-4">
                        <span class="relative z-10 flex items-center gap-2">
                            تسجيل الدخول
                            <svg class="w-5 h-5 -rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                        <div class="absolute inset-0 h-full w-full opacity-0 group-hover:opacity-20 bg-gradient-to-r from-indigo-500 via-violet-500 to-indigo-500 transition-opacity duration-500 z-0"></div>
                    </button>
                </form>
                
                <div class="mt-12 flex justify-center">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-indigo-600 transition p-2 rounded-xl hover:bg-indigo-50">
                        <span class="text-lg">←</span> عودة إلى الواجهة الرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
