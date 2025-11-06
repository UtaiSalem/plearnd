<?php

namespace App\Models\Play;
use App\Models\Shared\User;
use App\Models\Play\Activity;
use App\Models\Play\PostImage;
use App\Http\Resources\Shared\UserResource;
// use Illuminate\Database\Eloquent\Concerns\HasUlids;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;
    use Notifiable;
    // use HasUlids;

    // protected $fillable = [
    //     'user_id',
    //     'content',
    //     'status',
    //     'public',
    //     'meta',
    // ];

    protected $fillable = [
        'user_id',
        'content',
        'status',
        'public',
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

    protected $appends = ['post_url'];

    protected $casts = [
        'tags' => 'array',
        'meta' => 'array',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Shared\User::class);
        // return new UserResource($this->user);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Shared\User::class);
    }

    // public function activities(): MorphMany
    // {
    //     return $this->morphMany(Activity::class, 'activityable');
    // }
    // morphOne relationship
    public function activity(): MorphOne
    {
        return $this->morphOne(\App\Models\Play\Activity::class, 'activityable');
    }

    public function likedPost(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Shared\User::class, 'liked_posts', 'post_id', 'user_id');
    }

    /**
     * The disliked that belong to the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dislikedPost(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Shared\User::class, 'disliked_posts', 'post_id', 'user_id');
    }

    public function postImages(): HasMany
    {
        return $this->hasMany(\App\Models\Play\PostImage::class);
    }

    public function getPostUrlAttribute(): string
    {   
        return route('posts.show', $this->id);
    }

    /**
     * Get all of the comments for the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postComments(): HasMany
    {
        return $this->hasMany(\App\Models\Play\PostComment::class);
    }


    public function getComments()
    {
        // return $this->postComments()->latest()->paginate(3);
        return $this->postComments()->latest()->limit(3)->get();
    }


    public function likedByAuth(): bool
    {
        $like = $this->likedPost()->contains('user_id', auth()->user()->id)->first();

        return $like ? true : false;
    }

}
