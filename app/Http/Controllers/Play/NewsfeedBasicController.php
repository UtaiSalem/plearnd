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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NewsfeedBasicController extends Controller
{
    /**
     * Display a basic newsfeed page for debugging
     */
    public function index()
    {
        try {
            Log::info('NewsfeedBasicController::index - Starting to load basic newsfeed');
            
            // Get activities directly from the database
            $activities = Activity::with(['user', 'activityable'])
                ->with(['activityable.donation', 'activityable.reciever'])
                ->latest()
                ->paginate(5, ['*'], 'page', 1);
            
            Log::info('NewsfeedBasicController::index - Activities query count: ' . $activities->count());
            
            // Convert to array for debugging
            $activitiesArray = ActivityResource::collection($activities)->resolve();
            
            Log::info('NewsfeedBasicController::index - Activities result count: ' . count($activitiesArray));
            
            // Return the basic view with activities
            return Inertia::render('NewsfeedBasic', [
                'activities' => $activitiesArray,
                'pagination' => [
                    'current_page' => $activities->currentPage(),
                    'last_page' => $activities->lastPage(),
                    'total' => $activities->total(),
                ],
            ]);
        } catch (\Exception $e) {
            // Log the error and return a safe default structure
            Log::error('NewsfeedBasicController::index error: ' . $e->getMessage());
            Log::error('NewsfeedBasicController::index error trace: ' . $e->getTraceAsString());
            
            return Inertia::render('NewsfeedBasic', [
                'activities' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                ],
            ]);
        }
    }

    /**
     * Get paginated activities for the basic newsfeed
     */
    public function getPaginatedActivities(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 5);
            
            Log::info('NewsfeedBasicController::getPaginatedActivities - Fetching page ' . $page . ' with per_page ' . $perPage);
            
            // Get activities with proper relationships
            $activities = Activity::with(['user', 'activityable'])
                ->with(['activityable.donation', 'activityable.reciever'])
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);
            
            Log::info('NewsfeedBasicController::getPaginatedActivities - Activities query count: ' . $activities->count());
            
            // Return the paginated result
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
            Log::error('NewsfeedBasicController::getPaginatedActivities error: ' . $e->getMessage());
            Log::error('NewsfeedBasicController::getPaginatedActivities error trace: ' . $e->getTraceAsString());
            
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
}