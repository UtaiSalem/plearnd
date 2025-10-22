<?php

namespace App\Models\Learn;

use App\Models\User;
use App\Models\Learn\AcademyPost;
use App\Models\Learn\AcademyPostImageComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademyPostImage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['full_url'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(AcademyPost::class);
    }

    /**
     * Get all of the comments for the AcademyPostImage
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function image_comments(): HasMany
    {
        return $this->hasMany(AcademyPostImageComment::class, 'academy_post_image_id');
    }

    public function getFullUrlAttribute(): string
    {
        return asset('storage/images/academies/posts/' . $this->filename);
    }

    /**
     * The postImageLikes that belong to the AcademyPostImage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function postImageLikes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'academy_post_image_likes', 'post_image_id', 'user_id');
    }

    /**
     * The postImageDislikes that belong to the AcademyPostImage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function postImageDislikes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'academy_post_image_dislikes', 'post_image_id', 'user_id');
    }
}
