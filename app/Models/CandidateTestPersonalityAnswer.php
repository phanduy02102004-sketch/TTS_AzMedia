<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateTestPersonalityAnswer extends Model
{
    protected $guarded = [];

    public function candidateTest()
    {
        return $this->belongsTo(CandidateTest::class);
    }
}
