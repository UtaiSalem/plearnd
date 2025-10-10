<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Friend;
use App\Models\Activity;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ActivityResource;
use App\Http\Requests\StoreUserProfileRequest;
use App\Http\Requests\UpdateUserProfileRequest;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        // ตรวจสอบว่าผู้ใช้ที่เข้าสู่ระบบเป็นเพื่อนกับผู้ใช้ที่เป็นเจ้าของโปรไฟล์หรือไม่
        $heIsMyFriend = Friend::where('user_id', auth()->id())->where('friend_id', $user->id)
            ->whereStatus(1)->exists();
        
        // ตรวจสอบว่าผู้ใช้ที่เป็นเจ้าของโปรไฟล์ป็นเพื่อนกับผู้ใช้ที่เข้าสู่ระบบเหรือไม่
        $iAmHisFriend = Friend::where('friend_id', auth()->id())->where('user_id', $user->id)
            ->whereStatus(1)->exists();

        $friendWithAuth = $heIsMyFriend || $iAmHisFriend;

        $privacySettings = $friendWithAuth ? [2, 3] : [3];
    

        // ใช้ Eloquent ORM เพื่อดึงโพสต์จากตาราง "กิจกรรม" โดยกำหนดเงื่อนไขตามที่ระบุ
        $activities = Activity::whereHasMorph('activityable', // ชื่อของ relation ในโมเดล Activity
                [Post::class], // ระบุโมเดลที่เป็นไปได้ใน relation
                function ($query) use ($user, $privacySettings) {
                    $query->whereIn('privacy_settings', $privacySettings)
                        ->where(function ($query) use ($user) {
                            $query->where('user_id', $user->id); // โพสต์จากผู้ใช้ที่เป็นเจ้าของโปรไฟล์
                        });
                }
            )
            ->latest()
            ->paginate();

        return Inertia::render('UserProfile', [
            'user'          => $user,
            'activities'    => ActivityResource::collection($activities),
        ]);
    }

    function checkUsernameExists($name) {
        $user = User::where('name', $name)->first();
        if ($user) {
            return response()->json([
                'exists' => true,
                'message' => 'name already exists'
            ]);
        } else {
            return response()->json([
                'exists' => false,
                'message' => 'name is available'
            ]);
        }
    }

    function checkEmailExists($email) {
        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json([
                'exists' => true,
            ]);
        } else {
            return response()->json([
                'exists' => false,
            ]);
        }
    }

}
