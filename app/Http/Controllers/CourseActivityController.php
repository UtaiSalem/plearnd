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
        return Inertia::render('Learn/Course/Course', [
            'course' => new CourseResource($course),
            'activeTab' => 'feeds',
        ]);
    }

    public function getActivities(Course $course)
    {
        $activities = Activity::whereHasMorph('activityable', [CoursePost::class], function ($query) use ($course) {
                $query->where('course_id', $course->id);
        })->latest()->paginate();

        return response()->json([
            'success' => true,
            'data' => ActivityResource::collection($activities),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'total' => $activities->total(),
            ]
        ]);
    }
}
