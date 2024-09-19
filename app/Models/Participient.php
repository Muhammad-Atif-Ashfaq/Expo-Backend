<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participient extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'contest_id',
        'fields_values',
        'is_judged',
    ];


    protected $casts = [
        'fields_values' => 'array',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class,'contest_id');
    }

    public function receivedScores()
    {
        return $this->hasMany(Score::class, 'participant_id');
    }

}
