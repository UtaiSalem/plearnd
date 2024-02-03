<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    =>  $this->id,
            'course_id'             =>  $this->course_id,
            'user'                  =>  new UserResource($this->user),
            'group'                 =>  $this->group,
            'access_expiry_date'    =>  $this->access_expiry_date,
            'completion_date'       =>  $this->completion_date,
            'enrollment_date'       =>  $this->enrollment_date,
            'grade_progress'        =>  $this->grade_progress,
            'achieved_score'        =>  $this->achieved_score,
            'notes_comments'        =>  $this->notes_comments,
            'order_number'          =>  $this->order_number,
            'role'                  =>  $this->role,
            'status'                =>  $this->course_member_status,
            'course_member_status'  =>  $this->course_member_status,
            'group_member_status'   =>  $this->group_member_status,
            'updated_at'            =>  $this->updated_at,
            'created_at'            =>  $this->created_at,
        ];
    }
}
