<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    // use HasUlids;

    protected $fillable = [ 
        'user_id', 
        'activity_type', 
        'activity_detail', 
        'activityable_id', 
        'activityable_type'
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

    /**
     * Get the parent activityable model (user or post).
     */
    public function activityable()
    {
        return $this->morphTo();
    }
    
}
