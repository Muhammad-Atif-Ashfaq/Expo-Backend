<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    use HasFactory;
    protected $fillable = ['admin_id','file','contest_id'];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }
}
