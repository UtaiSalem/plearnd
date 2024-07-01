<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;



use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseGroupController;
use App\Http\Controllers\CourseQuizController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\LessonImageController;
use App\Http\Controllers\MentalMathController;
use App\Http\Controllers\NewsfeedController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SuggesterController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CourseLessonController;
use App\Http\Controllers\CourseMemberController;
use App\Http\Controllers\CourseActivityController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LessonQuestionController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuestionOptionController;
use App\Http\Controllers\AssignmentImageController;
use App\Http\Controllers\AssignmentAnswerController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\CourseAssignmentController;
use App\Http\Controllers\CourseAttendanceController;
use App\Http\Controllers\CourseQuizResultController;
use App\Http\Controllers\LessonAssignmentController;
use App\Http\Controllers\Auth\ProviderAuthController;
use App\Http\Controllers\CourseGroupMemberController;
use App\Http\Controllers\CourseQuizQuestionController;
use App\Http\Controllers\UserAnswerQuestionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/register/{user:reference_code}',[SuggesterController::class, 'index'])->name('register.reference');
// Route::get('/register/{user:reference_code}', function ($user) {
//     $suggester = User::where('reference_code', $user)->first();
//     return Inertia::render('Auth/Register', [
//         'suggester' => new UserResource($suggester),
//     ]);
// })->name('register.reference');

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
    // Route::get('/newsfeed/{user}', [NewsfeedController::class, 'show'] )->name('newsfeed.show');
    Route::get('/api/newsfeed/activities', [NewsfeedController::class, 'getActivities'] )->name('newsfeed.getActivities');

    Route::get('/users/{user:reference_code}/profile', [UserProfileController::class, 'index'])->name('user.profile');
});

Route::get('/auth/{provider}/redirect', [ProviderAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/{provider}/callback', [ProviderAuthController::class, 'handleGoogleCallback'])->name('google.callback');



Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/courses/{course}/lessons')->group(function () {
    // Route::resource('/', CourseLessonController::class);
    Route::get('/', [CourseLessonController::class, 'index'])->name('course.lessons.index');
    Route::post('/', [CourseLessonController::class, 'store'])->name('course.lessons.store');
    Route::get('/create', [CourseLessonController::class, 'create'])->name('course.lessons.create');
    Route::get('/{lesson}', [CourseLessonController::class, 'show'])->name('course.lessons.show');
    Route::get('/{lesson}/edit', [CourseLessonController::class, 'edit'])->name('course.lessons.edit');
    Route::put('/{lesson}', [CourseLessonController::class, 'update'])->name('course.lessons.update');
    Route::patch('/{lesson}', [CourseLessonController::class, 'update'])->name('course.lessons.part.update');
    Route::delete('/{lesson}', [CourseLessonController::class, 'destroy'])->name('course.lessons.destroy');

    // Route::resource('/{lesson}/images', LessonImageController::class);
    // Route::resource('/{lesson}/assignments', LessonAssignmentController::class);
    // Route::resource('/{lesson}/questions', LessonQuestionController::class);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/lessons')->group(function () {
    Route::resource('/', LessonController::class);
    Route::resource('/{lesson}/images', LessonImageController::class);
    Route::resource('/{lesson}/assignments', LessonAssignmentController::class);
    Route::resource('/{lesson}/questions', LessonQuestionController::class);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::resource('/assignments', AssignmentController::class);
    Route::resource('/asmimages', AssignmentImageController::class);
    Route::resource('/assignments/{assignment}/answers', AssignmentAnswerController::class);
    Route::post('/assignments/{assignment}/answers/{answer}/set-points', [AssignmentAnswerController::class, 'setAnswerPoints'])->name('assignments.answers.setAnswerPoints');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->prefix('/courses/{course}')->group(function () {
    Route::resource('/assignments', CourseAssignmentController::class);

    Route::resource('/quizzes', CourseQuizController::class);
    Route::resource('/quizzes/{quiz}/questions', CourseQuizQuestionController::class);
    Route::resource('/quizzes/{quiz}/results', CourseQuizResultController::class);
});


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->prefix('/courses/{course}/members')->group(function () {
    Route::get('/member-requesters', [CourseMemberController::class, 'getMembersRequesters'])->name('course.members.requesters');
    Route::get('/', [CourseMemberController::class, 'index'])->name('course.members.index');
    Route::post('/', [CourseMemberController::class, 'storemember']);
    Route::get('/{member}/progress', [CourseMemberController::class, 'show'])->name('course.member.show');
    Route::delete('/{member}', [CourseMemberController::class, 'destroy']);
    Route::delete('/{member}/delete', [CourseMemberController::class, 'deleteCourseMember']);
    Route::post('/{member}/set-active-tab', [CourseMemberController::class, 'setActiveTab']);
    Route::post('/{member}/set-active-group-tab', [CourseMemberController::class, 'setActiveGroupTab']);
    Route::patch('/{member}/update', [CourseMemberController::class, 'update']);
    Route::patch('/{member}/bonus-points', [CourseMemberController::class, 'updateBonusPoints']);
    Route::patch('/{member}/grade_progress', [CourseMemberController::class, 'updateGradeProgress']);
    Route::patch('/{member}/notes-comments', [CourseMemberController::class, 'updateNotesComments']);

    Route::get('/{course_member}/member-settings', [CourseMemberController::class, 'memberSettings'])->name('course.member.settings.page.show');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->prefix('/courses/{course}/groups')->group(function () {
    Route::resource('/', CourseGroupController::class);
    // Route::get('/', [CourseGroupController::class, 'show']);
    // Route::post('/', [CourseGroupController::class, 'store'])->name('course.groups.store');
    // Route::patch('/{group}', [CourseGroupController::class, 'update'])->name('course.groups.update');
    // Route::delete('/{group}', [CourseGroupController::class, 'destroy'])->name('course.groups.destroy');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::resource('/questions', QuestionController::class);
    Route::resource('/questions/{question}/options', QuestionOptionController::class);
    Route::resource('/options', QuestionOptionController::class);
    
    Route::resource('/questions/{question}/answers', QuestionAnswerController::class);
    // Route::resource('/users/{user}/answers/{answer}/questions', UserAnswerQuestionController::class);
    Route::resource('/users/{user}/questions/{question}/answers', UserAnswerQuestionController::class);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/courses/{course}/attendances', [CourseAttendanceController::class, 'index'])->name('attendances.index');
    // Route::post('/attendances', [CourseAttendanceController::class, 'store'])->name('attendances.store');
    // Route::get('/attendances/{attendance}', [CourseAttendanceController::class, 'show'])->name('attendances.show');
    // Route::patch('/attendances/{attendance}', [CourseAttendanceController::class, 'update'])->name('attendances.update');
    // Route::delete('/attendances/{attendance}', [CourseAttendanceController::class, 'destroy'])->name('attendances.destroy');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/attendances/{attendance}/details', [AttendanceDetailController::class, 'index'])->name('attendances.details.index');
    Route::post('/attendances/{attendance}/details', [AttendanceDetailController::class, 'store'])->name('attendances.details.store');
    Route::patch('/attendances/details/{detail}', [AttendanceDetailController::class, 'update'])->name('attendance.details.update');
});


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


require __DIR__ . '/earn/donate.php';
require __DIR__ . '/earn/advert.php';
require __DIR__ . '/play/post.php';
require __DIR__ . '/learn/academy.php';
require __DIR__ . '/learn/course.php';