<?php

namespace App\Models\Learn\Course\Attendances;

use App\Models\Learn\Course\members\CourseMember;
use App\Models\Learn\Course\attendances\CourseAttendance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function attendanceable()
    {
        return $this->morphTo();
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(CourseAttendance::class);
    }

    public function courseMember(): BelongsTo
    {
        return $this->belongsTo(CourseMember::class, 'course_member_id');
    }
    
}
