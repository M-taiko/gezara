<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>برنامج الأضاحي - منصة إدارة الأضاحي الاحترافية</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Cairo', sans-serif; }
        body { background: #f8f9fa; }

        .navbar { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }

        .hero {
            background: linear-gradient(135deg, #ffffff 0%, #f5f7fa 100%);
            padding: 80px 20px;
            text-align: center;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            color: #1a1a1a;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.8;
        }

        .btn-primary {
            background: #2c3e50;
            color: white;
            padding: 15px 40px;
            border-radius: 8px;
            font-weight: 900;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(44, 62, 80, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #2c3e50;
            border: 2px solid #2c3e50;
            padding: 13px 38px;
            border-radius: 8px;
            font-weight: 900;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
            margin-right: 15px;
        }

        .btn-secondary:hover {
            background: #f5f7fa;
            transform: translateY(-2px);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin: 80px 0;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            border-top: 4px solid #2c3e50;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            font-weight: 900;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .feature-card p {
            color: #666;
            font-size: 1rem;
            line-height: 1.8;
        }

        .cta-section {
            background: white;
            padding: 60px 20px;
            text-align: center;
            margin-top: 80px;
            border-top: 2px solid #e0e0e0;
            border-bottom: 2px solid #e0e0e0;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 900;
            color: #1a1a1a;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 60px 0;
            flex-wrap: wrap;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            color: #2c3e50;
        }

        .stat-label {
            color: #666;
            font-weight: 600;
            margin-top: 10px;
        }

        .services {
            max-width: 1200px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .services h2 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 900;
            color: #1a1a1a;
            margin-bottom: 50px;
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .service-item {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        }

        .service-item h4 {
            font-size: 1.2rem;
            font-weight: 900;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .service-item p {
            color: #666;
            line-height: 1.8;
        }

        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: 80px;
        }

        .footer p {
            margin: 0;
            font-size: 0.9rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .btn-secondary {
                margin-bottom: 10px;
                display: block;
                width: 100%;
                max-width: 300px;
                margin-left: auto;
                margin-right: auto;
            }

            .stats {
                gap: 20px;
            }

            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    {{-- Navigation --}}
    <nav class="navbar sticky top-0 z-40">
        <div class="container">
            <div class="flex justify-between items-center py-5">
                <div class="flex items-center gap-3">
                    <div style="font-size: 2rem;">🐑</div>
                    <span style="font-size: 1.3rem; font-weight: 900; color: #2c3e50;">برنامج الأضاحي</span>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('udhiya.dashboard') }}" class="btn-primary" style="padding: 10px 25px; font-size: 1rem;">
                            لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('signin') }}" class="btn-secondary" style="margin: 0; padding: 10px 25px; font-size: 1rem; border: 2px solid #2c3e50;">
                            تسجيل الدخول
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="hero">
        <div class="container">
            <h1>🐑 منصة إدارة الأضاحي الاحترافية</h1>
            <p>
                نوفر لك حلاً متكاملاً لإدارة عملية الأضاحي من البداية إلى النهاية، مع تتبع العملاء والعقود والمدفوعات بكل سهولة
            </p>

            <div style="margin-top: 40px;">
                <a href="#order-now" class="btn-primary">اطلب الآن</a>
                <a href="#features" class="btn-secondary">تعرف على المزيد</a>
            </div>

            {{-- Toast Messages --}}
            @if(session('toast_success'))
            <div style="margin-top: 30px; padding: 15px 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; color: #155724; text-align: right;">
                ✅ {{ session('toast_success') }}
            </div>
            @endif

            @if(session('toast_error'))
            <div style="margin-top: 30px; padding: 15px 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; color: #721c24; text-align: right;">
                ❌ {{ session('toast_error') }}
            </div>
            @endif
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="container">
        <h2 style="text-align: center; font-size: 2.5rem; font-weight: 900; color: #1a1a1a; margin-bottom: 50px;">المميزات الرئيسية</h2>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">📋</div>
                <h3>إدارة العقود</h3>
                <p>إنشاء وتحرير وطباعة عقود الأضاحي بسهولة مع تتبع حالة كل عقد</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>إدارة العملاء</h3>
                <p>حفظ بيانات العملاء وتاريخ تعاملاتهم والتواصل معهم بسهولة</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>المدفوعات والفواتير</h3>
                <p>تسجيل المدفوعات وتوليد الفواتير والتقارير المالية</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🐄</div>
                <h3>إدارة الحيوانات</h3>
                <p>تسجيل الأضاحي ومتابعة حالتها والأسعار والأوزان</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>التقارير والإحصائيات</h3>
                <p>الحصول على تقارير شاملة عن الأرباح والمبيعات والعملاء</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🔔</div>
                <h3>إشعارات فورية</h3>
                <p>تنبيهات للطلبات الجديدة والمدفوعات المستحقة والتحديثات</p>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="cta-section">
        <div class="container">
            <h2>لماذا تختار برنامج الأضاحي؟</h2>
            <div class="stats">
                <div class="stat">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">احترافية عالية</div>
                </div>
                <div class="stat">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">متاح دائماً</div>
                </div>
                <div class="stat">
                    <div class="stat-number">آمن</div>
                    <div class="stat-label">حماية البيانات</div>
                </div>
                <div class="stat">
                    <div class="stat-number">سهل</div>
                    <div class="stat-label">واجهة بسيطة</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Services Section --}}
    <section class="services">
        <h2>خدماتنا</h2>
        <div class="service-grid">
            <div class="service-item">
                <h4>🏪 عرض الأضاحي</h4>
                <p>اعرض أضاحيك المتاحة بسهولة وادع العملاء لطلب الحصص المفضلة لهم</p>
            </div>

            <div class="service-item">
                <h4>📱 طلبات سهلة</h4>
                <p>العميل يقدم طلبه من الموقع بخطوات بسيطة وسهلة جداً</p>
            </div>

            <div class="service-item">
                <h4>✅ إدارة الطلبات</h4>
                <p>استقبل الطلبات وحولها إلى عقود مع اختيار الأضاحي المناسبة</p>
            </div>

            <div class="service-item">
                <h4>📄 عقود احترافية</h4>
                <p>عقود جاهزة للطباعة بشكل احترافي وسهل التخزين</p>
            </div>

            <div class="service-item">
                <h4>💵 تتبع المدفوعات</h4>
                <p>سجل جميع المدفوعات والفواتير ومتابعة الأرصدة</p>
            </div>

            <div class="service-item">
                <h4>📈 تقارير مفصلة</h4>
                <p>احصل على تقارير شاملة عن أرباحك والعملاء والبيانات</p>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section id="order-now" style="background: white; padding: 60px 20px; text-align: center; margin-top: 80px; border-top: 2px solid #e0e0e0;">
        <div class="container">
            <h2 style="font-size: 2.5rem; font-weight: 900; color: #1a1a1a; margin-bottom: 20px;">ابدأ الآن!</h2>
            <p style="font-size: 1.1rem; color: #666; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                اختر الأضحية المفضلة لديك وقدم طلبك الآن، أو قم بإدارة أضاحيك بكل سهولة من خلال لوحة التحكم الاحترافية
            </p>

            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                @if(count($categoriesData) > 0)
                <a href="#categories" class="btn-primary">🛍️ تصفح الأضاحي</a>
                @endif
                @auth
                <a href="{{ route('udhiya.dashboard') }}" class="btn-primary">📊 إدارة الأضاحي</a>
                @else
                <a href="{{ route('signin') }}" class="btn-secondary">🔐 تسجيل الدخول</a>
                @endauth
            </div>
        </div>
    </section>

    {{-- Categories Section (if authenticated or showing) --}}
    @if(count($categoriesData) > 0)
    <section id="categories" style="background: #f8f9fa; padding: 80px 20px;">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; font-weight: 900; color: #1a1a1a; margin-bottom: 50px;">الأضاحي المتاحة</h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
                @foreach($categoriesData as $category)
                <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 15px;">
                        @switch($category['name'])
                            @case('عجول') 🐄 @break
                            @case('جمال') 🐪 @break
                            @case('خرفان') 🐑 @break
                            @case('جديان') 🐐 @break
                            @default 🐑
                        @endswitch
                    </div>
                    <h3 style="font-size: 1.3rem; font-weight: 900; color: #1a1a1a; margin-bottom: 10px;">{{ $category['name'] }}</h3>
                    <p style="color: #666; margin-bottom: 15px;">أضحية مختارة بعناية</p>

                    <div style="margin: 20px 0;">
                        @foreach($category['availableShares'] as $type => $shareData)
                        <button type="button" onclick="openRequestModal({{ $category['id'] }}, '{{ $category['name'] }}', '{{ $type }}', {{ $shareData['minPrice'] }})"
                                style="display: inline-block; margin: 5px; padding: 10px 15px; background: #f5f7fa; border: 2px solid #2c3e50; color: #2c3e50; border-radius: 6px; font-weight: 700; cursor: pointer; transition: all 0.3s;">
                            {{ $shareData['label'] }}: من {{ number_format($shareData['minPrice'], 0) }} ج.م
                        </button>
                        @endforeach
                    </div>

                    <button type="button" onclick="openRequestModal({{ $category['id'] }}, '{{ $category['name'] }}')"
                            class="btn-primary" style="width: 100%; margin-top: 15px;">
                        اطلب الآن
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Request Modal --}}
    <div id="requestModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
        <div style="background: white; border-radius: 12px; max-width: 600px; width: 100%; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            {{-- Header --}}
            <div style="padding: 25px; background: #2c3e50; color: white; display: flex; justify-content: space-between; align-items: center;">
                <h6 style="margin: 0; font-size: 1.3rem; font-weight: 900;">إكمال الطلب</h6>
                <button type="button" onclick="closeRequestModal()" style="background: none; border: none; color: white; font-size: 2rem; cursor: pointer;">×</button>
            </div>

            <form action="{{ route('public-animals.submit-request') }}" method="POST" style="padding: 30px; space-y: 20px;">
                @csrf

                <input type="hidden" id="categoryIdInput" name="category_id">

                {{-- Customer Info --}}
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">الاسم الكامل <span style="color: red;">*</span></label>
                    <input type="text" name="customer_name" required
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">رقم الهاتف <span style="color: red;">*</span></label>
                    <input type="tel" name="customer_phone" required
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">البريد الإلكتروني <span style="color: red;">*</span></label>
                    <input type="email" name="customer_email" required
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">نوع الحصة <span style="color: red;">*</span></label>
                    <select id="shareTypeSelect" name="share_type" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem;">
                        <option value="">— اختر الحصة —</option>
                        <option value="full">كامل</option>
                        <option value="half">نصف</option>
                        <option value="third">ثُلث</option>
                        <option value="quarter">ربع</option>
                        <option value="five">خُمس</option>
                        <option value="six">سُدس</option>
                        <option value="seven">سُبع</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">السعر المبدئي (قابل للتغيير)</label>
                    <input type="text" id="sharePriceDisplay" readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; background: #f5f7fa;">
                    <p style="font-size: 0.85rem; color: #999; margin-top: 5px;">ℹ️ هذا السعر مبدئي فقط ويتم تأكيده بعد التواصل معك</p>
                    <input type="hidden" id="sharePriceInput" name="share_price" value="0">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; color: #1a1a1a; margin-bottom: 8px;">ملاحظات (اختياري)</label>
                    <textarea name="notes" rows="3"
                              style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; resize: none;"
                              placeholder="أي طلبات خاصة..."></textarea>
                </div>

                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn-primary" style="flex: 1;">✅ تقديم الطلب</button>
                    <button type="button" onclick="closeRequestModal()" class="btn-secondary" style="flex: 1;">إلغاء</button>
                </div>

                <div style="background: #e3f2fd; border: 1px solid #90caf9; border-radius: 8px; padding: 15px; margin-top: 15px; text-align: right;">
                    <p style="margin: 0; color: #1565c0; font-weight: 600;">📞 سيتم التواصل معك قريباً لتأكيد الطلب والسعر النهائي</p>
                </div>
            </form>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="footer">
        <div class="container">
            <p>© 2026 برنامج الأضاحي - جميع الحقوق محفوظة</p>
        </div>
    </footer>

    <script>
        let modalShares = {};

        function openRequestModal(categoryId, categoryName, shareType = null, sharePrice = null) {
            document.getElementById('categoryIdInput').value = categoryId;

            const categoryGrid = document.querySelector(`.share-grid[data-category-id="${categoryId}"]`);
            modalShares = {};

            if (categoryGrid) {
                const shareButtons = categoryGrid.querySelectorAll('[data-share-type]');
                shareButtons.forEach(btn => {
                    const type = btn.getAttribute('data-share-type');
                    const label = btn.getAttribute('data-share-label');
                    const price = parseFloat(btn.getAttribute('data-share-price'));
                    modalShares[type] = { label, price };
                });
            }

            document.getElementById('shareTypeSelect').value = '';
            document.getElementById('sharePriceDisplay').value = '';
            document.getElementById('sharePriceInput').value = '0';

            if (shareType && shareType !== 'undefined') {
                document.getElementById('shareTypeSelect').value = shareType;
                if (sharePrice && sharePrice !== 'undefined') {
                    const minPrice = parseFloat(sharePrice);
                    if (minPrice > 0) {
                        document.getElementById('sharePriceDisplay').value = minPrice.toLocaleString('ar-EG');
                        document.getElementById('sharePriceInput').value = minPrice;
                    }
                }
            }

            document.getElementById('requestModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeRequestModal() {
            document.getElementById('requestModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        document.getElementById('shareTypeSelect')?.addEventListener('change', function() {
            const shareType = this.value;

            if (shareType && modalShares[shareType]) {
                const price = modalShares[shareType].price;
                document.getElementById('sharePriceDisplay').value = price.toLocaleString('ar-EG');
                document.getElementById('sharePriceInput').value = price;
            } else {
                document.getElementById('sharePriceDisplay').value = '';
                document.getElementById('sharePriceInput').value = '0';
            }
        });

        document.getElementById('requestModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeRequestModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRequestModal();
            }
        });
    </script>
</body>
</html>
