<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\QuestionOption;
use App\Models\UserAnswerQuestion;

class UserAnswerQuestionController extends Controller
{
    public function store(User $user, QuestionOption $answer, Request $request)
    {
        $question = Question::find($request->question_id);

        $userAnswer = $user->answerQuestions()->create([
                'question_id'       => $question->id,
                'correct_option_id' => $question->correct_option_id,
                'answer_id'         => $answer->id,
                'points'            => $answer->id === $question->correct_option_id ? $question->points: 0,           
        ]);

        if ($userAnswer) {
            return response()->json([
                'success'   => true,
                'points'    => $userAnswer->points,
                'message'   => 'บันทึก'
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'points' => 0
            ], 201);
        }
    }

    // public function update(Request $request, UserAnswerQuestion $userAnswerQuestion)
    public function update(User $user, QuestionOption $answer, Question $question)
    {
        $userAnswer = UserAnswerQuestion::updateOrCreate(
            [
                'user_id' => $user->id,
                'question_id' => $question->id,
                'correct_option_id' => $question->correct_option_id],
            [
                'answer_id' => $answer->id,
                'points' => $answer->id === $question->correct_option_id ? $question->points: 0,
            ]);
        
        $user->decrement('pp', 3);

        if ($userAnswer) {
            return response()->json([
                'success' => true,
                'points' => $userAnswer->points,
                'message'   => 'แก้ไข'
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'points' => 0
            ], 201);
        }
    }

}
