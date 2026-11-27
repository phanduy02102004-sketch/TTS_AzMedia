<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBirthDateAndStudentCodeToCandidatesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('candidates', 'birth_date')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->date('birth_date')->nullable()->after('phone');
            });
        }

        if (!Schema::hasColumn('candidates', 'student_code')) {
            Schema::table('candidates', function (Blueprint $table) {
                $table->string('student_code')->nullable()->index()->after('birth_date');
            });
        }
    }

    public function down()
    {
        $columns = [];

        if (Schema::hasColumn('candidates', 'birth_date')) {
            $columns[] = 'birth_date';
        }

        if (Schema::hasColumn('candidates', 'student_code')) {
            $columns[] = 'student_code';
        }

        if ($columns) {
            Schema::table('candidates', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
}
