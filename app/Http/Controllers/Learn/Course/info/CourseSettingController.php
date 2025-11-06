<?php

namespace App\Http\Controllers\Learn\Course\info;

use App\Models\Learn\Course\info\Course;

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
