<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\CourseGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\CourseAttendance;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\CourseGroupResource;
use App\Http\Resources\CourseAttendanceResource;

class CourseAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course, Request $request)
    {
        $courseMemberOfAuth = $course->courseMembers()->where('user_id', auth()->id())->first();

        return Inertia::render('Learn/Course/Attendance/Attendances', [
            'course'        => new CourseResource($course),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'courseMemberOfAuth'  => $courseMemberOfAuth,
            'isCourseAdmin' => $course->user_id === auth()->id(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Course $course, CourseGroup $group, Request $request)
    {
        $request->validate([
            'start_at' => 'required|date',
            'finish_at' => 'required|date',
            'late_time' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $attendance = $course->courseAttendances()->create([
            'instructor_id' => auth()->id(),
            'group_id'      => $group->id,
            'date'          => Carbon::parse($request->start_at)->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s'),
            'start_at'      => Carbon::parse($request->start_at)->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s'),
            'finish_at'     => Carbon::parse($request->finish_at)->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s'),
            'late_time'     => $request->late_time,
            'description'   => $request->description,
        ]);

        return response()->json([
            'success'       => true,
            'attendance'    => new CourseAttendanceResource($attendance),
        ], 200);
    }

    // Update Attendance
    public function update(CourseAttendance $attendance, Request $request)
    {
        $request->validate([
            'start_at' => 'required|date',
            'finish_at' => 'required|date',
            'late_time' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $attendance->update([
            'date'          => Carbon::parse($request->start_at)->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s'),
            'start_at'      => Carbon::parse($request->start_at)->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s'),
            'finish_at'     => Carbon::parse($request->finish_at)->setTimezone('Asia/Bangkok')->format('Y-m-d H:i:s'),
            'late_time'    => $request->late_time,
            'description'   => $request->description,
        ]);

        return response()->json([
            'success'       => true,
            'attendance'    => new CourseAttendanceResource($attendance),
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseAttendance $attendance)
    {
        //delete attendance details
        $attendance->details()->delete();
        //delete attendance
        $attendance->delete();

        return response()->json([
            'success' => true,
        ], 200);
    }

    // Get Course Attendance by Course and CourseGroup
    public function getCourseGroupAttendances(Course $course, CourseGroup $group, Request $request)
    {
        return response()->json([
            'success' => true,
            'attendances'   => CourseAttendanceResource::collection($course->courseAttendances->where('group_id', $group->id))
        ], 200);
    }

}
