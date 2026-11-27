<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\CandidateTest;
use App\Models\InternshipPosition;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class CandidateTestAutosaveTest extends TestCase
{
    use DatabaseTransactions;

    public function test_candidate_answers_are_saved_as_a_resumable_draft()
    {
        $candidate = Candidate::create([
            'name' => 'Autosave Test',
            'email' => 'autosave-' . Str::uuid() . '@example.test',
            'phone' => '0000000000',
            'university' => 'Test University',
            'major' => 'Test',
        ]);

        $position = InternshipPosition::create([
            'title' => 'Autosave Test',
            'slug' => 'autosave-' . Str::uuid(),
            'department' => 'Test',
        ]);

        $application = Application::create([
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'application_code' => 'AUTOSAVE-' . Str::uuid(),
            'cv_path' => 'test.pdf',
            'cv_filename' => 'test.pdf',
            'status' => 'offered',
        ]);

        $response = $this->withSession([
            'intern_user' => ['id' => $candidate->id, 'name' => $candidate->name],
        ])->postJson(route('azmedia.candidate.test.autosave', $application->application_code), [
            'fields' => [
                ['name' => 'goal_short', 'value' => 'Complete my study plan'],
                ['name' => 'wheel[health]', 'value' => '8'],
            ],
        ]);

        $response->assertOk()->assertJson(['saved' => true]);

        $candidateTest = CandidateTest::where('application_id', $application->id)->firstOrFail();
        $this->assertNull($candidateTest->completed_at);
        $this->assertSame([
            ['name' => 'goal_short', 'value' => 'Complete my study plan'],
            ['name' => 'wheel[health]', 'value' => '8'],
        ], json_decode($candidateTest->draft_data, true));

        $this->get(route('azmedia.candidate.test', $application->application_code))
            ->assertOk()
            ->assertSee('Complete my study plan');
    }

    public function test_candidate_cannot_autosave_another_candidates_test()
    {
        $candidate = Candidate::create([
            'name' => 'Autosave Test',
            'email' => 'autosave-' . Str::uuid() . '@example.test',
            'phone' => '0000000000',
            'university' => 'Test University',
            'major' => 'Test',
        ]);

        $otherCandidate = Candidate::create([
            'name' => 'Other Candidate',
            'email' => 'autosave-' . Str::uuid() . '@example.test',
            'phone' => '0000000001',
            'university' => 'Test University',
            'major' => 'Test',
        ]);

        $position = InternshipPosition::create([
            'title' => 'Autosave Test',
            'slug' => 'autosave-' . Str::uuid(),
            'department' => 'Test',
        ]);

        $application = Application::create([
            'candidate_id' => $otherCandidate->id,
            'position_id' => $position->id,
            'application_code' => 'AUTOSAVE-' . Str::uuid(),
            'cv_path' => 'test.pdf',
            'cv_filename' => 'test.pdf',
            'status' => 'offered',
        ]);

        $response = $this->withSession([
            'intern_user' => ['id' => $candidate->id, 'name' => $candidate->name],
        ])->postJson(route('azmedia.candidate.test.autosave', $application->application_code), [
            'fields' => [
                ['name' => 'goal_short', 'value' => 'Should not be saved'],
            ],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('candidate_tests', ['application_id' => $application->id]);
    }
}
