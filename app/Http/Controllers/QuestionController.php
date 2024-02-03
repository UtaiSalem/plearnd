<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\QuestionOption;

class QuestionController extends Controller
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
    public function store(Course $course, Question $question, Request $request)
    {
        $question = $request->all();
        
        return response()->json([
            'success' => true,
            'question' => $question,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Question $question, Request $request, )
    {
        $question->update([
            'correct_option_id' => $request->answer,
            'correct_answers' => $request->answer,
        ]);        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
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
        $course = $question->course;
        $course->decrement('total_score', $question->points);
        $question->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully',
        ], 204);
    }
}
