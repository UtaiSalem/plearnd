<?php

namespace App\Http\Resources;

use App\Models\LikedPost;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\PostCommentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            // 'post_by'           => $this->author,
            'author'            => new UserResource($this->user),       
            'title'             => $this->title,
            'content'           => $this->content,
            'status'            => $this->status,
            'public'            => $this->public,
            'meta'              => $this->meta,
            'likes'             => $this->likes,
            'dislikes'          => $this->dislikes,
            'isLikedByAuth'     => $this->when(auth()->check(), function () use ($request) {
                                        return $this->likedPost()->where('user_id', auth()->id())->exists() ;
                                    }),
            'isDislikedByAuth'  => $this->when(auth()->check(), function () use ($request) {
                                        return $this->dislikedPost()->where('user_id', auth()->id())->exists() ;
                                    }),
            // 'isLikedByAuth'     => $this->getAuthLikeState(),
            // 'isLikedByAuth'     => $this->likedPost()->withCount(
            //                             [
            //                                 'likedPost' => function($query)
            //                                             {
            //                                                 $query->where('user_id', auth()->id());   
            //                                             }
            //                             ]),
            'comments'          => $this->comments,
            'comments_count'    => $this->postComments->count(),
            'post_comments'     => PostCommentResource::collection($this->getComments()),
            'shares'            => $this->shares,
            'views'             => $this->views,
            'hashtags'          => $this->hashtags,
            'privacy_settings'  => $this->privacy_settings,
            'location'          => $this->location,
            'url'               => $this->url,
            'tags'              => $this->tags,
            'sentiment'         => $this->sentiment,
            'engagement_rate'   => $this->engagement_rate,
            'post_type'         => $this->post_type,
            'source_platform'   => $this->source_platform,
            'parent_post_id'    => $this->parent_post_id,
            // 'created_at'        => $this->created_at->toDateTimeString(),
            'created_at'        => $this->created_at,
            'diff_humans_created_at'    => $this->created_at->diffForHumans(),
        ];
    }

    // public function getAuthLikeState(): bool
    // {
    //     return $this->likedPost()->where('user_id', auth()->id())->exists();
    // }

}
