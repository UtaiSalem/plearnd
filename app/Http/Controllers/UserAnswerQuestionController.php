<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Question;
use App\Models\CourseMember;
use Illuminate\Http\Request;
use App\Models\QuestionOption;
use App\Models\CourseQuizResult;
use App\Models\UserAnswerQuestion;

class UserAnswerQuestionController extends Controller
{

    public function store(User $user, Question $question, Request $request)
    {
        $isCorrectAnswer = $request->answer_id === $question->correct_option_id;

        $userAnswerQuestion = $user->answerQuestions()->create([
                'course_id'         => $request->course_id,
                'quiz_id'           => $question->questionable_id,
                'question_id'       => $question->id,
                'correct_option_id' => $question->correct_option_id,
                'answer_id'         => $request->answer_id,         
                'points'            => $isCorrectAnswer ? $question->points: 0,           
        ]);

        $course_quiz = $question->questionable;
        $course_quiz_result = CourseQuizResult::where('course_id', $request->course_id)
                                                ->where('user_id', $user->id)
                                                ->where('quiz_id', $course_quiz->id)
                                                ->first();
    
        $course_quiz_result->increment('attempted_questions');
        
        $course_member = CourseMember::where('course_id', $request->course_id)->where('user_id', $user->id)->first();
        
        if ($isCorrectAnswer) {
            $course_quiz_result->increment('correct_answers');
            $course_quiz_result->increment('score', $question->points);

            $course_member->increment('achieved_score', $question->points);
        }else{
            $course_quiz_result->increment('incorrect_answers');
        }

        if ($userAnswerQuestion) {
            return response()->json([
                'success'               => true,
                'message'               => 'บันทึก',
                'authAnswerQuestion'    => $userAnswerQuestion->id,
                'points'                => $userAnswerQuestion->points,
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'points' => 0
            ], 201);
        }
    }

    // public function update(User $user, Question $question, QuestionOption $answer, Request $request)
    public function update(User $user, Question $question, UserAnswerQuestion $answer, Request $request)
    {
        $oldAnswer = $answer->answer_id;
        $newAnswer = $request->answer_id;
        $correctAnswer = $question->correct_option_id;

        $isOldAnswerCorrect = $oldAnswer === $correctAnswer;
        $isNewAnswerCorrect = $newAnswer === $correctAnswer;

        $answer->update([
            'answer_id'         => $newAnswer,
            'points'            => $isNewAnswerCorrect ? $question->points : 0,
        ]);

        $course_quiz_result = CourseQuizResult::where('course_id', $answer->course_id)->where('user_id', $answer->user_id)->where('quiz_id', $answer->quiz_id)->first();
        $course_member = CourseMember::where('course_id', $answer->course_id)->where('user_id', $answer->user_id)->first();        

        if (!$isOldAnswerCorrect && $isNewAnswerCorrect) {
            
            $course_quiz_result->increment('score', $question->points);
            $course_quiz_result->increment('correct_answers');
            
            $course_quiz_result->decrement('incorrect_answers');

            $course_member->increment('achieved_score', $question->points);
        }

        if ($isOldAnswerCorrect && !$isNewAnswerCorrect) {
            
            $course_quiz_result->decrement('score', $question->points);
            $course_quiz_result->decrement('correct_answers');
            
            $course_quiz_result->increment('incorrect_answers');

            $course_member->decrement('achieved_score', $question->points);
        }

        return response()->json([
            'success'               => true,
            'message'               => 'แก้ไข',
            'authAnswerQuestion'    => $answer->id,
            'points'                => $answer,
        ], 200);
    }

}
