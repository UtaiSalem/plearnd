<?php

namespace App\Http\Controllers;

use DateTime;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\CourseGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\CourseAttendance;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\CourseGroupResource;
use App\Http\Resources\CourseMemberResource;
use App\Http\Resources\AttendanceDetailResource;
use App\Http\Resources\CourseAttendanceResource;
use App\Http\Requests\StoreCourseAttendanceRequest;
use App\Http\Requests\UpdateCourseAttendanceRequest;
use App\Http\Resources\CourseGroupAttendanceResource;

class CourseAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        return Inertia::render('Learn/Course/Attendance/Attendances', [
            'course'        => new CourseResource($course),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'courseMemberOfAuth'=> $course->courseMembers()->where('user_id', auth()->id())->first(),
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
    public function store(Course $course, CourseGroup $group, Request $request)
    {

        $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $attendance = $course->courseAttendances()->create([
            'instructor_id'  => auth()->id(),
            'group_id'      => $group->id,
            'date'          => Carbon::parse($request->due_date)->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s'),
            'description'   => $request->description,
        ]);

        return response()->json([
            'success'       => true,
            'attendance'    => $attendance,
            'attendace_details'  => new CourseAttendanceResource($attendance),
            // 'attendace_member_details'  => AttendanceDetailResource::collection($attendance_details),
        ], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show(CourseAttendance $courseAttendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseAttendance $courseAttendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseAttendanceRequest $request, CourseAttendance $courseAttendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseAttendance $courseAttendance)
    {
        //
    }

    // Get Course Attendance by Course and CourseGroup
    public function getCourseGroupAttendances(Course $course, CourseGroup $group)
    {
        // $courseAttendances = CourseAttendance::where('course_id', $course->id)
        //     ->where('group_id', $group->id)
        //     ->get();
        // $courseAttendances = $group->attendances;

        return response()->json([
            'success' => true,
            'attendances' => $group->attendances,
            // 'attendances' => CourseGroupAttendanceResource::collection($group->attendances),
        ], 200);
    }

}
