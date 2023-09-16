<?php

namespace App\Models;

use App\Models\QuestionImage;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function questionable()
    {
        return $this->morphTo();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(QuestionImage::class, 'imageable');
    }

    public function options(): MorphMany
    {
        return $this->morphMany(QuestionOption::class, 'optionable');
    }


}
