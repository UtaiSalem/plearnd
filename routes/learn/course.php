<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoursePostController;
use App\Http\Controllers\CoursePostReactionController;
use App\Http\Controllers\CoursePostCommentController;
use App\Http\Controllers\CoursePostImageController;
use App\Http\Controllers\CoursePostImageCommentController;
use App\Http\Controllers\CoursePostImageCommentReactionController;


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/courses/{course}/settings', [CourseController::class, 'settings'])->name('course.settings.page.show');
});


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::post('/courses/posts/comments/{comment}/like', [CoursePostCommentReactionController::class, 'toggleLikeComment'])->name('toggle.like.course_post_comment');
    Route::post('/courses/posts/comments/{comment}/dislike', [CoursePostCommentReactionController::class, 'toggleDislikeComment'])->name('toggle.dislike.course_post_comment');
    
    Route::post('/courses/posts/images/comments/{comment}/like', [CoursePostImageCommentReactionController::class, 'toggleLikeComment'])->name('toggle.like.course_post_image_comment');
    Route::post('/courses/posts/images/comments/{comment}/dislike', [CoursePostImageCommentReactionController::class, 'toggleDislikeComment'])->name('toggle.dislike.course_post_image_comment');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->prefix('/courses/{course}/posts')->group(function () {

    // Route::get('/', [PostController::class, 'index'])->name('course.posts.index');
    Route::post('/', [CoursePostController::class, 'store'])->name('course.posts.store');
    Route::get('/{post}', [CoursePostController::class, 'show'])->name('course.posts.show');
    Route::get('/{post}/edit', [CoursePostController::class, 'edit'])->name('course.posts.edit');
    Route::patch('/{post}', [CoursePostController::class, 'update'])->name('course.posts.update');
    Route::delete('/{post}', [CoursePostController::class, 'destroy'])->name('course.posts.destroy');

    Route::post('/{post}/like', [CoursePostReactionController::class, 'toggleLike'])->name('toggle.like.course_post');
    Route::post('/{post}/dislike', [CoursePostReactionController::class, 'toggleDislike'])->name('toggle.dislike.course_post');
    
    Route::get('/{post}/comments', [CoursePostCommentController::class, 'index'])->name('course.post.comments.index');
    Route::post('/{post}/comments', [CoursePostCommentController::class, 'store'])->name('course.post.comments.store');
    Route::delete('/{post}/comments/{comment}', [CoursePostCommentController::class, 'destroy'])->name('course.post.comments.destroy');

    Route::post('/{post}/images/{image}/like', [CoursePostImageController::class, 'toggleLike'])->name('course.post.images.toggle-like');
    Route::post('/{post}/images/{image}/dislike', [CoursePostImageController::class, 'toggleDislike'])->name('course.post.images.toggle-dislike');
    
    Route::get('/{post}/images/{image}/comments', [CoursePostImageCommentController::class, 'index'])->name('course.post.images.comment.index');
    Route::post('/{post}/images/{image}/comments', [CoursePostImageCommentController::class, 'store'])->name('course.post.images.comment.store');
    Route::delete('/{post}/images/{image}/comments/{comment}', [CoursePostImageCommentController::class, 'destroy'])->name('course.post.images.comment.destroy');
    Route::put('/{post}/images/{image}/comments/{comment}', [CoursePostImageCommentController::class, 'update'])->name('course.post.images.comment.update');
    
    Route::post('/{post}/images/{image}/comments/{comment}/like', [CoursePostImageCommentReactionController::class, 'toggleLike'])->name('course.post.images.comment.toggle-like');
    Route::post('/{post}/images/{image}/comments/{comment}/dislike', [CoursePostImageCommentReactionController::class, 'toggleDislike'])->name('course.post.images.comment.toggle-dislike');

    // Route::get('/{post}/images', [PostImageController::class, 'index'])->name('course.post.images.index');
    // Route::post('/{post}/images', [PostImageController::class, 'store'])->name('course.post.images.store');
    // Route::delete('/{post}/images/{image}', [PostImageController::class, 'destroy'])->name('course.post.images.destroy');
    // Route::post('/{post}/images/{image}/set-as-cover', [PostImageController::class, 'setAsCover'])->name('course.post.images.setAsCover');
    
    // Route::post('/postimage/{post_image}/comments', [PostImageController::class, 'storeComment'])->name('course.post.image.comments.store');

});
