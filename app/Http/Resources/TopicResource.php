<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\AcademyResource;
use App\Http\Resources\AssignmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'author'            => new UserResource($this->user), 
            // 'academy'           => new AcademyResource($this->academy)?? null,
            // 'course'            => new CourseResource($this->course),
            'course'            => $this->course_id,
            // 'lesson'            => new LessonResource($this->lesson),
            'lesson_id'         => $this->lesson_id,
            'title'             => $this->title,
            'content'           => $this->content,
            'min_read'          => $this->min_read,
            'images'            => $this->images,
            'youtube_url'       => $this->youtube_url,
            'status'            => $this->status,
            'likes'             => $this->likes,
            'dislikes'          => $this->dislikes,
            'comments'          => $this->comments,
            'shares'            => $this->shares,
            'views'             => $this->views,
            'hashtags'          => $this->hashtags,
            'privacy_settings'  => $this->privacy_settings,
            'location'          => $this->location,
            'url'               => $this->url,
            'tags'              => $this->tags,
            'sentiment'         => $this->sentiment,
            'engagement_rate'   => $this->engagement_rate,
            'source_platform'   => $this->source_platform,
            'interacted_at'     => $this->interacted_at,
            'meta'              => $this->meta,

            // 'assignments'       => AssignmentResource::collection($this->assignments),
            // 'assignments'       => $this->assigments,
            
            'created_at'        => $this->created_at,
            'diff_humans_created_at'    => $this->created_at->diffForHumans(),

            // 'comments_count'    => $this->postComments->count(),
            // 'post_comments'     => PostCommentResource::collection($this->getComments()),
            // 'post_type'         => $this->post_type,
            // 'parent_post_id'    => $this->parent_post_id,

        ];
    }
}
