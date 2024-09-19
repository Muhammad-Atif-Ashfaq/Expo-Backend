<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'status'
    ];

    protected $with = ['contest'];

    public function contest()
    {
        return $this->hasMany(Contest::class);
    }
}
