<?php

namespace App\Models;

use App\Models\User;
use App\Models\Topic;
use App\Models\Course;
use App\Models\Question;
use App\Models\Assignment;
use App\Models\LessonImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;

    protected $guarded = [];
    // protected $fillable = [
    //     'course_id', 
    //     'creater', 
    //     'title', 
    //     'description', 
    //     'content', 
    //     'order', 
    //     'duration'
    // ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(LessonImage::class);
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
