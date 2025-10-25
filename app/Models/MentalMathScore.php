<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentalMathScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'player_name',
        'score',
        'difficulty',
        'combo',
        'questions_answered',
        'accuracy',
        'time_spent',
        'is_practice_mode',
        'is_daily_challenge',
        'date_played',
    ];

    protected $casts = [
        'score' => 'integer',
        'combo' => 'integer',
        'questions_answered' => 'integer',
        'accuracy' => 'float',
        'time_spent' => 'integer',
        'is_practice_mode' => 'boolean',
        'is_daily_challenge' => 'boolean',
        'date_played' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeTopScores($query, $limit = 10)
    {
        return $query->orderBy('score', 'desc')->limit($limit);
    }

    public function scopeDailyChallenge($query)
    {
        return $query->where('is_daily_challenge', true);
    }

    public function scopeNotPracticeMode($query)
    {
        return $query->where('is_practice_mode', false);
    }
}
