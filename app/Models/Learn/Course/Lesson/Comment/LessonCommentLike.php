<?php

namespace App\Models\Learn\Course\lessons\comments;

use App\Models\Shared\User;
use App\Models\Learn\Course\lessons\comments\LessonComment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCommentLike extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'lesson_comment_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lessonComment(): BelongsTo
    {
        return $this->belongsTo(LessonComment::class);
    }
}