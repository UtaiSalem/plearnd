<?php

namespace App\Models\Learn;

use App\Models\Learn\Course;
use App\Models\Learn\CourseAttendance;
use App\Models\Learn\CourseGroupMember;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseGroup extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(CourseMember::class, 'group_id');
    }

    public function course_group_members(): HasMany
    {
        return $this->hasMany(CourseGroupMember::class, 'group_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(CourseAttendance::class, 'group_id');
    }
}
