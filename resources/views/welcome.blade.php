<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>برنامج الأضاحي 🐄 - الواجهة الرئيسية</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 relative min-h-screen text-slate-800 antialiased overflow-x-hidden">
    
    {{-- Decorative Background --}}
    <div class="absolute inset-0 z-0">
        <div class="absolute top-0 start-0 w-full h-[600px] bg-gradient-to-b from-indigo-50/80 to-slate-50 mix-blend-multiply"></div>
        <div class="absolute top-[-10%] end-[-5%] w-[800px] h-[800px] bg-blue-100 rounded-full blur-3xl opacity-30 pointer-events-none"></div>
        <div class="absolute top-[20%] start-[-10%] w-[600px] h-[600px] bg-indigo-100 rounded-full blur-3xl opacity-30 pointer-events-none"></div>
    </div>

    {{-- Navigation --}}
    <nav class="relative z-10 w-full border-b border-slate-200/50 bg-white/60 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-tr from-indigo-600 to-blue-500 rounded-xl flex items-center justify-center text-white text-xl shadow-md transform -rotate-3">
                        🐄
                    </div>
                    <span class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-l from-indigo-700 to-blue-600">برنامج الأضاحي</span>
                </div>
                
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('udhiya.dashboard') }}" class="font-bold text-slate-600 hover:text-indigo-600 transition">لوحة التحكم</a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-bold text-white rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:ring-4 focus:ring-indigo-500/30 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            تسجيل الدخول <span class="ms-2">←</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <main class="relative z-10 pt-20 pb-32 sm:pt-32 lg:pb-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-50 border border-indigo-100/50 text-indigo-700 text-sm font-bold mb-8">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                أفضل نظام لإدارة الأضاحي والمواشي
            </div>
            
            <h1 class="text-5xl sm:text-6xl md:text-7xl font-black text-slate-900 tracking-tight mb-8 leading-tight">
                أدر أعمال <span class="bg-clip-text text-transparent bg-gradient-to-l from-indigo-600 to-blue-500">الأضاحي</span><br class="hidden sm:block"/> بكل سهولة واحترافية
            </h1>
            
            <p class="max-w-2xl mx-auto text-lg sm:text-xl text-slate-600 font-medium mb-10 leading-relaxed">
                نظام سحابي متكامل يتيح لك إدارة القطيع، الصكوك، العملاء، والمبيعات بضغطة زر. صُمم خصيصاً ليناسب احتياجات مزارع المواشي والجمعيات الخيرية.
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                @auth
                    <a href="{{ route('udhiya.dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white rounded-2xl bg-indigo-600 hover:bg-indigo-700 transition shadow-xl shadow-indigo-200 transform hover:-translate-y-1">
                        الذهاب للوحة التحكم
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-white rounded-2xl bg-indigo-600 hover:bg-indigo-700 transition shadow-xl shadow-indigo-200 transform hover:-translate-y-1">
                        إبدأ الآن
                    </a>
                @endauth
            </div>
        </div>
    </main>

    {{-- Features Section --}}
    <section class="relative z-10 py-20 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Feature 1 --}}
                <div class="group p-8 bg-slate-50 rounded-3xl border border-slate-100 hover:border-indigo-100 hover:bg-indigo-50/30 transition duration-300 pointer-events-none sm:pointer-events-auto">
                    <div class="w-14 h-14 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-center text-2xl mb-6 group-hover:scale-110 group-hover:shadow-md transition-all">
                        📋
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">إدارة الصكوك الذكية</h3>
                    <p class="text-slate-600 font-medium">إصدار وتتبع ومتابعة حالة صكوك الأضاحي مع العملاء وتتبع مراحل ذبحها وتوزيعها.</p>
                </div>
                
                {{-- Feature 2 --}}
                <div class="group p-8 bg-slate-50 rounded-3xl border border-slate-100 hover:border-blue-100 hover:bg-blue-50/30 transition duration-300 pointer-events-none sm:pointer-events-auto">
                    <div class="w-14 h-14 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-center text-2xl mb-6 group-hover:scale-110 group-hover:shadow-md transition-all">
                        🐄
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">سجل متكامل للقطيع</h3>
                    <p class="text-slate-600 font-medium">سجل شامل لكل رأس يتضمن النوع، الوزن، المورد، والتكلفة وحتى مرحلة البيع.</p>
                </div>
                
                {{-- Feature 3 --}}
                <div class="group p-8 bg-slate-50 rounded-3xl border border-slate-100 hover:border-emerald-100 hover:bg-emerald-50/30 transition duration-300 pointer-events-none sm:pointer-events-auto">
                    <div class="w-14 h-14 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-center text-2xl mb-6 group-hover:scale-110 group-hover:shadow-md transition-all">
                        📊
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">تقارير مالية دقيقة</h3>
                    <p class="text-slate-600 font-medium">متابعة دقيقة للأرباح والمصروفات، الإيرادات المتبقية، وتقارير تفصيلية لكل عميل ומورد.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-slate-900 border-t border-slate-800 py-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-3 mb-6">
                <div class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center text-white text-md">
                    🐄
                </div>
                <span class="text-xl font-bold text-white">برنامج الأضاحي</span>
            </div>
            <p class="text-slate-400 font-medium font-sm mb-6">
                جميع الحقوق محفوظة &copy; {{ date('Y') }}
            </p>
        </div>
    </footer>

</body>
</html>
