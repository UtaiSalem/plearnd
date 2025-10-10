<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoursePostImageResource extends JsonResource
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
            'post_id'           => $this->post_id,
            'filename'          => $this->filename,
            'full_url'          => $this->full_url,
            'likes'             => $this->likes,
            'dislikes'          => $this->dislikes,
            'comments'          => $this->comments,
            'shares'            => $this->shares,
            'views'             => $this->views,
            'image_comments'    => CoursePostImageCommentResource::collection($this->image_comments()->latest()->limit(3)->get()),
            'isLikedByAuth'     => $this->postImageLikes()->where('user_id', auth()->id())->exists(),
            'isDislikedByAuth'  => $this->postImageDislikes()->where('user_id', auth()->id())->exists(),
        ];
    }
}
