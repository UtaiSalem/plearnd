<?php

namespace App\Models\Learn\Course\lessons\comments;

use App\Models\Shared\User;
use App\Models\Learn\Course\lessons\Lesson;
use App\Models\Learn\Course\lessons\comments\LessonCommentImage;
use App\Models\Learn\Course\lessons\comments\LessonCommentLike;
use App\Models\Learn\Course\lessons\comments\LessonCommentDislike;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(LessonCommentImage::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(LessonCommentLike::class);
    }

    public function dislikes(): HasMany
    {
        return $this->hasMany(LessonCommentDislike::class);
    }
}