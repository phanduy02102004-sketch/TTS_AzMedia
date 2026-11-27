<?php

namespace Tests\Unit;

use App\Models\InternGraduation;
use Carbon\Carbon;
use Tests\TestCase;

class InternGraduationProgressTest extends TestCase
{
    public function test_completion_requires_all_checklist_items_test_and_signature()
    {
        $graduation = $this->completedCoreChecklist();

        $this->assertSame(83, $graduation->progress_percent);
        $this->assertFalse($graduation->isReadyForCompletionConfirmation());

        $graduation->exit_test_score = 80;
        $graduation->exit_test_completed_at = Carbon::now();

        $this->assertSame(92, $graduation->progress_percent);
        $this->assertTrue($graduation->isReadyForCompletionConfirmation());

        $graduation->completion_signature = 'data:image/png;base64,signature';
        $graduation->completion_confirmed_at = Carbon::now();

        $this->assertSame(100, $graduation->progress_percent);
    }

    public function test_incomplete_checklist_cannot_open_completion_confirmation()
    {
        $graduation = $this->completedCoreChecklist();
        $graduation->review_proof_link = null;
        $graduation->exit_test_score = 100;
        $graduation->exit_test_completed_at = Carbon::now();

        $this->assertFalse($graduation->isCoreChecklistComplete());
        $this->assertFalse($graduation->isReadyForCompletionConfirmation());
    }

    private function completedCoreChecklist()
    {
        return new InternGraduation([
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
        ]);
    }
}
