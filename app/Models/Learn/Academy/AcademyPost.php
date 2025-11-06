<?php

namespace App\Models\Learn\Academy;

use App\Models\Shared\User;
use App\Models\Learn\Academy;
use App\Models\Play\Activity;
use App\Models\Learn\Academy\AcademyPostImage;
use App\Models\Learn\Academy\AcademyPostComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AcademyPost extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'academy_id',
        'content',
        'status',
        'likes',
        'dislikes',
        'comments',
        'shares',
        'views',
        'hashtags',
        'privacy_settings',
        'location',
        'tags',
        'sentiment',
        'engagement_rate',
        'post_type',
        'source_platform',
        'interacted_at',
        'meta',
    ];

    protected $appends = ['academy_post_url'];

    protected $casts = [
        'hashtags' => 'array',
        'tags' => 'array',
        'meta' => 'array',
    ];

    public function academy()
    {
        return $this->belongsTo(Academy::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Shared\User::class);
    }

    public function activity(): MorphOne
    {
        return $this->morphOne(\App\Models\Play\Activity::class, 'activityable');
    }

    public function likedPost(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Shared\User::class, 'academy_post_likes', 'academy_post_id', 'user_id');
    }

    /**
     * The disliked that belong to the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dislikedPost(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Shared\User::class, 'academy_post_dislikes', 'academy_post_id', 'user_id');
    }

    public function images()
    {
        return $this->hasMany(AcademyPostImage::class);
    }

    public function getAcademyPostUrlAttribute()
    {
        return route('academy_post.show', ['academy' => $this->academy, 'post' => $this]);
    }

    /**
     * Get all of the comments for the AcademyPost
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(AcademyPostComment::class);
    }

    public function getComments()
    {
        // return $this->postComments()->latest()->paginate(3);
        return $this->comments()->latest()->limit(3)->get();
    }
}
