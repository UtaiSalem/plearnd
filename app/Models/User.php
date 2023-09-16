<?php

namespace App\Models;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Academy;
use App\Models\Activity;
use App\Models\PostComment;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserAnswerQuestion;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    // use HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'pp',
        'profile_photo_path'
    ];


    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function academies(): HasMany
    {
        return $this->hasMany(Academy::class);
    }

    public function academyMembers(): BelongsToMany
    {
        return $this->belongsToMany(Academy::class, 'academy_members', 'user_id', 'academy_id');
    }

    public function academyAdmins(): BelongsToMany
    {
        return $this->belongsToMany(Academy::class, 'academy_admins', 'user_id', 'academy_id');
    }

    public function courseMembers(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_members', 'user_id', 'course_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'user_id');
    }


    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function postComments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }


    public function likedPost(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'liked_posts', 'user_id', 'post_id');
    }

    public function dislikePost(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'disliked_posts', 'user_id', 'post_id');
    }

    public function likedPostComment(): BelongsToMany
    {
        return $this->belongsToMany(PostComment::class, 'liked_post_comments', 'user_id', 'comment_id');
    }

    public function dislikedPostComment(): BelongsToMany
    {
        return $this->belongsToMany(PostComment::class, 'disliked_post_comments', 'user_id', 'comment_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function answerQuestions(): HasMany
    {
        return $this->hasMany(UserAnswerQuestion::class);
    }
}
