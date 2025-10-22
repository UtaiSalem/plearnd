<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Play\NewsfeedBasicController;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Basic newsfeed routes for debugging
    Route::get('/newsfeed-basic', [NewsfeedBasicController::class, 'index'])->name('newsfeed.basic');
    Route::get('/api/newsfeed-basic/activities', [NewsfeedBasicController::class, 'getPaginatedActivities'])->name('api.newsfeed.basic.activities.paginated');
});