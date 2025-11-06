<?php

namespace App\Models\Earn;

use App\Models\Shared\User;
use App\Models\SupportViewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Support extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supporter',
        'title',
        'description',
        'media_type',
        'media_image',
        'media_video',
        'url',
        'target_views',
        'remaining_views',
        'status',
        'privacy_settings',
        'start_date',
        'end_date',
        'slip',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Shared\User::class);
    }

    public function advertiser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Shared\User::class, 'supporter');
    }

    public function activity(): MorphOne
    {
        return $this->morphOne(\App\Models\Play\Activity::class, 'activityable');
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
        return $this->belongsToMany(\App\Models\Shared\User::class, 'support_viewers', 'support_id', 'user_id')->withTimestamps();
    }

    public function supportViewers(): HasMany
    {
        return $this->hasMany(SupportViewer::class, 'support_id');
    }
}
