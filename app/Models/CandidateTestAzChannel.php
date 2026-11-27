<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateTestAzChannel extends Model
{
    protected $guarded = [];

    public function candidateTest()
    {
        return $this->belongsTo(CandidateTest::class);
    }
}
