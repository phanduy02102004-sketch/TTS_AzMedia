@extends('azmedia.layouts.app')

@section('title', 'Kết Quả Đánh Giá Năng Lực Toàn Diện - ' . $application->candidate->name)

@push('styles')

@endpush

@section('content')

@php
    $test = $application->testResult;
    $data = $test ? $test->answers_data : [];
    $nlp = $data['nlp'] ?? null;
    $personality = $data['personality'] ?? null;
    $wheel = $data['wheel_of_life'] ?? null;
    $survey = $data['survey'] ?? null;
    $azSurvey = $data['az_survey'] ?? null;
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-8">
    <!-- Header Thông Báo Hoàn Thành -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-10 text-center">
        <div class="w-20 h-20 rounded-full {{ ($test->passed ?? true) ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center text-4xl mx-auto mb-5 shadow-sm">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-extrabold uppercase tracking-wider mb-2">
            Đã Lưu Hồ Sơ Đánh Giá Thành Công
        </span>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">
            Hoàn Thành Bài Đánh Giá Tuyển Dụng AZHR!
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto">
            Hệ thống đã phân tích phong cách tư duy NLP, Bánh xe cuộc đời và đánh giá độ hiểu biết văn hóa AZ Media của bạn cho vị trí <strong>{{ $application->position->title }}</strong>.
        </p>

        <!-- Thống kê điểm văn hóa AZ -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 max-w-xl mx-auto mt-6">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="text-3xl font-black text-brand-600">{{ $test->score ?? 100 }}</div>
                <div class="text-[11px] text-slate-500 font-bold mt-1">Điểm Văn Hóa AZ</div>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="text-3xl font-black text-slate-800">{{ $test->correct_answers ?? 0 }} / {{ $test->total_questions ?? 0 }}</div>
                <div class="text-[11px] text-slate-500 font-bold mt-1">Câu trả lời đúng</div>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="text-3xl font-black text-slate-800">{{ round(($test->time_spent_seconds ?? 600) / 60) }}'</div>
                <div class="text-[11px] text-slate-500 font-bold mt-1">Thời gian làm</div>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <div class="text-2xl font-black text-emerald-600 mt-0.5">ĐẠT</div>
                <div class="text-[11px] text-emerald-700 font-bold mt-1">Sẵn sàng Phỏng vấn</div>
            </div>
        </div>
    </div>

    @if($nlp)
        <!-- Kết quả NLP Tính cách (File 2) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 sm:p-8">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg">
                        🔮
                    </span>
                    <div>
                        <h3 class="font-extrabold text-base text-slate-900">Kết Quả Nhận Diện Tính Cách NLP (V-A-K-Ad)</h3>
                        <p class="text-xs text-slate-500">Mô hình nhận thức giác quan & phong cách tư duy của ứng viên</p>
                    </div>
                </div>
                <span class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-orange-50 text-brand-700 border border-orange-200">
                    Tuýp: {{ $nlp['dominant_title'] ?? 'Đa dạng' }}
                </span>
            </div>

            <div class="p-4 rounded-2xl bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 mb-6">
                <div class="text-xs font-bold text-orange-900 uppercase tracking-wider mb-1">
                    Đặc điểm tư duy nổi trội của bạn:
                </div>
                <p class="text-sm text-slate-800 leading-relaxed font-medium">
                    {{ $nlp['dominant_summary'] ?? '' }}
                </p>
            </div>

            <!-- 4 Cột điểm V - A - K - Ad -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 text-center">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 {{ ($nlp['dominant_code'] ?? '') == 'V' ? 'ring-2 ring-orange-500 bg-orange-50/50' : '' }}">
                    <div class="text-xs font-bold text-slate-500">Visual (Nhìn)</div>
                    <div class="text-2xl font-black text-orange-600 mt-1">{{ $nlp['scores']['Visual'] ?? 0 }}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Thị giác / Hình ảnh</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 {{ ($nlp['dominant_code'] ?? '') == 'A' ? 'ring-2 ring-blue-500 bg-blue-50/50' : '' }}">
                    <div class="text-xs font-bold text-slate-500">Auditory (Nghe)</div>
                    <div class="text-2xl font-black text-blue-600 mt-1">{{ $nlp['scores']['Auditory'] ?? 0 }}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Thính giác / Âm thanh</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 {{ ($nlp['dominant_code'] ?? '') == 'K' ? 'ring-2 ring-emerald-500 bg-emerald-50/50' : '' }}">
                    <div class="text-xs font-bold text-slate-500">Kinesthetic (Cảm)</div>
                    <div class="text-2xl font-black text-emerald-600 mt-1">{{ $nlp['scores']['Kinesthetic'] ?? 0 }}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Cảm xúc / Trải nghiệm</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 {{ ($nlp['dominant_code'] ?? '') == 'Ad' ? 'ring-2 ring-purple-500 bg-purple-50/50' : '' }}">
                    <div class="text-xs font-bold text-slate-500">Auditory Digital (Logic)</div>
                    <div class="text-2xl font-black text-purple-600 mt-1">{{ $nlp['scores']['Auditory_Digital'] ?? 0 }}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Nội tâm / Lập luận</div>
                </div>
            </div>

            @if(!empty($nlp['raw_input']))
                @php
                    $nlpQuestionScores = $nlp['raw_input'];
                    $nlpTypes = [
                        'V' => ['label' => 'Visual (Nhìn)', 'total' => 'Visual'],
                        'A' => ['label' => 'Auditory (Nghe)', 'total' => 'Auditory'],
                        'K' => ['label' => 'Kinesthetic (Cảm xúc)', 'total' => 'Kinesthetic'],
                        'Ad' => ['label' => 'Auditory Digital (Nội tâm)', 'total' => 'Auditory_Digital'],
                    ];
                @endphp

                <div class="mt-6">
                    <h4 class="font-extrabold text-sm text-slate-900 mb-1">Bảng tổng kết điểm từng câu trả lời</h4>
                    <p class="text-xs text-slate-500 mb-3">Điểm số bạn đã chọn cho từng phong cách ở mỗi câu hỏi.</p>
                    <div class="overflow-x-auto rounded-2xl border border-slate-200">
                        <table class="w-full min-w-[640px] text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-700">
                                    <th class="py-2.5 px-3 border-b border-slate-200 font-bold">Câu hỏi</th>
                                    @foreach($nlpTypes as $type)
                                        <th class="py-2.5 px-3 border-b border-slate-200 text-center font-bold">{{ $type['label'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach(range(1, 5) as $questionNumber)
                                    <tr>
                                        <td class="py-2.5 px-3 font-semibold text-slate-800">Câu {{ $questionNumber }}</td>
                                        @foreach($nlpTypes as $code => $type)
                                            <td class="py-2.5 px-3 text-center text-slate-700">
                                                {{ data_get($nlpQuestionScores, $questionNumber . '.' . $code) ?? '—' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr class="bg-slate-50 font-extrabold text-slate-900">
                                    <td class="py-2.5 px-3">Tổng cộng</td>
                                    @foreach($nlpTypes as $type)
                                        <td class="py-2.5 px-3 text-center">{{ data_get($nlp, 'scores.' . $type['total'], 0) }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Bảng Thu Hoạch Đánh Giá Tính Cách Toàn Diện (NLP – DISC – MBTI – Nhân Số Học) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 sm:p-8 space-y-5">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg">
                    📊
                </span>
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">Báo Cáo Thu Hoạch NLP – DISC – MBTI</h3>
                    <p class="text-xs text-slate-500">Tổng hợp 3 phương pháp đánh giá tính cách và phong cách làm việc của ứng viên</p>
                </div>
            </div>
            <span class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                Chuẩn Nhân Sự AZ Media
            </span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold">
                        <th class="py-3 px-4 border-b border-slate-800">Phương pháp đánh giá</th>
                        <th class="py-3 px-4 border-b border-slate-800">Kết quả ghi nhận</th>
                        <th class="py-3 px-4 border-b border-slate-800">Đặc điểm cốt lõi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-bold text-slate-900">
                            <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-orange-500"></span> NLP (Tiếp nhận thông tin)</span>
                        </td>
                        <td class="py-3 px-4 font-bold text-orange-600">
                            {{ ($personality['nlp'] ?? $personality['nlb'] ?? [])['dominant_title'] ?? 'Thiên về Visual (Hình ảnh)' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600">
                            Tiếp nhận và xử lý công việc qua thị giác, mẫu thiết kế, giao diện và quan sát thực tế.
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-bold text-slate-900">
                            <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> DISC (Cách làm việc)</span>
                        </td>
                        <td class="py-3 px-4 font-bold text-blue-600">
                            {{ $personality['disc']['title'] ?? 'Thiên về C – Cẩn trọng' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600">
                            Chú ý chi tiết, yêu cầu sự chính xác, chu đáo và muốn hiểu rõ bản chất vấn đề trước khi thực hiện.
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-bold text-slate-900">
                            <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span> MBTI (Tính cách)</span>
                        </td>
                        <td class="py-3 px-4 font-bold text-indigo-600">
                            {{ $personality['mbti']['title'] ?? 'Nghiêng về I (Hướng nội), F (Cảm xúc)' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600">
                            Coi trọng sự hài hòa, thực tế kết hợp sáng tạo; bản tính trầm tính, khả năng quan sát thầm lặng và nhìn nhận sâu sắc.
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-bold text-slate-900">
                            <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Nhân số học</span>
                        </td>
                        <td class="py-3 px-4 font-bold text-purple-600">
                            {{ $personality['numerology']['title'] ?? 'Đường đời số 9' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600">
                            Lòng trắc ẩn, tính bao dung, trực giác tốt, tinh thần trách nhiệm và mong muốn mang lại giá trị tích cực cho cộng đồng.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if($wheel)
        <!-- Kết quả Bánh xe cuộc đời (File 3) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 sm:p-8">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
                <span class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
                </span>
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">Bánh xe cuộc đời</h3>
                    <p class="text-xs text-slate-500">Mức độ tự nhận thức và phân bổ năng lượng trong cuộc sống</p>
                </div>
            </div>

            @php
                $wheelShortLabels = [
                    'finance' => 'Tài chính',
                    'personal_growth' => 'Phát triển',
                    'health' => 'Sức khỏe',
                    'family' => 'Gia đình',
                    'relationships' => 'Quan hệ',
                    'social_life' => 'Xã hội',
                    'spirituality' => 'Tinh thần',
                    'career' => 'Sự nghiệp',
                ];
                $wheelChartItems = [];
                foreach ($wheel['dimensions'] as $key => $dimension) {
                    $wheelChartItems[] = [
                        'key' => $key,
                        'short_label' => $wheelShortLabels[$key] ?? $dimension['label'],
                        'score' => $dimension['score'],
                    ];
                }
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="flex justify-center">
                    <div class="w-full max-w-[440px] aspect-square">
                        @include('azmedia.candidate.partials.wheel_chart', ['chartId' => 'completedWheelChart'])
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="space-y-2 text-sm" aria-label="Điểm số từng lĩnh vực">
                        @foreach($wheel['dimensions'] as $key => $dim)
                            <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 py-2.5 border-b border-slate-100 last:border-b-0">
                                <span class="text-slate-700 font-medium">{{ $wheelShortLabels[$key] ?? $dim['label'] }}</span>
                                <span class="min-w-[4rem] text-right tabular-nums font-extrabold {{ $dim['score'] >= 7 ? 'text-emerald-600' : ($dim['score'] >= 5 ? 'text-blue-600' : 'text-amber-600') }}">
                                    {{ $dim['score'] }}<span class="text-slate-400 font-semibold">/10</span>
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if(!empty($wheel['lesson']))
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                            <span class="font-bold text-slate-800 block mb-1">
                                <i class="fa-solid fa-lightbulb text-amber-500 mr-1" aria-hidden="true"></i> Bài học tự nhận thức:
                            </span>
                            <p class="text-slate-600 italic leading-relaxed">"{{ $wheel['lesson'] }}"</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($survey)
        <!-- Kết quả Khảo sát Hoài bão, Động cơ & Tự đánh giá (File 1) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 sm:p-8">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
                <span class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg">
                    🎯
                </span>
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">Hoài Bão, Động Cơ & Tự Nhận Thức Bản Thân</h3>
                    <p class="text-xs text-slate-500">Mục tiêu phát triển và cam kết đồng hành cùng AZHR</p>
                </div>
            </div>

            <div class="space-y-4 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="font-bold text-indigo-700 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-flag text-xs"></i> 1. Mục tiêu 5 tháng tới tại AZHR:
                        </div>
                        <p class="text-slate-700 leading-relaxed">{{ $survey['goal_short'] ?? 'Chưa cung cấp' }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="font-bold text-indigo-700 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-mountain text-xs"></i> 2. Mục tiêu nghề nghiệp 5 năm tới:
                        </div>
                        <p class="text-slate-700 leading-relaxed">{{ $survey['goal_long'] ?? 'Chưa cung cấp' }}</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="font-bold text-slate-800 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-trophy text-amber-500 text-xs"></i> 3. Thành công lớn nhất & Bài học rút ra:
                    </div>
                    <p class="text-slate-700 leading-relaxed">{{ $survey['biggest_success'] ?? 'Chưa cung cấp' }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="font-bold text-slate-800 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-heart text-rose-500 text-xs"></i> 4. Lý do lựa chọn AZHR:
                        </div>
                        <p class="text-slate-700 leading-relaxed">{{ $survey['why_apply'] ?? 'Chưa cung cấp' }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="font-bold text-slate-800 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-list-check text-emerald-600 text-xs"></i> 5. Phương pháp tổ chức công việc:
                        </div>
                        <p class="text-slate-700 leading-relaxed">{{ $survey['work_organization'] ?? 'Chưa cung cấp' }}</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="font-bold text-slate-800 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-lightbulb text-amber-500 text-xs"></i> 6. Thất bại / sai lầm & Bài học đắt giá:
                    </div>
                    <p class="text-slate-700 leading-relaxed">{{ $survey['failure_lesson'] ?? 'Chưa cung cấp' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200">
                    <div class="font-bold text-orange-900 mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-star text-orange-500 text-xs"></i> 7. Lý do AZHR nên chọn bạn:
                    </div>
                    <p class="text-slate-800 font-medium leading-relaxed">{{ $survey['why_choose_you'] ?? 'Chưa cung cấp' }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- 4. Tìm Hiểu & Cảm Nhận Về AZ Media -->
    @if($azSurvey)
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-brand-600 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900">4. Kênh Tìm Hiểu & Cảm Nhận Về AZ Media</h2>
                    <p class="text-xs text-slate-500">Thông tin khảo sát mức độ quan tâm và hiểu biết về doanh nghiệp</p>
                </div>
            </div>

            <div class="space-y-4 text-xs sm:text-sm">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="font-bold text-slate-800 mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-bullhorn text-brand-500 text-xs"></i> Kênh biết đến và tìm hiểu về AZ Media:
                    </div>
                    @if(!empty($azSurvey['channels']))
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach($azSurvey['channels'] as $ch)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                                    <i class="fa-solid fa-check text-[10px]"></i> {{ $ch }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                    @if(!empty($azSurvey['channel_other']))
                        <div class="mt-2 text-slate-600 italic">
                            <span class="font-semibold text-slate-700">Ghi chú khác:</span> {{ $azSurvey['channel_other'] }}
                        </div>
                    @endif
                    @if(empty($azSurvey['channels']) && empty($azSurvey['channel_other']))
                        <p class="text-slate-500">Chưa cung cấp</p>
                    @endif
                </div>

                @if(!empty($azSurvey['impression']))
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-orange-50/60 to-amber-50/60 border border-orange-200">
                        <div class="font-bold text-orange-900 mb-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-heart text-rose-500 text-xs"></i> Điều ấn tượng nhất & Mong muốn thực tập tại AZ Media:
                        </div>
                        <p class="text-slate-800 font-medium leading-relaxed whitespace-pre-line">{{ $azSurvey['impression'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Banner Nhóm Zalo Hỗ Trợ & Nhận Thông Báo -->
    <div class="bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 rounded-3xl border-2 border-emerald-300 p-6 sm:p-7 shadow-md flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-lg shadow-md flex-shrink-0">
                Zalo
            </div>
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800 mb-1">
                    Kênh Liên Lạc Ứng Viên Chính Thức
                </span>
                <h3 class="text-base sm:text-lg font-black text-slate-900">
                    Nhóm AZ HR - ỨNG TUYỂN
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed mt-0.5">
                    Tham gia nhóm Zalo để nhận thông báo lịch phỏng vấn, trao đổi trực tiếp với Mentor và cập nhật kết quả tuyển dụng nhanh nhất!
                </p>
            </div>
        </div>
        <a href="https://zalo.me/g/fcypks402" target="_blank"
           class="px-6 py-3.5 rounded-xl font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg transition text-xs sm:text-sm flex items-center gap-2 whitespace-nowrap flex-shrink-0">
            <i class="fa-solid fa-comments text-base"></i> Tham Gia Nhóm Zalo Ngay
        </a>
    </div>

    <!-- Bottom Actions -->
    <div class="flex flex-col sm:flex-row justify-center gap-3 pt-2">
        <a href="{{ route('azmedia.candidate.test.pdf', $application->application_code) }}" target="_blank"
           class="px-7 py-3.5 rounded-xl font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md hover:shadow-lg transition text-xs sm:text-sm text-center flex items-center justify-center gap-2">
            <i class="fa-solid fa-file-pdf"></i> Tải & Xem Bản PDF Bài Đánh Giá (Phần 1, 2, 3, 4)
        </a>
        <a href="{{ route('azmedia.candidate.track', ['code' => $application->application_code]) }}"
           class="px-8 py-3.5 rounded-xl font-bold text-white gradient-brand shadow hover:shadow-lg transition text-xs sm:text-sm text-center">
            <i class="fa-solid fa-timeline mr-1"></i> Quay lại Trang Tra Cứu Tiến Độ
        </a>
        <a href="{{ route('azmedia.home') }}"
           class="px-6 py-3.5 rounded-xl font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition text-xs sm:text-sm text-center">
            Trang Chủ Tuyển Dụng
        </a>
    </div>
</div>

@endsection
