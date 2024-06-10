<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        // Add any other relevant fields
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

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Add any other custom methods or relationships
}
