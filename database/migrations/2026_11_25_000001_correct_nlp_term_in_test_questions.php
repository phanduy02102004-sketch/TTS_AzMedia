<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CorrectNlpTermInTestQuestions extends Migration
{
    private $oldQuestion = 'Trong phương pháp đánh giá NLB, một người có phong cách tiếp nhận và xử lý công việc thiên về Visual (Hình ảnh / Thị giác) có đặc điểm nổi bật nào?';

    private $newQuestion = 'Trong phương pháp đánh giá NLP, một người có phong cách tiếp nhận và xử lý công việc thiên về Visual (Hình ảnh / Thị giác) có đặc điểm nổi bật nào?';

    public function up()
    {
        DB::table('test_questions')
            ->where('question', $this->oldQuestion)
            ->update(['question' => $this->newQuestion]);
    }

    public function down()
    {
        DB::table('test_questions')
            ->where('question', $this->newQuestion)
            ->update(['question' => $this->oldQuestion]);
    }
}