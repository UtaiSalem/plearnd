<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseAssignmentApiController extends Controller
{
    /**
     * Get upcoming assignments for a course
     */
    public function upcoming(Course $course)
    {
        // Get assignments that are not past due or have no due date
        $upcomingAssignments = $course->courseAssignments()
            ->where(function ($query) {
                $query->whereNull('due_date')
                    ->orWhere('due_date', '>=', now());
            })
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get(['id', 'title', 'due_date', 'points']);

        return response()->json([
            'success' => true,
            'data' => $upcomingAssignments
        ]);
    }
}
