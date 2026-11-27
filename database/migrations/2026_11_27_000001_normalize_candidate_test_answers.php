<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class NormalizeCandidateTestAnswers extends Migration
{
    public function up()
    {
        if (Schema::hasTable('candidate_tests') && !Schema::hasColumn('candidate_tests', 'pdf_file_path')) {
            Schema::table('candidate_tests', function (Blueprint $table) {
                $table->string('pdf_file_path', 255)->nullable();
            });
        }

        Schema::create('candidate_test_quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_test_id');
            $table->unsignedBigInteger('question_id')->nullable();
            $table->text('question_text')->nullable();
            $table->string('selected_option', 20);
            $table->string('correct_option', 20)->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            $table->foreign('candidate_test_id')->references('id')->on('candidate_tests')->onDelete('cascade');
        });

        Schema::create('candidate_test_nlp_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_test_id');
            $table->unsignedSmallInteger('question_number');
            $table->char('code', 2);
            $table->unsignedTinyInteger('score');
            $table->timestamps();
            $table->foreign('candidate_test_id')->references('id')->on('candidate_tests')->onDelete('cascade');
            $table->unique(['candidate_test_id', 'question_number', 'code'], 'ct_nlp_answer_unique');
        });

        Schema::create('candidate_test_personalities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_test_id')->unique();
            $table->char('nlp_dominant_code', 2)->nullable();
            $table->unsignedTinyInteger('nlp_visual')->default(0);
            $table->unsignedTinyInteger('nlp_auditory')->default(0);
            $table->unsignedTinyInteger('nlp_kinesthetic')->default(0);
            $table->unsignedTinyInteger('nlp_auditory_digital')->default(0);
            $table->char('disc_group', 1)->nullable();
            $table->char('mbti_energy', 1)->nullable();
            $table->char('mbti_decision', 1)->nullable();
            $table->char('mbti_code', 2)->nullable();
            $table->date('numerology_dob')->nullable();
            $table->unsignedSmallInteger('life_path_number')->nullable();
            $table->timestamps();
            $table->foreign('candidate_test_id')->references('id')->on('candidate_tests')->onDelete('cascade');
        });

        Schema::create('candidate_test_wheel_dimensions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_test_id');
            $table->string('dimension_key', 50);
            $table->string('horizon', 20);
            $table->unsignedTinyInteger('score');
            $table->timestamps();
            $table->foreign('candidate_test_id')->references('id')->on('candidate_tests')->onDelete('cascade');
            $table->unique(['candidate_test_id', 'dimension_key', 'horizon'], 'ct_wheel_dim_unique');
        });

        Schema::create('candidate_test_surveys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_test_id')->unique();
            $table->text('wheel_lesson')->nullable();
            $table->text('goal_short')->nullable();
            $table->text('goal_long')->nullable();
            $table->text('biggest_success')->nullable();
            $table->text('why_apply')->nullable();
            $table->text('work_organization')->nullable();
            $table->text('failure_lesson')->nullable();
            $table->text('why_choose_you')->nullable();
            $table->text('az_channel_other')->nullable();
            $table->text('az_impression')->nullable();
            $table->timestamps();
            $table->foreign('candidate_test_id')->references('id')->on('candidate_tests')->onDelete('cascade');
        });

        Schema::create('candidate_test_az_channels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_test_id');
            $table->string('channel', 50);
            $table->timestamps();
            $table->foreign('candidate_test_id')->references('id')->on('candidate_tests')->onDelete('cascade');
            $table->unique(['candidate_test_id', 'channel'], 'ct_az_channel_unique');
        });

        Schema::create('candidate_test_personality_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('candidate_test_id');
            $table->string('section', 20);
            $table->string('item_key', 50);
            $table->string('answer', 255);
            $table->timestamps();
            $table->foreign('candidate_test_id')->references('id')->on('candidate_tests')->onDelete('cascade');
            $table->unique(['candidate_test_id', 'section', 'item_key'], 'ct_personality_answer_unique');
        });

        foreach (DB::table('candidate_tests')->whereNotNull('answers_data')->get(['id', 'answers_data']) as $test) {
            $data = json_decode((string) $test->answers_data, true);
            if (!is_array($data)) {
                continue;
            }

            if (!empty($data['quiz']['details']) && is_array($data['quiz']['details'])) {
                foreach ($data['quiz']['details'] as $detail) {
                    DB::table('candidate_test_quiz_answers')->insert([
                        'candidate_test_id' => $test->id,
                        'question_id' => $detail['question_id'] ?? null,
                        'question_text' => $detail['question_text'] ?? null,
                        'selected_option' => $detail['selected_option'] ?? '',
                        'correct_option' => $detail['correct_option'] ?? null,
                        'is_correct' => !empty($detail['is_correct']),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            if (!empty($data['nlp']['raw_input']) && is_array($data['nlp']['raw_input'])) {
                foreach ($data['nlp']['raw_input'] as $questionNumber => $ratings) {
                    foreach ($ratings as $code => $score) {
                        DB::table('candidate_test_nlp_answers')->insert([
                            'candidate_test_id' => $test->id,
                            'question_number' => (int) $questionNumber,
                            'code' => (string) $code,
                            'score' => (int) $score,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            $nlp = $data['nlp'] ?? [];
            $scores = $nlp['scores'] ?? [];
            $disc = $data['personality']['disc'] ?? [];
            $mbti = $data['personality']['mbti'] ?? [];
            $numerology = $data['personality']['numerology'] ?? [];
            DB::table('candidate_test_personalities')->insert([
                'candidate_test_id' => $test->id,
                'nlp_dominant_code' => $nlp['dominant_code'] ?? null,
                'nlp_visual' => $scores['Visual'] ?? 0,
                'nlp_auditory' => $scores['Auditory'] ?? 0,
                'nlp_kinesthetic' => $scores['Kinesthetic'] ?? 0,
                'nlp_auditory_digital' => $scores['Auditory_Digital'] ?? 0,
                'disc_group' => $disc['group'] ?? null,
                'mbti_energy' => $mbti['energy'] ? substr((string) $mbti['energy'], 0, 1) : null,
                'mbti_decision' => $mbti['decision'] ? substr((string) $mbti['decision'], 0, 1) : null,
                'mbti_code' => $mbti['code'] ?? null,
                'numerology_dob' => $numerology['dob'] ?? null,
                'life_path_number' => $numerology['life_path_number'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach (['disc', 'mbti'] as $section) {
                $answers = $data['personality'][$section]['answers'] ?? [];
                if (is_array($answers)) {
                    foreach ($answers as $key => $value) {
                        DB::table('candidate_test_personality_answers')->insert([
                            'candidate_test_id' => $test->id,
                            'section' => $section,
                            'item_key' => (string) $key,
                            'answer' => (string) $value,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            foreach ([['current', 'dimensions'], ['desired', 'desired_dimensions']] as [$horizon, $field]) {
                foreach ($data['wheel_of_life'][$field] ?? [] as $key => $dimension) {
                    DB::table('candidate_test_wheel_dimensions')->insert([
                        'candidate_test_id' => $test->id,
                        'dimension_key' => (string) $key,
                        'horizon' => $horizon,
                        'score' => (int) ($dimension['score'] ?? 5),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            $survey = $data['survey'] ?? [];
            $wheel = $data['wheel_of_life'] ?? [];
            $azSurvey = $data['az_survey'] ?? [];
            DB::table('candidate_test_surveys')->insert([
                'candidate_test_id' => $test->id,
                'wheel_lesson' => $wheel['lesson'] ?? null,
                'goal_short' => $survey['goal_short'] ?? null,
                'goal_long' => $survey['goal_long'] ?? null,
                'biggest_success' => $survey['biggest_success'] ?? null,
                'why_apply' => $survey['why_apply'] ?? null,
                'work_organization' => $survey['work_organization'] ?? null,
                'failure_lesson' => $survey['failure_lesson'] ?? null,
                'why_choose_you' => $survey['why_choose_you'] ?? null,
                'az_channel_other' => $azSurvey['channel_other'] ?? null,
                'az_impression' => $azSurvey['impression'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($azSurvey['channels'] ?? [] as $channel) {
                DB::table('candidate_test_az_channels')->insert([
                    'candidate_test_id' => $test->id,
                    'channel' => (string) $channel,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $legacy = json_decode((string) $test->answers_data, true);
            if (!empty($legacy['pdf_file'])) {
                DB::table('candidate_tests')->where('id', $test->id)->update([
                    'pdf_file_path' => $legacy['pdf_file'],
                ]);
            }
        }

        DB::table('candidate_tests')->whereNotNull('answers_data')->update(['answers_data' => null]);
    }

    public function down()
    {
        Schema::dropIfExists('candidate_test_personality_answers');
        Schema::dropIfExists('candidate_test_az_channels');
        Schema::dropIfExists('candidate_test_surveys');
        Schema::dropIfExists('candidate_test_wheel_dimensions');
        Schema::dropIfExists('candidate_test_personalities');
        Schema::dropIfExists('candidate_test_nlp_answers');
        Schema::dropIfExists('candidate_test_quiz_answers');

        if (Schema::hasTable('candidate_tests') && Schema::hasColumn('candidate_tests', 'pdf_file_path')) {
            Schema::table('candidate_tests', function (Blueprint $table) {
                $table->dropColumn('pdf_file_path');
            });
        }
    }
}
