<?php

namespace App\Models\Play;

use App\Models\Play\Post;
use App\Models\Play\PostComment;
use App\Models\Play\PostCommentImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PostComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'likes',
        'dislikes',
        'replies',
        'parent_comment_id',
        'sentiment',
        'privacy_settings',
    ];

    /**
     * Get the user that owns the PostComment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get the Post that owns the PostComment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the parent comment for the reply.
     */
    public function parentComment(): BelongsTo
    {
        return $this->belongsTo(PostComment::class, 'parent_comment_id');
    }

    /**
     * Get the replies for the comment.
     */
    public function replies()
    {
        return $this->hasMany(PostComment::class, 'parent_comment_id');
    }

    public function postCommentImages(): HasMany
    {
        return $this->hasMany(PostCommentImage::class);
    }


    /**
     * The likedPostComment that belong to the Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likedPostComment(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'liked_post_comments', 'post_comment_id', 'user_id');
    }

    /**
     * The dislikedComment that belong to the Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dislikedPostComment(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'disliked_post_comments', 'post_comment_id', 'user_id');
    }
}
