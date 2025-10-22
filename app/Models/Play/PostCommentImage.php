<?php

namespace App\Models\Play;

use App\Models\Play\PostComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostCommentImage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['url'];

    public function postComment()
    {
        return $this->belongsTo(PostComment::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/images/posts/comments/' . $this->filename);
    }
    
}
