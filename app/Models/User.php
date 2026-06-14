<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'username', 'name', 'email', 'password', 'role',
        'status', 'user_type', 'student_id', 'employee_id',
        'faculty', 'department', 'phone', 'default_advisor',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function managedRooms(): HasMany
    {
        return $this->hasMany(Room::class, 'manager_user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'admin'], true);
    }

    public function canReview(Booking $booking): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return $this->id !== null && (int) $booking->room->manager_user_id === (int) $this->id;
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'admin' => 'ผู้ดูแลระบบ',
            'staff' => 'เจ้าหน้าที่ห้องปฏิบัติการ',
            default => $this->user_type === 'student' ? 'นักศึกษา' : 'บุคลากร',
        };
    }

    public function identifier(): ?string
    {
        return $this->user_type === 'student' ? $this->student_id : $this->employee_id;
    }

    public function homeRoute(): string
    {
        return match ($this->role) {
            'staff' => 'staff.queue',
            'admin' => 'admin.dashboard',
            default => 'dashboard',
        };
    }
}
