<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id', 'user_id',
        'requester_name', 'requester_type', 'requester_identifier',
        'faculty', 'department', 'phone', 'advisor_name',
        'start_at', 'end_at', 'attendees', 'purpose', 'requirements',
        'status', 'staff_status', 'rejection_reason',
        'reviewed_by', 'reviewed_at',
        'cancelled_at', 'cancelled_by', 'updated_by', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function statusBadge(): array
    {
        if ($this->cancelled_at) {
            return ['label' => 'ยกเลิกแล้ว', 'class' => 'bg-gray-100 text-gray-800'];
        }
        if ($this->status === 'rejected') {
            return ['label' => 'ไม่อนุมัติ', 'class' => 'bg-red-100 text-red-800'];
        }
        if ($this->status === 'pending') {
            return ['label' => 'รอพิจารณา', 'class' => 'bg-yellow-100 text-yellow-800'];
        }
        return match ($this->staff_status) {
            'ready' => ['label' => 'พร้อมใช้งาน', 'class' => 'bg-green-100 text-green-800'],
            'in_use' => ['label' => 'กำลังใช้งาน', 'class' => 'bg-blue-100 text-blue-800'],
            'cleanup' => ['label' => 'กำลังทำความสะอาด', 'class' => 'bg-gray-100 text-gray-800'],
            'issue' => ['label' => 'มีปัญหา', 'class' => 'bg-red-100 text-red-800'],
            default => ['label' => 'อนุมัติแล้ว', 'class' => 'bg-indigo-100 text-indigo-800'],
        };
    }

    public static function hasConflict(int $roomId, string $startAt, string $endAt, ?int $ignoreId = null): bool
    {
        $start = \Illuminate\Support\Carbon::parse($startAt);
        $end = \Illuminate\Support\Carbon::parse($endAt);

        return static::query()
            ->where('room_id', $roomId)
            ->whereNull('cancelled_at')
            ->where('status', '!=', 'rejected')
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }
}
