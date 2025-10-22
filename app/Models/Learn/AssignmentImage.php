<?php

namespace App\Models\Learn;

use App\Models\Learn\Assignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignmentImage extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['full_url'];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function getFullUrlAttribute(): string
    {
        return asset('storage/images/courses/assignments/' . $this->image_url);
    }
}
