<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDraftDataToCandidateTestsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('candidate_tests') && !Schema::hasColumn('candidate_tests', 'draft_data')) {
            Schema::table('candidate_tests', function (Blueprint $table) {
                $table->longText('draft_data')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('candidate_tests') && Schema::hasColumn('candidate_tests', 'draft_data')) {
            Schema::table('candidate_tests', function (Blueprint $table) {
                $table->dropColumn('draft_data');
            });
        }
    }
}
