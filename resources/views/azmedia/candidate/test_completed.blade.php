@extends('azmedia.layouts.app')

@section('title', 'Kết Quả Đánh Giá Năng Lực Toàn Diện - ' . $application->candidate->name)

@push('styles')
<style>
    .candidate-test-report .report-section {
        padding: 1.5rem;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        box-shadow: none;
    }

    .candidate-test-report .report-section-heading {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin: -1.5rem -1.5rem 1.25rem;
        padding: 0.85rem 1.5rem;
        background: #f1f5f9;
        border-bottom: 1px solid #cbd5e1;
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .candidate-test-report .report-section-heading > div {
        min-width: 0;
        flex: 1;
    }

    .candidate-test-report .report-section-heading h2,
    .candidate-test-report .report-section-heading h3 {
        margin: 0;
        color: #1e293b;
        font-size: 0.95rem;
        font-weight: 800;
        line-height: 1.45;
        text-transform: uppercase;
    }

    .candidate-test-report .report-section-heading p {
        margin-top: 0.2rem;
        color: #475569;
        font-size: 0.75rem;
        line-height: 1.5;
    }

    .candidate-test-report .report-icon {
        display: flex;
        width: 2rem;
        height: 2rem;
        flex: 0 0 2rem;
        align-items: center;
        justify-content: center;
        color: #334155;
    }

    .candidate-test-report .report-table-wrap {
        overflow-x: auto;
        border: 1px solid #cbd5e1;
        border-radius: 0.25rem;
    }

    .candidate-test-report .report-table {
        width: 100%;
        border-collapse: collapse;
        color: #1e293b;
        font-size: 0.75rem;
        text-align: left;
    }

    .candidate-test-report .report-table th {
        padding: 0.65rem 0.75rem;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        font-weight: 700;
        vertical-align: top;
    }

    .candidate-test-report .report-table td {
        padding: 0.65rem 0.75rem;
        border: 1px solid #cbd5e1;
        vertical-align: top;
    }

    .candidate-test-report .report-answer {
        padding: 0.8rem 0.9rem;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 0.25rem;
        break-inside: avoid;
    }

    .candidate-test-report .report-answer-label {
        display: block;
        margin-bottom: 0.3rem;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .candidate-test-report .report-answer p {
        color: #334155;
        font-size: 0.8125rem;
        line-height: 1.65;
        white-space: pre-line;
    }

    .candidate-test-report .report-channel-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem 1.1rem;
        margin: 0;
        padding-left: 1.1rem;
        color: #334155;
        list-style: disc;
    }

    @media (max-width: 640px) {
        .candidate-test-report .report-section-heading {
            flex-wrap: wrap;
        }

        .candidate-test-report .report-heading-badge {
            margin-left: 2.75rem;
        }
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 16mm 14mm;
        }

        body {
            background: #fff !important;
            color: #1e293b !important;
            font-size: 10pt;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body > div.bg-slate-900,
        body > header,
        body > footer,
        .candidate-test-report .report-utility {
            display: none !important;
        }

        .candidate-test-report {
            max-width: none !important;
            padding: 0 !important;
        }

        .candidate-test-report > :not([hidden]) ~ :not([hidden]) {
            margin-top: 10px !important;
        }

        .candidate-test-report .report-section {
            padding: 14px;
            border-color: #94a3b8;
            border-radius: 0;
            box-shadow: none;
            break-inside: auto;
        }

        .candidate-test-report .report-section-heading {
            margin: -14px -14px 12px;
            padding: 8px 14px;
            border-radius: 0;
            break-after: avoid;
        }

        .candidate-test-report .report-answer,
        .candidate-test-report .report-table-wrap,
        .candidate-test-report tr {
            break-inside: avoid;
        }

        .candidate-test-report .report-answer {
            padding: 8px 10px;
        }

        .candidate-test-report .report-table {
            font-size: 9pt;
        }

        .candidate-test-report .report-table th,
        .candidate-test-report .report-table td {
            padding: 6px 7px;
        }

        .candidate-test-report .report-answer p {
            font-size: 9.5pt;
        }
    }
</style>
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

<div class="candidate-test-report max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-8">
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

        @if(!empty($wheel['desired_dimensions']))
            @php
                $desiredWheelChartItems = [];
                foreach ($wheel['desired_dimensions'] as $key => $dimension) {
                    $desiredWheelChartItems[] = [
                        'key' => $key,
                        'short_label' => $wheelShortLabels[$key] ?? $dimension['label'],
                        'score' => $dimension['score'],
                    ];
                }
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center mt-6 pt-6 border-t border-slate-100">
                <div class="flex justify-center">
                    <div class="w-full max-w-[440px] aspect-square">
                        @include('azmedia.candidate.partials.wheel_chart', ['chartId' => 'desiredCompletedWheelChart', 'wheelChartItems' => $desiredWheelChartItems])
                    </div>
                </div>

                <div class="space-y-2 text-sm" aria-label="Điểm mong muốn trong 10 năm tới">
                    <h4 class="font-extrabold text-sm text-slate-800 mb-3">Bánh xe cuộc đời mong muốn trong 10 năm tới</h4>
                    @foreach($wheel['desired_dimensions'] as $key => $dim)
                        <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 py-2.5 border-b border-slate-100 last:border-b-0">
                            <span class="text-slate-700 font-medium">{{ $wheelShortLabels[$key] ?? $dim['label'] }}</span>
                            <span class="min-w-[4rem] text-right tabular-nums font-extrabold text-emerald-600">
                                {{ $dim['score'] }}<span class="text-slate-400 font-semibold">/10</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if($nlp)
        <!-- Kết quả NLP Tính cách (File 2) -->
        <div class="report-section">
            <div class="report-section-heading">
                <span class="report-icon" aria-hidden="true"><i class="fa-solid fa-brain"></i></span>
                <div>
                    <h3>Kết quả nhận diện tính cách NLP (V-A-K-Ad)</h3>
                    <p>Mô hình nhận thức giác quan & phong cách tư duy của ứng viên</p>
                </div>
                <span class="report-heading-badge ml-auto shrink-0 rounded border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-800">
                    Tuýp: {{ $nlp['dominant_title'] ?? 'Đa dạng' }}
                </span>
            </div>

            <div class="report-answer mb-5">
                <div class="report-answer-label">
                    Đặc điểm tư duy nổi trội của bạn:
                </div>
                <p>
                    {{ $nlp['dominant_summary'] ?? '' }}
                </p>
            </div>

            <div class="report-table-wrap">
                <table class="report-table min-w-[640px]">
                    <thead>
                        <tr>
                            <th>Visual (Thị giác / Nhìn)</th>
                            <th>Auditory (Thính giác / Nghe)</th>
                            <th>Kinesthetic (Cảm giác / Trải nghiệm)</th>
                            <th>Auditory Digital (Logic nội tâm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center font-bold tabular-nums">
                            <td>{{ $nlp['scores']['Visual'] ?? 0 }} điểm</td>
                            <td>{{ $nlp['scores']['Auditory'] ?? 0 }} điểm</td>
                            <td>{{ $nlp['scores']['Kinesthetic'] ?? 0 }} điểm</td>
                            <td>{{ $nlp['scores']['Auditory_Digital'] ?? 0 }} điểm</td>
                        </tr>
                    </tbody>
                </table>
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

                <div class="mt-5">
                    <h4 class="mb-1 text-sm font-bold text-slate-900">Bảng tổng kết điểm từng câu trả lời</h4>
                    <p class="mb-3 text-xs text-slate-600">Điểm số bạn đã chọn cho từng phong cách ở mỗi câu hỏi.</p>
                    <div class="report-table-wrap">
                        <table class="report-table min-w-[640px]">
                            <thead>
                                <tr>
                                    <th>Câu hỏi</th>
                                    @foreach($nlpTypes as $type)
                                        <th class="text-center">{{ $type['label'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(range(1, 5) as $questionNumber)
                                    <tr>
                                        <td class="font-semibold">Câu {{ $questionNumber }}</td>
                                        @foreach($nlpTypes as $code => $type)
                                            <td class="text-center">
                                                {{ data_get($nlpQuestionScores, $questionNumber . '.' . $code) ?? '—' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr class="bg-slate-50 font-extrabold">
                                    <td>Tổng cộng</td>
                                    @foreach($nlpTypes as $type)
                                        <td class="text-center">{{ data_get($nlp, 'scores.' . $type['total'], 0) }}</td>
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
    <div class="report-section space-y-5">
        <div class="report-section-heading">
            <span class="report-icon" aria-hidden="true"><i class="fa-solid fa-list-check"></i></span>
            <div>
                <h3>Báo cáo thu hoạch NLP – DISC – MBTI</h3>
                <p>Tổng hợp các phương pháp đánh giá tính cách và phong cách làm việc của ứng viên</p>
            </div>
            <span class="report-heading-badge ml-auto shrink-0 rounded border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700">
                Chuẩn Nhân Sự AZ Media
            </span>
        </div>

        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Phương pháp đánh giá</th>
                        <th>Kết quả ghi nhận</th>
                        <th>Đặc điểm cốt lõi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="font-bold">
                            <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-orange-500"></span> NLP (Tiếp nhận thông tin)</span>
                        </td>
                        <td class="font-bold">
                            {{ ($personality['nlp'] ?? $personality['nlb'] ?? [])['dominant_title'] ?? 'Thiên về Visual (Hình ảnh)' }}
                        </td>
                        <td>
                            Tiếp nhận và xử lý công việc qua thị giác, mẫu thiết kế, giao diện và quan sát thực tế.
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">
                            <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> DISC (Cách làm việc)</span>
                        </td>
                        <td class="font-bold">
                            {{ $personality['disc']['title'] ?? 'Thiên về C – Cẩn trọng' }}
                        </td>
                        <td>
                            Chú ý chi tiết, yêu cầu sự chính xác, chu đáo và muốn hiểu rõ bản chất vấn đề trước khi thực hiện.
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">
                            <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span> MBTI (Tính cách)</span>
                        </td>
                        <td class="font-bold">
                            {{ $personality['mbti']['title'] ?? 'Nghiêng về I (Hướng nội), F (Cảm xúc)' }}
                        </td>
                        <td>
                            Coi trọng sự hài hòa, thực tế kết hợp sáng tạo; bản tính trầm tính, khả năng quan sát thầm lặng và nhìn nhận sâu sắc.
                        </td>
                    </tr>
                    <tr>
                        <td class="font-bold">
                            <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Nhân số học</span>
                        </td>
                        <td class="font-bold">
                            {{ $personality['numerology']['title'] ?? 'Đường đời số 9' }}
                        </td>
                        <td>
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
                    <h4 class="font-extrabold text-sm text-slate-800">Bánh xe cuộc đời hiện tại</h4>
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
        <div class="report-section">
            <div class="report-section-heading">
                <span class="report-icon" aria-hidden="true"><i class="fa-solid fa-bullseye"></i></span>
                <div>
                    <h3>Hoài bão, Động cơ & Tự nhận thức bản thân</h3>
                    <p>Mục tiêu phát triển và cam kết đồng hành cùng AZHR</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="report-answer">
                    <strong class="report-answer-label">1. Mục tiêu 5 tháng tới tại AZHR</strong>
                    <p>{{ $survey['goal_short'] ?? 'Chưa cung cấp' }}</p>
                </div>

                <div class="report-answer">
                    <strong class="report-answer-label">2. Mục tiêu nghề nghiệp 5 năm tới</strong>
                    <p>{{ $survey['goal_long'] ?? 'Chưa cung cấp' }}</p>
                </div>

                <div class="report-answer">
                    <strong class="report-answer-label">3. Thành công lớn nhất & bài học rút ra</strong>
                    <p>{{ $survey['biggest_success'] ?? 'Chưa cung cấp' }}</p>
                </div>

                <div class="report-answer">
                    <strong class="report-answer-label">4. Lý do lựa chọn ứng tuyển vào AZ Media / AZHR</strong>
                    <p>{{ $survey['why_apply'] ?? 'Chưa cung cấp' }}</p>
                </div>

                <div class="report-answer">
                    <strong class="report-answer-label">5. Phương pháp tổ chức và quản lý công việc</strong>
                    <p>{{ $survey['work_organization'] ?? 'Chưa cung cấp' }}</p>
                </div>

                <div class="report-answer">
                    <strong class="report-answer-label">6. Thất bại / sai lầm lớn nhất và bài học đắt giá rút ra</strong>
                    <p>{{ $survey['failure_lesson'] ?? 'Chưa cung cấp' }}</p>
                </div>

                <div class="report-answer">
                    <strong class="report-answer-label">7. Lý do AZ Media nên chọn bạn thay vì ứng viên khác</strong>
                    <p>{{ $survey['why_choose_you'] ?? 'Chưa cung cấp' }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- 4. Tìm Hiểu & Cảm Nhận Về AZ Media -->
    @if($azSurvey)
        <div class="report-section">
            <div class="report-section-heading">
                <span class="report-icon" aria-hidden="true"><i class="fa-solid fa-building"></i></span>
                <div>
                    <h2>4. Kênh Tìm Hiểu & Cảm Nhận Về AZ Media</h2>
                    <p>Thông tin khảo sát mức độ quan tâm và hiểu biết về doanh nghiệp</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="report-answer">
                    <strong class="report-answer-label">Kênh biết đến và tìm hiểu về AZ Media</strong>
                    @if(!empty($azSurvey['channels']))
                        @php
                            $azChannelLabels = [
                                'social_media' => 'Mạng xã hội AZ Media',
                                'school' => 'Trường học & Thầy cô giáo',
                                'friends' => 'Bạn bè & Cựu sinh viên',
                                'job_fair' => 'Ngày hội việc làm / Workshop',
                                'recruitment_sites' => 'Trang tin tuyển dụng',
                                'google_website' => 'Tìm kiếm Google & Website',
                            ];
                        @endphp
                        <ul class="report-channel-list">
                            @foreach($azSurvey['channels'] as $ch)
                                <li>{{ $azChannelLabels[$ch] ?? $ch }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if(!empty($azSurvey['channel_other']))
                        <div class="mt-2 text-sm leading-relaxed text-slate-700">
                            <strong>Ghi chú khác:</strong> {{ $azSurvey['channel_other'] }}
                        </div>
                    @endif
                    @if(empty($azSurvey['channels']) && empty($azSurvey['channel_other']))
                        <p>Chưa cung cấp</p>
                    @endif
                </div>

                @if(!empty($azSurvey['impression']))
                    <div class="report-answer">
                        <strong class="report-answer-label">Điều ấn tượng nhất & mong muốn thực tập tại AZ Media</strong>
                        <p>{{ $azSurvey['impression'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Banner Nhóm Zalo Hỗ Trợ & Nhận Thông Báo -->
    <div class="report-utility bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 rounded-3xl border-2 border-emerald-300 p-6 sm:p-7 shadow-md flex flex-col md:flex-row items-center justify-between gap-6">
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
    <div class="report-utility flex flex-col sm:flex-row justify-center gap-3 pt-2">
        <button type="button" onclick="window.print()"
           class="px-7 py-3.5 rounded-xl font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition text-xs sm:text-sm text-center flex items-center justify-center gap-2">
            <i class="fa-solid fa-print" aria-hidden="true"></i> In / Lưu PDF
        </button>
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
