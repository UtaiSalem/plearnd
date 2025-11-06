<?php

namespace App\Http\Controllers\Learn\Course\Assignments;

use App\Http\Controllers\Controller;
use App\Models\Learn\Course\info\Course;
use App\Models\Learn\Course\assignments\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Http\Resources\Learn\info\CourseResource;
use App\Http\Resources\Learn\lessons\LessonResource;
use App\Http\Resources\Learn\assignments\AssignmentResource;
use App\Http\Resources\Learn\groups\CourseGroupResource;

class AssignmentController extends Controller
{
    public function index(Course $course)
    {
        return Inertia::render('Learn/Course/Lesson/Lessons',[
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->lessons),
            'assignments'       => AssignmentResource::collection($course->assignments),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'courseMemberOfAuth'=> $course->courseMembers()->where('user_id', auth()->id())->first(),
        ]);
    }

    public function destroy(Assignment $assignment)
    {
        foreach ( $assignment->answers as $answer) {            
            foreach ($answer->images as $image) {
                Storage::disk('public')->delete('images/courses/assignments/answers/'. $image->filename);
            }
            $answer->images()->delete();
        }

        foreach ($assignment->images as $image) {
            Storage::disk('public')->delete('images/courses/assignments/'.$image->image_url);
        }

        $course = $assignment->assignmentable;
        $course->decrement('total_score', $assignment->points);

        $assignment->answers()->delete();
        $assignment->images()->delete();
        $assignment->delete();

        return response()->json(['success' => true], 200);
    }
}
