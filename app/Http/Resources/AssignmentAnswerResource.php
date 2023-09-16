<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        return [
            'id' => $this->id,
            // 'assignment'        => new AssignmentResource($this->assignment),
            'student'           => new UserResource($this->user),
            // 'user'              => new UserResource($this->user),
            'submission_date'   => $this->submission_date,
            'content'           => $this->content,
            'status'            => $this->status,
            'points'            => $this->points,
            'late_submission'   => $this->late_submission,
            'images'            => $this->images,
            'created_at'        => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d/m/Y H:i:s'),
            'updated_at'        => $this->updated_at,
        ];
    }
}
