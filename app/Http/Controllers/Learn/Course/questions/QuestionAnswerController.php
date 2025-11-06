<?php

namespace App\Http\Controllers\Learn\Course\Questions;

use App\Http\Controllers\Controller;
use App\Models\Learn\Course\questions\Question;

class QuestionAnswerController extends Controller
{
    public function index(Question $question)
    {
        return response()->json([
            'answer' => $question->correct_option_id
        ], 200);
    }
}
