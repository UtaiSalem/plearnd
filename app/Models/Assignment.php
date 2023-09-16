<?php

namespace App\Models;

use App\Models\AssignmentImage;
use App\Models\AssignmentAnswer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function assignmentable()
    {
        return $this->morphTo();
    }

    public function images(): HasMany
    {
        return $this->hasMany(AssignmentImage::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssignmentAnswer::class);
    }
}
