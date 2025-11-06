<?php

namespace App\Models\Learn\Course\lessons\comments;

use App\Models\Learn\Course\lessons\comments\LessonComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonCommentImage extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $appends = ['url'];

    public function lessonComment(): BelongsTo
    {
        return $this->belongsTo(LessonComment::class);
    }
}