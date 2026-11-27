<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExitTestAndCompletionSignatureToInternGraduations extends Migration
{
    public function up()
    {
        Schema::table('intern_graduations', function (Blueprint $table) {
            $table->json('exit_test_answers')->nullable();
            $table->unsignedTinyInteger('exit_test_score')->nullable();
            $table->timestamp('exit_test_completed_at')->nullable();
            $table->longText('completion_signature')->nullable();
            $table->timestamp('completion_confirmed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('intern_graduations', function (Blueprint $table) {
            $table->dropColumn([
                'exit_test_answers',
                'exit_test_score',
                'exit_test_completed_at',
                'completion_signature',
                'completion_confirmed_at',
            ]);
        });
    }
}
