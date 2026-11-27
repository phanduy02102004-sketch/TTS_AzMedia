@extends('azmedia.layouts.app')

@section('title', 'Thông tin cá nhân - Cổng Thực tập sinh AZ Media')

@section('content')
@php
    $contactFields = [
        ['name' => 'name', 'label' => 'Họ và tên', 'required' => true, 'span' => 'md:col-span-2'],
        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
        ['name' => 'phone', 'label' => 'Số điện thoại', 'type' => 'tel', 'required' => true],
        ['name' => 'address', 'label' => 'Địa chỉ', 'span' => 'md:col-span-2'],
    ];
    $educationFields = [
        ['name' => 'university', 'label' => 'Trường đại học / cao đẳng', 'required' => true, 'span' => 'md:col-span-2'],
        ['name' => 'major', 'label' => 'Chuyên ngành', 'required' => true],
        ['name' => 'academic_year', 'label' => 'Năm học', 'placeholder' => 'Ví dụ: Năm 3'],
        ['name' => 'gpa', 'label' => 'GPA', 'type' => 'number', 'step' => '0.01', 'min' => '0', 'max' => '4'],
        ['name' => 'github_url', 'label' => 'Liên kết GitHub', 'type' => 'url', 'placeholder' => 'https://github.com/...'],
        ['name' => 'portfolio_url', 'label' => 'Portfolio / LinkedIn', 'type' => 'url', 'placeholder' => 'https://...'],
    ];
@endphp
<div class="min-h-screen bg-slate-50 py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('azmedia.intern.dashboard') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 inline-flex items-center gap-2 mb-3"><i class="fa-solid fa-arrow-left"></i> Quay lại bảng điều khiển</a>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Thông tin cá nhân</h1>
                <p class="mt-1 text-sm text-slate-500">Xem và cập nhật hồ sơ thực tập sinh của bạn.</p>
            </div>
            <div class="flex items-center gap-3 rounded-2xl bg-white border border-slate-200 px-4 py-3 shadow-sm">
                <div class="w-10 h-10 rounded-xl gradient-brand text-white font-extrabold flex items-center justify-center text-lg">{{ mb_strtoupper(mb_substr($candidate->name, 0, 1)) }}</div>
                <div><div class="text-sm font-extrabold text-slate-800">{{ $candidate->name }}</div><div class="text-[11px] text-slate-500">Tài khoản thực tập sinh</div></div>
            </div>
        </div>

        <form method="POST" action="{{ route('azmedia.intern.profile.update') }}" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            @csrf
            @method('PUT')
            <div class="p-6 sm:p-8 space-y-8">
                <section>
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-5"><div class="w-10 h-10 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center"><i class="fa-solid fa-address-card"></i></div><div><h2 class="font-extrabold text-slate-900">Thông tin liên hệ</h2><p class="text-xs text-slate-500">Thông tin dùng để AZ Media liên hệ với bạn.</p></div></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">@foreach($contactFields as $field) @include('azmedia.intern.partials.profile-input', ['field' => $field, 'candidate' => $candidate]) @endforeach</div>
                </section>
                <section>
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-5"><div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center"><i class="fa-solid fa-graduation-cap"></i></div><div><h2 class="font-extrabold text-slate-900">Học vấn và hồ sơ năng lực</h2><p class="text-xs text-slate-500">Cập nhật để hồ sơ ứng tuyển luôn chính xác.</p></div></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($educationFields as $field) @include('azmedia.intern.partials.profile-input', ['field' => $field, 'candidate' => $candidate]) @endforeach
                        <div><label for="working_shift" class="block text-xs font-bold text-slate-700 mb-2">Ca thực tập <span class="text-rose-500">*</span></label><select id="working_shift" name="working_shift" required class="w-full rounded-xl border border-slate-300 px-3.5 py-3 text-sm text-slate-800 focus:border-brand-500 focus:ring-brand-500">@foreach(['morning' => 'Buổi sáng', 'afternoon' => 'Buổi chiều', 'fulltime' => 'Toàn thời gian', 'flexible' => 'Linh hoạt'] as $key => $label)<option value="{{ $key }}" {{ old('working_shift', $candidate->working_shift) === $key ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select>@error('working_shift')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror</div>
                    </div>
                </section>
            </div>
            <div class="px-6 sm:px-8 py-5 bg-slate-50 border-t border-slate-200 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3"><p class="text-xs text-slate-500">Các trường có dấu <span class="text-rose-500">*</span> là bắt buộc.</p><button type="submit" class="inline-flex justify-center items-center gap-2 px-5 py-3 rounded-xl gradient-brand text-white text-sm font-extrabold shadow-sm hover:opacity-95 transition"><i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi</button></div>
        </form>
    </div>
</div>
@endsection
