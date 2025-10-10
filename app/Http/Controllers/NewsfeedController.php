<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
// use Illuminate\Http\Request;
use App\Models\Donate;
use App\Models\Friend;
use App\Models\Support;
use App\Models\Activity;
use App\Models\AcademyPost;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DonateResource;
use App\Http\Resources\SupportResource;
use App\Http\Resources\ActivityResource;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\FriendshipResource;

class NewsfeedController extends Controller
{
    public function index()
    {
        $activities = $this->getActivities();

        // $peopleMayKnow = User::where('id', '!=', Auth::id())
        //     ->whereDoesntHave('friendships', function (Builder $query) {
        //         $query->where('status', 'accepted');
        //     })
        //     ->inRandomOrder()
        //     ->limit(5)
        //     ->get();

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

        $activities = ActivityResource::collection($user->activities()->latest()->paginate());

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
            ->whereHasMorph('activityable', 
                [
                    'App\Models\Post', 
                    'App\Models\AcademyPost', 
                    'App\Models\CoursePost',
                    'App\Models\Donate',
                    'App\Models\Support',
                    'App\Models\DonateRecipient'
                ], 
                function ($query) use ($targetUsersIds) {
                    $query->whereIn('user_id', $targetUsersIds)
                        ->whereIn('privacy_settings', [2,3])
                        ->whereStatus(1);
                })
            ->latest()
            ->paginate();

        return response()->json([
            'success' => true,
            'activities' => ActivityResource::collection($activities),
        ]);
    }
}
