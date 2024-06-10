<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Resources\TopicResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\AcademyResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\CourseGroupResource;


class CourseLessonController extends Controller
{
    public function index(Course $course)
    {
        return Inertia::render('Learn/Course/Lesson/Lessons',[
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->courseLessons()->orderBy('order')->paginate()),
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'courseMemberOfAuth'=> $course->courseMembers()->where('user_id', auth()->id())->first(),

            // 'groups'       => CourseGroupResource::collection($course->courseGroups()->orderBy('order')->get()),
            'groups'       => $course->courseGroups()->get(['id', 'name']),
        ]);
    }

    public function show(Course $course, Lesson $lesson)
    {
        $isCourseAdmin = $lesson->user_id === auth()->id();
        try {
            if (!$isCourseAdmin && (auth()->user()->pp < $lesson->point_tuition_fee)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have enough points to access this lesson // คุณมีพอยต์ไม่เพียงพอเพื่อเข้าถึงบทเรียนนี้'
                ], 401);
            }

            $lesson->increment('view_count');

            if (!$isCourseAdmin) { auth()->user()->decrement('pp', $lesson->point_tuition_fee); }

            $course->increment('points', $lesson->point_tuition_fee);

            return Inertia::render('Learn/Course/Lesson/Lesson',[
                'course'                => new CourseResource($course),
                'lesson'                => new LessonResource($lesson),
                'isCourseAdmin'         => $lesson->user_id === auth()->id(),
                'courseMemberOfAuth'    => $course->courseMembers()->where('user_id', auth()->id())->first(),
                'authUserPP'            => auth()->user()->pp,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lesson not found'
            ], 404);
        }
    }

        /**
     * Store a newly created resource in storage.
     */
    public function store(Course $course, Request $request)
    {
        $validated  = $request->validate([
            'title'             => 'required',
            'description'       => 'nullable',
            'content'           => 'required',
            'youtube_url'       => 'nullable',
            'order'             => 'required',
            'min_read'          => 'required',
            'point_tuition_fee' => 'required||numeric||min:0',
            'status'            => 'required',
        ]);

        $validated['user_id'] = auth()->id();

        $lesson = $course->CourseLessons()->create($validated);

        if($lesson){ $course->increment('lessons'); }

        if($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/courses/lessons', $image, $fileName);

                $lesson->images()->create([
                    'filename' => $fileName,
                ]);
            }
        }

        if($lesson){
            $course->increment('lessons');
        }

        return response()->json([
            'success' => true,
            'newLesson' => new LessonResource($lesson)
        ], 200);
    }

    //edit lesson page
    public function edit(Course $course, Lesson $lesson)
    {
        try {
            if ($course->user_id !== auth()->id()) {
                return to_route('course.lessons.index', $course->id, $lesson->id);
            }
            return Inertia::render('Learn/Course/Lesson/EditLesson',[
                'isCourseAdmin'         => $lesson->user_id === auth()->id(),
                'courseMemberOfAuth'    => $course->courseMembers()->where('user_id', auth()->id())->first(),
                'course' => new CourseResource($course),
                'lesson' => new LessonResource($lesson),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lesson not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Course $course, Lesson $lesson, Request $request)
    {
        try {
            if ($course->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            $validated = $request->validate([
                'title'             => 'required',
                'description'       => 'nullable',
                'content'           => 'required',
                'youtube_url'       => 'nullable',
                'min_read'          => 'required',
                'order'             => 'required',
                'point_tuition_fee' => 'required||numeric||min:0',
                'status'            => 'required',
            ]);

            $lesson->update($validated);

            if($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $image) {
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('images/courses/lessons', $image, $fileName);
                    $lesson->images()->create([
                        'filename' => $fileName,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'lesson' => new LessonResource($lesson),
                'message' => 'Lesson updated successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }


        /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Lesson $lesson)
    {
        if ($lesson->images) {
            foreach ($lesson->images as $image) {
                Storage::disk('public')->delete('images/courses/lessons/'. $image->image_url);
                $image->delete();   
            }
        }

        $lesson->delete();
        $course->decrement('lessons');

        return response()->json([
            'success' => true
        ], 200);
    }
}
