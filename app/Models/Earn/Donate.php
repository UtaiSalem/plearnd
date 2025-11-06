<?php

namespace App\Models\Earn;

use App\Models\Shared\User;
use App\Models\Play\Activity;
use Illuminate\Support\Carbon;
use App\Models\Earn\DonateRecipient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Donate extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id',
        'doner_name',
        'amounts',
        'slip',
        'transfer_date',
        'transfer_time',
        'donation_date',
        'doner_email',
        'donation_purpose',
        'payment_method',
        'transaction_id',
        'doner_address',
        'status',
        'approved_by',
        'privacy_settings',
        'notes'
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'donation_date' => 'datetime',
        'hashtags' => 'array',
        'tags' => 'array',
        'meta' => 'array',
    ];

    protected $appends = [
        'total_points',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Shared\User::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Shared\User::class, 'donor_id');
    }

    public function activity(): MorphOne
    {
        return $this->morphOne(\App\Models\Play\Activity::class, 'activityable');
    }

    // public function getStatusAttribute($value)
    // {
    //     return $value == 0 ? 'pending' : 'received';
    // }

    public function getSlipAttribute($value)
    {
        return $value ? '/storage/images/donates/'.$value : null;
    }

    public function getTransferDateAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    // user who recieve the donation, belons to many users
    public function recipients()
    {
        return $this->belongsToMany(\App\Models\Shared\User::class, 'donate_recipients', 'donate_id', 'user_id')->withTimestamps();
    }

    public function donateRecipients(): HasMany
    {
        return $this->hasMany(\App\Models\Earn\DonateRecipient::class, 'donate_id');
    }

    public function getTotalPointsAttribute()
    {
        return $this->amounts * 1080;
    }

}
