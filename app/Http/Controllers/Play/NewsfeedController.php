<?php

namespace App\Http\Controllers\Play;

use App\Http\Controllers\Controller;
use App\Models\Play\Post;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Earn\Donate;
use App\Models\Play\Friend;
use App\Models\Earn\Support;
use App\Models\Play\Activity;
use App\Models\Learn\AcademyPost;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DonateResource;
use App\Http\Resources\SupportResource;
use App\Http\Resources\ActivityResource;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\FriendshipResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NewsfeedController extends Controller
{
    /**
     * Display the newsfeed with optimized loading
     */
    public function index()
    {
        try {
            \Log::info('NewsfeedController::index - Starting to load newsfeed');
            
            // Use cache for static data that doesn't change frequently
            $cacheKey = 'newsfeed_initial_' . auth()->id();
            \Log::info('NewsfeedController::index - Cache key: ' . $cacheKey);
            
            $cachedData = Cache::remember($cacheKey, 300, function () {
                \Log::info('NewsfeedController::index - Cache miss, fetching fresh data');
                
                $peopleMayKnow = $this->getPeopleYouMayKnow();
                \Log::info('NewsfeedController::index - People you may know count: ' . (is_array($peopleMayKnow) ? count($peopleMayKnow) : 'not an array'));
                
                $pendingFriends = $this->getPendingFriends();
                \Log::info('NewsfeedController::index - Pending friends count: ' . (is_array($pendingFriends) ? count($pendingFriends) : 'not an array'));
                
                $donates = $this->getActiveDonates();
                \Log::info('NewsfeedController::index - Donates count: ' . (is_array($donates) ? count($donates) : 'not an array'));
                
                $advertises = $this->getActiveAdvertises();
                \Log::info('NewsfeedController::index - Advertises count: ' . (is_array($advertises) ? count($advertises) : 'not an array'));
                
                return [
                    'peopleMayKnow' => $peopleMayKnow,
                    'pendingFriends' => $pendingFriends,
                    'donates' => $donates,
                    'advertises' => $advertises,
                ];
            });

            // Get initial page of activities (smaller batch size)
            \Log::info('NewsfeedController::index - Fetching activities');
            $activities = $this->getActivities(1, 5);
            \Log::info('NewsfeedController::index - Activities count: ' . (isset($activities['activities']) ? count($activities['activities']) : 'no activities key'));

            // Ensure all data has proper structure before passing to Inertia
            \Log::info('NewsfeedController::index - Preparing to render Inertia view');
            return Inertia::render('Newsfeed', [
                'initialActivities' => $activities['activities'] ?? [],
                'initialPagination' => [
                    'current_page' => $activities['current_page'] ?? 1,
                    'last_page' => $activities['last_page'] ?? 1,
                    'total' => $activities['total'] ?? 0,
                ],
                'peopleMayKnow' => $cachedData['peopleMayKnow'] ?? [],
                'pendingFriends' => $cachedData['pendingFriends'] ?? [],
                'donates' => $cachedData['donates'] ?? [],
                'advertises' => $cachedData['advertises'] ?? [],
            ]);
        } catch (\Exception $e) {
            // Log the error and return a safe default structure
            \Log::error('NewsfeedController::index error: ' . $e->getMessage());
            \Log::error('NewsfeedController::index error trace: ' . $e->getTraceAsString());
            
            return Inertia::render('Newsfeed', [
                'initialActivities' => [],
                'initialPagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                ],
                'peopleMayKnow' => [],
                'pendingFriends' => [],
                'donates' => [],
                'advertises' => [],
            ]);
        }
    }

    /**
     * Get paginated activities for infinite scroll
     */
    public function getPaginatedActivities(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 5);
            
            $activities = $this->getActivities($page, $perPage);
            
            return response()->json([
                'success' => true,
                'activities' => $activities['activities'] ?? [],
                'pagination' => [
                    'current_page' => $activities['current_page'] ?? 1,
                    'last_page' => $activities['last_page'] ?? 1,
                    'total' => $activities['total'] ?? 0,
                    'has_more' => ($activities['current_page'] ?? 1) < ($activities['last_page'] ?? 1),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('NewsfeedController::getPaginatedActivities error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load activities',
                'activities' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                    'has_more' => false,
                ]
            ], 500);
        }
    }

    /**
     * Get activities with optimized queries
     */
    private function getActivities($page = 1, $perPage = 10)
    {
        try {
            \Log::info('getActivities - Fetching activities page ' . $page . ' with per_page ' . $perPage);
            
            // Get activities with proper relationships
            $query = Activity::with(['user', 'activityable'])
                ->with(['activityable.donation', 'activityable.reciever']) // Load nested relationships for DonateRecipient
                ->latest();
                
            // Execute the paginated query
            $activities = $query->paginate($perPage, ['*'], 'page', $page);
            \Log::info('getActivities - Activities query count: ' . $activities->count());
            
            // Return the paginated result directly without wrapping in another response
            // Convert the resource collection to an array
            $result = [
                'success' => true,
                'activities' => ActivityResource::collection($activities)->resolve(),
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'total' => $activities->total(),
            ];
            
            \Log::info('getActivities - Activities result count: ' . count($result['activities']));
            
            return $result;
        } catch (\Exception $e) {
            \Log::error('getActivities error: ' . $e->getMessage());
            \Log::error('getActivities error trace: ' . $e->getTraceAsString());
            return [
                'success' => false,
                'activities' => [],
                'current_page' => 1,
                'last_page' => 1,
                'total' => 0,
            ];
        }
    }

    /**
     * Get people you may know with caching
     */
    private function getPeopleYouMayKnow()
    {
        try {
            $cacheKey = 'people_may_know_' . auth()->id();
            \Log::info('getPeopleYouMayKnow - Cache key: ' . $cacheKey);
            
            return Cache::remember($cacheKey, 1800, function () {
                \Log::info('getPeopleYouMayKnow - Cache miss, fetching fresh data');
                
                $authFriends = auth()->user()->getFriends()->pluck('id')->toArray();
                \Log::info('getPeopleYouMayKnow - Auth friends count: ' . count($authFriends));

                $peopleMayKnow = User::where('id', '!=', Auth::id())
                    ->whereNotIn('id', $authFriends)
                    ->select('id', 'name', 'personal_code')
                    ->inRandomOrder()
                    ->limit(15)
                    ->get();

                \Log::info('getPeopleYouMayKnow - People you may know query count: ' . $peopleMayKnow->count());
                
                $result = UserResource::collection($peopleMayKnow)->resolve();
                \Log::info('getPeopleYouMayKnow - Result type: ' . gettype($result));
                
                return $result;
            });
        } catch (\Exception $e) {
            \Log::error('getPeopleYouMayKnow error: ' . $e->getMessage());
            \Log::error('getPeopleYouMayKnow error trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Get pending friend requests with caching
     */
    private function getPendingFriends()
    {
        try {
            $cacheKey = 'pending_friends_' . auth()->id();
            \Log::info('getPendingFriends - Cache key: ' . $cacheKey);
            
            return Cache::remember($cacheKey, 300, function () {
                \Log::info('getPendingFriends - Cache miss, fetching fresh data');
                
                $pendingFriends = auth()->user()->getFriendRequests();
                \Log::info('getPendingFriends - Pending friends count: ' . $pendingFriends->count());
                
                $result = FriendshipResource::collection($pendingFriends)->resolve();
                \Log::info('getPendingFriends - Result type: ' . gettype($result));
                
                return $result;
            });
        } catch (\Exception $e) {
            \Log::error('getPendingFriends error: ' . $e->getMessage());
            \Log::error('getPendingFriends error trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Get active donations with caching
     */
    private function getActiveDonates()
    {
        try {
            $cacheKey = 'active_donates';
            \Log::info('getActiveDonates - Cache key: ' . $cacheKey);
            
            return Cache::remember($cacheKey, 600, function () {
                \Log::info('getActiveDonates - Cache miss, fetching fresh data');
                
                $donates = Donate::whereNotIn('status', [2])
                    ->select('id', 'user_id', 'amounts', 'status', 'created_at')
                    ->with(['user' => function($query) {
                        $query->select('id', 'name');
                    }])
                    ->orderBy('amounts', 'DESC')
                    ->latest()
                    ->paginate(5);
                
                \Log::info('getActiveDonates - Donates query count: ' . $donates->count());
                    
                $result = DonateResource::collection($donates)->resolve();
                \Log::info('getActiveDonates - Result type: ' . gettype($result));
                
                return $result;
            });
        } catch (\Exception $e) {
            \Log::error('getActiveDonates error: ' . $e->getMessage());
            \Log::error('getActiveDonates error trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Get active advertisements with caching
     */
    private function getActiveAdvertises()
    {
        try {
            $cacheKey = 'active_advertises';
            \Log::info('getActiveAdvertises - Cache key: ' . $cacheKey);
            
            return Cache::remember($cacheKey, 600, function () {
                \Log::info('getActiveAdvertises - Cache miss, fetching fresh data');
                
                $advertises = Support::where('status', 1)
                    ->select('id', 'user_id', 'supporter', 'status', 'media_image', 'total_views', 'remaining_views', 'duration', 'created_at')
                    ->with([
                        'user' => function($query) {
                            $query->select('id', 'name');
                        },
                        'advertiser' => function($query) {
                            $query->select('id', 'name', 'personal_code', 'no_of_ref');
                        }
                    ])
                    ->latest()
                    ->paginate(5);
                
                \Log::info('getActiveAdvertises - Advertises query count: ' . $advertises->count());
                    
                $result = SupportResource::collection($advertises)->resolve();
                \Log::info('getActiveAdvertises - Result type: ' . gettype($result));
                
                return $result;
            });
        } catch (\Exception $e) {
            \Log::error('getActiveAdvertises error: ' . $e->getMessage());
            \Log::error('getActiveAdvertises error trace: ' . $e->getTraceAsString());
            return [];
        }
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
    
    // Clear activity page caches for this user (clear first 5 pages)
    for ($page = 1; $page <= 5; $page++) {
        Cache::forget("activities_page_{$page}_5_user_{$userId}");
        Cache::forget("activities_page_{$page}_10_user_{$userId}");
    }
    
    // Clear global caches
    Cache::forget('active_donates');
    Cache::forget('active_advertises');
    
    return response()->json(['success' => true]);
}

/**
 * Clear all activity caches for the authenticated user
 */
public function clearActivityCache()
{
    $userId = auth()->id();
    
    // Clear all possible activity page caches for this user
    for ($page = 1; $page <= 20; $page++) {
        foreach ([5, 10, 15, 20] as $perPage) {
            Cache::forget("activities_page_{$page}_{$perPage}_user_{$userId}");
        }
    }
    
    return response()->json(['success' => true]);
}
}




