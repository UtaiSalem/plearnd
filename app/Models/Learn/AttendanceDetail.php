<?php

namespace App\Models\Learn;

use App\Models\Learn\CourseMember;
use App\Models\Learn\CourseAttendance;
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
