<?php

namespace App\Models\Shared;

use Carbon\Carbon;
use App\Models\Shared\User;
use App\Models\Learn\Course\info\Course;
use App\Models\Learn\Lesson;
use App\Models\Learn\Academy;
use App\Models\Shared\Support;
use App\Models\Play\Activity;
use App\Models\Learn\CourseQuiz;
use App\Models\Play\Friendship;
use App\Models\Learn\LessonLike;
use App\Models\Learn\CourseGroup;
use App\Models\Play\PostComment;
use App\Models\Shared\UserProfile;
use Illuminate\Support\Str;
use App\Models\Shared\PlearndAdmin;
use App\Models\Learn\AcademyMember;
use App\Models\Learn\LessonComment;
use App\Models\Learn\LessonDislike;

use App\Models\Learn\AssignmentAnswer;
use App\Models\Learn\CourseQuizResult;
use App\Models\Learn\CourseGroupMember;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserAnswerQuestion;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
// use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use \Multicaret\Acquaintances\Traits\Friendable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'personal_code',
        'reference_code',
        'suggester_code',
        'no_of_ref',
        'pp',
        'wallet',
        'email_verified_at',
        'phone_verified_at',
        'verified',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'referal_link',
        'is_plearnd_admin',
    ];

    public function profile()
    {
        return $this->hasOne(\App\Models\Shared\UserProfile::class);
    }

    //Generate Reference Code
    public static function generateReferenceCode()
    {
        do {
            $reference_code = Str::random(10);
        } while (\App\Models\Shared\User::where('reference_code', $reference_code)->exists());

        return $reference_code;
    }

    //Generate Personal Code
    public static function generatePersonalCode()
    {
        $personal_code = mt_rand(10000000, 99999900);
        if (\App\Models\Shared\User::where('personal_code', $personal_code)->exists()) {
            return this->generatePersonalCode();
        }
        return $personal_code;
    }


    // //get suggester
    // public function suggester(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'suggester_code', 'reference_code');
    // }

    //get referal link
    public function getReferalLinkAttribute()
    {
        // return env('APP_URL') . '/register?ref=' . $this->reference_code;
        return env('APP_URL') . '/register/' . $this->reference_code;
    }

    public function academies(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\Academy::class);
    }

    public function academyMembers(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Learn\AcademyMember::class, 'academy_members', 'user_id', 'academy_id')
                    ->withPivot(
                        'status',
                    )->withTimestamps();
    }

    public function academyAdmins(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Learn\Academy::class, 'academy_admins', 'user_id', 'academy_id');
    }

    public function academyPosts(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\AcademyPost::class);
    }

    public function courseMembers(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Learn\Course\info\Course::class, 'course_members', 'user_id', 'course_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(\App\Models\Play\Activity::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(\App\Models\Play\Post::class, 'user_id');
    }

    public function postComments(): HasMany
    {
        return $this->hasMany(\App\Models\Play\PostComment::class);
    }

    // public function likedPost(): BelongsToMany
    // {
    //     return $this->belongsToMany(Post::class, 'liked_posts', 'user_id', 'post_id');
    // }

    // public function dislikePost(): BelongsToMany
    // {
    //     return $this->belongsToMany(Post::class, 'disliked_posts', 'user_id', 'post_id');
    // }

    // public function likedPostComment(): BelongsToMany
    // {
    //     return $this->belongsToMany(PostComment::class, 'liked_post_comments', 'user_id', 'comment_id');
    // }

    // public function dislikedPostComment(): BelongsToMany
    // {
    //     return $this->belongsToMany(PostComment::class, 'disliked_post_comments', 'user_id', 'comment_id');
    // }

    public function courses(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\Course\info\Course::class);
    }

    public function coursesGroups(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\CourseGroup::class);
    }

    public function coursesGroupMembers(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\CourseGroupMember::class);
    }

    public function courseQuizzes(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\CourseQuiz::class);
    }

    public function courseQuizResults(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\CourseQuizResult::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\Lesson::class, 'user_id');
    }

    public function assignmentAnswers(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\AssignmentAnswer::class);
    }

    public function answerQuestions(): HasMany
    {
        return $this->hasMany(\App\Models\Shared\UserAnswerQuestion::class);
    }

    public function supports(): HasMany
    {
        return $this->hasMany(\App\Models\Shared\Support::class);
    }

    public function supportViewers(): HasMany
    {
        return $this->hasMany(\App\Models\Shared\SupportViewer::class);
    }

    public function isPlearndAdmin(): bool
    {
        return \App\Models\Shared\PlearndAdmin::where('user_id', $this->id)->exists() && $this->hasVerifiedEmail();
    }

    public function getIsPlearndAdminAttribute()
    {
        return $this->isPlearndAdmin();
    }

    public function getCreatedAtAttribute($value)
    {
        return date('dd-mm-Y H:i:s', strtotime($value));
    }

    public function memberAcademies(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\AcademyMember::class, 'user_id');
    }

    public function donateRecipients(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Shared\User::class, 'donate_recipients', 'user_id', 'donate_id');
    }

    public function donateReciever(): HasMany
    {
        return $this->hasMany(\App\Models\Earn\Donate::class, 'user_id');
    }

    public function isFriendWithAuth($userId): bool
    {
        return $this->isFriendWith(\App\Models\Shared\User::find($userId));
    }

    public function friend_senders(): MorphToMany
    {
        return $this->morphToMany(\App\Models\Play\Friendship::class, 'senderable');
    }

    public function friend_recipients(): MorphToMany
    {
        return $this->morphToMany(\App\Models\Play\Friendship::class, 'recipientable');
    }

    public function friendships_status($friendId)
    {
        if(Auth::check()){
            $authUser = auth()->user();
            $friend = \App\Models\Shared\User::find($friendId);
            if ($friend) { 
                if ($authUser->isFriendWith($friend)) {
                    return 1; 
                    //accepted
                } elseif ($authUser->hasSentFriendRequestTo($friend)) {
                    return 0; 
                    //pending
                } else {
                    return null; 
                    //not friend
                }
            }else {
                return null;
            }
        }else {
            return null;
        }

    }
    
    // has many likedLessons
    public function likeLessons(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\LessonLike::class);
    }

    public function dislikeLessons(): HasMany
    {
        return $this->hasMany(\App\Models\Learn\LessonDislike::class);
    }

    public function lessonComments()
{
    return $this->hasMany(\App\Models\Learn\LessonComment::class);
}

}
