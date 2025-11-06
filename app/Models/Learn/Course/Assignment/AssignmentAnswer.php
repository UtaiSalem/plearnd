<?php

namespace App\Models\Learn\Course\Assignments;

use App\Models\Shared\User;
use App\Models\Learn\Course\assignments\Assignment;
use App\Models\Learn\Course\assignments\AnswerImage;
use App\Models\Learn\Course\assignments\AssignmentAnswerImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignmentAnswer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    // public function images(): MorphMany
    // {
    //     return $this->morphMany(AnswerImage::class, 'imageable');
    // }

    public function images(): HasMany
    {
        return $this->hasMany(AssignmentAnswerImage::class);
    }
}
