<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostImageController;
use App\Http\Controllers\PostImageReactionController;
use App\Http\Controllers\PostImageCommentReactionController;
use App\Http\Controllers\PostCommentReactionController;


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::resource('/posts', PostController::class);
    Route::get('/posts/{post}/getPostActivity', [PostController::class, 'getPostActivity'])->name('post.activity');

    Route::post('/posts/{post}/like', [PostReactionController::class, 'toggleLikePost'])->name('toggle.like.post');
    Route::post('/posts/{post}/dislike', [PostReactionController::class, 'toggleDislikePost'])->name('toggle.dislike.post');
    
    Route::get('/posts/{post}/comments', [PostCommentController::class, 'index'])->name('post.comments.index');
    Route::post('/posts/{post}/comments', [PostCommentController::class, 'store'])->name('post.comments.store');
    Route::delete('/posts/{post}/comments/{comment}', [PostCommentController::class, 'destroy'])->name('post.comments.destroy');

    Route::post('/post_comments/{post_comment}/like', [PostCommentReactionController::class, 'toggleLikePostComment'])->name('toggle.like.post.comment');
    Route::post('/post_comments/{post_comment}/dislike', [PostCommentReactionController::class, 'toggleDislikePostComment'])->name('toggle.dislike.post.comment');

    // Route::resource('/posts/{post}/images', PostImageController::class)->name('post.images');
    Route::get('/posts/{post}/images', [PostImageController::class, 'index'])->name('post.images.index');
    Route::post('/posts/{post}/images', [PostImageController::class, 'store'])->name('post.images.store');
    Route::delete('/posts/{post}/images/{image}', [PostImageController::class, 'destroy'])->name('post.images.destroy');
    // Route::post('/posts/{post}/images/{image}/set-as-cover', [PostImageController::class, 'setAsCover'])->name('post.images.setAsCover');
    
    Route::post('/postimage/{post_image}/comments', [PostImageController::class, 'storeComment'])->name('post.image.comments.store');
    Route::get('/postimage/{post_image}/comments', [PostImageController::class, 'getComments'])->name('post.image.comments.getComments');

    Route::post('/post_images/{post_image}/like', [PostImageReactionController::class, 'toggleLikePostImage'])->name('toggle.like.post.image');
    Route::post('/post_images/{post_image}/dislike', [PostImageReactionController::class, 'toggleDislikePostImage'])->name('toggle.dislike.post.image');

    // Route::delete('/postimage/{post_image}/comments/{comment_id}', [PostImageController::class, 'destroyComment'])->name('post.image.comments.destroy');
    Route::delete('/post_image_comments/{post_image_comment}', [PostImageController::class, 'destroyComment'])->name('post.image.comments.destroy');

    Route::post('/post_image_comments/{post_image_comment}/like', [PostImageCommentReactionController::class, 'toggleLikePostImageComment'])->name('toggle.like.post.image.comment');
    Route::post('/post_image_comments/{post_image_comment}/dislike', [PostImageCommentReactionController::class, 'toggleDislikePostImageComment'])->name('toggle.dislike.post.image.comment');

});
