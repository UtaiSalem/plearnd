<?php

namespace App\Http\Controllers\Play;

use App\Models\Play\Post;
use App\Models\Shared\User;
use Inertia\Inertia;
// use Illuminate\Http\Request;
use App\Models\Earn\Donate;
use App\Models\Play\Friend;
use App\Models\Earn\Support;
use App\Models\Play\Activity;
use App\Models\Learn\AcademyPost;
use App\Models\Play\Poll;
use App\Http\Resources\Shared\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Earn\DonateResource;
use App\Http\Resources\Shared\SupportResource;
use App\Http\Resources\Play\ActivityResource;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\Play\FriendshipResource;

class NewsfeedController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $activities = $this->getActivities();

        $authFriends = auth()->user()->getFriends()->pluck('id')->toArray();

        $peopleMayKnow = User::where('id', '!=', Auth::id())
            ->whereNotIn('id', $authFriends)
            ->inRandomOrder()
            ->limit(15)
            ->get();

        // dd($authFriends);
        $pendingFriends = auth()->user()->getFriendRequests();

        return Inertia::render('Newsfeed', [
            'activities'        => $activities->original['activities'],
            'peopleMayKnow'     => UserResource::collection($peopleMayKnow),
            'pendingFriends'    => FriendshipResource::collection($pendingFriends),
            'donates'           => DonateResource::collection(Donate::whereNotIn('status',[2])->orderBy('remaining_points', 'DESC')->latest()->paginate(5)),
            'advertises'        => SupportResource::collection(Support::where('status',1)->where('remaining_views', '>', 0)->orderBy('remaining_views', 'DESC')->latest()->paginate(5)),
        ]);
    }

    public function show(User $user){

        $activities = ActivityResource::collection($user->activities()->with(['user', 'activityable'])->latest()->paginate());

        return Inertia::render('Myfeed', [
            'user'       => $user,
            'activities' => $activities,
        ], 200);
        
    }

    public function getActivities()
    {
        
        $authUser = Auth::user();
        
        $authFriends = auth()->user()->getFriends()->pluck('id')->toArray();

        $targetUsersIds = array_unique(array_merge($authFriends, [$authUser->id, 7]));
                
        $activities = Activity::whereIn('user_id', $targetUsersIds)
            ->with(['user', 'activityable'])
            ->whereHasMorph('activityable',
                [
                    'App\Models\Play\Post',
                    'App\Models\Learn\Academy\AcademyPost',
                    'App\Models\Learn\Course\Post\CoursePost',
                    'App\Models\Earn\Donate',
                    'App\Models\Earn\Support',
                    'App\Models\Earn\DonateRecipient',
                    'App\Models\Play\Poll',
                    // 'App\Models\Earn\SupportViewer'
                ],
                function ($query, $type) use ($targetUsersIds) {
                    // Filter by user_id for all types
                    $query->whereIn('user_id', $targetUsersIds);
                    
                    // Apply additional filters based on model type
                    if (in_array($type, [
                        'App\Models\Play\Post',
                        'App\Models\Learn\Academy\AcademyPost',
                        'App\Models\Learn\Course\Post\CoursePost'
                    ])) {
                        $query->whereIn('privacy_settings', [2,3])->where('status', 1);
                    } elseif ($type === 'App\Models\Earn\Donate') {
                        $query->where('status', 1);
                    } elseif ($type === 'App\Models\Earn\Support') {
                        $query->where('status', 1);
                    } elseif ($type === 'App\Models\Play\Poll') {
                        $query->where('is_active', 1)->where('is_public', 1);
                    }
                    // DonateRecipient doesn't have privacy_settings or status fields
                })
            ->latest()
            ->paginate();

        return response()->json([
            'success' => true,
            'activities' => ActivityResource::collection($activities),
        ]);
    }
}
