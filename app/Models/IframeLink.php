<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IframeLink extends Model
{
    use HasFactory;

    protected $table='iframe_link';

    protected $fillable = [
        'contest_id',
        'iframe_link',
    ];
}
