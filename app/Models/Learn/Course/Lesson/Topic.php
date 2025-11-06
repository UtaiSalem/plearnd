<?php

namespace App\Models\Learn\Course\lessons;

use App\Models\Shared\User;
use App\Models\Learn\Course\info\Course;
use App\Models\Learn\Course\lessons\Lesson;
use App\Models\Learn\Academy\Academy;
use App\Models\Learn\Course\questions\Question;
use App\Models\Learn\Course\assignments\Assignment;
use App\Models\Learn\Course\lessons\TopicImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    use HasFactory;
    
    protected $guarded = [];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academy::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(TopicImage::class);
    }

    public function assignments(): MorphMany
    {
        return $this->morphMany(Assignment::class, 'assignmentable');
    }

    public function questions(): MorphMany
    {
        return $this->morphMany(Question::class, 'questionable');
    }

}
