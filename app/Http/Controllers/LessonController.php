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
use App\Http\Resources\CourseGroupResource;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        // $isCourseAdmin = $course->user_id == auth()->id() ? true: false;
        return Inertia::render('Learn/Course/Lesson/Lessons',[
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->lessons),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
        ]);
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
            'description' => 'nullable',
            'content' => 'required',
            'youtube_url' => '',
            'status' => 'required',
        ]);
        $validated['user_id'] = auth()->id();

        $lesson = $course->lessons()->create([
            'user_id'       =>  $validated['user_id'],
            'title'         =>  $validated['title'],
            'description'   =>  $validated['description'],
            'content'       =>  $validated['content'],
            'youtube_url'   =>  $validated['youtube_url'],
            'status'        =>  $validated['status'],
            // 'video_url'     =>  $validated['video_url'],
        ]);

        if($request->hasFile('images')) {
            $images = $request->file('images');
            // $fileNames = [];
            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/courses/lessons', $image, $fileName);
                // $fileNames[] = $fileName;

                $lesson->images()->create([
                    // 'image_url' => $image_url
                    'image_url' => $fileName,
                ]);
            }
        }

        // return redirect()->route('lessons.index')->with('success', 'Lesson created successfully!');
        return response()->json([
            'success' => true,
            'newLesson' => new LessonResource(Lesson::find($lesson->id))
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, Lesson $lesson)
    {
        // $lesson->user_id == auth()->id() ? $isCourseAdmin = true: $isCourseAdmin = false;
        $isCourseAdmin = $lesson->user_id == auth()->id() ? true: false;
        $questions = $lesson->questions;
        $assignments = $lesson->assignments;
        $topics = $lesson->topics;
        // $course = $lesson->course;
        $academy = $course->academy;

        return Inertia::render('Learn/Lesson/Lesson',[
            'isCourseAdmin' => $isCourseAdmin,
            'academy'       => $academy ? new AcademyResource($academy) : null,
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->lessons),
            'lesson'        => new LessonResource($lesson),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'topics'        => TopicResource::collection($topics),
            'assignments'   => AssignmentResource::collection($assignments),
            'questions'     => QuestionResource::collection($questions),
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
                $image_url = Storage::disk('public')->putFileAs('images/courses/lessons', $image, $fileName);
                // $fileNames[] = $fileName;

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

        if ($lesson->images) {
            foreach ($lesson->images as $image) {
                Storage::disk('public')->delete('images/courses/lessons/'. $image->image_url);
                $image->delete();   
            }
        }

        $lesson->delete();

        return response()->json([
            'success' => true
        ], 200);
    }
}
