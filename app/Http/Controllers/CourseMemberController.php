<?php

namespace App\Http\Controllers;

use App\Models\Course;

class CourseMemberController extends Controller
{
    public function store(Course $course)
    {
        $course->members()->toggle(auth()->id());
        $isMember = $course->isMember(auth()->user());
        $isMember ? $course->increment('enrolled_students'): $course->decrement('enrolled_students');

        return response()->json([
            'success' => true,
            'isMember' => $isMember,
            'enrolledStudents' => $course->enrolled_students,
        ], 200);
    }
}
