<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = ['contest_id', 'judge_id', 'participant_id','field_name','score', 'status'];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function judge()
    {
        return $this->belongsTo(User::class, 'judge_id');
    }

    public function participant()
    {
        return $this->belongsTo(Participient::class,'participant_id');
    }
}
