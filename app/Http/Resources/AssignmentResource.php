<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\AssignmentAnswerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    protected $targetUserId = null;

    /**
     * Set target user ID to filter answers
     */
    public function setTargetUserId($userId)
    {
        $this->targetUserId = $userId;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $courseUserId = $this->assignmentable->user_id;
        
        // ถ้ามี targetUserId ให้ใช้ตัวนั้น ไม่งั้นใช้ auth()->id()
        $userId = $this->targetUserId ?? auth()->id();

        if ($courseUserId === auth()->id()) {
            // ถ้าเป็นเจ้าของคอร์ส ใช้ answers ที่โหลดมาทั้งหมด (ถูก filter แล้วจาก eager load)
            $answers = $this->whenLoaded('answers', function () {
                return $this->answers;
            }, collect());
        } else {
            // ถ้าไม่ใช่เจ้าของคอร์ส แสดงเฉพาะของตัวเอง
            $answers = $this->whenLoaded('answers', function () {
                return $this->answers->where('user_id', auth()->id());
            }, collect());
        }

        return [
            'id'                    => $this->id,
            'assignmentable'        => $courseUserId,
            'assignmentable_id'     => $this->assignmentable_id,
            'assignmentable_type'   => $this->assignmentable_type,
            'title'                 => $this->title,
            'description'           => $this->description,
            'images'                => $this->whenLoaded('images', function () {
                return $this->images;
            }, []),
            'answers'               => AssignmentAnswerResource::collection($answers),
            'due_date'              => Carbon::parse($this->due_date)->toDateTimeString(),
            'start_date'            => Carbon::parse($this->start_date)->toDateTimeString(),
            'end_date'              => Carbon::parse($this->end_date)->toDateTimeString(),
            'points'                => $this->points,
            'increase_points'       => $this->increase_points,
            'decrease_points'       => $this->decrease_points,
            'assignment_type'       => $this->assignment_type,
            'submission_method'     => $this->submission_method,
            'max_file_size'         => $this->max_file_size,
            'is_group_assignment'   => $this->is_group_assignment,
            // 'target_groups'         => collect(explode(",", $this->target_groups))->map(function($item){
            //                                 return (int) $item;
            //                             }),
            // 'target_groups'         => explode(",", $this->target_groups),
            // 'target_groups'         => $this->target_groups,
            'target_groups'         => $this->target_groups ? array_map('intval', $this->target_groups): [],
            'is_published'          => $this->is_published,
            'status'                => $this->status,
            'grading_rubric'        => $this->grading_rubric,
            'graded_score'          => $this->graded_score,
            'feedback'              => $this->feedback,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
