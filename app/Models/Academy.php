<?php

namespace App\Models;

use App\Models\User;
use App\Models\Course;
use App\Models\AcademyAdmin;
use App\Models\AcademyMember;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Academy extends Model
{
    use HasFactory;

    // protected $fillable = [];
    protected $guarded = [];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function academyAdmins(): HasMany
    {
        return $this->hasMany(AcademyAdmin::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'academy_members', 'academy_id', 'user_id');
    }

    public function isMember(User $user)
    {
        return $this->members->contains($user);
    }
}
