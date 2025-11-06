<?php

namespace App\Models\Learn\Course\Questions;

use App\Models\Shared\User;
use App\Models\Learn\Course\info\Course;
use App\Models\Learn\Course\questions\Question;
use App\Models\Learn\Course\quizzes\CourseQuiz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAnswerQuestion extends Model
{
    use HasFactory;

    protected $guarded = [];

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

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }


}
