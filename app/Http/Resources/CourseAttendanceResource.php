<?php

namespace App\Http\Resources;

use App\Models\CourseMember;
use Illuminate\Http\Request;
use App\Models\AttendanceDetail;
use App\Http\Resources\AttendanceDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseAttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get the authenticated user's attendance detail for this attendance
        $authMemberId = null;
        if (auth()->check()) {
            $authMemberId = CourseMember::where('user_id', auth()->id())
                ->where('course_id', $this->course_id)
                ->value('id');
        }
        
        $authAttendanceDetail = null;
        if ($authMemberId) {
            $authAttendanceDetail = AttendanceDetail::where('course_attendance_id', $this->id)
                ->where('course_member_id', $authMemberId)
                ->first();
        }
        
        return [
            'id'                    => $this->id,
            'course_id'             => $this->course_id,
            'group_id'              => $this->group_id,
            'instructor_id'         => $this->instructor_id,
            'date'                  => $this->date,
            'start_at'              => $this->start_at,
            'finish_at'             => $this->finish_at,
   'late_time'             => $this->late_time,
            'description'           => $this->description,
            'details'               => AttendanceDetailResource::collection($this->attendanceDetails),
            'authAttendanceStatus'  => $authAttendanceDetail,
        ];
    }
}
