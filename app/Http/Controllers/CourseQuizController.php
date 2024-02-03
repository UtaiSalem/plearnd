<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\CourseQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CourseQuizResource;

class CourseQuizController extends Controller
{
    public function index() 
    {
        $quizzes = CourseQuiz::all();
        
        return Inertia::render('Quizzes/Index', [
            'quizzes' => $quizzes
        ]);
    }

    public function create()
    {
        return Inertia::render('Quizzes/Create');
    }

    public function store(Course $course, Request $request)
    {
        $validated = $request->validate([
            'title'             => 'required|string',
            'description'       => 'nullable|string',
            'time_limit'        => 'required|integer|min:1',
            'is_active'         => 'boolean',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after:start_date',
            'shuffle_questions' => 'boolean',
            'passing_score'     => 'nullable|integer|min:0',
        ]);

        $validated['user_id']       = auth()->id();
        $validated['start_date']    = $validated['start_date'] ?  Carbon::parse($validated['start_date']) : null;
        $validated['end_date']      = $validated['end_date'] ? Carbon::parse($validated['end_date']) : null;

        try {

            $quiz = $course->courseQuizzes()->create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Quiz created successfully',
                'quiz' => new CourseQuizResource($quiz),
            ], 201); // Created

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create quiz',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }

    public function show(CourseQuiz $quiz)
    {
        return Inertia::render('Quizzes/Show', [
            'quiz' => $quiz
        ]);
    }

    public function edit(CourseQuiz $quiz)
    {
        return Inertia::render('Quizzes/Edit', [
            'quiz' => $quiz 
        ]);
    }

    public function update(Request $request, CourseQuiz $quiz)
    {
        $quiz->update($request->all());

        return redirect()->route('quizzes.show', $quiz);
    }

    public function destroy(Course $course, CourseQuiz $quiz)
    {
        $quiz->delete();

        // return redirect()->route('quizzes.index');
        return response()->json([
            'success' => true,
        ], 200); // OK
    }
}

