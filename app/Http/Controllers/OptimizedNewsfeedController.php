<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OptimizedNewsfeedController extends Controller
{
    /**
     * Display the newsfeed with optimized loading
     */
    public function index()
    {
        // Use cache for static data that doesn't change frequently
        $cacheKey = 'newsfeed_initial_' . auth()->id();
        $cachedData = Cache::remember($cacheKey, 300, function () {
            return [
                'peopleMayKnow' => $this->getPeopleYouMayKnow(),
                'pendingFriends' => $this->getPendingFriends(),
                'donates' => $this->getActiveDonates(),
                'advertises' => $this->getActiveAdvertises(),
            ];
        });

        // Get initial page of activities (smaller batch size)
        $activities = $this->getActivities(1, 5);

        return Inertia::render('OptimizedNewsfeed', [
            'initialActivities' => $activities->original['activities'],
            'initialPagination' => [
                'current_page' => $activities->original['current_page'],
                'last_page' => $activities->original['last_page'],
                'total' => $activities->original['total'],
            ],
            'peopleMayKnow' => $cachedData['peopleMayKnow'],
            'pendingFriends' => $cachedData['pendingFriends'],
            'donates' => $cachedData['donates'],
            'advertises' => $cachedData['advertises'],
        ]);
    }

    /**
     * Get paginated activities for infinite scroll
     */
    public function getPaginatedActivities(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 5);
        
        $activities = $this->getActivities($page, $perPage);
        
        return response()->json([
            'success' => true,
            'activities' => $activities->original['activities'],
            'pagination' => [
                'current_page' => $activities->original['current_page'],
                'last_page' => $activities->original['last_page'],
                'total' => $activities->original['total'],
                'has_more' => $activities->original['current_page'] < $activities->original['last_page'],
            ]
        ]);
    }

    /**
     * Get activities with optimized queries
     */
    private function getActivities($page = 1, $perPage = 10)
    {
        $authUser = Auth::user();
        
        // Cache user's friends list for better performance
        $cacheKey = 'user_friends_' . $authUser->id;
        $authFriends = Cache::remember($cacheKey, 600, function () use ($authUser) {
            return $authUser->getFriends()->pluck('id')->toArray();
        });

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
            ->with([
                'user' => function($query) {
                    $query->select('id', 'name', 'profile_photo_url');
                },
                'activityable' => function($query) {
                    // Optimize related data loading based on type
                    $query->select('id', 'content', 'user_id', 'created_at');
                }
            ])
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'activities' => ActivityResource::collection($activities),
            'current_page' => $activities->currentPage(),
            'last_page' => $activities->lastPage(),
            'total' => $activities->total(),
        ]);
    }

    /**
     * Get people you may know with caching
     */
    private function getPeopleYouMayKnow()
    {
        $cacheKey = 'people_may_know_' . auth()->id();
        
        return Cache::remember($cacheKey, 1800, function () {
            $authFriends = auth()->user()->getFriends()->pluck('id')->toArray();

            $peopleMayKnow = User::where('id', '!=', Auth::id())
                ->whereNotIn('id', $authFriends)
                ->select('id', 'name', 'profile_photo_url', 'personal_code')
                ->inRandomOrder()
                ->limit(15)
                ->get();

            return UserResource::collection($peopleMayKnow);
        });
    }

    /**
     * Get pending friend requests with caching
     */
    private function getPendingFriends()
    {
        $cacheKey = 'pending_friends_' . auth()->id();
        
        return Cache::remember($cacheKey, 300, function () {
            $pendingFriends = auth()->user()->getFriendRequests();
            return FriendshipResource::collection($pendingFriends);
        });
    }

    /**
     * Get active donations with caching
     */
    private function getActiveDonates()
    {
        $cacheKey = 'active_donates';
        
        return Cache::remember($cacheKey, 600, function () {
            return DonateResource::collection(
                Donate::whereNotIn('status', [2])
                    ->select('id', 'user_id', 'title', 'description', 'target_points', 'remaining_points', 'created_at')
                    ->with(['user' => function($query) {
                        $query->select('id', 'name', 'profile_photo_url');
                    }])
                    ->orderBy('remaining_points', 'DESC')
                    ->latest()
                    ->paginate(5)
            );
        });
    }

    /**
     * Get active advertisements with caching
     */
    private function getActiveAdvertises()
    {
        $cacheKey = 'active_advertises';
        
        return Cache::remember($cacheKey, 600, function () {
            return SupportResource::collection(
                Support::where('status', 1)
                    ->where('remaining_views', '>', 0)
                    ->select('id', 'user_id', 'title', 'description', 'remaining_views', 'created_at')
                    ->with(['user' => function($query) {
                        $query->select('id', 'name', 'profile_photo_url');
                    }])
                    ->orderBy('remaining_views', 'DESC')
                    ->latest()
                    ->paginate(5)
            );
        });
    }

    /**
     * Clear relevant caches when new content is posted
     */
    public function clearNewsfeedCache()
    {
        $userId = auth()->id();
        
        // Clear user-specific caches
        Cache::forget('newsfeed_initial_' . $userId);
        Cache::forget('user_friends_' . $userId);
        Cache::forget('people_may_know_' . $userId);
        Cache::forget('pending_friends_' . $userId);
        
        // Clear global caches
        Cache::forget('active_donates');
        Cache::forget('active_advertises');
        
        return response()->json(['success' => true]);
    }
}