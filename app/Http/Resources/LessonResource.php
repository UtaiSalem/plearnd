<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\AssignmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'creater'       => new UserResource($this->user),
            'course'        => new CourseResource($this->course),
            'title'         => $this->title,
            'description'   => $this->description,
            'content'       => $this->content,
            'duration'      => $this->duration,
            'status'        => $this->status,
            'order'         => $this->order,
            'images'        => $this->images,
            // 'assignments'   => AssignmentResource::collection($this->assignments),
        ];
    }
}
