<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\Activity;
use App\Models\CoursePost;
use App\Http\Resources\CourseResource;
use App\Http\Resources\AcademyResource;
use App\Http\Resources\ActivityResource;

class CourseActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Course $course)
    {
        try {

            $isCourseAdmin = $course->user_id == auth()->id();
            $cma = $course->courseMembers()->where('user_id', auth()->id())->first();
            $coursesResource = new CourseResource($course);
    
            $activities = Activity::whereHasMorph('activityable', [CoursePost::class], function ($query) use ($course) {
                    $query->where('course_id', $course->id);
            })->latest()->paginate();
    
            return Inertia::render('Learn/Course/CourseFeeds', [
                'success'               => true,
                'academy'               => $course->academy ? new AcademyResource($course->academy) : null,
                'course'                => $coursesResource,
                'courseGroups'          => $course->courseGroups,
                'isCourseAdmin'         => $isCourseAdmin,
                'courseMemberOfAuth'    => $cma,
                'activities'            => ActivityResource::collection($activities),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load course activity'. $e->getMessage());
        }
    }

    public function getActivities(Course $course)
    {
        $activities = Activity::whereHasMorph('activityable', [CoursePost::class], function ($query) use ($course) {
                $query->where('course_id', $course->id);
        })->latest()->paginate();

        return response()->json([
            'success' => true,
            'activities' => ActivityResource::collection($activities),
        ]);
    }
}
