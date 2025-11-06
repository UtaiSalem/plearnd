<?php

namespace App\Http\Controllers;

use App\Models\Question;

class QuestionAnswerController extends Controller
{
    public function index(Question $question)
    {
        return response()->json([
            'answer' => $question->correct_option_id
        ], 200);
    }
}
