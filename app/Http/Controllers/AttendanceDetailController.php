<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AttendanceDetail;
use App\Models\CourseAttendance;
use App\Http\Resources\AttendanceDetailResource;
use App\Http\Resources\CourseAttendanceResource;

class AttendanceDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CourseAttendance $attendance, Request $request)
    {
        return response()->json([
            'success' => true,
            'attendance_resource' => new CourseAttendanceResource($attendance),
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseAttendance $attendance, Request $request)
    {
        // compute time in is late or not by compare with attendance finish time+20 minutes
        $isLate = now() > Carbon::parse($attendance->start_at)->addMinutes($attendance->late_time);

        $detail = $attendance->details()->create([
            'course_attendance_id'  => $attendance->id,
            'course_id'             => $attendance->course_id,
            'group_id'              => $attendance->group_id,
            'course_member_id'      => $request->course_member_id,
            'time_in'               => now(),    
            'status'                => $isLate ? 2 : 1,
            'comments'              => $request->comments,
        ]);

        return response()->json([
            'success' => true,
            'attendance_detail' => new AttendanceDetailResource($detail),
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(AttendanceDetail $detail, Request $request)
    {
        $detail->update([
            'status'    => $request->status,
            'comments'  => $request->comments,
        ]);
    }

}
