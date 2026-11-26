@extends('azmedia.layouts.app')

@section('title', 'Đăng Ký Tài Khoản Mới – AZ Media Đà Nẵng')

@section('content')
<div class="min-h-[calc(100vh-11rem)] bg-slate-50 bg-cover bg-center py-8 flex items-center justify-center px-4 sm:px-6 lg:px-8" style="background-image: url('{{ asset('uploads/intern-login-background.jpg') }}');">
    <div class="max-w-xl w-full space-y-8">

        <!-- Card Form Đăng Ký -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 space-y-6">
            <form action="{{ route('azmedia.intern.register.submit') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="redirect" value="{{ $redirectUrl }}">

                <h1 class="mb-4 text-center text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                    Đăng Ký Tài Khoản Mới
                </h1>

                <!-- Họ và tên -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Họ và tên của bạn <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required
                           value="{{ old('name') }}"
                           placeholder="vd: Nguyễn Thị Thanh Hằng"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <!-- Email đã ứng tuyển -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center justify-between">
                        <span>Email ứng tuyển <span class="text-rose-500">*</span></span>
                        <span class="text-[10px] text-brand-600 font-semibold">Trùng với email đã nộp CV</span>
                    </label>
                    <input type="email" name="email" required
                           value="{{ old('email', $suggestedEmail) }}"
                           placeholder="vd: Hn2500215@gmail.com"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                </div>

                <!-- Số điện thoại & Mã hồ sơ -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Số điện thoại <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" required
                               value="{{ old('phone') }}"
                               placeholder="vd: 0843619219"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mã hồ sơ (nếu có)</label>
                        <input type="text" name="application_code"
                               value="{{ old('application_code', $suggestedCode) }}"
                               placeholder="vd: AZ-2026-0732"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none font-mono">
                    </div>
                </div>

                <!-- Mật khẩu & Nhập lại mật khẩu -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Mật khẩu mới <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required minlength="6"
                               placeholder="Tối thiểu 6 ký tự"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Xác nhận mật khẩu <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" required minlength="6"
                               placeholder="Nhập lại mật khẩu"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <!-- Nút Submit -->
                <button type="submit" class="w-full py-3.5 rounded-2xl gradient-brand text-white font-extrabold text-sm shadow-lg hover:shadow-orange-500/30 transition active:scale-98 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-key"></i> Kích Hoạt Tài Khoản & Bắt Đầu
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Đã kích hoạt tài khẩu trước đó?</span>
                <a href="{{ route('azmedia.intern.login', ['redirect' => $redirectUrl]) }}" class="font-extrabold text-brand-600 hover:text-brand-700">
                    Đăng nhập tại đây <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
