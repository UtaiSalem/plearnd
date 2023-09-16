<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonImage;
use Illuminate\Http\Request;

class LessonImageController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LessonImage $lessonImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LessonImage $lessonImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LessonImage $lessonImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson, LessonImage $image)
    {
        $image->delete();
    }
}
