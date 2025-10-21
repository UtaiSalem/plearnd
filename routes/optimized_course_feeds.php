<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OptimizedCourseFeedsController;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Optimized course feeds routes
    Route::get('/courses/{course}/feeds/optimized', [OptimizedCourseFeedsController::class, 'index'])->name('courses.feeds.optimized');
    Route::get('/api/courses/{course}/feeds/paginated', [OptimizedCourseFeedsController::class, 'getPaginatedActivities'])->name('api.courses.feeds.paginated');
    Route::post('/api/courses/{course}/feeds/clear-cache', [OptimizedCourseFeedsController::class, 'clearCourseFeedsCache'])->name('api.courses.feeds.clear-cache');
    
    // API endpoints for lazy loading course post data
    Route::get('/api/courses/{course}/posts/{post}/images', [OptimizedCourseFeedsController::class, 'getCoursePostImages'])->name('api.courses.posts.images');
    Route::get('/api/courses/{course}/posts/{post}/reactions', [OptimizedCourseFeedsController::class, 'getCoursePostReactions'])->name('api.courses.posts.reactions');
    Route::get('/api/courses/{course}/posts/{post}/comments', [OptimizedCourseFeedsController::class, 'getCoursePostComments'])->name('api.courses.posts.comments');
});