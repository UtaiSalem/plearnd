<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSetting;
use App\Http\Requests\StoreCourseSettingRequest;
use App\Http\Requests\UpdateCourseSettingRequest;

class CourseSettingController extends Controller
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
    public function store(StoreCourseSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseSetting $courseSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseSetting $courseSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseSettingRequest $request, CourseSetting $courseSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseSetting $courseSetting)
    {
        //
    }

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
