<?php

namespace App\Models;

use App\Models\Contest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScoreCard extends Model
{
    use HasFactory;
    protected $table = 'score_card';

    protected $fillable = [
        'admin_id',
        'contest_id',
        'judge_id',
        'fields',
        'current_participant_name',
        'current_participant_id',
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class,'contest_id');
    }

    public function judges()
    {
        return $this->belongsToMany(User::class, 'user_scorecard', 'scorecard_id', 'user_id');
    }
}
