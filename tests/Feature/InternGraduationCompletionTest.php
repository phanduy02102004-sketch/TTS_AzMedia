<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\InternGraduation;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class InternGraduationCompletionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_exit_test_records_score_and_requires_all_answers_correct()
    {
        $candidate = $this->createCandidate();
        $graduation = $this->createGraduation($candidate);
        $session = ['intern_user' => ['id' => $candidate->id, 'name' => $candidate->name]];
        $url = route('azmedia.graduation.exit-test', $graduation->graduation_code);

        $this->withSession($session)->post($url, [
            'answers' => ['q1' => 'A', 'q2' => 'B', 'q3' => 'A', 'q4' => 'D', 'q5' => 'A'],
        ])->assertSessionHas('success');

        $graduation->refresh();
        $this->assertSame(80, (int) $graduation->exit_test_score);
        $this->assertNotNull($graduation->exit_test_completed_at);

        $this->withSession($session)->post($url, [
            'answers' => ['q1' => 'A', 'q2' => 'B', 'q3' => 'A', 'q4' => 'D', 'q5' => 'C'],
        ])->assertSessionHas('success');

        $graduation->refresh();
        $this->assertSame(100, (int) $graduation->exit_test_score);
        $this->assertNotNull($graduation->exit_test_completed_at);
    }

    public function test_completion_requires_owned_record_full_checklist_test_and_signature()
    {
        $candidate = $this->createCandidate();
        $otherCandidate = $this->createCandidate();
        $graduation = $this->createGraduation($candidate, [
            'survey_1_completed' => true,
            'survey_2_completed' => true,
            'survey_proof_link' => 'https://example.test/survey',
            'report_draft_submitted' => true,
            'report_final_submitted' => true,
            'appointment_booked' => true,
            'referral_completed' => true,
            'word_report_submitted' => true,
            'video_submitted' => true,
            'review_map1' => true,
            'review_map2' => true,
            'review_page_homes' => true,
            'review_page_media' => true,
            'review_page_hr' => true,
            'review_proof_link' => 'https://example.test/reviews',
            'exit_test_score' => 100,
            'exit_test_completed_at' => now(),
        ]);
        $url = route('azmedia.graduation.confirm-completion', $graduation->graduation_code);
        $signature = 'data:image/png;base64,' . base64_encode("\x89PNG\r\n\x1a\n");

        $this->withSession(['intern_user' => ['id' => $otherCandidate->id]])
            ->post($url, [
                'completion_signature' => $signature,
                'completion_acknowledged' => '1',
            ])->assertNotFound();

        $this->withSession(['intern_user' => ['id' => $candidate->id]])
            ->post($url, [
                'completion_signature' => $signature,
                'completion_acknowledged' => '1',
            ])->assertSessionHas('success');

        $graduation->refresh();
        $this->assertSame(100, $graduation->progress_percent);
        $this->assertSame('pending_review', $graduation->status);
        $this->assertSame($signature, $graduation->completion_signature);
        $this->assertNotNull($graduation->completion_confirmed_at);
    }

    public function test_graduation_page_exposes_test_and_signature_steps_after_checklist_is_ready()
    {
        $candidate = $this->createCandidate();
        $graduation = $this->createGraduation($candidate, [
            'survey_1_completed' => true,
            'survey_2_completed' => true,
            'survey_proof_link' => 'https://example.test/survey',
            'report_draft_submitted' => true,
            'report_final_submitted' => true,
            'appointment_booked' => true,
            'referral_completed' => true,
            'word_report_submitted' => true,
            'video_submitted' => true,
            'review_map1' => true,
            'review_map2' => true,
            'review_page_homes' => true,
            'review_page_media' => true,
            'review_page_hr' => true,
            'review_proof_link' => 'https://example.test/reviews',
            'exit_test_score' => 80,
            'exit_test_completed_at' => now(),
        ]);

        $this->withSession(['intern_user' => [
            'id' => $candidate->id,
            'name' => $candidate->name,
            'email' => $candidate->email,
            'university' => $candidate->university,
        ]])
            ->get(route('azmedia.graduation.index', ['code' => $graduation->graduation_code]))
            ->assertOk()
            ->assertSee('Bài test đầu ra & xác nhận kết thúc', false)
            ->assertSee('Tiến độ chuẩn đầu ra: 92%')
            ->assertSee('completion-signature-canvas', false)
            ->assertSee('Ký & xác nhận hoàn tất 100%', false);
    }

    public function test_intern_dashboard_renders_for_authenticated_candidate_with_graduation()
    {
        $candidate = $this->createCandidate();
        $this->createGraduation($candidate);

        $this->withSession([
            'intern_user' => [
                'id' => $candidate->id,
                'name' => $candidate->name,
                'email' => $candidate->email,
                'university' => $candidate->university,
            ],
        ])->get(route('azmedia.intern.dashboard'))
            ->assertOk()
            ->assertSee($candidate->name)
            ->assertSee('Chuẩn Đầu Ra & Cấp Mộc', false);
    }

    public function test_signature_and_admin_approval_are_blocked_before_progress_reaches_100_percent()
    {
        $candidate = $this->createCandidate();
        $graduation = $this->createGraduation($candidate, [
            'exit_test_score' => 80,
            'exit_test_completed_at' => now(),
        ]);
        $signature = 'data:image/png;base64,' . base64_encode("\x89PNG\r\n\x1a\n");

        $this->withSession(['intern_user' => ['id' => $candidate->id]])
            ->post(route('azmedia.graduation.confirm-completion', $graduation->graduation_code), [
                'completion_signature' => $signature,
                'completion_acknowledged' => '1',
            ])->assertSessionHas('error');

        $this->withSession(['portal_user' => ['name' => 'Test Admin']])
            ->post(route('azmedia.portal.graduations.status', $graduation->id), [
                'status' => 'approved',
            ])->assertSessionHas('error');

        $graduation->refresh();
        $this->assertSame('in_progress', $graduation->status);
        $this->assertNull($graduation->completion_signature);
        $this->assertNull($graduation->completion_confirmed_at);
    }

    private function createCandidate()
    {
        return Candidate::create([
            'name' => 'Graduation Test',
            'email' => 'graduation-' . Str::uuid() . '@example.test',
            'phone' => '0000000000',
            'university' => 'Test University',
            'major' => 'Test',
        ]);
    }

    private function createGraduation(Candidate $candidate, array $attributes = [])
    {
        return InternGraduation::create(array_merge([
            'graduation_code' => 'CDR-TEST-' . Str::upper(Str::random(8)),
            'candidate_id' => $candidate->id,
            'student_name' => $candidate->name,
            'email' => $candidate->email,
            'phone' => $candidate->phone,
            'status' => 'in_progress',
        ], $attributes));
    }
}
