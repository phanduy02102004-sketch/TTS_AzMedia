<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cổng Tuyển Dụng Thực Tập Sinh - AZ Media Đà Nẵng')</title>
    <meta name="description" content="Hệ thống tuyển dụng thực tập sinh AZ Media - Định hình tư duy, Chinh phục thị trường số. Cơ hội thực tập CNTT, Marketing, AI tại Đà Nẵng.">

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                            dark: '#0f172a',
                            navy: '#1e293b',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-brand { background: linear-gradient(135deg, #ea580c 0%, #f97316 50%, #fb923c 100%); }
        .gradient-dark { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        .text-gradient { background: linear-gradient(135deg, #ea580c 0%, #f97316 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .site-nav-link {
            position: relative;
            padding: 0.65rem 0.15rem;
        }
        .site-nav-link::after {
            position: absolute;
            right: 0.15rem;
            bottom: 0.25rem;
            left: 0.15rem;
            height: 2px;
            content: "";
            background: #ea580c;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 180ms ease;
        }
        .site-nav-link:hover,
        .site-nav-link.is-active {
            color: #ea580c;
        }
        .site-nav-link:hover::after,
        .site-nav-link.is-active::after {
            transform: scaleX(1);
        }

        /* Hiệu ứng nhấp nháy nút Ứng tuyển */
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 8px rgba(249,115,22,0.4), 0 4px 15px rgba(234,88,12,0.2); transform: scale(1); }
            50% { box-shadow: 0 0 20px rgba(249,115,22,0.7), 0 4px 25px rgba(234,88,12,0.4); transform: scale(1.05); }
        }
        .btn-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        .btn-pulse-glow:hover {
            animation: none;
            transform: scale(1.07);
            box-shadow: 0 0 25px rgba(249,115,22,0.8), 0 6px 30px rgba(234,88,12,0.5);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">

    <!-- Top Announcement Bar -->
    <div class="bg-slate-900 text-slate-300 text-sm py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-brand-600 text-white animate-pulse">
                    TUYỂN DỤNG ĐỢT 2026
                </span>
                <span>🔥 Đang mở đợt tuyển Thực tập sinh tại <strong>84 Nguyễn Hữu Dật, Đà Nẵng</strong></span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <a href="tel:0968825068" class="hover:text-brand-400 transition flex items-center gap-1">
                    <i class="fa-solid fa-phone text-xs"></i> 0968.825.068
                </a>
                <span class="hidden sm:inline">|</span>
                @if(Session::has('intern_user'))
                    <a href="{{ route('azmedia.intern.dashboard') }}" class="hover:text-orange-300 transition flex items-center gap-1 text-orange-400 font-semibold">
                        <i class="fa-solid fa-user-check text-xs"></i> TTS: {{ Session::get('intern_user')['name'] }}
                    </a>
                    <span class="hidden sm:inline">|</span>
                @else
                    <a href="{{ route('azmedia.intern.login') }}" class="hover:text-orange-300 transition flex items-center gap-1 text-orange-400 font-semibold">
                        <i class="fa-solid fa-right-to-bracket text-xs"></i> Đăng nhập
                    </a>
                    <span class="hidden sm:inline">|</span>
                @endif
                <a href="{{ route('azmedia.graduation.index') }}" class="hover:text-orange-300 transition flex items-center gap-1 text-orange-400 font-semibold">
                    <i class="fa-solid fa-graduation-cap text-xs"></i> Chuẩn đầu ra TTS
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200 shadow-[0_8px_24px_rgba(15,23,42,0.06)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-28 lg:h-32">
                <a href="{{ route('azmedia.home') }}" aria-label="AZmedia - Trang chủ" class="flex shrink-0 items-center group">
                    <img src="{{ asset('frontend/assets/images/azmedia-logo.png') }}" alt="AZ Media" class="block w-52 lg:w-60 h-auto object-contain object-left transition group-hover:scale-[1.03]">
                </a>

                <!-- Desktop Menu -->
                <nav id="desktop-site-navigation" class="hidden xl:flex items-center gap-5 2xl:gap-6 text-base font-bold text-slate-700">
                    <a href="{{ route('azmedia.home') }}" class="site-nav-link whitespace-nowrap transition">
                        Trang chủ
                    </a>
                    <a href="{{ route('azmedia.home') }}#positions" class="site-nav-link whitespace-nowrap transition">
                        Vị trí Tuyển dụng
                    </a>
                    <a href="{{ route('azmedia.home') }}#internship-program" class="site-nav-link whitespace-nowrap transition">
                        Lộ trình TTS
                    </a>
                    <a href="{{ route('azmedia.home') }}#core-values" class="site-nav-link whitespace-nowrap transition">
                        7 Giá trị cốt lõi
                    </a>
                    <a href="{{ route('azmedia.home') }}#about" class="site-nav-link whitespace-nowrap transition">
                        Về AZ Media
                    </a>
                    <a href="{{ route('azmedia.graduation.index') }}" class="site-nav-link whitespace-nowrap transition flex items-center gap-1.5">
                        <i class="fa-solid fa-user-graduate text-xs"></i> Chuẩn đầu ra TTS
                    </a>
                </nav>

                <div class="hidden xl:flex items-center">
                    <a href="{{ route('azmedia.home') }}#positions" class="whitespace-nowrap px-4 py-2.5 text-sm font-bold text-white gradient-brand rounded-xl transition btn-pulse-glow">
                        Ứng tuyển ngay <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <!-- Mobile Hamburger Toggle -->
                <div class="flex xl:hidden">
                    <button id="mobile-menu-btn" type="button" class="text-slate-600 hover:text-slate-900 p-2 rounded-lg focus:outline-none">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div id="mobile-menu" class="hidden xl:hidden border-t border-slate-100 bg-white px-4 pt-3 pb-6 space-y-2 shadow-lg">
            <a href="{{ route('azmedia.home') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">Trang chủ</a>
            <a href="{{ route('azmedia.home') }}#positions" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">Vị trí tuyển dụng</a>
            <a href="{{ route('azmedia.home') }}#internship-program" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">Lộ trình thực tập</a>
            <a href="{{ route('azmedia.home') }}#core-values" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">7 Giá trị cốt lõi</a>
            <a href="{{ route('azmedia.home') }}#about" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600">Giới thiệu AZ Media</a>
            <a href="{{ route('azmedia.graduation.index') }}" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-700 hover:bg-orange-50 hover:text-brand-600 flex items-center justify-between">
                <span><i class="fa-solid fa-user-graduate text-brand-600 mr-2"></i> Chuẩn đầu ra TTS</span>
            </a>
            <div class="pt-2">
                <a href="{{ route('azmedia.home') }}#positions" class="block w-full text-center px-4 py-2.5 rounded-xl font-bold text-white gradient-brand btn-pulse-glow">
                    Ứng tuyển ngay
                </a>
            </div>
        </div>
    </header>

    @if(session('success') || session('error') || session('warning') || (isset($errors) && $errors->any()))
        @php
            $notificationType = session('error') || (isset($errors) && $errors->any())
                ? 'error'
                : (session('warning') ? 'warning' : 'success');
        @endphp
        <div id="flash-notification" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm" role="presentation">
            <div class="absolute inset-0" data-notification-backdrop></div>
            <section
                class="relative w-full max-w-md rounded-3xl border bg-white p-6 shadow-2xl sm:p-8 {{ $notificationType === 'success' ? 'border-emerald-100' : ($notificationType === 'warning' ? 'border-amber-100' : 'border-rose-100') }}"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="flash-notification-title"
                aria-describedby="flash-notification-message"
            >
                <button type="button" class="absolute right-4 top-4 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" data-notification-close aria-label="Đóng thông báo">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full {{ $notificationType === 'success' ? 'bg-emerald-100 text-emerald-600' : ($notificationType === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600') }}">
                    <i class="fa-solid {{ $notificationType === 'success' ? 'fa-circle-check' : ($notificationType === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-exclamation') }} text-2xl"></i>
                </div>
                <h2 id="flash-notification-title" class="mb-3 text-center text-lg font-extrabold text-slate-900">
                    {{ $notificationType === 'success' ? 'Thành công' : ($notificationType === 'warning' ? 'Lưu ý' : 'Có lỗi xảy ra') }}
                </h2>
                <div id="flash-notification-message" class="text-center text-sm leading-relaxed text-slate-600">
                    @if(session('success'))
                        <p>{{ session('success') }}</p>
                    @elseif(session('warning'))
                        <p>{{ session('warning') }}</p>
                    @elseif(session('error'))
                        <p>{{ session('error') }}</p>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <p class="mb-2 font-semibold text-rose-700">Vui lòng kiểm tra lại thông tin:</p>
                        <ul class="space-y-1 text-left">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button type="button" class="mt-6 w-full rounded-xl px-4 py-3 text-sm font-bold text-white transition {{ $notificationType === 'success' ? 'bg-emerald-600 hover:bg-emerald-700' : ($notificationType === 'warning' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-rose-600 hover:bg-rose-700') }}" data-notification-close data-notification-dismiss>
                    Đã hiểu
                </button>
            </section>
        </div>
    @endif

    <!-- Main Content Slot -->
    <main class="{{ request()->routeIs('azmedia.intern.login', 'azmedia.intern.register') ? 'flex-none' : 'flex-1' }}">
        @yield('content')
    </main>

    <!-- Footer with Full AZ Media Profile & Student Info -->
    <footer class="bg-[#ff8d00] text-stone-800 text-base leading-relaxed pt-16 pb-10 border-t border-brand-200 {{ request()->routeIs('azmedia.intern.login', 'azmedia.intern.register') ? 'mt-0' : 'mt-20' }}">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-12">
                <!-- Col 1: AZ Media Overview -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex rounded-lg bg-white p-2">
                            <img src="{{ asset('frontend/assets/images/azmedia-logo.png') }}" alt="AZ Media" class="block w-24 h-auto object-contain">
                        </span>
                        <div>
                            <p class="text-sm text-stone-700">Coaching & Marketing Online</p>
                        </div>
                    </div>
                    <p class="mb-4">
                        <strong>CÔNG TY TNHH ĐẦU TƯ PHÁT TRIỂN DOANH NGHIỆP AZ HOMES – CHI NHÁNH AZ MEDIA</strong><br>
                        Slogan: <em>“Định hình tư duy – Chinh phục thị trường số”</em>. Tiên phong đào tạo kỹ năng thực chiến và giải pháp chuyển đổi số cho cá nhân và doanh nghiệp.
                    </p>
                    <div class="flex items-center gap-3 text-brand-800">
                        <a href="https://www.facebook.com/share/1A5N2tisXh/?mibextid=wwXIfr" target="_blank" class="w-9 h-9 rounded-lg bg-brand-50 hover:bg-brand-200 flex items-center justify-center transition shadow-sm" title="Facebook Fanpage AZ Media">
                            <i class="fa-brands fa-facebook-f text-base"></i>
                        </a>
                        <a href="https://www.tiktok.com/@ctyazmedia" target="_blank" class="w-9 h-9 rounded-lg bg-brand-50 hover:bg-brand-200 flex items-center justify-center transition" title="TikTok @ctyazmedia">
                            <i class="fa-brands fa-tiktok text-base"></i>
                        </a>
                        <a href="https://www.instagram.com/azmediaacademy/" target="_blank" class="w-9 h-9 rounded-lg bg-brand-50 hover:bg-brand-200 flex items-center justify-center transition" title="Instagram">
                            <i class="fa-brands fa-instagram text-base"></i>
                        </a>
                        <a href="https://www.threads.com/@congtyazmedia" target="_blank" class="w-9 h-9 rounded-lg bg-brand-50 hover:bg-brand-200 flex items-center justify-center transition" title="Threads">
                            <i class="fa-brands fa-threads text-base"></i>
                        </a>
                        <a href="https://azmedia.com.vn/" target="_blank" class="w-9 h-9 rounded-lg bg-brand-50 hover:bg-brand-200 flex items-center justify-center transition" title="Website chính thức">
                            <i class="fa-solid fa-globe text-base"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Văn phòng & Pháp lý -->
                <div>
                    <h4 class="text-lg font-bold text-brand-900 uppercase tracking-wider mb-4 border-l-2 border-brand-300 pl-2">Văn phòng & Pháp lý</h4>
                    <ul class="space-y-3">
                        <li>
                            <strong>📍 Chi nhánh Đà Nẵng:</strong><br>
                            84 Nguyễn Hữu Dật, Phường Hòa Cường, Quận Hải Châu, TP. Đà Nẵng
                        </li>
                        <li>
                            <strong>🏢 Trụ sở chính TP.HCM:</strong><br>
                            Tầng 3, Tòa nhà WMC, 102A-B-C Cống Quỳnh, P. Bến Thành, TP. Hồ Chí Minh
                        </li>
                        <li>
                            <strong>📋 Mã số thuế:</strong> 0314247738-002
                        </li>
                        <li>
                            <strong>👤 Đại diện pháp luật:</strong> Tổng Giám Đốc Lê Đình Quang
                        </li>
                        <li>
                            <strong>📞 Hotline:</strong> <a href="tel:0968825068" class="font-semibold underline underline-offset-4">0968.825.068</a>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Triết lý & Giá trị cốt lõi -->
                <div>
                    <h4 class="text-lg font-bold text-brand-900 uppercase tracking-wider mb-4 border-l-2 border-brand-300 pl-2">Triết lý AZ Media</h4>
                    <ul class="space-y-2">
                        <li><strong>A (Action):</strong> "Học lý thuyết để nắm rõ bản chất, hành động để thành công"</li>
                        <li><strong>Z (Zoom):</strong> Tầm nhìn xa trông rộng, sự phát triển vượt bậc</li>
                        <li><strong>M (Media):</strong> Truyền thông số & Giải pháp công nghệ</li>
                        <li><strong>🌱 Môi trường:</strong> "Thầy giỏi - Bạn tốt - Sách hay"</li>
                        <li><strong>🎯 Tận tâm:</strong> "Có tâm ắt sẽ có tầm"</li>
                        <li><strong>⭐ Đích đến:</strong> Từ "bình thường" sang "phi thường", từ "bé nhỏ" thành "khổng lồ"</li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 border-t border-brand-200 text-center text-sm flex flex-col sm:flex-row justify-between items-center gap-4">
                <p>© 2026 AZ Media - Bản quyền thuộc Công ty TNHH Đầu tư Phát triển Doanh nghiệp AZ Homes.</p>
                <p>Hệ thống Tuyển dụng Thực tập sinh trực tuyến thiết kế theo chuẩn Client-Server & RESTful API.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Drawer Script -->
    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        const flashNotification = document.getElementById('flash-notification');
        if (flashNotification) {
            const dismissNotification = () => flashNotification.remove();
            flashNotification.querySelectorAll('[data-notification-close]').forEach((button) => {
                button.addEventListener('click', dismissNotification);
            });
            flashNotification.querySelector('[data-notification-backdrop]')?.addEventListener('click', dismissNotification);
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    dismissNotification();
                }
            });
            flashNotification.querySelector('[data-notification-dismiss]')?.focus();
        }

        const desktopNavigation = document.getElementById('desktop-site-navigation');
        if (desktopNavigation) {
            const navigationLinks = Array.from(desktopNavigation.querySelectorAll('.site-nav-link'));
            const setActiveLink = (activeLink) => {
                navigationLinks.forEach((link) => {
                    const isActive = link === activeLink;
                    link.classList.toggle('is-active', isActive);
                    if (isActive) {
                        link.setAttribute('aria-current', 'page');
                    } else {
                        link.removeAttribute('aria-current');
                    }
                });
            };
            const currentLink = navigationLinks.find((link) => {
                const linkUrl = new URL(link.href, window.location.origin);
                return linkUrl.pathname === window.location.pathname
                    && linkUrl.hash === window.location.hash;
            });

            if (currentLink) {
                setActiveLink(currentLink);
            }

            desktopNavigation.addEventListener('click', (event) => {
                const selectedLink = event.target.closest('.site-nav-link');
                if (selectedLink) {
                    setActiveLink(selectedLink);
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
