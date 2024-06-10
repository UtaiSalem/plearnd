<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\CourseMemberResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'course_attendance_id'  => $this->course_attendance_id,
            'status'                => $this->status,
            'comments'              => $this->comments,
            'course_member'         => new CourseMemberResource($this->courseMember),
        ];
    }
}
