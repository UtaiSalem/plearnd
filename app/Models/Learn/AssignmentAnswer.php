<?php

namespace App\Models\Learn;

use App\Models\User;
use App\Models\Learn\Assignment;
use App\Models\Learn\AnswerImage;
use App\Models\Learn\AssignmentAnswerImage;
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
