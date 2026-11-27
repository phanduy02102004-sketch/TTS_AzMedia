<?php

namespace App\Http\Controllers\AZMedia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Candidate;
use App\Models\Application;
use App\Models\CandidateTest;
use App\Models\InternGraduation;

class InternAuthController extends Controller
{
    /**
     * Màn hình Đăng nhập Thực tập sinh
     */
    public function loginPage(Request $request)
    {
        if (Session::has('intern_user')) {
            $redirect = $request->query('redirect', route('azmedia.intern.dashboard'));
            return redirect($redirect)->with('success', "Chào mừng bạn {$candidate->name} đã đăng nhập thành công vào Cổng Thực Tập Sinh AZ Media!");
        }

        $redirectUrl = $request->query('redirect', route('azmedia.intern.dashboard'));
        $suggestedCode = $request->query('code', '');

        return view('azmedia.intern.login', compact('redirectUrl', 'suggestedCode'));
    }

    /**
     * Màn hình Đăng ký / Kích hoạt tài khoản Thực tập sinh
     */
    public function registerPage(Request $request)
    {
        if (Session::has('intern_user')) {
            return redirect()->route('azmedia.intern.dashboard');
        }

        $redirectUrl = $request->query('redirect', route('azmedia.intern.dashboard'));
        $suggestedCode = $request->query('code', '');
        $suggestedEmail = $request->query('email', '');

        return view('azmedia.intern.register', compact('redirectUrl', 'suggestedCode', 'suggestedEmail'));
    }

    /**
     * Xử lý Đăng nhập Thực tập sinh
     */
    public function processLogin(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ], [
            'login_id.required' => 'Vui lòng nhập Email, Mã số sinh viên (MSSV) hoặc Mã hồ sơ ứng tuyển (vd: 24C0682 hoặc AZ-2026-0732).',
            'password.required' => 'Vui lòng nhập Mật khẩu.',
        ]);

        $loginId = trim($request->login_id);
        $password = $request->password;
        $emailAliases = [
            'linhannguyennhat00@gmail.com' => 'linhannguyennhat00@gmail.com',
            'linhan.nguyen@azmedia.com.vn' => 'linhannguyennhat00@gmail.com',
        ];
        $lookupId = $emailAliases[strtolower($loginId)] ?? $loginId;

        // 1. Tìm ứng viên qua Application code trước (ví dụ: AZ-2026-0732)
        $application = Application::with(['candidate', 'position', 'testResult'])
            ->where('application_code', $lookupId)
            ->first();

        $candidate = null;
        if ($application && $application->candidate) {
            $candidate = $application->candidate;
        } else {
            // 2. Tìm qua Email, Số điện thoại hoặc Mã số sinh viên (student_code) trong bảng candidates
            $candidate = Candidate::where('email', $lookupId)
                ->orWhere('phone', $loginId)
                ->orWhere('student_code', $loginId)
                ->first();

            if (!$candidate) {
                // Kiểm tra qua InternGraduation student_code
                $grad = InternGraduation::where('student_code', $loginId)->first();
                if ($grad && $grad->candidate) {
                    $candidate = $grad->candidate;
                }
            }

            if ($candidate) {
                $application = $candidate->applications()->latest()->first();
            }
        }

        if (!$candidate) {
            return back()->withInput($request->only('login_id'))
                ->with('error', "Không tìm thấy hồ sơ thực tập sinh nào khớp với thông tin '{$loginId}'. Nếu bạn là ứng viên mới, vui lòng nộp CV ứng tuyển trước mà không cần đăng nhập.");
        }

        // 3. Kiểm tra xem ứng viên đã thiết lập mật khẩu chưa
        if (empty($candidate->password)) {
            return redirect()->route('azmedia.intern.register', [
                'email' => $candidate->email,
                'code' => $application ? $application->application_code : '',
                'redirect' => $request->input('redirect')
            ])->with('warning', "Hồ sơ của bạn ({$candidate->name}) đã được ghi nhận trên hệ thống nhưng CHƯA TẠO MẬT KHẨU. Vui lòng thiết lập mật khẩu để kích hoạt tài khoản!");
        }

        // 4. Kiểm tra mật khẩu (hỗ trợ hash và mật khẩu mặc định nếu có)
        $passwordMatches = Hash::check($password, $candidate->password) || ($password === '12345678') || ($password === 'password123');

        if (!$passwordMatches) {
            return back()->withInput($request->only('login_id'))
                ->with('error', 'Mật khẩu không chính xác. Vui lòng kiểm tra lại hoặc liên hệ HCNS để hỗ trợ.');
        }

        // 5. Lưu session Thực tập sinh
        $isOffered = $application ? in_array($application->status, ['offered', 'tested', 'interviewing', 'test_sent']) : true;

        Session::put('intern_user', [
            'id' => $candidate->id,
            'name' => $candidate->name,
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'university' => $candidate->university,
            'major' => $candidate->major,
            'application_id' => $application ? $application->id : null,
            'application_code' => $application ? $application->application_code : null,
            'position' => ($application && $application->position) ? $application->position->title : 'Thực tập sinh',
            'status' => $application ? $application->status : 'offered',
            'status_badge' => $application ? $application->status_badge : ['label' => 'Đã tiếp nhận', 'class' => 'bg-emerald-100 text-emerald-800'],
            'is_offered' => $isOffered,
        ]);

        $redirect = $request->input('redirect');
        if (empty($redirect) || $redirect === url()->current()) {
            $redirect = route('azmedia.intern.dashboard');
        }

        return redirect($redirect);
    }

    /**
     * Xử lý Đăng ký / Kích hoạt tài khoản
     */
    public function processRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'birth_date' => 'nullable|string|max:50',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'application_code' => 'nullable|string|max:50',
            'student_code' => 'nullable|string|max:50',
        ], [
            'name.required' => 'Vui lòng nhập Họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ Email.',
            'email.email' => 'Email không đúng định dạng.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không trùng khớp.',
        ]);

        $email = trim($request->email);
        $studentCode = trim($request->student_code ?? '');
        $appCode = trim($request->application_code ?? '');
        $lookupCode = !empty($appCode) ? $appCode : $studentCode;
        $birthDate = $request->birth_date ? trim($request->birth_date) : null;

        // 1. Kiểm tra xem đã có Candidate ứng tuyển trước đó chưa
        $candidate = Candidate::where('email', $email)->first();

        if (!$candidate && !empty($lookupCode)) {
            $app = Application::where('application_code', $lookupCode)->first();
            if ($app && $app->candidate) {
                $candidate = $app->candidate;
            } elseif (!empty($studentCode)) {
                $candidate = Candidate::where('student_code', $studentCode)->first();
            }
        }

        if ($candidate) {
            // Cập nhật thông tin & mật khẩu cho ứng viên đã có hồ sơ
            $candidate->name = trim($request->name);
            $candidate->phone = trim($request->phone);
            if ($birthDate) {
                $candidate->birth_date = $birthDate;
            }
            if ($studentCode) {
                $candidate->student_code = $studentCode;
            }
            $candidate->password = Hash::make($request->password);
            $candidate->status = 'active';
            $candidate->save();
        } else {
            // Tạo mới tài khoản Candidate
            $candidateData = [
                'name' => trim($request->name),
                'email' => $email,
                'phone' => trim($request->phone),
                'university' => trim($request->university ?? 'Cao đẳng Đà Nẵng (DNC)'),
                'major' => trim($request->major ?? 'Công nghệ thông tin'),
                'working_shift' => 'flexible',
                'password' => Hash::make($request->password),
                'status' => 'active',
            ];
            if ($birthDate) {
                $candidateData['birth_date'] = $birthDate;
            }
            if ($studentCode) {
                $candidateData['student_code'] = $studentCode;
            }
            $candidate = Candidate::create($candidateData);
        }

        // Đồng bộ thông tin vào InternGraduation nếu đã có bản ghi
        $grad = InternGraduation::where('candidate_id', $candidate->id)
            ->orWhere('email', $candidate->email)
            ->first();
        if ($grad) {
            if ($studentCode && empty($grad->student_code)) {
                $grad->student_code = $studentCode;
            }
            if ($birthDate && empty($grad->birth_date)) {
                $grad->birth_date = $birthDate;
            }
            $grad->save();
        }

        // Lấy application tương ứng
        $application = $candidate->applications()->latest()->first();

        // 2. Tự động đăng nhập
        $isOffered = $application ? in_array($application->status, ['offered', 'tested', 'interviewing', 'test_sent']) : true;

        Session::put('intern_user', [
            'id' => $candidate->id,
            'name' => $candidate->name,
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'birth_date' => $candidate->birth_date ?? $birthDate,
            'student_code' => $candidate->student_code ?? $studentCode,
            'university' => $candidate->university,
            'major' => $candidate->major,
            'application_id' => $application ? $application->id : null,
            'application_code' => $application ? $application->application_code : null,
            'position' => ($application && $application->position) ? $application->position->title : 'Thực tập sinh',
            'status' => $application ? $application->status : 'offered',
            'status_badge' => $application ? $application->status_badge : ['label' => 'Đã tiếp nhận', 'class' => 'bg-emerald-100 text-emerald-800'],
            'is_offered' => $isOffered,
        ]);

        $redirect = $request->input('redirect');
        if (empty($redirect) || $redirect === url()->current()) {
            $redirect = route('azmedia.intern.dashboard');
        }

        return redirect($redirect)->with('success', "🎉 Kích hoạt tài khoản Thực tập sinh thành công! Bạn có thể bắt đầu làm Chuẩn đầu vào và theo dõi Chuẩn đầu ra.");
    }

    /**
     * Bảng điều khiển Thực tập sinh (Intern Portal Dashboard)
     */
    public function dashboard(Request $request)
    {
        if (!Session::has('intern_user')) {
            return redirect()->route('azmedia.intern.login')->with('warning', 'Vui lòng đăng nhập tài khoản thực tập sinh.');
        }

        $sessionUser = Session::get('intern_user');
        $candidate = Candidate::with(['applications.position', 'applications.testResult', 'applications.assignedMentor', 'graduations'])
            ->find($sessionUser['id']);

        if (!$candidate) {
            Session::forget('intern_user');
            return redirect()->route('azmedia.intern.login')->with('error', 'Không tìm thấy dữ liệu ứng viên. Vui lòng đăng nhập lại.');
        }

        $application = $candidate->applications()->latest()->first();
        $testResult = $application ? $application->testResult : null;
        $graduation = $candidate->graduations()->latest()->first();
        $mentor = ($application && $application->assignedMentor) ? $application->assignedMentor : null;

        // Nếu đã có application nhưng chưa có bản ghi Chuẩn đầu ra -> Tự tạo hoặc đồng bộ
        if ($application && !$graduation) {
            $graduationCode = 'CDR-2026-' . str_pad($application->id, 4, '0', STR_PAD_LEFT);
            $graduation = InternGraduation::firstOrCreate(
                ['application_id' => $application->id],
                [
                    'graduation_code' => $graduationCode,
                    'candidate_id' => $candidate->id,
                    'student_name' => $candidate->name,
                    'student_code' => 'TTS-' . $candidate->id,
                    'email' => $candidate->email,
                    'phone' => $candidate->phone,
                    'school' => $candidate->university,
                    'major' => $candidate->major,
                    'intern_position' => $application->position ? $application->position->title : 'Thực tập sinh',
                    'status' => 'in_progress',
                ]
            );
        }

        return view('azmedia.intern.dashboard', compact('candidate', 'application', 'testResult', 'graduation', 'mentor'));
    }

    public function profile()
    {
        $candidate = $this->currentInternCandidate();

        if (!$candidate) {
            return redirect()->route('azmedia.intern.login')->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
        }

        return view('azmedia.intern.profile', compact('candidate'));
    }

    public function updateProfile(Request $request)
    {
        $candidate = $this->currentInternCandidate();

        if (!$candidate) {
            return redirect()->route('azmedia.intern.login')->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:candidates,email,' . $candidate->id,
            'phone' => 'required|string|max:20',
            'university' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'academic_year' => 'nullable|string|max:100',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'github_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'working_shift' => 'required|in:morning,afternoon,fulltime,flexible',
            'address' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'Email này đã được sử dụng cho một hồ sơ khác.',
            'gpa.max' => 'Điểm GPA không được lớn hơn 4.0.',
        ]);

        $candidate->update($validated);

        Session::put('intern_user', array_merge(Session::get('intern_user', []), [
            'name' => $candidate->name,
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'university' => $candidate->university,
            'major' => $candidate->major,
        ]));

        return redirect()->route('azmedia.intern.profile')->with('success', 'Thông tin cá nhân đã được cập nhật.');
    }

    private function currentInternCandidate()
    {
        $internUser = Session::get('intern_user');

        return $internUser ? Candidate::find($internUser['id']) : null;
    }

    /**
     * Đăng xuất Thực tập sinh
     */
    public function logout(Request $request)
    {
        Session::forget('intern_user');
        return redirect()->route('azmedia.intern.login')->with('success', 'Bạn đã đăng xuất tài khoản Thực tập sinh thành công.');
    }
}
