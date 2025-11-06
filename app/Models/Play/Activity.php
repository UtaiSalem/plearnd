<?php

namespace App\Models\Play;

// use Illuminate\Database\Eloquent\Concerns\HasUlids;
use App\Models\Shared\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;
    // use HasUlids;

    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_details',
        'activityable_id',
        'activityable_type',
        'action'
    ];


    /**
     * Get the user that owns the Activity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activityable(): MorphTo
    {
        return $this->morphTo();
    }

}
