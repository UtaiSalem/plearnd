<?php

namespace App\Models;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Academy;
use App\Models\Question;
use App\Models\Assignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];
    // protected $fillable = ['title', 'description', 'instructor_id', 'duration', 'price', 'category', 'level', 'language', 'start_date', 'end_date', 'enrolled_students', 'rating', 'image', 'syllabus', 'prerequisites', 'certificate'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academy::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_members', 'course_id', 'user_id');
    }

    public function isMember(User $user)
    {
        return $this->members->contains($user);
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
