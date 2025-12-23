<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\CourseMember;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Load user if not already loaded
        if (!$this->relationLoaded('user')) {
            $this->load('user');
        }

        // ดึง course_member เฉพาะเมื่อมี relationship ครบ
        $course_member = null;
        
        if ($this->user && $this->relationLoaded('assignment') && $this->assignment) {
            $course_member = CourseMember::where('user_id', $this->user->id)
                ->where('course_id', $this->assignment->assignmentable->id)
                ->first();
        }

        return [
            'id' => $this->id,
            'assignment_id' => $this->assignment_id,
            
            // ส่ง student เสมอ
            'student' => $this->user ? new UserResource($this->user) : null,
            
            'user' => $this->user?->id,
            
            'member_name' => $course_member?->member_name,
            
            'course_group' => $course_member?->group_id,
            
            'submission_date' => $this->submission_date,
            'content' => $this->content,
            'status' => $this->status,
            'points' => $this->points,
            'late_submission' => $this->late_submission,
            'images' => $this->images,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('d/m/Y H:i:s') : null,
            'updated_at' => $this->updated_at,
        ];
    }
}
