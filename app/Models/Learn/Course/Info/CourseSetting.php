<?php

namespace App\Models\Learn\Course\info;

use App\Models\Learn\Course\info\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseSetting extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $guarded = [];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'id');
    }

}
