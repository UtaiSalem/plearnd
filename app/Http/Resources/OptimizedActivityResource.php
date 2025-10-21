<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptimizedActivityResource extends JsonResource
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
            'user_id' => $this->user_id,
            'activity_type' => $this->activity_type,
            'action' => $this->action,
            'action_to' => $this->action_to,
            'action_to_id' => $this->action_to_id,
            'privacy_settings' => $this->privacy_settings,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'diff_humans_created_at' => $this->created_at->diffForHumans(),
            
            // Optimized user data - only essential fields
            'action_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->profile_photo_url,
            ],
            
            // Optimized target resource data based on type
            'target_resource' => $this->getOptimizedTargetResource(),
        ];
    }

    /**
     * Get optimized target resource data based on the activity type
     */
    private function getOptimizedTargetResource()
    {
        $activityable = $this->activityable;
        
        if (!$activityable) {
            return null;
        }

        switch ($this->action_to) {
            case 'Post':
                return [
                    'id' => $activityable->id,
                    'content' => $activityable->content,
                    'privacy_settings' => $activityable->privacy_settings,
                    'hashtags' => $activityable->hashtags,
                    'status' => $activityable->status,
                    'created_at' => $activityable->created_at,
                    'post_url' => route('posts.show', $activityable->id),
                    'author' => [
                        'id' => $activityable->user->id,
                        'name' => $activityable->user->name,
                    ],
                    'images' => $this->getOptimizedImages($activityable, 'Post'),
                    'reactions_count' => $activityable->likes_count + $activityable->dislikes_count,
                    'comments_count' => $activityable->postComments()->count(),
                ];
                
            case 'AcademyPost':
                return [
                    'id' => $activityable->id,
                    'content' => $activityable->content,
                    'privacy_settings' => $activityable->privacy_settings,
                    'status' => $activityable->status,
                    'created_at' => $activityable->created_at,
                    'academy' => [
                        'id' => $activityable->academy->id,
                        'name' => $activityable->academy->name,
                    ],
                    'author' => [
                        'id' => $activityable->user->id,
                        'name' => $activityable->user->name,
                    ],
                    'images' => $this->getOptimizedImages($activityable, 'AcademyPost'),
                    'reactions_count' => $activityable->likes_count + $activityable->dislikes_count,
                    'comments_count' => $activityable->academyPostComments()->count(),
                ];
                
            case 'CoursePost':
                return [
                    'id' => $activityable->id,
                    'content' => $activityable->content,
                    'privacy_settings' => $activityable->privacy_settings,
                    'status' => $activityable->status,
                    'created_at' => $activityable->created_at,
                    'course' => [
                        'id' => $activityable->course->id,
                        'name' => $activityable->course->name,
                    ],
                    'author' => [
                        'id' => $activityable->user->id,
                        'name' => $activityable->user->name,
                    ],
                    'images' => $this->getOptimizedImages($activityable, 'CoursePost'),
                    'reactions_count' => $activityable->likes_count + $activityable->dislikes_count,
                    'comments_count' => $activityable->coursePostComments()->count(),
                ];
                
            case 'Donate':
                return [
                    'id' => $activityable->id,
                    'title' => $activityable->title,
                    'description' => $activityable->description,
                    'target_points' => $activityable->target_points,
                    'remaining_points' => $activityable->remaining_points,
                    'status' => $activityable->status,
                    'created_at' => $activityable->created_at,
                    'author' => [
                        'id' => $activityable->user->id,
                        'name' => $activityable->user->name,
                    ],
                ];
                
            case 'DonateRecipient':
                return [
                    'id' => $activityable->id,
                    'title' => $activityable->title,
                    'description' => $activityable->description,
                    'target_amount' => $activityable->target_amount,
                    'remaining_amount' => $activityable->remaining_amount,
                    'status' => $activityable->status,
                    'created_at' => $activityable->created_at,
                    'author' => [
                        'id' => $activityable->user->id,
                        'name' => $activityable->user->name,
                    ],
                ];
                
            case 'Support':
                return [
                    'id' => $activityable->id,
                    'title' => $activityable->title,
                    'description' => $activityable->description,
                    'remaining_views' => $activityable->remaining_views,
                    'status' => $activityable->status,
                    'created_at' => $activityable->created_at,
                    'author' => [
                        'id' => $activityable->user->id,
                        'name' => $activityable->user->name,
                    ],
                ];
                
            default:
                return [
                    'id' => $activityable->id,
                    'type' => $this->action_to,
                ];
        }
    }

    /**
     * Get optimized images data - only essential fields
     */
    private function getOptimizedImages($activityable, $type)
    {
        $images = [];
        
        switch ($type) {
            case 'Post':
                $images = $activityable->postImages()->select('id', 'filename')->get();
                break;
            case 'AcademyPost':
                $images = $activityable->academyPostImages()->select('id', 'filename')->get();
                break;
            case 'CoursePost':
                $images = $activityable->coursePostImages()->select('id', 'filename')->get();
                break;
        }
        
        return $images->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => '/storage/images/posts/' . $image->filename,
                'thumbnail' => '/storage/images/posts/thumbnails/' . $image->filename,
            ];
        })->toArray();
    }
}