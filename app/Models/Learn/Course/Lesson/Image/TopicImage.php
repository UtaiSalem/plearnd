<?php

namespace App\Models\Learn\Course\lessons\images;

use App\Models\Learn\Course\lessons\topics\Topic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'filename',
    ];

    protected $appends = ['image_url'];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/images/courses/lessons/topics/' . $this->filename);
    }
}