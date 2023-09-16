<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Resources\TopicResource;
use App\Http\Resources\TopicResourse;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\AcademyResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\QuestionResource;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Http\Resources\AssignmentResource;

class LessonController extends Controller
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
    public function store(Course $course, Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            // 'content' => 'required',
            // 'duration' => 'nullable|string',
            // 'status' => 'required|string',
            // 'creater' => auth()->id()
        ]);

        $validated['user_id'] = auth()->id();

        // $lesson = new Lesson();
        // $lesson->fill($request->all());
        // $lesson->save();
        $lesson = $course->lessons()->create($validated);

        // return redirect()->route('lessons.index')->with('success', 'Lesson created successfully!');
        return response()->json([
            'success' => true,
            'newLesson' => new LessonResource(Lesson::find($lesson->id))
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        $lesson->user_id == auth()->id() ? $isCourseAdmin = true: $isCourseAdmin = false;
        $questions = $lesson->questions;
        $assignments = $lesson->assignments;
        $topics = $lesson->topics;
        $course = $lesson->course;
        $academy = $course->academy;

        return Inertia::render('Lesson',[
            'isCourseAdmin' => $isCourseAdmin,
            'academy'       => new AcademyResource($academy),
            'course'        => new CourseResource($course),
            'lesson'        => new LessonResource($lesson),
            'topics'        => TopicResource::collection($topics),
            'assignments'   => AssignmentResource::collection($assignments),
            'questions'     => QuestionResource::collection($questions),
            'imagePath'     => '/../../'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Course $course, Lesson $lesson, Request $request)
    {
        $lesson->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if($request->hasFile('images')) {
            $images = $request->file('images');
            $fileNames = [];
            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = Storage::disk('public')->putFileAs('images/lessons', $image, $fileName);
                $fileNames[] = $fileName;

                $lessonImages[] = $lesson->images()->create([
                    'image_url' => $image_url
                ]);
            }
        }
        return response()->json([
            // 'lesson' => new LessonResource($lesson),
            'images' => $lessonImages ?? []
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
    }
}
