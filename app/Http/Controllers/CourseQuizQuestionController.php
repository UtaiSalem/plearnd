<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Question;
use App\Models\CourseQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\QuestionResource;

class CourseQuizQuestionController extends Controller
{
    public function store(Course $course, CourseQuiz $quiz, Request $request)
    {
        $question = $quiz->questions()->create([
            'user_id'   => auth()->id(),
            'course_id' => $course->id,
            'text'      => $request->text,
            'points'    => $request->points,
        ]);

        $course->increment('total_score', $request->points);
        $quiz->increment('total_score', $request->points);
        $quiz->increment('total_questions');

        if($request->hasFile('images')) {
            $images = $request->file('images');
            $fileNames = [];
            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = Storage::disk('public')->putFileAs('images/courses/quizzes/questions', $image, $fileName);
                $fileNames[] = $fileName;

                $question->images()->create([
                    'image_url' => $image_url
                ]);
            }
        }
        return response()->json([
            'success' => true,
            'question' => new QuestionResource($question),
        ], 200);
    }

    public function destroy(Course $course, CourseQuiz $quiz, Question $question)
    {
        if ($question->images) {
            foreach ($question->images as $image) {
                Storage::disk('public')->delete($image->image_url);
            }
            $question->images()->delete();
        }

        if ($question->options) {
            foreach ($question->options as $option) {
                if ($option->images) {
                    foreach ($option->images as $image) {
                        Storage::disk('public')->delete($image->image_url);
                    }
                    $option->images()->delete();
                }
            }
            $question->options()->delete();
        }
        
        // $userAnswerQuestion 
        $userAnswerQuestion = $question->userAnswers;
        foreach ($userAnswerQuestion as $answer) {
            $answer->delete();
        }
        
        $course->decrement('total_score', $question->points);
        $quiz->decrement('total_score', $question->points);
        $quiz->decrement('total_questions');
        
        $question->delete();
        return response()->json([
            'success' => true,
        ], 204);
    }
}
