<?php

namespace App\Http\Controllers\Learn;

use App\Http\Controllers\Controller;
use App\Models\Course;

class CourseSettingController extends Controller
{
    public function setAutoAcceptMembers()
    {
        try {
            $courses = Course::all();

            foreach ($courses as $course) {
                $course->courseSettings()->create([
                    'auto_accept_members' => 1
                ]);
            }

            return response()->json([
                'success' => true
            ], 200);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}







