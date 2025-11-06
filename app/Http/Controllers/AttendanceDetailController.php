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
        // Validate the request
        $request->validate([
            'course_member_id' => 'required|exists:course_members,id',
            'status' => 'nullable|integer|in:1,2,3',
            'comments' => 'nullable|string',
        ]);

        // compute time in is late or not by compare with attendance start time + late_time
        $isLate = now() > Carbon::parse($attendance->start_at)->addMinutes($attendance->late_time ?? 0);

        $detail = $attendance->details()->create([
            'course_attendance_id'  => $attendance->id,
            'course_id'             => $attendance->course_id,
            'group_id'              => $attendance->group_id,
            'course_member_id'      => $request->course_member_id,
            'time_in'               => now(),
            'status'                => $request->status ?? ($isLate ? 2 : 1),
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


    /**
     * Get member join status for a specific attendance
     */
    public function getMemberJoinStatus(CourseAttendance $attendance, Request $request, $member)
    {
        $detail = AttendanceDetail::where('course_attendance_id', $attendance->id)
                    ->where('course_member_id', $member)
                    ->first();

        // compute status to return 
        $status = null;
        
        if ($detail) {
            // ถ้ามีข้อมูลการเข้าเรียน ให้คำนวณสถานะ
            $timeIn = Carbon::parse($detail->time_in);
            $startAt = Carbon::parse($attendance->start_at);
            $lateThreshold = $startAt->copy()->addMinutes($attendance->late_time);
            
            // ถ้า time_in <= start_at + late_time แสดงว่ามาทันเวลา (status = 1)
            // ถ้า time_in > start_at + late_time แสดงว่ามาสาย (status = 2)
            $status = $timeIn->lessThanOrEqualTo($lateThreshold) ? 1 : 2;
        }
        // ถ้าไม่มีข้อมูล return null (ยังไม่ได้เข้าเรียน)

        return response()->json([
            'success' => true,
            'status' => $status,
        ], 200);
    
    }
}
