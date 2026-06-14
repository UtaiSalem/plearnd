<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'building', 'floor', 'category', 'capacity',
        'manager_user_id', 'manager_name', 'contact',
        'status', 'open_hours', 'summary', 'equipment',
        'is_bookable', 'sort_order', 'color',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function statusBadge(): array
    {
        return match ($this->status) {
            'available' => ['label' => 'เปิดให้บริการ', 'class' => 'bg-green-100 text-green-800'],
            'limited' => ['label' => 'จำกัดการใช้งาน', 'class' => 'bg-yellow-100 text-yellow-800'],
            default => ['label' => 'ปิดปรับปรุง', 'class' => 'bg-red-100 text-red-800'],
        };
    }
}
