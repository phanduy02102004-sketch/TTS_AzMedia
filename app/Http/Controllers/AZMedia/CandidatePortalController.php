<?php

namespace App\Http\Controllers\AZMedia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\TestQuestion;
use App\Models\CandidateTest;
use App\Models\CandidateTestQuizAnswer;
use App\Models\CandidateTestNlpAnswer;
use App\Models\CandidateTestPersonality;
use App\Models\CandidateTestWheelDimension;
use App\Models\CandidateTestSurvey;
use App\Models\CandidateTestAzChannel;
use App\Models\CandidateTestPersonalityAnswer;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use PDF;

class CandidatePortalController extends Controller
{
    /**
     * Trang tra cứu tiến độ ứng tuyển dành cho ứng viên
     */
    public function trackPage(Request $request)
    {
        $application = null;
        $searchKey = $request->query('code') ?? $request->query('email');

        if ($searchKey) {
            $application = Application::with(['candidate', 'position', 'testResult', 'evaluations', 'emailLogs'])
                ->where('application_code', trim($searchKey))
                ->orWhereHas('candidate', function ($q) use ($searchKey) {
                    $q->where('email', trim($searchKey));
                })
                ->latest()
                ->first();
        }

        return view('azmedia.candidate.track', compact('application', 'searchKey'));
    }

    /**
     * Xử lý tìm kiếm hồ sơ qua Form Tra cứu
     */
    public function trackSubmit(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string',
        ], [
            'keyword.required' => 'Vui lòng nhập Mã hồ sơ (VD: AZ-2026-0732) hoặc Địa chỉ Email của bạn.',
        ]);

        $keyword = trim($request->keyword);

        $application = Application::with(['candidate', 'position', 'testResult', 'evaluations', 'emailLogs'])
            ->where('application_code', $keyword)
            ->orWhereHas('candidate', function ($q) use ($keyword) {
                $q->where('email', $keyword);
            })
            ->latest()
            ->first();

        if (!$application) {
            return redirect()->route('azmedia.candidate.track')
                ->with('error', "Không tìm thấy hồ sơ ứng tuyển nào khớp với thông tin '{$keyword}'. Vui lòng kiểm tra lại Mã hồ sơ hoặc Email.");
        }

        return redirect()->route('azmedia.candidate.track', ['code' => $application->application_code]);
    }

    /**
     * Mở giao diện làm bài Test Online trắc nghiệm kỹ thuật
     */
    public function takeTest($code)
    {
        // 1. Kiểm tra xác thực Thực tập sinh (Yêu cầu đăng ký / đăng nhập để làm Chuẩn đầu vào)
        if (!Session::has('intern_user')) {
            return redirect()->route('azmedia.intern.login', [
                'redirect' => route('azmedia.candidate.test', $code),
                'code' => $code
            ])->with('warning', 'Theo quy định AZ Media: Chuẩn đầu vào chỉ dành cho Thực tập sinh đã được công ty tiếp nhận. Vui lòng đăng nhập tài khoản TTS để làm bài!');
        }

        $application = Application::with(['candidate', 'position', 'testResult'])
            ->where('application_code', $code)
            ->firstOrFail();

        // 2. Kiểm tra điều kiện: Nếu hồ sơ mới nộp hoặc đang sơ loại CV (chưa có offer)
        if (!in_array($application->status, ['offered', 'test_sent', 'tested', 'interviewing'])) {
            return view('azmedia.candidate.test_not_eligible', compact('application'));
        }

        // Kiểm tra xem ứng viên đã làm bài test chưa
        if ($application->testResult && $application->testResult->completed_at) {
            return view('azmedia.candidate.test_completed', compact('application'));
        }

        // Lấy câu hỏi văn hóa AZ Media cho Phần 4.
        // Hỗ trợ cả dữ liệu mới (azmedia) và dữ liệu hiện có (general).
        $allQuestions = TestQuestion::where('position_category', 'azmedia')
            ->orderBy('id', 'asc')
            ->get();

        if ($allQuestions->isEmpty()) {
            $allQuestions = TestQuestion::where('position_category', 'general')
                ->whereIn('question', [
                    'Slogan chính thức của công ty AZ Media là gì?',
                    'Ý nghĩa chữ "A" trong tên thương hiệu AZ Media đại diện cho giá trị cốt lõi nào?',
                    'Văn hóa môi trường làm việc tại AZ Media được xây dựng trên triết lý cốt lõi nào sau đây?',
                ])
                ->orderBy('id', 'asc')
                ->get();
        }

        if ($allQuestions->isEmpty()) {
            $allQuestions = TestQuestion::whereIn('id', [9, 10, 11])->get();
        }

        $draftFields = [];
        if ($application->testResult && $application->testResult->draft_data) {
            $decodedDraft = json_decode($application->testResult->draft_data, true);
            $draftFields = is_array($decodedDraft) ? $decodedDraft : [];
        }

        return view('azmedia.candidate.take_test', compact('application', 'allQuestions', 'draftFields'));
    }

    /**
     * Persist an in-progress test without marking it as completed.
     */
    public function autosaveTest(Request $request, $code)
    {
        if (!Session::has('intern_user')) {
            return response()->json(['message' => 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.'], 401);
        }

        $validated = $request->validate([
            'fields' => 'required|array|max:200',
            'fields.*.name' => 'required|string|max:100',
            'fields.*.value' => 'present|nullable|string|max:10000',
        ]);

        $draftFields = [];
        foreach ($validated['fields'] as $field) {
            $value = (string) ($field['value'] ?? '');
            if (!$this->isValidDraftField($field['name'], $value)) {
                return response()->json([
                    'message' => 'Có câu trả lời không hợp lệ. Vui lòng tải lại trang và thử lại.',
                ], 422);
            }

            $draftFields[] = [
                'name' => $field['name'],
                'value' => $value,
            ];
        }

        $draftData = json_encode($draftFields, JSON_UNESCAPED_UNICODE);
        if ($draftData === false || strlen($draftData) > 100000) {
            return response()->json([
                'message' => 'Bản nháp vượt quá dung lượng cho phép. Vui lòng rút ngắn nội dung.',
            ], 422);
        }

        try {
            $application = Application::where('application_code', $code)->firstOrFail();
            $intern = Session::get('intern_user', []);

            if ((int) ($intern['id'] ?? 0) !== (int) $application->candidate_id) {
                return response()->json(['message' => 'Bạn không có quyền lưu bài test này.'], 403);
            }

            if (!in_array($application->status, ['offered', 'test_sent', 'tested', 'interviewing'], true)) {
                return response()->json(['message' => 'Hồ sơ hiện không đủ điều kiện làm bài test.'], 403);
            }

            foreach ($draftFields as $field) {
                if (preg_match('/^answers\[(\d+)\]$/', $field['name'], $matches)
                    && !TestQuestion::whereKey((int) $matches[1])->exists()) {
                    return response()->json([
                        'message' => 'Câu hỏi không còn hợp lệ. Vui lòng tải lại trang và thử lại.',
                    ], 422);
                }
            }

            $alreadyCompleted = false;
            DB::transaction(function () use ($application, $draftData, &$alreadyCompleted) {
                Application::whereKey($application->id)->lockForUpdate()->firstOrFail();
                $candidateTest = CandidateTest::where('application_id', $application->id)
                    ->lockForUpdate()
                    ->first();

                if ($candidateTest && $candidateTest->completed_at) {
                    $alreadyCompleted = true;
                    return;
                }

                if (!$candidateTest) {
                    $candidateTest = new CandidateTest();
                    $candidateTest->application_id = $application->id;
                }

                $candidateTest->draft_data = $draftData;
                $candidateTest->save();
            });

            if ($alreadyCompleted) {
                return response()->json(['message' => 'Bài test đã được nộp trước đó.'], 409);
            }

            return response()->json(['saved' => true]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('[Candidate Test Autosave] Không thể lưu bản nháp.', [
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Máy chủ chưa lưu được câu trả lời. Vui lòng kiểm tra kết nối và thử lại.',
            ], 503);
        }
    }

    private function isValidDraftField($name, $value)
    {
        if (preg_match('/^answers\[(\d+)\]$/', $name, $matches)) {
            return ctype_digit($matches[1])
                && in_array(strtoupper($value), ['A', 'B', 'C', 'D'], true);
        }

        if (preg_match('/^nlp\[([1-5])\]\[(V|A|K|Ad)\]$/', $name)) {
            return $value === '' || in_array($value, ['1', '2', '3', '4'], true);
        }

        if (preg_match('/^(wheel|desired_wheel)\[(finance|personal_growth|health|family|relationships|social_life|spirituality|career)\]$/', $name)) {
            return ctype_digit($value) && (int) $value >= 1 && (int) $value <= 10;
        }

        if ($name === 'az_channels[]') {
            return in_array($value, [
                'social_media',
                'school',
                'friends',
                'job_fair',
                'recruitment_sites',
                'google_website',
            ], true);
        }

        if ($name === 'disc_choice') {
            return in_array($value, ['D', 'I', 'S', 'C'], true);
        }

        if ($name === 'disc[q2]') {
            return in_array($value, ['goal', 'spirit', 'support', 'precision'], true);
        }

        if ($name === 'mbti_energy') {
            return in_array($value, ['I', 'E'], true);
        }

        if ($name === 'mbti_decision') {
            return in_array($value, ['F', 'T'], true);
        }

        if ($name === 'time_spent') {
            return ctype_digit($value) && (int) $value <= 604800;
        }

        if ($name === 'numerology_dob') {
            return $value === '' || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1;
        }

        return in_array($name, [
            'wheel_lesson',
            'goal_short',
            'goal_long',
            'biggest_success',
            'why_apply',
            'work_organization',
            'failure_lesson',
            'why_choose_you',
            'az_channel_other',
            'az_impression',
        ], true);
    }

    /**
     * Nộp bài Test Online & Tự động chấm điểm (Tích hợp NLP, Bánh xe cuộc đời & Khảo sát động cơ)
     */
    public function submitTest(Request $request, $code)
    {
        if (!Session::has('intern_user')) {
            return redirect()->route('azmedia.intern.login', [
                'redirect' => route('azmedia.candidate.test', $code),
                'code' => $code
            ])->with('error', 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại để nộp bài test!');
        }

        $application = Application::with(['candidate', 'position', 'testResult'])
            ->where('application_code', $code)
            ->firstOrFail();

        $missing = [];
        $nlpInput = $request->input('nlp', []);
        for ($questionNumber = 1; $questionNumber <= 5; $questionNumber++) {
            $ratings = $nlpInput[$questionNumber] ?? [];
            $completed = is_array($ratings) ? array_filter($ratings, fn($value) => $value !== null && $value !== '') : [];
            if (count($completed) !== 4) {
                $missing[] = "Phần 1 - NLP, Câu {$questionNumber}: còn mức điểm chưa chọn.";
            }
        }
        if (!$request->filled('disc_choice')) {
            $missing[] = 'Phần 1 - DISC: chưa chọn xu hướng làm việc.';
        }
        if (!($request->input('disc.q2'))) {
            $missing[] = 'Phần 1 - DISC Câu 2: chưa chọn đáp án.';
        }
        if (!$request->filled('mbti_energy')) {
            $missing[] = 'Phần 1 - MBTI: chưa chọn Hướng nội (I) hoặc Hướng ngoại (E).';
        }
        if (!$request->filled('mbti_decision')) {
            $missing[] = 'Phần 1 - MBTI: chưa chọn Cảm xúc (F) hoặc Lý trí (T).';
        }
        if (!$request->filled('numerology_dob')) {
            $missing[] = 'Phần 1 - Nhân số học: chưa chọn ngày sinh.';
        }
        if (!$request->filled('wheel_lesson')) {
            $missing[] = 'Phần 2 - Bánh xe cuộc đời: chưa nhập bài học sâu sắc rút ra.';
        }

        $surveyLabels = [
            'goal_short' => 'Mục tiêu ngắn hạn',
            'goal_long' => 'Mục tiêu dài hạn',
            'biggest_success' => 'Thành công lớn nhất',
            'why_apply' => 'Lý do ứng tuyển',
            'work_organization' => 'Cách sắp xếp công việc',
            'failure_lesson' => 'Bài học từ thất bại',
            'why_choose_you' => 'Lý do công ty nên chọn bạn',
        ];
        foreach ($surveyLabels as $field => $label) {
            if (!trim((string) $request->input($field, ''))) {
                $missing[] = "Phần 3 - {$label}: chưa nhập nội dung.";
            }
        }

        $answers = $request->input('answers', []);
        $quizQuestions = TestQuestion::where('position_category', 'azmedia')->orderBy('id')->get();
        if ($quizQuestions->isEmpty()) {
            $quizQuestions = TestQuestion::where('position_category', 'general')
                ->whereIn('question', [
                    'Slogan chính thức của công ty AZ Media là gì?',
                    'Ý nghĩa chữ "A" trong tên thương hiệu AZ Media đại diện cho giá trị cốt lõi nào?',
                    'Văn hóa môi trường làm việc tại AZ Media được xây dựng trên triết lý cốt lõi nào sau đây?',
                ])
                ->orderBy('id')
                ->get();
        }
        if ($quizQuestions->isEmpty()) {
            $quizQuestions = TestQuestion::whereIn('id', [9, 10, 11])->get();
        }

        foreach ($quizQuestions as $index => $question) {
            if (!array_key_exists($question->id, $answers) || $answers[$question->id] === null || $answers[$question->id] === '') {
                $missing[] = 'Phần 4 - Câu trắc nghiệm AZ Media số ' . ($index + 1) . ': chưa chọn đáp án.';
            }
        }

        $azChannels = $request->input('az_channels', []);
        if (!is_array($azChannels) || count($azChannels) === 0) {
            $missing[] = 'Phần 4 - Kênh tìm hiểu AZ Media: chưa chọn ít nhất một kênh.';
        }
        $azImpression = trim((string) $request->input('az_impression', ''));
        if (mb_strlen($azImpression) < 10) {
            $missing[] = 'Phần 4 - Cảm nhận và mong muốn thực tập: cần nhập ít nhất 10 ký tự.';
        }

        if (!empty($missing)) {
            return back()->withInput()->with('error', 'Bạn chưa hoàn thành đầy đủ bài đánh giá: ' . implode(' ', $missing));
        }

        // 1. Xử lý Trắc nghiệm Chuyên môn & Văn hóa
        $answers = $request->input('answers', []);
        $timeSpent = (int) $request->input('time_spent', 600); // số giây

        $totalQuestions = 0;
        $correctCount = 0;
        $quizDetails = [];

        foreach ($answers as $qId => $selectedOption) {
            $totalQuestions++;
            $q = TestQuestion::find($qId);
            $isCorrect = false;

            if ($q && strtoupper(trim($selectedOption)) === strtoupper(trim($q->correct_option))) {
                $correctCount++;
                $isCorrect = true;
            }

            $quizDetails[] = [
                'question_id' => $qId,
                'question_text' => $q ? $q->question : '',
                'selected_option' => $selectedOption,
                'correct_option' => $q ? $q->correct_option : '',
                'is_correct' => $isCorrect,
            ];
        }

        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 100;
        $passed = ($score >= 60);

        // 2. Xử lý Bài test nhận diện tính cách NLP (V-A-K-Ad) - File 2
        $nlpInput = $request->input('nlp', []);
        // Each NLP question may use each score from 1 to 4 only once.
        foreach ($nlpInput as $questionNumber => $ratings) {
            if (!is_array($ratings)) {
                return back()->withInput()->withErrors([
                    'nlp' => "Dữ liệu trả lời câu {$questionNumber} không hợp lệ.",
                ]);
            }

            $values = [];
            foreach ($ratings as $code => $value) {
                if (!in_array($code, ['V', 'A', 'K', 'Ad'], true)
                    || ($value !== null && !is_string($value) && !is_int($value))) {
                    return back()->withInput()->withErrors([
                        'nlp' => "Dữ liệu trả lời câu {$questionNumber} không hợp lệ.",
                    ]);
                }

                if ($value === null || $value === '') {
                    continue;
                }

                $value = (string) $value;
                if (!in_array($value, ['1', '2', '3', '4'], true)) {
                    return back()->withInput()->withErrors([
                        'nlp' => "Câu {$questionNumber}: mức điểm phải nằm trong khoảng 1-4.",
                    ]);
                }

                if (in_array($value, $values, true)) {
                    return back()->withInput()->withErrors([
                        'nlp' => "Câu {$questionNumber}: mỗi mức điểm chỉ được chọn một lần.",
                    ]);
                }

                $values[] = $value;
            }
        }
        // Ánh xạ đáp án V-A-K-Ad theo mẫu chuẩn AZ Media
        // Câu 1: K, A, V, Ad
        // Câu 2: A, V, Ad, K
        // Câu 3: V, K, Ad, A
        // Câu 4: A, Ad, K, V
        // Câu 5: A, Ad, K, V
        $vTotal  = (int)($nlpInput[1]['V'] ?? 0) + (int)($nlpInput[2]['V'] ?? 0) + (int)($nlpInput[3]['V'] ?? 0) + (int)($nlpInput[4]['V'] ?? 0) + (int)($nlpInput[5]['V'] ?? 0);
        $aTotal  = (int)($nlpInput[1]['A'] ?? 0) + (int)($nlpInput[2]['A'] ?? 0) + (int)($nlpInput[3]['A'] ?? 0) + (int)($nlpInput[4]['A'] ?? 0) + (int)($nlpInput[5]['A'] ?? 0);
        $kTotal  = (int)($nlpInput[1]['K'] ?? 0) + (int)($nlpInput[2]['K'] ?? 0) + (int)($nlpInput[3]['K'] ?? 0) + (int)($nlpInput[4]['K'] ?? 0) + (int)($nlpInput[5]['K'] ?? 0);
        $adTotal = (int)($nlpInput[1]['Ad'] ?? 0) + (int)($nlpInput[2]['Ad'] ?? 0) + (int)($nlpInput[3]['Ad'] ?? 0) + (int)($nlpInput[4]['Ad'] ?? 0) + (int)($nlpInput[5]['Ad'] ?? 0);

        $nlpScores = [
            'V'  => $vTotal,
            'A'  => $aTotal,
            'K'  => $kTotal,
            'Ad' => $adTotal,
        ];

        // Xác định tuýp nổi bật nhất
        arsort($nlpScores);
        $dominantKey = array_key_first($nlpScores);

        $nlpProfiles = [
            'V' => [
                'type' => 'Visual (Nhìn / Thị giác)',
                'summary' => 'Bạn tiếp nhận thông tin tốt nhất qua hình ảnh, sơ đồ và màu sắc. Thích phong cách làm việc trực quan, ngăn nắp và nhạy bén với giao diện người dùng.',
                'color' => '#f97316'
            ],
            'A' => [
                'type' => 'Auditory (Nghe / Thính giác)',
                'summary' => 'Bạn có khả năng lắng nghe và giao tiếp xuất sắc. Tiếp thu nhanh qua thảo luận, giải thích lời nói và nhạy cảm với âm điệu, giọng nói.',
                'color' => '#3b82f6'
            ],
            'K' => [
                'type' => 'Kinesthetic (Cảm xúc / Trải nghiệm)',
                'summary' => 'Bạn học hỏi và làm việc hiệu quả nhất qua hành động và trải nghiệm thực tế. Giàu cảm xúc, chân thành và coi trọng sự kết nối giữa các thành viên.',
                'color' => '#10b981'
            ],
            'Ad' => [
                'type' => 'Auditory Digital (Nội tâm / Tư duy Logic)',
                'summary' => 'Bạn có tư duy phân tích hệ thống sắc sảo, cẩn trọng và luôn dựa trên số liệu, dẫn chứng logic. Phù hợp xuất sắc với vị trí kỹ thuật, giải thuật và dữ liệu.',
                'color' => '#8b5cf6'
            ],
        ];

        $nlpResult = [
            'scores' => [
                'Visual' => $vTotal,
                'Auditory' => $aTotal,
                'Kinesthetic' => $kTotal,
                'Auditory_Digital' => $adTotal,
            ],
            'dominant_code' => $dominantKey,
            'dominant_title' => $nlpProfiles[$dominantKey]['type'] ?? 'Toàn diện',
            'dominant_summary' => $nlpProfiles[$dominantKey]['summary'] ?? '',
            'dominant_color' => $nlpProfiles[$dominantKey]['color'] ?? '#ea580c',
            'raw_input' => $nlpInput,
        ];

        // 2.1 Xử lý DISC, MBTI & Nhân số học (bổ sung từ Báo cáo thu hoạch NLP - DISC - MBTI)
        $discChoice = $request->input('disc_choice', 'C');
        $discDescriptions = [
            'D' => ['title' => 'Nhóm D – Quyết đoán (Dominance)', 'summary' => 'Hành động quyết liệt, tập trung vào kết quả nhanh nhất, tự tin và sẵn sàng chấp nhận thử thách.'],
            'I' => ['title' => 'Nhóm I – Hòa đồng (Influence)', 'summary' => 'Nhiệt huyết, truyền cảm hứng, cởi mở trong giao tiếp và tạo không khí tích cực cho tập thể.'],
            'S' => ['title' => 'Nhóm S – Điềm tĩnh (Steadiness)', 'summary' => 'Kiên định, chân thành, biết lắng nghe và luôn là điểm tựa đáng tin cậy của đồng đội.'],
            'C' => ['title' => 'Nhóm C – Cẩn trọng (Conscientiousness)', 'summary' => 'Chú ý đến chi tiết, yêu cầu sự chính xác cao, chu đáo và luôn muốn hiểu rõ bản chất vấn đề trước khi thực hiện.'],
        ];

        $discProfile = [
            'group' => $discChoice,
            'title' => $discDescriptions[$discChoice]['title'] ?? 'Nhóm C – Cẩn trọng',
            'summary' => $discDescriptions[$discChoice]['summary'] ?? '',
            'answers' => $request->input('disc', []),
        ];

        $mbtiEnergy = $request->input('mbti_energy', 'I');
        $mbtiDecision = $request->input('mbti_decision', 'F');
        $mbtiCode = $mbtiEnergy . $mbtiDecision;
        $mbtiProfile = [
            'code' => $mbtiCode,
            'energy' => $mbtiEnergy === 'I' ? 'I - Hướng nội (Introversion)' : 'E - Hướng ngoại (Extraversion)',
            'decision' => $mbtiDecision === 'F' ? 'F - Cảm xúc (Feeling)' : 'T - Lý trí (Thinking)',
            'title' => ($mbtiEnergy === 'I' ? 'I (Hướng nội)' : 'E (Hướng ngoại)') . ' & ' . ($mbtiDecision === 'F' ? 'F (Cảm xúc)' : 'T (Lý trí)'),
            'summary' => 'Coi trọng sự hài hòa, thực tế kết hợp sáng tạo; bản tính trầm tính, khả năng quan sát thầm lặng và nhìn nhận vấn đề sâu sắc hơn.',
            'answers' => $request->input('mbti', []),
        ];

        $dob = $request->input('numerology_dob', $application->candidate->birth_date ?? '2004-10-02');
        $digits = preg_replace('/[^0-9]/', '', (string)$dob);
        $lifePathSum = !empty($digits) ? array_sum(str_split($digits)) : 9;
        while ($lifePathSum > 9 && !in_array($lifePathSum, [11, 22, 33])) {
            $lifePathSum = array_sum(str_split((string)$lifePathSum));
        }

        $numerologyDescriptions = [
            9 => 'Đường đời số 9: Gắn với lòng trắc ẩn, tính bao dung, trực giác tốt, tinh thần trách nhiệm cao và mong muốn mang lại những giá trị tích cực cho cộng đồng.',
            1 => 'Đường đời số 1: Tinh thần tiên phong, độc lập và khả năng dẫn dắt xuất sắc.',
            2 => 'Đường đời số 2: Khả năng ngoại giao, hòa giải và gắn kết đồng đội tuyệt vời.',
            3 => 'Đường đời số 3: Tư duy sáng tạo, hoạt ngôn và lan tỏa năng lượng tích cực.',
            4 => 'Đường đời số 4: Tính kỷ luật, thực tế, vững chắc và có quy chuẩn rõ ràng.',
            5 => 'Đường đời số 5: Thích nghi nhanh, linh hoạt và đam mê khám phá điều mới.',
            6 => 'Đường đời số 6: Tinh thần phụng sự, yêu thương, quan tâm chăm sóc gia đình và tập thể.',
            7 => 'Đường đời số 7: Tư duy phân tích sâu sắc, tìm tòi chân lý và học hỏi không ngừng.',
            8 => 'Đường đời số 8: Bản lĩnh quản trị, tư duy tài chính và điều hành hiệu quả.',
        ];

        $numerologyProfile = [
            'dob' => $dob,
            'life_path_number' => $lifePathSum,
            'title' => "Con số đường đời {$lifePathSum}",
            'summary' => $numerologyDescriptions[$lifePathSum] ?? $numerologyDescriptions[9],
        ];

        $personalityResult = [
            'nlp' => $nlpResult,
            'disc' => $discProfile,
            'mbti' => $mbtiProfile,
            'numerology' => $numerologyProfile,
        ];

        // 3. Xử lý Bánh xe cuộc đời (Wheel of Life) - File 3
        $wheelInput = $request->input('wheel', []);
        $wheelLabels = [
            'finance'         => 'Tài chính',
            'personal_growth' => 'Phát triển bản thân',
            'health'          => 'Sức khỏe',
            'family'          => 'Gia đình',
            'relationships'   => 'Các mối quan hệ',
            'social_life'     => 'Đời sống xã hội',
            'spirituality'    => 'Đời sống tinh thần',
            'career'          => 'Sự nghiệp',
        ];

        $wheelScores = [];
        foreach ($wheelLabels as $k => $label) {
            $wheelScores[$k] = [
                'label' => $label,
                'score' => (int)($wheelInput[$k] ?? 5),
            ];
        }

        $desiredWheelInput = $request->input('desired_wheel', []);
        $desiredWheelScores = [];
        foreach ($wheelLabels as $k => $label) {
            $desiredWheelScores[$k] = [
                'label' => $label,
                'score' => (int)($desiredWheelInput[$k] ?? 5),
            ];
        }

        $wheelResult = [
            'dimensions' => $wheelScores,
            'desired_dimensions' => $desiredWheelScores,
            'lesson' => (string) $request->input('wheel_lesson', ''),
        ];

        // 4. Xử lý Phiếu khảo sát Hoài bão, Động cơ & Tự đánh giá - File 1
        $surveyResult = [
            'goal_short' => (string) $request->input('goal_short', ''),
            'goal_long'  => (string) $request->input('goal_long', ''),
            'biggest_success' => (string) $request->input('biggest_success', ''),
            'why_apply' => (string) $request->input('why_apply', ''),
            'work_organization' => (string) $request->input('work_organization', ''),
            'failure_lesson' => (string) $request->input('failure_lesson', ''),
            'why_choose_you' => (string) $request->input('why_choose_you', ''),
        ];

        // 5. Xử lý Khảo sát tìm hiểu về AZ Media & Tự trả lời
        $azChannels = $request->input('az_channels', []);
        $azChannelOther = (string) $request->input('az_channel_other', '');
        $azImpression = (string) $request->input('az_impression', '');

        $azSurveyResult = [
            'channels' => $azChannels,
            'channel_other' => $azChannelOther,
            'impression' => $azImpression,
        ];

        // Đóng gói toàn bộ cấu trúc bài test
        $fullAnswersData = [
            'quiz' => [
                'total' => $totalQuestions,
                'correct' => $correctCount,
                'details' => $quizDetails,
            ],
            'nlp' => $nlpResult,
            'personality' => $personalityResult,
            'wheel_of_life' => $wheelResult,
            'survey' => $surveyResult,
            'az_survey' => $azSurveyResult,
        ];

        // Lưu kết quả bài test vào CSDL
        $candidateTest = DB::transaction(function () use (
            $application,
            $totalQuestions,
            $correctCount,
            $score,
            $timeSpent,
            $passed,
            $quizDetails,
            $nlpInput,
            $nlpScores,
            $dominantKey,
            $discChoice,
            $discProfile,
            $mbtiEnergy,
            $mbtiDecision,
            $mbtiCode,
            $mbtiProfile,
            $dob,
            $lifePathSum,
            $wheelScores,
            $desiredWheelScores,
            $wheelResult,
            $surveyResult,
            $azChannels,
            $azChannelOther,
            $azImpression
        ) {
            $candidateTest = CandidateTest::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'total_questions' => $totalQuestions > 0 ? $totalQuestions : 10,
                    'correct_answers' => $correctCount,
                    'score' => $score,
                    'time_spent_seconds' => $timeSpent,
                    'passed' => $passed,
                    'answers_data' => null,
                    'draft_data' => null,
                    'completed_at' => now(),
                ]
            );

            $candidateTest->quizAnswers()->delete();
            foreach ($quizDetails as $detail) {
                CandidateTestQuizAnswer::create([
                    'candidate_test_id' => $candidateTest->id,
                    'question_id' => $detail['question_id'],
                    'question_text' => $detail['question_text'],
                    'selected_option' => $detail['selected_option'],
                    'correct_option' => $detail['correct_option'],
                    'is_correct' => $detail['is_correct'],
                ]);
            }

            $candidateTest->nlpAnswers()->delete();
            foreach ($nlpInput as $questionNumber => $ratings) {
                foreach ($ratings as $code => $value) {
                    if ($value === null || $value === '') {
                        continue;
                    }
                    CandidateTestNlpAnswer::create([
                        'candidate_test_id' => $candidateTest->id,
                        'question_number' => (int) $questionNumber,
                        'code' => $code,
                        'score' => (int) $value,
                    ]);
                }
            }

            CandidateTestPersonality::updateOrCreate(
                ['candidate_test_id' => $candidateTest->id],
                [
                    'nlp_dominant_code' => $dominantKey,
                    'nlp_visual' => $nlpScores['V'],
                    'nlp_auditory' => $nlpScores['A'],
                    'nlp_kinesthetic' => $nlpScores['K'],
                    'nlp_auditory_digital' => $nlpScores['Ad'],
                    'disc_group' => $discChoice,
                    'mbti_energy' => $mbtiEnergy,
                    'mbti_decision' => $mbtiDecision,
                    'mbti_code' => $mbtiCode,
                    'numerology_dob' => $dob,
                    'life_path_number' => $lifePathSum,
                ]
            );

            $candidateTest->personalityAnswers()->delete();
            foreach ($discProfile['answers'] as $key => $value) {
                CandidateTestPersonalityAnswer::create([
                    'candidate_test_id' => $candidateTest->id,
                    'section' => 'disc',
                    'item_key' => (string) $key,
                    'answer' => (string) $value,
                ]);
            }
            foreach ($mbtiProfile['answers'] as $key => $value) {
                CandidateTestPersonalityAnswer::create([
                    'candidate_test_id' => $candidateTest->id,
                    'section' => 'mbti',
                    'item_key' => (string) $key,
                    'answer' => (string) $value,
                ]);
            }

            $candidateTest->wheelDimensions()->delete();
            foreach ($wheelScores as $key => $dimension) {
                CandidateTestWheelDimension::create([
                    'candidate_test_id' => $candidateTest->id,
                    'dimension_key' => $key,
                    'horizon' => 'current',
                    'score' => $dimension['score'],
                ]);
            }
            foreach ($desiredWheelScores as $key => $dimension) {
                CandidateTestWheelDimension::create([
                    'candidate_test_id' => $candidateTest->id,
                    'dimension_key' => $key,
                    'horizon' => 'desired',
                    'score' => $dimension['score'],
                ]);
            }

            CandidateTestSurvey::updateOrCreate(
                ['candidate_test_id' => $candidateTest->id],
                [
                    'wheel_lesson' => $wheelResult['lesson'],
                    'goal_short' => $surveyResult['goal_short'],
                    'goal_long' => $surveyResult['goal_long'],
                    'biggest_success' => $surveyResult['biggest_success'],
                    'why_apply' => $surveyResult['why_apply'],
                    'work_organization' => $surveyResult['work_organization'],
                    'failure_lesson' => $surveyResult['failure_lesson'],
                    'why_choose_you' => $surveyResult['why_choose_you'],
                    'az_channel_other' => $azChannelOther,
                    'az_impression' => $azImpression,
                ]
            );

            $candidateTest->azChannels()->delete();
            foreach ($azChannels as $channel) {
                CandidateTestAzChannel::create([
                    'candidate_test_id' => $candidateTest->id,
                    'channel' => (string) $channel,
                ]);
            }

            return $candidateTest;
        });

        // Cập nhật trạng thái hồ sơ ứng viên
        $application->status = 'tested';
        $application->stage_step = 3;
        $application->save();

        // Tự động chuyển toàn bộ câu trả lời Phần 1, 2, 3, 4 thành file PDF lưu trữ cho Admin & TTS
        try {
            $dir = public_path('uploads/test_results');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $pdfFilename = "Bai_Test_AZ_{$application->application_code}.pdf";
            $pdfPath = "{$dir}/{$pdfFilename}";

            // Tải lại quan hệ để view PDF nhận đầy đủ dữ liệu
            $application->load(['candidate', 'position', 'testResult']);

            $pdf = PDF::loadView('azmedia.pdf.test_result_pdf', compact('application'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'tempDir' => public_path(),
                    'chroot'  => public_path(),
                    'isRemoteEnabled' => true,
                ]);
            $pdf->save($pdfPath);

            $candidateTest->pdf_file_path = "uploads/test_results/{$pdfFilename}";
            $candidateTest->save();
        } catch (\Exception $e) {
            Log::error("[Candidate Test PDF] Lỗi tạo file PDF bài test {$application->application_code}: " . $e->getMessage());
        }

        return redirect()->route('azmedia.candidate.test', ['code' => $application->application_code])
            ->with('success', "Chúc mừng bạn đã hoàn thành trọn vẹn Bài Đánh Giá Toàn Diện AZHR! Toàn bộ câu trả lời Phần 1, 2, 3, 4 đã được hệ thống xuất thành file PDF lưu trữ thành công.");
    }

    /**
     * Tải xuống hoặc xem file PDF kết quả bài test Phần 1, 2, 3, 4 (Dành cho Ứng viên)
     */
    public function downloadTestPDF($code, Request $request)
    {
        $application = Application::with(['candidate', 'position', 'testResult'])
            ->where('application_code', $code)
            ->firstOrFail();

        if (!$application->testResult || !$application->testResult->completed_at) {
            return back()->with('error', 'Ứng viên chưa hoàn thành bài test.');
        }

        $filename = "Bai_Test_AZ_{$application->application_code}.pdf";
        $filePath = public_path("uploads/test_results/{$filename}");

        $templatePath = resource_path('views/azmedia/pdf/test_result_pdf.blade.php');
        $templateHash = hash_file('sha256', $templatePath);
        if ($templateHash === false) {
            throw new \RuntimeException('Không thể đọc mẫu báo cáo PDF kết quả bài test.');
        }

        $hashDirectory = storage_path('app/test_result_template_hashes');
        $hashPath = $hashDirectory . DIRECTORY_SEPARATOR . sha1($application->application_code) . '.sha256';
        $cachedTemplateHash = '';
        if (is_file($hashPath)) {
            $cachedTemplateHash = file_get_contents($hashPath);
            if ($cachedTemplateHash === false) {
                throw new \RuntimeException('Không thể đọc phiên bản mẫu báo cáo PDF đã lưu.');
            }
            $cachedTemplateHash = trim($cachedTemplateHash);
        }

        if (!file_exists($filePath) || !hash_equals($templateHash, $cachedTemplateHash)) {
            $dir = public_path('uploads/test_results');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $pdf = PDF::loadView('azmedia.pdf.test_result_pdf', compact('application'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'tempDir' => public_path(),
                    'chroot'  => public_path(),
                    'isRemoteEnabled' => true,
                ]);
            $pdf->save($filePath);

            if (!is_dir($hashDirectory) && !mkdir($hashDirectory, 0755, true) && !is_dir($hashDirectory)) {
                throw new \RuntimeException('Không thể tạo thư mục lưu phiên bản mẫu báo cáo PDF.');
            }
            if (file_put_contents($hashPath, $templateHash, LOCK_EX) === false) {
                throw new \RuntimeException('Không thể lưu phiên bản mẫu báo cáo PDF.');
            }
        }

        $isDownload = $request->has('download');
        if ($isDownload) {
            return response()->download($filePath, "Ket_Qua_Danh_Gia_AZHR_{$application->application_code}.pdf", [
                'Content-Type' => 'application/pdf',
            ]);
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
