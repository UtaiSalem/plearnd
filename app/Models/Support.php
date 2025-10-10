<?php

namespace App\Models;

use App\Models\User;
use App\Models\SupportViewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Support extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supporter');
    }

    public function activity(): MorphOne
    {
        return $this->morphOne(Activity::class, 'activityable');
    }

    public function getMediaImageAttribute($value)
    {
        return $value ? asset('/storage/images/supports/medias/' . $value) : null;
    }

    public function getSlipAttribute($value)
    {
        return $value ? '/storage/images/supports/slips/'.$value : null;
    }

    public function viewers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'support_viewers', 'support_id', 'user_id')->withTimestamps();
    }

    public function supportViewers(): HasMany
    {
        return $this->hasMany(SupportViewer::class, 'support_id');
    }
}
