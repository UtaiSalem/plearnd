<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\PollResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\DonateResource;
use App\Http\Resources\SupportResource;
use App\Http\Resources\AcademyPostResource;
use App\Http\Resources\DonateRecipientResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {       
        // 'resource' => new PostResource($this->activityable),
        return [
            'id'                            => $this->id,
            'action'                        => $this->activity_type,
            'action_by'                     => new UserResource($this->user),
            'action_to'                     => Str::of($this->activityable_type)->substr(11),
            'action_to_id'                  => $this->activityable_id,
            'target_resource'               => $this->relateResource(class_basename($this->activityable_type), $this->activityable),
            'description'                   => $this->description,
            'diff_humans_created_at'        => $this->created_at->diffForHumans(),
            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
            // 'class_name'                    => class_basename($this->activityable_type),
        ];
    }

    public function relateResource($type, $model)
    {
        if($type === 'Post'){
            return new PostResource($model);
        } elseif ($type === 'AcademyPost') {
            return new AcademyPostResource($model);
        } elseif ($type === 'CoursePost') {
            return new CoursePostResource($model);
        }
        elseif ($type === 'Donate') {
            return new DonateResource($model);
        }  
        elseif ($type === 'Support') {
            return new SupportResource($model);
        }  
        elseif ($type === 'DonateRecipient') {
            return new DonateRecipientResource($model);
        }  
        elseif ($type === 'Poll') {
            return new PollResource($model);
        };
    }
}
