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
            'id'                    => $this->id,
            'creater'               => new UserResource($this->user),
            'course_id'             => $this->course_id,
            'course'                => new CourseResource($this->course),
            'title'                 => $this->title,
            'description'           => $this->description,
            'content'               => $this->content,
            'youtube_url'           => $this->youtube_url,
            'duration'              => $this->duration,
            'min_read'              => $this->min_read,
            'view_count'            => $this->view_count,
            'like_count'            => $this->like_count,
            'comment_count'         => $this->comment_count,
            'share_count'           => $this->share_count,
            'download_count'        => $this->download_count,
            'status'                => $this->status,
            'assigned_groups'       => $this->assigned_groups,
            'point_tuition_fee'     => $this->point_tuition_fee,
            'order'                 => $this->order,
            'images'                => $this->images,
            'created_at'            => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_for_humans' => $this->created_at->diffForHumans(),
            // 'assignments'   => AssignmentResource::collection($this->assignments),
        ];
    }
}
