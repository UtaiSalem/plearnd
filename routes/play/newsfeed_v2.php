<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Play\NewsfeedV2Controller;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Newsfeed V2 routes
    Route::get('/newsfeed-v2', [NewsfeedV2Controller::class, 'index'])->name('newsfeed.v2');
    Route::get('/api/newsfeed-v2/activities', [NewsfeedV2Controller::class, 'getPaginatedActivities'])->name('api.newsfeed.v2.activities.paginated');
    Route::get('/api/newsfeed-v2/search', [NewsfeedV2Controller::class, 'searchActivities'])->name('api.newsfeed.v2.search');
    Route::post('/api/newsfeed-v2/clear-cache', [NewsfeedV2Controller::class, 'clearNewsfeedCache'])->name('api.newsfeed.v2.clear-cache');
    Route::post('/api/newsfeed-v2/clear-activity-cache', [NewsfeedV2Controller::class, 'clearActivityCache'])->name('api.newsfeed.v2.clear-activity-cache');
    
    // API endpoints for lazy loading post data (reuse from original newsfeed)
    Route::get('/api/posts/{post}/images', function ($post) {
        $post = \App\Models\Post::findOrFail($post);
        $images = $post->postImages()->select('id', 'filename')->get();
        
        return response()->json([
            'success' => true,
            'images' => $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => '/storage/images/posts/' . $image->filename,
                    'thumbnail' => '/storage/images/posts/thumbnails/' . $image->filename,
                ];
            })
        ]);
    })->name('api.posts.images');
    
    Route::get('/api/posts/{post}/reactions', function ($post) {
        $post = \App\Models\Post::findOrFail($post);
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
    })->name('api.posts.reactions');
    
    Route::get('/api/posts/{post}/comments', function ($post) {
        $post = \App\Models\Post::findOrFail($post);
        $limit = request()->get('limit', 5);
        
        $comments = $post->postComments()
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
    })->name('api.posts.comments');
});