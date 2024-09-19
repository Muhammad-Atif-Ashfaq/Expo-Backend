<?php

namespace App\Models;

use App\Models\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = [
        'expo_id',
        'name',
        'start_date_time',
        'end_date_time',
        'max_contestent',
        'status'
    ];

    protected $with = ['judges', 'participient'];

    public function expo()
    {
        return $this->belongsTo(Expo::class);
    }

    public function judges()
    {
        return $this->hasMany(User::class);
    }

    public function participient()
    {
        return $this->hasMany(Participient::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function files()
    {
        return $this->hasMany(FileUpload::class);
    }
}
