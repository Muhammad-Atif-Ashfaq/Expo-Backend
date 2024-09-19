<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profile_picture',
        'role',
        'invitation_link',
        'admin_id',
        'original_password',
        'contest_id'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function formField()
    {
        return $this->hasMany(FormField::class);
    }

    public function givenScores()
    {
        return $this->hasMany(Score::class, 'judge_id');
    }


    public function scorecards()
    {
        return $this->belongsToMany(ScoreCard::class, 'user_scorecard', 'user_id', 'scorecard_id');
    }
}
