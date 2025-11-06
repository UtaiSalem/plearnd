<?php

namespace App\Http\Resources\Play;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCommentResource extends JsonResource
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
            // 'comment_id' => $this->id,
            'post_id'           => $this->post_id,
            'user'              => new UserResource($this->user),
            'content'           => $this->content,
            'images'            => $this->postCommentImages,
            'timestamp'         => $this->created_at->toDateTimeString(),
            'likes'             => $this->likes,
            'dislikes'          => $this->dislikes,
            'replies'           => $this->replies,
            // 'isLikedByAuth'     => $this->when(auth()->check(), function () {
            //                             return $this->likedPostComment()->where('user_id', auth()->id())->exists() ;
            //                         }),
            // 'isDislikedByAuth'  => $this->when(auth()->check(), function () {
            //                             return $this->dislikedComment()->where('user_id', auth()->id())->exists() ;
            //                         }),
            'isLikedByAuth'     => $this->likedPostComment()->where('user_id', auth()->id())->exists(),
            'isDislikedByAuth'  => $this->dislikedPostComment()->where('user_id', auth()->id())->exists(),
            
            'parent_comment_id' => $this->parent_comment_id,
            'sentiment'         => $this->sentiment,
            'privacy_settings' => $this->privacy_settings,
            // 'create_at' => $this->created_at->toDateTimeString(),
            'create_at'         => $this->created_at->diffForHumans(),
        ];
    }
}
