<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateTest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'passed' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function quizAnswers()
    {
        return $this->hasMany(CandidateTestQuizAnswer::class);
    }

    public function nlpAnswers()
    {
        return $this->hasMany(CandidateTestNlpAnswer::class);
    }

    public function personality()
    {
        return $this->hasOne(CandidateTestPersonality::class);
    }

    public function wheelDimensions()
    {
        return $this->hasMany(CandidateTestWheelDimension::class);
    }

    public function survey()
    {
        return $this->hasOne(CandidateTestSurvey::class);
    }

    public function azChannels()
    {
        return $this->hasMany(CandidateTestAzChannel::class);
    }

    public function personalityAnswers()
    {
        return $this->hasMany(CandidateTestPersonalityAnswer::class);
    }

    public function getAnswersDataAttribute($value)
    {
        if ($this->relationLoaded('quizAnswers')
            || $this->relationLoaded('nlpAnswers')
            || $this->relationLoaded('personality')
            || $this->relationLoaded('wheelDimensions')
            || $this->relationLoaded('survey')) {
            return $this->buildStructuredAnswers();
        }

        if ($value) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return $this->buildStructuredAnswers();
    }

    public function buildStructuredAnswers()
    {
        $this->loadMissing(['quizAnswers', 'nlpAnswers', 'personality', 'wheelDimensions', 'survey', 'azChannels']);

        $nlpScores = [
            'Visual' => $this->personality->nlp_visual ?? 0,
            'Auditory' => $this->personality->nlp_auditory ?? 0,
            'Kinesthetic' => $this->personality->nlp_kinesthetic ?? 0,
            'Auditory_Digital' => $this->personality->nlp_auditory_digital ?? 0,
        ];

        $rawNlp = [];
        foreach ($this->nlpAnswers as $answer) {
            $rawNlp[$answer->question_number][$answer->code] = (string) $answer->score;
        }

        $wheelLabels = [
            'finance' => 'Tài chính',
            'personal_growth' => 'Phát triển bản thân',
            'health' => 'Sức khỏe',
            'family' => 'Gia đình',
            'relationships' => 'Các mối quan hệ',
            'social_life' => 'Đời sống xã hội',
            'spirituality' => 'Đời sống tinh thần',
            'career' => 'Sự nghiệp',
        ];

        $dimensions = [];
        $desiredDimensions = [];
        foreach ($this->wheelDimensions as $dimension) {
            $target = $dimension->horizon === 'desired' ? $desiredDimensions : $dimensions;
            $target[$dimension->dimension_key] = [
                'label' => $wheelLabels[$dimension->dimension_key] ?? $dimension->dimension_key,
                'score' => $dimension->score,
            ];
            if ($dimension->horizon === 'desired') {
                $desiredDimensions = $target;
            } else {
                $dimensions = $target;
            }
        }

        $discProfile = null;
        $mbtiProfile = null;
        $numerologyProfile = null;
        if ($this->personality) {
            $discProfile = [
                'group' => $this->personality->disc_group,
                'title' => $this->personality->disc_group,
                'summary' => '',
                'answers' => [],
            ];
            $mbtiProfile = [
                'code' => $this->personality->mbti_code,
                'energy' => $this->personality->mbti_energy,
                'decision' => $this->personality->mbti_decision,
                'title' => $this->personality->mbti_code,
                'summary' => '',
                'answers' => [],
            ];
            $numerologyProfile = [
                'dob' => $this->personality->numerology_dob,
                'life_path_number' => $this->personality->life_path_number,
                'title' => 'Con số đường đời ' . $this->personality->life_path_number,
                'summary' => '',
            ];
        }

        $survey = $this->survey;

        return [
            'quiz' => [
                'total' => $this->total_questions,
                'correct' => $this->correct_answers,
                'details' => $this->quizAnswers->map(function ($answer) {
                    return [
                        'question_id' => $answer->question_id,
                        'question_text' => $answer->question_text,
                        'selected_option' => $answer->selected_option,
                        'correct_option' => $answer->correct_option,
                        'is_correct' => (bool) $answer->is_correct,
                    ];
                })->toArray(),
            ],
            'nlp' => [
                'scores' => $nlpScores,
                'dominant_code' => $this->personality->nlp_dominant_code ?? null,
                'raw_input' => $rawNlp,
            ],
            'personality' => [
                'disc' => $discProfile,
                'mbti' => $mbtiProfile,
                'numerology' => $numerologyProfile,
            ],
            'wheel_of_life' => [
                'dimensions' => $dimensions,
                'desired_dimensions' => $desiredDimensions,
                'lesson' => $survey->wheel_lesson ?? '',
            ],
            'survey' => $survey ? [
                'goal_short' => $survey->goal_short,
                'goal_long' => $survey->goal_long,
                'biggest_success' => $survey->biggest_success,
                'why_apply' => $survey->why_apply,
                'work_organization' => $survey->work_organization,
                'failure_lesson' => $survey->failure_lesson,
                'why_choose_you' => $survey->why_choose_you,
            ] : [],
            'az_survey' => $survey ? [
                'channels' => $this->azChannels->pluck('channel')->toArray(),
                'channel_other' => $survey->az_channel_other,
                'impression' => $survey->az_impression,
            ] : [],
            'pdf_file' => $this->pdf_file_path,
        ];
    }
}
