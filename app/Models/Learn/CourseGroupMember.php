<?php

namespace App\Models\Learn;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseGroupMember extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function course_group(): BelongsTo
    {
        return $this->belongsTo(CourseGroup::class, 'group_id');
    }
}
