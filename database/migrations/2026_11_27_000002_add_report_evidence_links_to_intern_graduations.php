<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReportEvidenceLinksToInternGraduations extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('intern_graduations', 'report_draft_link')) {
            Schema::table('intern_graduations', function (Blueprint $table) {
                $table->text('report_draft_link')->nullable();
            });
        }

        if (!Schema::hasColumn('intern_graduations', 'report_final_link')) {
            Schema::table('intern_graduations', function (Blueprint $table) {
                $table->text('report_final_link')->nullable();
            });
        }
    }

    public function down()
    {
        $columns = [];

        if (Schema::hasColumn('intern_graduations', 'report_draft_link')) {
            $columns[] = 'report_draft_link';
        }

        if (Schema::hasColumn('intern_graduations', 'report_final_link')) {
            $columns[] = 'report_final_link';
        }

        if ($columns) {
            Schema::table('intern_graduations', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
}
