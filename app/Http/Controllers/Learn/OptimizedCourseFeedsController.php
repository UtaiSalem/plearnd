<?php

namespace App\Http\Controllers\Learn;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Activity;
use App\Models\CoursePost;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
class OptimizedCourseFeedsController extends Controller
{
    /**
     * Display the course feeds page with optimized loading
     */
    public function index(Course $course)
    {
        // Use cache for static course data
        $cacheKey = 'course_feeds_data_' . $course->id . '_' . auth()->id();
        $cachedData = Cache::remember($cacheKey, 300, function () use ($course) {
            return [
                'course' => $this->getOptimizedCourseData($course),
                'academy' => $this->getOptimizedAcademyData($course->academy),
            ];
        });

        // Get initial page of activities (smaller batch size)
        $activities = $this->getCourseActivities($course, 1, 5);
        
        // Get course member of auth user
        $courseMemberOfAuth = $course->courseMembers()
            ->where('user_id', auth()->id())
            ->first();

        return Inertia::render('Learn/Course/OptimizedCourseFeeds', [
            'course' => $cachedData['course'],
            'academy' => $cachedData['academy'],
            'initialActivities' => $activities['data'],
            'initialPagination' => [
                'current_page' => $activities['current_page'],
                'last_page' => $activities['last_page'],
                'total' => $activities['total'],
            ],
            'courseMemberOfAuth' => $courseMemberOfAuth,
            'isCourseAdmin' => $course->user_id === auth()->id(),
        ]);
    }

    /**
     * Get paginated course activities for infinite scroll
     */
    public function getPaginatedActivities(Request $request, Course $course)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 5);
        
        $activities = $this->getCourseActivities($course, $page, $perPage);
        
        return response()->json([
            'success' => true,
            'activities' => $activities['data'],
            'pagination' => [
                'current_page' => $activities['current_page'],
                'last_page' => $activities['last_page'],
                'total' => $activities['total'],
                'has_more' => $activities['current_page'] < $activities['last_page'],
            ]
        ]);
    }

    /**
     * Get optimized course data
     */
    private function getOptimizedCourseData(Course $course)
    {
        return [
            'id' => $course->id,
            'name' => $course->name,
            'slug' => $course->slug,
            'code' => $course->code,
            'description' => $course->description,
            'logo' => $course->logo,
            'cover' => $course->cover,
            'user_id' => $course->user_id,
            'academy_id' => $course->academy_id,
        ];
    }

    /**
     * Get optimized academy data
     */
    private function getOptimizedAcademyData($academy)
    {
        if (!$academy) return null;
        
        return [
            'id' => $academy->id,
            'name' => $academy->name,
            'logo' => $academy->logo,
        ];
    }

    /**
     * Get course activities with optimized queries
     */
    private function getCourseActivities(Course $course, $page = 1, $perPage = 10)
    {
        $activities = Activity::where('activityable_type', CoursePost::class)
            ->whereHas('activityable', function (Builder $query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->with([
                'user' => function($query) {
                    $query->select('id', 'name', 'profile_photo_url');
                },
                'activityable' => function($query) {
                    $query->select('id', 'content', 'user_id', 'course_id', 'created_at');
                }
            ])
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        // Transform activities to optimized format
        $transformedActivities = $activities->getCollection()->map(function ($activity) {
            return [
                'id' => $activity->id,
                'user_id' => $activity->user_id,
                'activity_type' => $activity->activity_type,
                'action' => $activity->action,
                'action_to' => $activity->action_to,
                'action_to_id' => $activity->action_to_id,
                'privacy_settings' => $activity->privacy_settings,
                'status' => $activity->status,
                'created_at' => $activity->created_at,
                'diff_humans_created_at' => $activity->created_at->diffForHumans(),
                
                // Optimized user data
                'action_by' => [
                    'id' => $activity->user->id,
                    'name' => $activity->user->name,
                    'avatar' => $activity->user->profile_photo_url,
                ],
                
                // Optimized target resource data
                'target_resource' => $this->getOptimizedTargetResource($activity),
            ];
        });

        return [
            'data' => $transformedActivities,
            'current_page' => $activities->currentPage(),
            'last_page' => $activities->lastPage(),
            'total' => $activities->total(),
        ];
    }

    /**
     * Get optimized target resource data
     */
    private function getOptimizedTargetResource($activity)
    {
        $activityable = $activity->activityable;
        
        if (!$activityable) {
            return null;
        }

        return [
            'id' => $activityable->id,
            'content' => $activityable->content,
            'privacy_settings' => $activityable->privacy_settings,
            'status' => $activityable->status,
            'created_at' => $activityable->created_at,
            'post_url' => route('courses.posts.show', [$activityable->course_id, $activityable->id]),
            'author' => [
                'id' => $activityable->user->id,
                'name' => $activityable->user->name,
            ],
            'images_count' => $activityable->coursePostImages()->count(),
            'reactions_count' => $activityable->likes_count + $activityable->dislikes_count,
            'comments_count' => $activityable->coursePostComments()->count(),
        ];
    }

    /**
     * Clear course feeds cache
     */
    public function clearCourseFeedsCache(Course $course)
    {
        // Clear all course feeds caches for this course
        $cachePattern = 'course_feeds_data_' . $course->id . '_*';
        Cache::forget($cachePattern);
        
        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Get course post images with lazy loading
     */
    public function getCoursePostImages(Request $request, Course $course, $postId)
    {
        $post = CoursePost::findOrFail($postId);
        
        // Verify post belongs to this course
        if ($post->course_id !== $course->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }
        
        $images = $post->coursePostImages()
            ->select('id', 'filename')
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json([
            'success' => true,
            'images' => $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => '/storage/images/course_posts/' . $image->filename,
                    'thumbnail' => '/storage/images/course_posts/thumbnails/' . $image->filename,
                ];
            })
        ]);
    }

    /**
     * Get course post reactions with lazy loading
     */
    public function getCoursePostReactions(Request $request, Course $course, $postId)
    {
        $post = CoursePost::findOrFail($postId);
        
        // Verify post belongs to this course
        if ($post->course_id !== $course->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }
        
        $userReaction = null;
        
        // Check if authenticated user has reacted to this post
        if (auth()->check()) {
            $like = $post->likes()->where('user_id', auth()->id())->first();
            $dislike = $post->dislikes()->where('user_id', auth()->id())->first();
            
            if ($like) {
                $userReaction = 'like';
            } elseif ($dislike) {
                $userReaction = 'dislike';
            }
        }
        
        return response()->json([
            'success' => true,
            'reactions' => [
                'likes' => $post->likes()->count(),
                'dislikes' => $post->dislikes()->count(),
                'user_reaction' => $userReaction
            ]
        ]);
    }

    /**
     * Get course post comments with lazy loading
     */
    public function getCoursePostComments(Request $request, Course $course, $postId)
    {
        $post = CoursePost::findOrFail($postId);
        
        // Verify post belongs to this course
        if ($post->course_id !== $course->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }
        
        $limit = $request->get('limit', 5);
        
        $comments = $post->coursePostComments()
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'profile_photo_url');
            }])
            ->latest()
            ->limit($limit)
            ->get();
        
        return response()->json([
            'success' => true,
            'comments' => $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'avatar' => $comment->user->profile_photo_url,
                    ],
                    'created_at' => $comment->created_at->diffForHumans(),
                ];
            })
        ]);
    }
}