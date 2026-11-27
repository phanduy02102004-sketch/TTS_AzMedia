<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateTestQuizAnswer extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function candidateTest()
    {
        return $this->belongsTo(CandidateTest::class);
    }
}
