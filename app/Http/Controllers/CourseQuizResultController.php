<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseQuiz;
use Illuminate\Http\Request;
use App\Models\CourseQuizResult;
use App\Http\Requests\StoreCourseQuizResultRequest;
use App\Http\Requests\UpdateCourseQuizResultRequest;
use Illuminate\Support\Facades\DB;

class CourseQuizResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Course $course, CourseQuiz $quiz, Request $request)
    {

        $quizResult = $course->courseQuizResults()
            ->where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($quizResult) {
            $quizResult->update([
                'status'        => 0,
                'started_at'    => date('Y-m-d H:i:s'),
                'completed_at'  => $request->completed_at,
            ]);

            return response()->json([
                'status'        => true,
                'quizResult'    => $quizResult
            ], 201);

        }else {
            $quizResult = CourseQuizResult::create([
                'user_id'       => auth()->id(),
                'course_id'     => $course->id,
                'quiz_id'       => $quiz->id,
                'status'        => 0,
                'started_at'    => date('Y-m-d H:i:s'),
            ]);

            return response()->json([
                'status'        => true,
                'quizResult'    => $quizResult
            ], 201);
        }
    
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseQuizResult $courseQuizResult)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseQuizResult $courseQuizResult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseQuizResultRequest $request, CourseQuizResult $courseQuizResult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseQuizResult $courseQuizResult)
    {
        //
    }
}
