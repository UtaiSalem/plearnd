<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\AssignmentAnswerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
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
            'assignmentable_id'     => $this->assignmentable_id,
            'assignmentable_type'   => $this->assignmentable_type,
            'title'                 => $this->title,
            'description'           => $this->description,
            'images'                => $this->images,
            'answers'               => AssignmentAnswerResource::collection($this->answers),  
            'due_date'              => $this->due_date,
            'start_date'            => $this->start_date,
            'end_date'              => $this->end_date,
            'points'                => $this->points,
            'assignment_type'       => $this->assignment_type,
            'submission_method'     => $this->submission_method,
            'max_file_size'         => $this->max_file_size,
            'is_group_assignment'   => $this->is_group_assignment,
            'assigned_to'           => $this->assigned_to,
            'status'                => $this->status,
            'grading_rubric'        => $this->grading_rubric,
            'graded_score'          => $this->graded_score,
            'feedback'              => $this->feedback,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
