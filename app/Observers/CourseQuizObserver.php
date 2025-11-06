<?php

namespace App\Observers;

use App\Models\CourseQuiz;
use App\Models\Course;

class CourseQuizObserver
{
    /**
     * Handle the CourseQuiz "created" event.
     */
    public function created(CourseQuiz $courseQuiz): void
    {
        $course = $courseQuiz->course;
        if ($course) {
            $course->increment('quizzes');
        }
    }

    /**
     * Handle the CourseQuiz "deleted" event.
     */
    public function deleted(CourseQuiz $courseQuiz): void
    {
        $course = $courseQuiz->course;
        if ($course && $course->quizzes > 0) {
            $course->decrement('quizzes');
        }
    }

    /**
     * Handle the CourseQuiz "restored" event.
     */
    public function restored(CourseQuiz $courseQuiz): void
    {
        $this->created($courseQuiz);
    }

    /**
     * Handle the CourseQuiz "force deleted" event.
     */
    public function forceDeleted(CourseQuiz $courseQuiz): void
    {
        $this->deleted($courseQuiz);
    }
}