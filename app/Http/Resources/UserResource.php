<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
// use Multicaret\Acquaintances\Traits\Friendable;
class UserResource extends JsonResource
{
    // use Friendable;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'email'                     => $this->email,
            'points'                    => $this->pp,
            'wallet'                    => $this->wallet,
            'avatar'                    => $this->profile_photo_url,
            'personal_code'             => $this->personal_code,
            'referal_link'              => $this->referal_link,
            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at,
            'no_of_ref'                 => $this->no_of_ref,
            'is_plearnd_admin'          => $this->is_plearnd_admin,
            // 'is_friend_with'            => $this->isFriendWith($this->id),
            'is_friend'                 => $this->isFriendWithAuth($this->id),
            'friendships_status'        => $this->friendships_status($this->id),
            'profile_picture'           => $this->profile()->profile_picture ?? $this->profile_photo_url,
            'cover_image'               => $this->profile()->cover_image ?? '/storage/images/banner/banner-profile-stats.jpg',
        ];
    }

}
