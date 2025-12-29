<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Class AssessmentResult
 *
 * Represents a user's assessment attempt, including answers, question IDs, and scores.
 *
 * @property int $id
 * @property int $user_id
 * @property array $answers
 * @property array|null $question_ids
 * @property int $total_questions
 * @property int $correct_answers
 * @property int $wrong_answers
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property User $user
 */
class AssessmentResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'user_id',
        'answers',
        'question_ids',
        'total_questions',
        'correct_answers',
        'wrong_answers',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'answers' => 'array',
        'question_ids' => 'array',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
    ];

    /**
     * Get the user that owns this assessment result.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
