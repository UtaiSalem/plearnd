<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\CourseQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CourseQuizResource;
use App\Http\Resources\CourseGroupResource;

class CourseQuizController extends Controller
{
    public function index(Course $course) 
    {
        return Inertia::render('Learn/Course/Quiz/Quizzes',[
            'course'                => new CourseResource($course),
            'quizzes'               => CourseQuizResource::collection($course->courseQuizzes),
            // 'groups'             => CourseGroupResource::collection($course->courseGroups),
            'isCourseAdmin'         => $course->user_id === auth()->id(),
            'courseMemberOfAuth'    => $course->courseMembers()->where('user_id', auth()->id())->first(),
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
            // 'end_date'          => 'nullable|date|after:start_date',
            'end_date'          => 'nullable|date',
            'shuffle_questions' => 'boolean',
            'passing_score'     => 'nullable|integer|min:0',
        ]);

        $validated['user_id']       = auth()->id();
        $validated['is_active']     = $validated['is_active'] ? 1 : 0;
        $validated['shuffle_questions'] = $validated['shuffle_questions'] ? 1 : 0;
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

    public function update(Course $course, CourseQuiz $quiz, Request $request)
    {
        // $quiz->update($request->all());
        $validated = $request->validate([
            'title'             => 'required|string',
            'description'       => 'nullable|string',
            'time_limit'        => 'required|integer|min:1',
            'is_active'         => 'boolean',
            'start_date'        => 'nullable|date',
            // 'end_date'          => 'nullable|date|after:start_date',
            'end_date'          => 'nullable|date',
            'shuffle_questions' => 'boolean',
            'passing_score'     => 'nullable|integer|min:0',
        ]);

        $validated['user_id']       = auth()->id();
        $validated['is_active']     = $validated['is_active'] ? 1 : 0;
        $validated['shuffle_questions'] = $validated['shuffle_questions'] ? 1 : 0;
        $validated['start_date']    = $validated['start_date'] ?  Carbon::parse($validated['start_date']) : null;
        $validated['end_date']      = $validated['end_date'] ? Carbon::parse($validated['end_date']) : null;

        $quiz->update($validated);

        // return redirect()->route('quizzes.show', $quiz);
        return response()->json([
            'success' => true,
            'message' => 'Quiz updated successfully',
            'quiz' => new CourseQuizResource($quiz),
        ], 200); // OK
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

