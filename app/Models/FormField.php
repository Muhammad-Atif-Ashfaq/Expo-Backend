<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'name',
        'type',
        'label',
        'required',
        'contest_id',
        'is_important'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
