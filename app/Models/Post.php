<?php

namespace App\Models;
use App\Models\User;
use App\Models\Activity;
use App\Models\PostImage;
use App\Http\Resources\UserResource;
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

    protected $guarded = [];

    protected $appends = ['post_url'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
        // return new UserResource($this->user);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // public function activities(): MorphMany
    // {
    //     return $this->morphMany(Activity::class, 'activityable');
    // }
    // morphOne relationship
    public function activity(): MorphOne
    {
        return $this->morphOne(Activity::class, 'activityable');
    }

    public function likedPost(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'liked_posts', 'post_id', 'user_id');
    }

    /**
     * The disliked that belong to the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dislikedPost(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'disliked_posts', 'post_id', 'user_id');
    }

    public function postImages(): HasMany
    {
        return $this->hasMany(PostImage::class);
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
        return $this->hasMany(PostComment::class);
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
