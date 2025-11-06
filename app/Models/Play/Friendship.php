<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Friendship extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'sender_type',
        'sender_id',
        'recipient_type',
        'recipient_id',
    ];
    

    public function sender(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'senderable');
    }

    public function recipient(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'recipientable');
    }

}
