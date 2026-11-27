<?php

namespace App\Http\Controllers\AZMedia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\InternshipPosition;
use App\Models\Candidate;
use App\Models\Application;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use App\Mail\AZMediaEmail;
use Database\Seeders\InternshipPositionSeeder;

class AZMediaCareersController extends Controller
{
    /**
     * Trang chủ tuyển dụng AZ Media (Careers Landing Page)
     */
    public function index(Request $request)
    {
        if (!InternshipPosition::exists()) {
            app(InternshipPositionSeeder::class)->run();
        }

        $query = InternshipPosition::where('status', 'open');

        // Bộ lọc chuyên ngành / phòng ban
        if ($request->filled('category') && $request->category !== 'all') {
            $query->forCategory($request->category);
        }

        // Bộ lọc hình thức (part_time, full_time)
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Bộ lọc địa điểm
        if ($request->filled('location') && $request->location !== 'all') {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $positions = $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc')->get();

        // Thống kê nhanh
        $stats = [
            'total_open' => InternshipPosition::where('status', 'open')->count(),
            'total_applied' => Application::count(),
            'total_offered' => Application::where('status', 'offered')->count(),
            'partner_enterprises' => '300+',
            'trained_students' => '1000+',
        ];

        $internUser = session('intern_user');
        $candidate = !empty($internUser['id']) ? Candidate::find($internUser['id']) : null;
        $careerCatalog = config('career_catalog');

        return view('azmedia.careers.index', compact('positions', 'stats', 'candidate', 'careerCatalog'));
    }

    /**
     * Chi tiết vị trí tuyển dụng (Job Detail)
     */
    public function jobDetail($slug)
    {
        $position = InternshipPosition::where('slug', $slug)->firstOrFail();
        $position->increment('views_count');

        // Các vị trí liên quan khác
        $relatedPositions = InternshipPosition::where('id', '!=', $position->id)
            ->where('status', 'open')
            ->limit(3)
            ->get();

        $internUser = session('intern_user');
        $candidate = !empty($internUser['id']) ? Candidate::find($internUser['id']) : null;
        $careerCatalog = config('career_catalog');

        return view('azmedia.careers.detail', compact('position', 'relatedPositions', 'candidate', 'careerCatalog'));
    }

    /**
     * Nộp đơn nhanh (Quick Apply)
     */
    public function submitApply(Request $request)
    {
        $request->validate([
            'position_id' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'birth_date' => 'required|date|before_or_equal:today',
            'university' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'working_shift' => 'required|string',
            'cv_file' => 'required|file|mimes:pdf|max:5120', // Tối đa 5MB, định dạng PDF
        ], [
            'name.required' => 'Vui lòng nhập họ và tên của bạn.',
            'email.required' => 'Vui lòng cung cấp email liên hệ chính xác.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'birth_date.required' => 'Vui lòng nhập ngày tháng năm sinh.',
            'birth_date.date' => 'Ngày tháng năm sinh không hợp lệ.',
            'birth_date.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hiện tại.',
            'university.required' => 'Vui lòng chọn hoặc nhập trường Đại học / Cao đẳng của bạn.',
            'major.required' => 'Vui lòng nhập ngành học của bạn.',
            'cv_file.required' => 'Vui lòng tải lên file CV định dạng PDF.',
            'cv_file.mimes' => 'Hệ thống chỉ chấp nhận file CV định dạng PDF theo chuẩn tuyển dụng.',
            'cv_file.max' => 'Dung lượng file CV không được vượt quá 5MB.',
        ]);

        if ($request->position_id === 'other') {
            $customTitle = trim($request->input('custom_position_title', ''));
            if (empty($customTitle)) {
                $customTitle = 'Thực tập sinh (Vị trí tự đề xuất)';
            }
            $position = InternshipPosition::firstOrCreate(
                ['title' => $customTitle],
                [
                    'slug' => Str::slug($customTitle) . '-' . Str::random(4),
                    'department' => 'Tổng hợp / Đa ngành',
                    'category' => 'general',
                    'location' => '84 Nguyễn Hữu Dật, Hải Châu, Đà Nẵng',
                    'type' => 'part_time',
                    'quantity' => 5,
                    'stipend' => 'Thỏa thuận theo năng lực + Hỗ trợ dấu mộc',
                    'summary' => 'Vị trí thực tập do ứng viên tự đề xuất và đăng ký nguyện vọng.',
                    'description' => 'Vị trí thực tập do ứng viên tự đề xuất nguyện vọng và định hướng chuyên môn tại AZ Media.',
                    'requirements' => 'Nhiệt huyết, tinh thần học hỏi cao, cam kết hoàn thành tốt công việc được giao.',
                    'benefits' => 'Được hướng dẫn bởi Mentor Lê Đình Quang, hỗ trợ tài liệu & cấp dấu mộc thực tập chuẩn chỉ.',
                    'status' => 'open',
                ]
            );
        } else {
            $position = InternshipPosition::findOrFail($request->position_id);
        }

        // Lưu hoặc cập nhật thông tin ứng viên
        $candidate = Candidate::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'university' => $request->university,
                'major' => $request->major,
                'academic_year' => $request->academic_year ?? 'Năm 3',
                'gpa' => $request->gpa ?? null,
                'github_url' => $request->github_url ?? null,
                'portfolio_url' => $request->portfolio_url ?? null,
                'working_shift' => $request->working_shift,
                'address' => $request->address ?? 'Đà Nẵng',
            ]
        );

        // Chống gửi trùng lặp nếu người dùng click đúp gửi đơn nhiều lần liên tiếp
        $recentApp = Application::where('candidate_id', $candidate->id)
            ->where('position_id', $position->id)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->first();

        if ($recentApp) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cảm ơn bạn đã ứng tuyển công ty chúng tôi. Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.',
                    'application_code' => $recentApp->application_code,
                    'track_url' => route('azmedia.candidate.track', ['code' => $recentApp->application_code]),
                ]);
            }
            return redirect()->route('azmedia.candidate.track', ['code' => $recentApp->application_code])
                ->with('success', 'Cảm ơn bạn đã ứng tuyển công ty chúng tôi. Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.');
        }

        // Xử lý upload file CV an toàn
        $file = $request->file('cv_file');
        $safeName = 'CV_' . Str::slug($candidate->name, '_') . '_' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('uploads/cvs');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $file->move($destinationPath, $safeName);
        $cvPath = 'uploads/cvs/' . $safeName;

        // Sinh mã hồ sơ chuẩn: AZ-2026-XXXX
        $appCode = 'AZ-2026-' . strtoupper(Str::random(4));
        while (Application::where('application_code', $appCode)->exists()) {
            $appCode = 'AZ-2026-' . strtoupper(Str::random(4));
        }

        // Tạo hồ sơ ứng tuyển - TỰ ĐỘNG DUYỆT CV (Bỏ qua bước 1, 2 duyệt thủ công)
        $application = Application::create([
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'application_code' => $appCode,
            'cv_path' => $cvPath,
            'cv_filename' => $file->getClientOriginalName(),
            'cover_letter' => $request->cover_letter ?? null,
            'status' => 'test_sent', // Tự động duyệt qua vòng 1 và 2, chuyển thẳng sang Bài Test
            'stage_step' => 3,
        ]);

        $trackUrl = route('azmedia.candidate.track', ['code' => $appCode]);
        $testUrl  = route('azmedia.candidate.test', ['code' => $appCode]);

        // Tự động gửi Email thông báo Duyệt CV & Mời làm bài Test trực tuyến (Email Automation)
        $template = EmailTemplate::where('code', 'test_invitation')->first();
        if ($template) {
            $replacements = [
                '{candidate_name}'   => $candidate->name,
                '{position_title}'   => $position->title,
                '{application_code}' => $application->application_code,
                '{track_url}'        => $trackUrl,
                '{test_link}'        => $testUrl,
            ];

            $subject = str_replace(array_keys($replacements), array_values($replacements), $template->subject);
            $body    = str_replace(array_keys($replacements), array_values($replacements), $template->body_html);

            $emailStatus = 'sent';
            try {
                Mail::to($candidate->email, $candidate->name)
                    ->send(new AZMediaEmail($subject, $body, $candidate->name));
            } catch (\Exception $e) {
                $emailStatus = 'failed';
                Log::error('[AZ Media] Lỗi gửi email tự động duyệt và mời test: ' . $e->getMessage());
            }

            EmailLog::create([
                'application_id'  => $application->id,
                'recipient_email' => $candidate->email,
                'recipient_name'  => $candidate->name,
                'template_code'   => 'test_invitation',
                'subject'         => $subject,
                'body_preview'    => mb_substr(strip_tags($body), 0, 500, 'UTF-8'),
                'status'          => $emailStatus,
                'sent_at'         => now(),
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã ứng tuyển công ty chúng tôi. Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.',
                'application_code' => $appCode,
                'track_url' => $trackUrl,
                'test_url' => $testUrl,
            ]);
        }

        return redirect()->route('azmedia.candidate.test', ['code' => $appCode])
            ->with('success', 'Cảm ơn bạn đã ứng tuyển công ty chúng tôi. Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.');
    }
}
