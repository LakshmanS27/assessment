<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['question_text', 'question_type', 'options', 'correct_answer'];


    protected $casts = [
        'options' => 'array',
    ];
}
