<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceDetail;
use App\Models\CourseAttendance;
use App\Http\Resources\AttendanceDetailResource;
use App\Http\Resources\CourseAttendanceResource;
use App\Http\Requests\StoreAttendanceDetailRequest;
use App\Http\Requests\UpdateAttendanceDetailRequest;

class AttendanceDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CourseAttendance $attendance)
    {
        return response()->json([
            'success' => true,
            // 'attendace_details' => AttendanceDetailResource::collection($attendance->details),
            'attendance_resource' => new CourseAttendanceResource($attendance),
        ], 200);
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
    public function store(CourseAttendance $attendance, Request $request)
    {
        $detail = $attendance->details()->updateOrCreate(
            [
                'course_id'             => $attendance->course_id,
                'group_id'              => $attendance->group_id,
                'course_attendance_id'  => $attendance->id,
                'course_member_id'      => $request->course_member_id,
            ],
            [
                'status'                => $request->status,
                'comments'              => $request->comments,
            ]
        );

        return response()->json([
            'success' => true,
            'detail' => $detail,
            'attendace_detail' => new AttendanceDetailResource($detail),
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceDetail $attendanceDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceDetail $attendanceDetail)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttendanceDetail $attendanceDetail)
    {
        //
    }
}
