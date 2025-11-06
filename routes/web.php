<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FriendController;
use App\Http\Controllers\MentalMathController;
use App\Http\Controllers\NewsfeedController;
use App\Http\Controllers\SuggesterController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Auth\ProviderAuthController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/register/{user:reference_code}',[SuggesterController::class, 'index'])->name('register.reference');
Route::get('/suggester/check/{user:personal_code}', [SuggesterController::class, 'checkSuggesterExist'])->name('suggester.check');
Route::get('/check-username-exists/{name}', [UserProfileController::class, 'checkUsernameExists'])->name('profile.username.check');
Route::get('/check-email-exists/{email}', [UserProfileController::class, 'checkEmailExists'])->name('profile.username.check');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'isPlearndAdmin' => auth()->user()->isPlearndAdmin()
        ]);
    })->name('dashboard');
    
    Route::get('/newsfeed', [NewsfeedController::class, 'index'] )->name('newsfeed');
    Route::get('/api/newsfeed/activities', [NewsfeedController::class, 'getActivities'] )->name('newsfeed.getActivities');
    Route::get('/users/{user:reference_code}/profile', [UserProfileController::class, 'index'])->name('user.profile');
});

Route::get('/auth/{provider}/redirect', [ProviderAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/{provider}/callback', [ProviderAuthController::class, 'handleGoogleCallback'])->name('google.callback');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('forgot-pasword');
    Route::post('/forgot-password/getuser', [ForgotPasswordController::class, 'getUser'])->name('forgot-pasword.get-user');
    Route::post('/forgot-password/reset/{user}', [ForgotPasswordController::class, 'resetPassword'])->name('forgot-pasword.reset');
    Route::post('/forgot-password/exchange/{user}', [ForgotPasswordController::class, 'exchangeMoney'])->name('forgot-pasword.exchange');
    Route::delete('/forgot-password/users/{user}', [ForgotPasswordController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::post('/friends/{recipient}', [FriendController::class, 'addFriendRequest'])->name('friend-request');
    Route::delete('/friends/{friend}', [FriendController::class, 'deleteFriendRequest'])->name('delete-friend-request');   
    Route::patch('/friends/{friend}/accept', [FriendController::class, 'acceptFriendRequest'])->name('accept-friend-request');
    Route::post('/friends/{friend}/deny', [FriendController::class, 'denyFriendRequest'])->name('deny-friend-request');
    Route::post('/friends/{friend}/unfriend', [FriendController::class, 'unfriend'])->name('unfriend');
    Route::get('/friends', [FriendController::class, 'index'])->name('friends');

});

Route::get('/mental-math', [MentalMathController::class, 'index'])->name('mental-math');

// Test routes for development
Route::get('/test/course-profile-cover', function () {
    return Inertia::render('Test/CourseProfileCoverTest');
})->name('test.course-profile-cover');

require __DIR__ . '/earn/donate.php';
require __DIR__ . '/earn/advert.php';
require __DIR__ . '/play/post.php';
require __DIR__ . '/play/game.php';
require __DIR__ . '/learn/academy.php';
require __DIR__ . '/learn/course.php';
require __DIR__ . '/apis/v2/course.php';
require __DIR__ . '/learn/student.php';
require __DIR__ . '/homevisit/homevisit.php';
require __DIR__ . '/studentcard/studentcard.php';
require __DIR__ . '/apis/academies.php';
