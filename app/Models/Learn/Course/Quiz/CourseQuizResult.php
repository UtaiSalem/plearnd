<?php

namespace App\Models\Learn\Course\Quizzes;

use App\Models\Shared\User;
use App\Models\Learn\Course\info\Course;
use App\Models\Learn\Course\quizzes\CourseQuiz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseQuizResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'quiz_id',
        'score',
        'percentage',
        'started_at',
        'completed_at',
        'duration',
        'attempted_questions',
        'correct_answers',
        'incorrect_answers',
        'skipped_questions',
        'passed',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Define relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(CourseQuiz::class);
    }


    // Add any other custom methods or relationships
}
