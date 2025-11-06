<?php

namespace App\Services;

use App\Constants\QuizConstants;

class QuizEfficiencyService
{
    public function calculateEfficiency($quizResult, $quiz)
    {
        $SR = $this->calculateScoreRatio($quizResult->score, $quiz->total_score);
        $TE = $this->calculateTimeEfficiency($quiz->time_limit, $quizResult->duration);
        $ACC = $this->calculateAccuracy($quizResult->correct_answers, $quiz->total_questions);
        $EP = $this->calculateEditPenalty($quizResult->edit_count);

        return ceil(
            ($SR * QuizConstants::SCORE_RATIO_WEIGHT) + 
            ($TE * QuizConstants::TIME_EFFICIENCY_WEIGHT) + 
            ($ACC * QuizConstants::ACCURACY_WEIGHT) + 
            ($EP * QuizConstants::EDIT_PENALTY_WEIGHT)
        );
    }

    private function calculateScoreRatio($score, $totalScore)
    {
        return $totalScore > 0 ? ($score / $totalScore) : 0;
    }

    private function calculateTimeEfficiency($timeLimit, $duration)
    {
        $timeLimit = $timeLimit * 60;
        return ($timeLimit - ($duration/1000)) / $timeLimit;
    }

    private function calculateAccuracy($correctAnswers, $totalQuestions)
    {
        return $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) : 0;
    }

    private function calculateEditPenalty($totalEdits)
    {
        return max(0, QuizConstants::MAX_PENALTY - $totalEdits);
    }
}
