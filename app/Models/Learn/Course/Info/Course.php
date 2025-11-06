<?php

namespace App\Models\Learn\Course\info;

use App\Models\User;
use App\Models\Learn\Course\lessons\Lesson;
use App\Models\Learn\Academy\Academy;
use App\Models\Learn\Course\questions\Question;
use App\Models\Learn\Course\assignments\Assignment;
use App\Models\Learn\Course\quizzes\CourseQuiz;
use App\Models\Learn\Course\info\CourseSetting;
use App\Models\Learn\Course\attendances\CourseAttendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
    protected $casts = [
        'price' => 'integer',
        'credit_units' => 'integer',
        'duration' => 'integer',
        'tuition_fees' => 'integer',
        'hours_per_week' => 'integer',
        'capacity' => 'integer',
        'enrolled_students' => 'integer',
        'rating' => 'integer',
        'enrolled_students' => 'integer',
    ];

    public function courseSettings(): HasOne
    {
        return $this->hasOne(\App\Models\Learn\CourseSetting::class, 'course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academy::class);
    }

    public function course_lessons(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\Lesson::class);
    }

    /**
     * Get all of the courseLessons for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseLessons(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\Lesson::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\Topic::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'course_members', 'course_id', 'user_id');
    }

    public function courseMembers(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\CourseMember::class);
    }

    public function isMember(\App\Models\User $user)
    {
        return $this->members->contains($user);
    }

    public function member_status($id)
    {
        // return \App\Models\Learn\Course\Members\CourseMember::where('user_id', auth()->id())->where('course_id', $id)->pluck('member_status')->first();
        return \App\Models\Learn\Course\Members\CourseMember::where('user_id', auth()->id())->where('course_id', $id)->pluck('course_member_status')->first();
    }

    public function assignments(): MorphMany
    {
        return $this->morphMany(\App\Models\Learn\Assignment::class, 'assignmentable');
    }
    public function courseAssignments(): MorphMany
    {
        return $this->morphMany(\App\Models\Learn\Assignment::class, 'assignmentable');
    }

    public function questions(): MorphMany
    {
        return $this->morphMany(\App\Models\Learn\Question::class, 'questionable');
    }

    public function courseGroups(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\CourseGroup::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get all of the courseQuizzez for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseQuizzes(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\CourseQuiz::class);
    }
    
    /**
     * Get all of the courseQuizResult for the CourseQuiz
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseQuizResults(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\CourseQuizResult::class);
    }

    public function courseAttendances(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\CourseAttendance::class);
    }
}
