<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Class AssessmentResult
 *
 * @property int $id
 * @property int $user_id
 * @property array $answers
 * @property array|null $question_ids
 * @property int $total_questions
 * @property int $correct_answers
 * @property int $wrong_answers
 * @property int $violations
 * @property bool $is_submitted
 * @property int|null $time_left
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property User $user
 */
class AssessmentResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'answers',
        'question_ids',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'violations',        // ✅ added
        'is_submitted',
        'time_left',
        'started_at',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'answers' => 'array',
        'question_ids' => 'array',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'violations' => 'integer',   // ✅ added
        'is_submitted' => 'boolean',
        'time_left' => 'integer',
        'started_at' => 'datetime',
    ];

    /**
     * Get the user that owns this assessment result.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
