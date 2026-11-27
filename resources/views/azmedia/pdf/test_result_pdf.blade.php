<!DOCTYPE html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Báo Cáo Kết Quả Bài Kiểm Tra Đầu Vào - {{ $application->candidate->name }}</title>
    <style type="text/css">
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
            box-sizing: border-box;
        }
        body {
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        @page {
            margin: 34px 38px 38px;
        }
        .no-break {
            page-break-inside: avoid;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #ea580c;
            padding-bottom: 9px;
            margin-bottom: 14px;
        }
        .company-name {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
        }
        .company-meta {
            font-size: 8px;
            color: #475569;
            margin-top: 4px;
        }

        .doc-title {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin: 12px 0 4px;
            letter-spacing: 0;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 10px;
            color: #475569;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .info-table {
            width: 100%;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 7px;
            font-size: 9px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            color: #334155;
            width: 19%;
            background-color: #f1f5f9;
        }
        .info-val {
            color: #0f172a;
        }

        .section-header {
            background-color: #172554;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            padding: 8px 10px;
            margin-top: 14px;
            margin-bottom: 9px;
            text-transform: uppercase;
            border: 1px solid #c2410c;
            page-break-after: avoid;
        }
        .subsection-header {
            font-size: 10px;
            font-weight: bold;
            color: #172554;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            margin: 10px 0 6px;
            page-break-after: avoid;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 9px;
        }
        .data-table thead {
            display: table-header-group;
        }
        .data-table tr {
            page-break-inside: avoid;
        }
        .data-table th {
            background-color: #172554;
            color: #ffffff;
            font-size: 9px;
            padding: 6px 6px;
            border: 1px solid #cbd5e1;
            text-align: center;
            vertical-align: middle;
        }
        .data-table td {
            font-size: 9px;
            padding: 5px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .answer-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 9px;
            margin-bottom: 7px;
            font-size: 10px;
            page-break-inside: avoid;
        }
        .answer-box strong {
            color: #172554;
        }
        .answer-box p {
            margin: 4px 0 0;
            color: #334155;
            line-height: 1.5;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 2px;
        }
        .badge-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-danger {
            background-color: #fff1f2;
            color: #9f1239;
            border: 1px solid #fecdd3;
        }
        .badge-orange {
            background-color: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
        }

        .result-summary {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 13px;
        }
        .result-summary td {
            width: 33.333%;
            padding: 7px 8px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }
        .result-summary-label {
            display: block;
            color: #475569;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .result-summary-value {
            display: block;
            margin-top: 3px;
            color: #172554;
            font-size: 10px;
            font-weight: bold;
        }

        .sign-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .sign-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 10px;
        }
    </style>
</head>
<body>

    @php
        $test = $application->testResult;
        $data = $test ? $test->answers_data : [];
        $nlp = $data['nlp'] ?? null;
        $personality = $data['personality'] ?? null;
        $disc = $personality['disc'] ?? null;
        $mbti = $personality['mbti'] ?? null;
        $numerology = $personality['numerology'] ?? null;
        $wheel = $data['wheel_of_life'] ?? null;
        $survey = $data['survey'] ?? null;
        $azSurvey = $data['az_survey'] ?? null;
        $quiz = $data['quiz'] ?? null;
    @endphp

    <!-- HEADER TRANG BÁO CÁO -->
    <table class="header-table">
        <tr>
            <td style="width: 70%; vertical-align: top;">
                <div class="company-name">CÔNG TY TNHH ĐẦU TƯ PHÁT TRIỂN DOANH NGHIỆP AZ HOMES</div>
                <div style="font-size: 11px; font-weight: bold; color: #333333; margin-top: 1px;">CHI NHÁNH AZ MEDIA – ĐÀ NẴNG</div>
                <div class="company-meta">
                    Địa chỉ: 84 Nguyễn Hữu Dật, P. Hòa Cường, Q. Hải Châu, TP. Đà Nẵng<br>
                    Hotline: 0968.825.068 | Website: azmedia.com.vn | Fanpage: fb.com/congtyazmedia
                </div>
            </td>
            <td style="width: 30%; text-align: right; vertical-align: top;">
                <div style="font-size: 10px; font-weight: bold; color: #333333;">HỆ THỐNG TUYỂN DỤNG AZHR</div>
                <div style="font-size: 9px; color: #666666; margin-top: 2px;">Mã HS: <strong>{{ $application->application_code }}</strong></div>
                <div style="font-size: 9px; color: #666666;">Thời gian nộp: {{ $test->completed_at ? date('H:i - d/m/Y', strtotime($test->completed_at)) : date('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-title">BÁO CÁO KẾT QUẢ BÀI KIỂM TRA ĐẦU VÀO</div>
    <div class="doc-subtitle">Hồ sơ đánh giá ứng viên thực tập sinh · AZ Media</div>

    <!-- THÔNG TIN ỨNG VIÊN -->
    <table class="info-table">
        <tr>
            <td class="info-label">Họ và tên ứng viên:</td>
            <td class="info-val"><strong>{{ $application->candidate->name }}</strong></td>
            <td class="info-label">Vị trí ứng tuyển:</td>
            <td class="info-val"><strong>{{ $application->position->title }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Email liên hệ:</td>
            <td class="info-val">{{ $application->candidate->email ?: 'Chưa cung cấp' }}</td>
            <td class="info-label">Số điện thoại:</td>
            <td class="info-val">{{ $application->candidate->phone ?: 'Chưa cung cấp' }}</td>
        </tr>
        <tr>
            <td class="info-label">Trường ĐH / Cao đẳng:</td>
            <td class="info-val">{{ $application->candidate->university ?: 'Chưa cung cấp' }}</td>
            <td class="info-label">Chuyên ngành đào tạo:</td>
            <td class="info-val">{{ $application->candidate->major ?: 'Chưa cung cấp' }}</td>
        </tr>
    </table>

    <table class="result-summary">
        <tr>
            <td>
                <span class="result-summary-label">Kết quả văn hóa AZ</span>
                <span class="result-summary-value">{{ isset($test->score) ? $test->score . ' / 100 điểm' : 'Chưa có dữ liệu' }}</span>
            </td>
            <td>
                <span class="result-summary-label">Số câu trả lời đúng</span>
                <span class="result-summary-value">{{ isset($test->correct_answers, $test->total_questions) ? $test->correct_answers . ' / ' . $test->total_questions : 'Chưa có dữ liệu' }}</span>
            </td>
            <td>
                <span class="result-summary-label">Thời gian hoàn thành</span>
                <span class="result-summary-value">{{ isset($test->time_spent_seconds) ? round($test->time_spent_seconds / 60) . ' phút' : 'Chưa có dữ liệu' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="result-summary-label">Phong cách NLP nổi trội</span>
                <span class="result-summary-value">{{ data_get($nlp, 'dominant_title', 'Chưa có dữ liệu') }}</span>
            </td>
            <td>
                <span class="result-summary-label">Nhóm DISC</span>
                <span class="result-summary-value">{{ data_get($disc, 'title', data_get($disc, 'group', 'Chưa có dữ liệu')) }}</span>
            </td>
            <td>
                <span class="result-summary-label">Mã tính cách MBTI</span>
                <span class="result-summary-value">{{ data_get($mbti, 'code', 'Chưa có dữ liệu') }}</span>
            </td>
        </tr>
    </table>

    <!-- ==================== PHẦN 1 ==================== -->
    <div class="section-header">PHẦN 1. NĂNG LỰC & PHONG CÁCH LÀM VIỆC (NLP · DISC · MBTI)</div>

    <!-- 1.1 NLP -->
    <div class="subsection-header">1.1 Mô hình NLP (Phong cách tiếp nhận thông tin V-A-K-Ad)</div>
    @if($nlp)
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Visual (Thị giác / Nhìn)</th>
                    <th style="width: 25%;">Auditory (Thính giác / Nghe)</th>
                    <th style="width: 25%;">Kinesthetic (Cảm giác / Trải nghiệm)</th>
                    <th style="width: 25%;">Auditory Digital (Logic nội tâm)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ isset($nlp['scores']['Visual']) ? $nlp['scores']['Visual'] . ' điểm' : '—' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ isset($nlp['scores']['Auditory']) ? $nlp['scores']['Auditory'] . ' điểm' : '—' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ isset($nlp['scores']['Kinesthetic']) ? $nlp['scores']['Kinesthetic'] . ' điểm' : '—' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ isset($nlp['scores']['Auditory_Digital']) ? $nlp['scores']['Auditory_Digital'] . ' điểm' : '—' }}</td>
                </tr>
            </tbody>
        </table>
        <div class="answer-box">
            <strong>Phong cách tiếp nhận vượt trội:</strong> <span class="badge badge-orange">{{ $nlp['dominant_title'] ?? 'Chưa xác định' }}</span><br>
            @if(!empty($nlp['dominant_summary']))
                <p>{{ $nlp['dominant_summary'] }}</p>
            @endif
        </div>
        @if(!empty($nlp['raw_input']) && is_array($nlp['raw_input']))
            @php
                $nlpTypes = [
                    'V' => ['label' => 'Visual (Nhìn)', 'score' => 'Visual'],
                    'A' => ['label' => 'Auditory (Nghe)', 'score' => 'Auditory'],
                    'K' => ['label' => 'Kinesthetic (Cảm)', 'score' => 'Kinesthetic'],
                    'Ad' => ['label' => 'Auditory Digital (Logic)', 'score' => 'Auditory_Digital'],
                ];
            @endphp
            <div class="subsection-header">Bảng chi tiết điểm từng câu trả lời NLP</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 14%;">Câu hỏi</th>
                        @foreach($nlpTypes as $type)
                            <th style="width: 21.5%;">{{ $type['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($nlp['raw_input'] as $questionNumber => $ratings)
                        <tr>
                            <td style="font-weight: bold; text-align: center;">Câu {{ $questionNumber }}</td>
                            @foreach($nlpTypes as $code => $type)
                                <td style="text-align: center;">{{ data_get($ratings, $code, '—') }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr>
                        <td style="font-weight: bold;">Tổng điểm</td>
                        @foreach($nlpTypes as $type)
                            <td style="font-weight: bold; text-align: center;">{{ data_get($nlp, 'scores.' . $type['score'], '—') }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        @else
            <div class="answer-box">Chưa có bảng câu trả lời chi tiết NLP.</div>
        @endif
    @else
        <div class="answer-box">Chưa có dữ liệu đánh giá NLP.</div>
    @endif

    <!-- 1.2 DISC -->
    @php
        $discChoiceLabels = [
            'D' => 'Nhóm D (Quyết đoán): Hành động quyết liệt, chấp nhận rủi ro, tập trung vào kết quả nhanh nhất và thích sự cạnh tranh.',
            'I' => 'Nhóm I (Hòa đồng): Nhiệt tình, hướng ngoại, thích kết nối, truyền cảm hứng và tạo không khí vui vẻ cho cả nhóm.',
            'S' => 'Nhóm S (Điềm tĩnh): Kiên định, chân thành, luôn sẵn sàng hỗ trợ đồng đội, duy trì sự ổn định và tránh đối đầu.',
            'C' => 'Nhóm C (Cẩn trọng): Chú ý đến chi tiết, yêu cầu sự chính xác cao, chu đáo và luôn muốn hiểu rõ bản chất vấn đề trước khi thực hiện.',
        ];
        $discTeamworkLabels = [
            'goal' => 'Tốc độ giải quyết vấn đề và năng suất vượt trội.',
            'spirit' => 'Mối quan hệ cởi mở, sáng tạo và sự gắn bó giữa các thành viên.',
            'support' => 'Sự đồng lòng, thấu hiểu và chia sẻ khối lượng công việc.',
            'precision' => 'Chuẩn mực chất lượng, tài liệu rõ ràng, hạn chế tối đa sai sót.',
        ];
    @endphp
    <div class="subsection-header">1.2 Phong cách làm việc · DISC</div>
    @if($disc)
        <div class="answer-box">
            <strong>Nhóm nổi trội:</strong> <span class="badge badge-orange">{{ $disc['title'] ?? $disc['group'] ?? 'Chưa có dữ liệu' }}</span>
            @if(!empty($disc['summary']))
                <p>{{ $disc['summary'] }}</p>
            @endif
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 13%;">Câu hỏi</th>
                    <th style="width: 34%;">Nội dung</th>
                    <th>Phương án ứng viên đã chọn</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold; text-align: center;">Câu 1</td>
                    <td>Khi tiếp nhận một nhiệm vụ hoặc dự án kỹ thuật mới, xu hướng làm việc tự nhiên nhất của bạn là:</td>
                    <td>{{ $discChoiceLabels[$disc['group'] ?? ''] ?? ($disc['group'] ?? 'Chưa có dữ liệu') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; text-align: center;">Câu 2</td>
                    <td>Trong quy trình phối hợp làm việc nhóm, điều bạn chú trọng hàng đầu là:</td>
                    <td>{{ $discTeamworkLabels[data_get($disc, 'answers.q2', '')] ?? (data_get($disc, 'answers.q2') ?: 'Chưa có dữ liệu') }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="answer-box">Chưa có dữ liệu DISC.</div>
    @endif

    <!-- 1.3 MBTI -->
    @php
        $mbtiEnergyCode = substr((string) ($mbti['energy'] ?? ''), 0, 1);
        $mbtiDecisionCode = substr((string) ($mbti['decision'] ?? ''), 0, 1);
        $mbtiEnergyLabels = [
            'I' => 'I – Hướng nội (Introversion): Bản tính trầm tĩnh, ít nói, có khả năng quan sát thầm lặng và nhìn nhận vấn đề sâu sắc hơn; nạp năng lượng qua không gian yên tĩnh.',
            'E' => 'E – Hướng ngoại (Extraversion): Sôi nổi, thích kết nối đám đông, thích thể hiện ý tưởng bằng lời nói và nạp năng lượng từ các buổi giao lưu.',
        ];
        $mbtiDecisionLabels = [
            'F' => 'F – Cảm xúc (Feeling): Coi trọng sự hài hòa và cảm xúc con người; có sự cân bằng giữa tính thực tế và khả năng sáng tạo; giàu lòng trắc ẩn.',
            'T' => 'T – Lý trí (Thinking): Đưa ra quyết định thuần túy dựa trên quy chuẩn logic, tính khách quan tuyệt đối và phân tích số liệu không thiên vị.',
        ];
    @endphp
    <div class="subsection-header">1.3 Xu hướng tính cách · MBTI</div>
    @if($mbti)
        <div class="answer-box">
            <strong>Đặc tính MBTI:</strong> <span class="badge badge-orange">{{ $mbti['title'] ?? $mbti['code'] ?? 'Chưa có dữ liệu' }}</span>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 13%;">Câu hỏi</th>
                    <th style="width: 38%;">Khía cạnh đánh giá</th>
                    <th>Phương án ứng viên đã chọn</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold; text-align: center;">Câu 1</td>
                    <td>Về xu hướng năng lượng & giao tiếp (Energy Orientation):</td>
                    <td>{{ $mbtiEnergyLabels[$mbtiEnergyCode] ?? ($mbti['energy'] ?? 'Chưa có dữ liệu') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; text-align: center;">Câu 2</td>
                    <td>Về cách xử lý cảm xúc & ra quyết định (Decision Making):</td>
                    <td>{{ $mbtiDecisionLabels[$mbtiDecisionCode] ?? ($mbti['decision'] ?? 'Chưa có dữ liệu') }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="answer-box">Chưa có dữ liệu MBTI.</div>
    @endif

    <!-- 1.4 Thần số học -->
    @if($numerology)
        <div class="subsection-header" style="margin-top: 6px;">1.4 Con số đường đời (Nhân số học ứng dụng)</div>
        <div class="answer-box">
            <strong>{{ $numerology['title'] ?? 'Con số đường đời' }}</strong> (Ngày sinh: {{ $numerology['dob'] ?? $application->candidate->birth_date ?? 'Chưa cung cấp' }}):
            @if(!empty($numerology['summary']))
                <p>{{ $numerology['summary'] }}</p>
            @endif
        </div>
    @endif

    <!-- ==================== PHẦN 2 ==================== -->
    <div style="page-break-inside: avoid;">
        <div class="section-header" style="margin-top: 14px;">PHẦN 2. BÁNH XE CUỘC ĐỜI (8 KHÍA CẠNH)</div>
    @if($wheel && !empty($wheel['dimensions']))
        @php
            $wheelChartLabels = [
                'finance' => 'Tài chính',
                'personal_growth' => 'Phát triển',
                'health' => 'Sức khỏe',
                'family' => 'Gia đình',
                'relationships' => 'Mối quan hệ',
                'social_life' => 'Xã hội',
                'spirituality' => 'Tinh thần',
                'career' => 'Sự nghiệp',
            ];
            $wheelChartCenterX = 260;
            $wheelChartCenterY = 178;
            $wheelChartRadius = 112;
            $wheelChartAngles = [];
            $wheelChartVertices = [];
            $wheelChartCount = count($wheelChartLabels);

            foreach ($wheelChartLabels as $key => $label) {
                $angle = deg2rad(-90 + count($wheelChartAngles) * (360 / $wheelChartCount));
                $score = max(0, min(10, (int) ($wheel['dimensions'][$key]['score'] ?? 0)));
                $x = round($wheelChartCenterX + cos($angle) * $wheelChartRadius * $score / 10, 2);
                $y = round($wheelChartCenterY + sin($angle) * $wheelChartRadius * $score / 10, 2);
                $labelRadius = $wheelChartRadius + 34;
                $wheelChartAngles[] = $angle;
                $wheelChartVertices[] = [
                    'x' => $x,
                    'y' => $y,
                    'label' => $label,
                    'score' => $score,
                    'label_x' => round($wheelChartCenterX + cos($angle) * $labelRadius, 2),
                    'label_y' => round($wheelChartCenterY + sin($angle) * $labelRadius, 2),
                    'anchor' => abs(cos($angle)) < 0.1 ? 'middle' : (cos($angle) > 0 ? 'start' : 'end'),
                ];
            }

            $wheelChartPolygon = implode(' ', array_map(function ($vertex) {
                return $vertex['x'] . ',' . $vertex['y'];
            }, $wheelChartVertices));
            $wheelChartRings = [];
            foreach ([2, 4, 6, 8, 10] as $level) {
                $ringPoints = [];
                foreach ($wheelChartAngles as $angle) {
                    $ringPoints[] = round($wheelChartCenterX + cos($angle) * $wheelChartRadius * $level / 10, 2)
                        . ',' . round($wheelChartCenterY + sin($angle) * $wheelChartRadius * $level / 10, 2);
                }
                $wheelChartRings[] = implode(' ', $ringPoints);
            }

            $wheelSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="520" height="360" viewBox="0 0 520 360">';
            foreach ($wheelChartRings as $ring) {
                $wheelSvg .= '<polygon points="' . $ring . '" fill="none" stroke="#cbd5e1" stroke-width="1.2" />';
            }
            foreach ($wheelChartAngles as $angle) {
                $axisX = round($wheelChartCenterX + cos($angle) * $wheelChartRadius, 2);
                $axisY = round($wheelChartCenterY + sin($angle) * $wheelChartRadius, 2);
                $wheelSvg .= '<line x1="' . $wheelChartCenterX . '" y1="' . $wheelChartCenterY . '" x2="' . $axisX . '" y2="' . $axisY . '" stroke="#cbd5e1" stroke-width="1" />';
            }
            $wheelSvg .= '<polygon points="' . $wheelChartPolygon . '" fill="#f97316" fill-opacity="0.22" stroke="#c2410c" stroke-width="2.5" stroke-linejoin="round" />';
            foreach ($wheelChartVertices as $vertex) {
                $label = htmlspecialchars($vertex['label'], ENT_QUOTES | ENT_XML1, 'UTF-8');
                $wheelSvg .= '<circle cx="' . $vertex['x'] . '" cy="' . $vertex['y'] . '" r="4" fill="#c2410c" stroke="#ffffff" stroke-width="1.5" />';
                $wheelSvg .= '<text x="' . $vertex['label_x'] . '" y="' . ($vertex['label_y'] - 3) . '" text-anchor="' . $vertex['anchor'] . '" font-family="DejaVu Sans" font-size="10" font-weight="bold" fill="#1e293b">';
                $wheelSvg .= '<tspan x="' . $vertex['label_x'] . '" dy="0">' . $label . '</tspan>';
                $wheelSvg .= '<tspan x="' . $vertex['label_x'] . '" dy="13" font-size="9" font-weight="normal" fill="#475569">' . $vertex['score'] . '/10</tspan></text>';
            }
            $wheelSvg .= '</svg>';
        @endphp
        <div style="text-align: center; margin: 8px 0 12px; page-break-inside: avoid;">
            <img src="data:image/svg+xml;base64,{{ base64_encode($wheelSvg) }}" width="520" height="360" alt="Biểu đồ Bánh xe cuộc đời với điểm số tám lĩnh vực">
        </div>
        @endif
    </div>
    @if($wheel && !empty($wheel['dimensions']))
        <div class="subsection-header">Bảng điểm hiện tại và mục tiêu</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 31%;">Lĩnh vực</th>
                    <th style="width: 23%;">Hiện tại</th>
                    <th style="width: 23%;">Mong muốn 10 năm</th>
                    <th style="width: 23%;">Chênh lệch</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wheelChartLabels as $key => $label)
                    @php
                        $currentScore = data_get($wheel, 'dimensions.' . $key . '.score');
                        $desiredScore = data_get($wheel, 'desired_dimensions.' . $key . '.score');
                    @endphp
                    <tr>
                        <td style="font-weight: bold;">{{ $label }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ is_numeric($currentScore) ? $currentScore . '/10' : '—' }}</td>
                        <td style="text-align: center;">{{ is_numeric($desiredScore) ? $desiredScore . '/10' : '—' }}</td>
                        <td style="text-align: center;">{{ is_numeric($currentScore) && is_numeric($desiredScore) ? (($desiredScore - $currentScore > 0 ? '+' : '') . ($desiredScore - $currentScore)) : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if(!empty($wheel['lesson']))
            <div class="answer-box">
                <strong>Nhận thức & Bài học rút ra từ Bánh xe cuộc đời:</strong>
                <p>{{ $wheel['lesson'] }}</p>
            </div>
        @endif
    @else
        <div class="answer-box">Chưa có dữ liệu Bánh xe cuộc đời.</div>
    @endif

    <!-- ==================== PHẦN 3 ==================== -->
    <div class="section-header">PHẦN 3. HOÀI BÃO, ĐỘNG CƠ & TỰ NHẬN THỨC BẢN THÂN</div>
    @if($survey)
        <div class="answer-box">
            <strong>1. Mục tiêu ngắn hạn (1 - 2 năm tới):</strong>
            <p>{{ $survey['goal_short'] ?? 'Chưa cung cấp' }}</p>
        </div>
        <div class="answer-box">
            <strong>2. Mục tiêu dài hạn (3 - 5 năm tới):</strong>
            <p>{{ $survey['goal_long'] ?? 'Chưa cung cấp' }}</p>
        </div>
        <div class="answer-box">
            <strong>3. Thành công lớn nhất từng đạt được:</strong>
            <p>{{ $survey['biggest_success'] ?? 'Chưa cung cấp' }}</p>
        </div>
        <div class="answer-box">
            <strong>4. Lý do lựa chọn ứng tuyển vào AZ Media / AZHR:</strong>
            <p>{{ $survey['why_apply'] ?? 'Chưa cung cấp' }}</p>
        </div>
        <div class="answer-box">
            <strong>5. Phương pháp tổ chức và quản lý công việc:</strong>
            <p>{{ $survey['work_organization'] ?? 'Chưa cung cấp' }}</p>
        </div>
        <div class="answer-box">
            <strong>6. Thất bại / sai lầm lớn nhất và bài học đắt giá rút ra:</strong>
            <p>{{ $survey['failure_lesson'] ?? 'Chưa cung cấp' }}</p>
        </div>
        <div class="answer-box">
            <strong>7. Lý do AZ Media nên chọn bạn thay vì ứng viên khác:</strong>
            <p>{{ $survey['why_choose_you'] ?? 'Chưa cung cấp' }}</p>
        </div>
    @else
        <div class="answer-box">Chưa có dữ liệu khảo sát Hoài bão, Động cơ & Tự nhận thức bản thân.</div>
    @endif

    <!-- ==================== PHẦN 4 ==================== -->
    <div class="section-header" style="margin-top: 14px; page-break-before: always;">PHẦN 4. KIẾN THỨC & MỨC ĐỘ TÌM HIỂU VỀ AZ MEDIA</div>

    <!-- 4.1 Trắc nghiệm văn hóa -->
    <div class="subsection-header">4.1 Kết quả trắc nghiệm hiểu biết về AZ Media
        @if(isset($quiz['correct'], $quiz['total']))
            ({{ $quiz['correct'] }}/{{ $quiz['total'] }} câu đúng)
        @else
            (Chưa có dữ liệu điểm)
        @endif
    </div>
    @if(!empty($quiz['details']))
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 6%;">STT</th>
                    <th style="width: 52%;">Nội dung câu hỏi trắc nghiệm</th>
                    <th style="width: 16%;">Đã chọn</th>
                    <th style="width: 16%;">Đáp án đúng</th>
                    <th style="width: 10%;">Kết quả</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quiz['details'] as $idx => $item)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                        <td>{{ $item['question_text'] }}</td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ $item['selected_option'] }}
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            {{ $item['correct_option'] }}
                        </td>
                        <td style="text-align: center;">
                            <span class="badge {{ $item['is_correct'] ? 'badge-success' : 'badge-danger' }}">
                                {{ $item['is_correct'] ? 'ĐÚNG' : 'SAI' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="answer-box">Chưa có dữ liệu chi tiết cho phần trắc nghiệm văn hóa AZ Media.</div>
    @endif

    <!-- 4.2 Kênh tìm hiểu & Tự luận cảm nhận -->
    @if($azSurvey)
        <div class="subsection-header">4.2 Kênh biết đến & Khảo sát tìm hiểu về AZ Media</div>
        <div class="answer-box">
            <strong>Các kênh ứng viên đã tiếp cận:</strong>
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
                <div style="margin-top: 3px;">
                    @foreach($azSurvey['channels'] as $ch)
                        <span class="badge badge-orange" style="margin-right: 4px;">✓ {{ $azChannelLabels[$ch] ?? $ch }}</span>
                    @endforeach
                </div>
            @endif
            @if(!empty($azSurvey['channel_other']))
                <p><em>Kênh khác bổ sung:</em> {{ $azSurvey['channel_other'] }}</p>
            @endif
        </div>

        @if(!empty($azSurvey['impression']))
            <div class="subsection-header">4.3 Điều ấn tượng nhất & Mong muốn thực tập tại AZ Media</div>
            <div class="answer-box">
                <p style="white-space: pre-line;">{{ $azSurvey['impression'] }}</p>
            </div>
        @endif
    @else
        <div class="answer-box">Chưa có dữ liệu khảo sát về kênh tìm hiểu và cảm nhận về AZ Media.</div>
    @endif

    <!-- XÁC NHẬN CHỮ KÝ -->
    <table class="sign-table no-break">
        <tr>
            <td>
                <strong>Ứng viên thực hiện bài test</strong><br>
                <span style="font-size: 8.5px; color: #64748b; font-style: italic;">(Ký và ghi rõ họ tên)</span>
                <div style="height: 30px;"></div>
                <strong>{{ $application->candidate->name }}</strong>
            </td>
            <td>
                <strong>Người duyệt hồ sơ / Mentor phụ trách</strong><br>
                <span style="font-size: 8.5px; color: #64748b; font-style: italic;">(Ký và xác nhận kết quả)</span>
                <div style="height: 30px;"></div>
                <strong>Lê Đình Quang</strong><br>
                <span style="font-size: 9px; color: #64748b;">Tổng Giám Đốc / Mentor AZ Media</span>
            </td>
        </tr>
    </table>

</body>
</html>
