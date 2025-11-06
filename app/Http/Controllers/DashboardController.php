<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $auth = auth()->user();

        $authQuestionsAnswer = $auth->answerQuestions;
        $authAssignmentsAnswer = $auth->answerAssignments;

        return Inertia::render('Dashboard', [
            'profilePath' => '/../',
            'question_answers' => $authQuestionsAnswer,
            'assignment_answers' => $authAssignmentsAnswer,
        ]);
    }
}
