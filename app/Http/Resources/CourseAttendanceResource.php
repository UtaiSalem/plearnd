<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
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
        return [
            'id'                    => $this->id,
            'course_id'             => $this->course_id,
            'group_id'              => $this->group_id,
            'instructor_id'         => $this->instructor_id,
            'date'                  => $this->date,
            'description'           => $this->description,
            'details'               => AttendanceDetailResource::collection($this->attendanceDetails),
        ];
    }
}
