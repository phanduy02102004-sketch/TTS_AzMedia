<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateTestPersonality extends Model
{
    protected $guarded = [];

    protected $casts = [
        'numerology_dob' => 'date',
    ];

    public function candidateTest()
    {
        return $this->belongsTo(CandidateTest::class);
    }
}
