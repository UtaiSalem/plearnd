<?php

namespace App\Repositories;

use App\Models\CourseQuizResult;
use App\Models\UserAnswerQuestion;

class QuizResultRepository
{
    public function updateQuizResult($quiz, $userId, $courseId)
    {
        $answers = UserAnswerQuestion::where('user_id', $userId)
            ->where('quiz_id', $quiz->id)
            ->get();

        return CourseQuizResult::updateOrCreate(
            [
                'course_id' => $courseId,
                'user_id' => $userId,
                'quiz_id' => $quiz->id,
            ],
            [
                'attempted_questions' => $answers->count(),
                'correct_answers' => $answers->where('answer_id', 'correct_option_id')->count(),
                'incorrect_answers' => $answers->where('answer_id', '!=', 'correct_option_id')->count(),
                'score' => $answers->sum('points')
            ]
        );
    }
}
