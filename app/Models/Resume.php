<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    protected $fillable = ['user_id','file_path','status','percentage','matched_keywords','extracted_text','file_hash'];

    protected $casts = [
        'matched_keywords' => 'array',
    ];
}
