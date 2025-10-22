<?php

namespace App\Http\Controllers\Play;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Play\NewsfeedController;
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

class NewsfeedV2Controller extends Controller
{
    /**
     * Display the new newsfeed with enhanced features
     */
    public function index()
    {
        try {
            \Log::info('NewsfeedV2Controller::index - Starting to load newsfeed V2');
            
            // Use the existing NewsfeedController to get the same data
            $newsfeedController = new NewsfeedController();
            
            // Get the same data as the original newsfeed
            $cacheKey = 'newsfeed_v2_initial_' . auth()->id();
            \Log::info('NewsfeedV2Controller::index - Cache key: ' . $cacheKey);
            
            $cachedData = Cache::remember($cacheKey, 300, function () use ($newsfeedController) {
                \Log::info('NewsfeedV2Controller::index - Cache miss, fetching fresh data');
                
                // Use reflection to access private methods
                $reflection = new \ReflectionClass($newsfeedController);
                
                $peopleMayKnowMethod = $reflection->getMethod('getPeopleYouMayKnow');
                $peopleMayKnowMethod->setAccessible(true);
                $peopleMayKnow = $peopleMayKnowMethod->invoke($newsfeedController);
                \Log::info('NewsfeedV2Controller::index - People you may know count: ' . (is_array($peopleMayKnow) ? count($peopleMayKnow) : 'not an array'));
                
                $pendingFriendsMethod = $reflection->getMethod('getPendingFriends');
                $pendingFriendsMethod->setAccessible(true);
                $pendingFriends = $pendingFriendsMethod->invoke($newsfeedController);
                \Log::info('NewsfeedV2Controller::index - Pending friends count: ' . (is_array($pendingFriends) ? count($pendingFriends) : 'not an array'));
                
                $donatesMethod = $reflection->getMethod('getActiveDonates');
                $donatesMethod->setAccessible(true);
                $donates = $donatesMethod->invoke($newsfeedController);
                \Log::info('NewsfeedV2Controller::index - Donates count: ' . (is_array($donates) ? count($donates) : 'not an array'));
                
                $advertisesMethod = $reflection->getMethod('getActiveAdvertises');
                $advertisesMethod->setAccessible(true);
                $advertises = $advertisesMethod->invoke($newsfeedController);
                \Log::info('NewsfeedV2Controller::index - Advertises count: ' . (is_array($advertises) ? count($advertises) : 'not an array'));
                
                return [
                    'peopleMayKnow' => $peopleMayKnow,
                    'pendingFriends' => $pendingFriends,
                    'donates' => $donates,
                    'advertises' => $advertises,
                ];
            });

            // Don't fetch activities initially - let infinite scroll handle it
            \Log::info('NewsfeedV2Controller::index - Activities will be loaded by infinite scroll');

            // Get user notifications for the enhanced features
            $notifications = $this->getUserNotifications();

            // Ensure all data has proper structure before passing to Inertia
            \Log::info('NewsfeedV2Controller::index - Preparing to render Inertia view');
            return Inertia::render('NewsfeedV2', [
                // Remove initial activities to prevent the "Unknown User" issue
                'peopleMayKnow' => $cachedData['peopleMayKnow'] ?? [],
                'pendingFriends' => $cachedData['pendingFriends'] ?? [],
                'donates' => $cachedData['donates'] ?? [],
                'advertises' => $cachedData['advertises'] ?? [],
                'notifications' => $notifications,
            ]);
        } catch (\Exception $e) {
            // Log the error and return a safe default structure
            \Log::error('NewsfeedV2Controller::index error: ' . $e->getMessage());
            \Log::error('NewsfeedV2Controller::index error trace: ' . $e->getTraceAsString());
            
            return Inertia::render('NewsfeedV2', [
                'peopleMayKnow' => [],
                'pendingFriends' => [],
                'donates' => [],
                'advertises' => [],
                'notifications' => [],
            ]);
        }
    }

    /**
     * Get paginated activities with filtering options
     */
    public function getPaginatedActivities(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 5);
            $filter = $request->get('filter', 'all');
            
            // Use the existing NewsfeedController to get activities
            $newsfeedController = new NewsfeedController();
            $activitiesMethod = new \ReflectionMethod($newsfeedController, 'getActivities');
            $activitiesMethod->setAccessible(true);
            $activities = $activitiesMethod->invoke($newsfeedController, $page, $perPage);
            
            // Apply filtering if needed
            if ($filter !== 'all') {
                $activities['activities'] = $this->filterActivities($activities['activities'] ?? [], $filter);
            }
            
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
            \Log::error('NewsfeedV2Controller::getPaginatedActivities error: ' . $e->getMessage());
            
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
     * Search activities by content or user name
     */
    public function searchActivities(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            
            if (empty(trim($query))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required',
                    'activities' => [],
                ]);
            }
            
            // Get activities with search
            $activities = Activity::with(['user', 'activityable'])
                ->with(['activityable.donation', 'activityable.reciever'])
                ->where(function($q) use ($query) {
                    $q->whereHas('user', function($userQuery) use ($query) {
                        $userQuery->where('name', 'like', '%' . $query . '%');
                    })
                    ->orWhereHas('activityable', function($activityableQuery) use ($query) {
                        if (method_exists($activityableQuery->getModel(), 'getContentAttribute')) {
                            $activityableQuery->where('content', 'like', '%' . $query . '%');
                        } else {
                            $activityableQuery->where('body', 'like', '%' . $query . '%');
                        }
                    });
                })
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);
            
            return response()->json([
                'success' => true,
                'activities' => ActivityResource::collection($activities)->resolve(),
                'pagination' => [
                    'current_page' => $activities->currentPage(),
                    'last_page' => $activities->lastPage(),
                    'total' => $activities->total(),
                    'has_more' => $activities->currentPage() < $activities->lastPage(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('NewsfeedV2Controller::searchActivities error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search activities',
                'activities' => [],
            ], 500);
        }
    }

    /**
     * Get user notifications
     */
    private function getUserNotifications()
    {
        try {
            // In a real implementation, you would fetch from a notifications table
            // For now, return mock data
            return [
                [
                    'id' => 1,
                    'type' => 'like',
                    'message' => 'John Doe ไลค์โพสต์ของคุณ',
                    'time' => '5 นาทีที่แล้ว',
                    'read' => false,
                    'avatar' => '/storage/images/avatars/user1.jpg',
                    'link' => '/posts/123'
                ],
                [
                    'id' => 2,
                    'type' => 'comment',
                    'message' => 'Jane Smith คอมเมนต์บนโพสต์ของคุณ: "เยี่ยมมาก!"',
                    'time' => '15 นาทีที่แล้ว',
                    'read' => false,
                    'avatar' => '/storage/images/avatars/user2.jpg',
                    'link' => '/posts/123#comment-456'
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('NewsfeedV2Controller::getUserNotifications error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Filter activities by type
     */
    private function filterActivities($activities, $filter)
    {
        if ($filter === 'all') {
            return $activities;
        }
        
        return array_filter($activities, function($activity) use ($filter) {
            switch ($filter) {
                case 'posts':
                    return $activity['action_to'] === 'Post';
                case 'academy':
                    return $activity['action_to'] === 'AcademyPost';
                case 'courses':
                    return $activity['action_to'] === 'CoursePost';
                case 'donations':
                    return $activity['action_to'] === 'Donate' || $activity['action_to'] === 'DonateRecipient';
                default:
                    return true;
            }
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
        Cache::forget('newsfeed_v2_initial_' . $userId);
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