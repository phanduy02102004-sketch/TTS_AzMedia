@extends('azmedia.layouts.app')

@section('title', 'Đăng Nhập Cổng Thực Tập Sinh – AZ Media Đà Nẵng')

@section('content')
<div class="min-h-[calc(100vh-11rem)] bg-slate-900 bg-cover bg-center py-8 flex items-center justify-center px-4 sm:px-6 lg:px-8" style="background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.58), rgba(15, 23, 42, 0.3) 55%, rgba(124, 45, 18, 0.38)), url('{{ asset('frontend/assets/images/intern-login-background.jpg') }}');">
    <div class="max-w-xl w-full space-y-8">

        <!-- Card Form Đăng Nhập -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
            <form action="{{ route('azmedia.intern.login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="redirect" value="{{ $redirectUrl }}">

                <h1 class="mb-4 text-center text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                    Đăng Nhập Tài Khoản TTS
                </h1>

                <!-- Email, MSSV hoặc Mã hồ sơ -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Email, MSSV hoặc Mã hồ sơ ứng tuyển <span class="text-rose-500">*</span></span>
                        <span class="text-[10px] text-slate-400 font-normal">VD: 24C0682 hoặc AZ-2026-0732</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user-tag text-xs"></i>
                        </div>
                        <input type="text" name="login_id" required
                               value="{{ old('login_id', $suggestedCode) }}"
                               placeholder="Nhập email, mã số sinh viên (MSSV) hoặc mã hồ sơ..."
                               class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none font-medium">
                    </div>
                </div>

                <!-- Mật khẩu -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Mật khẩu <span class="text-rose-500">*</span></label>
                        <a href="{{ route('azmedia.intern.register', ['redirect' => $redirectUrl]) }}" class="text-[11px] text-brand-600 font-bold hover:underline">
                            Chưa có mật khẩu? Kích hoạt ngay
                        </a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </div>
                        <input type="password" name="password" required
                               placeholder="Nhập mật khẩu của bạn..."
                               class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <!-- Nút Submit -->
                <button type="submit" class="w-full py-3.5 rounded-2xl gradient-brand text-white font-extrabold text-sm shadow-lg hover:shadow-orange-500/30 transition active:scale-98 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập Cổng Thực Tập Sinh
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <span class="text-slate-500">Chưa tạo tài khoản hoặc chưa có mật khẩu?</span>
                <a href="{{ route('azmedia.intern.register', ['redirect' => $redirectUrl]) }}" class="font-extrabold text-brand-600 hover:text-brand-700">
                    Kích hoạt tài khoản TTS <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i>
                </a>
            </div>
        </div>

    </div>
</div>

@endsection
