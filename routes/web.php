<?php

use Inertia\Inertia;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\AcademyController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\NewsfeedController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CourseQuizController;
use App\Http\Controllers\MentalMathController;
use App\Http\Controllers\TopicImageController;
use App\Http\Controllers\CourseGroupController;
use App\Http\Controllers\LessonImageController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\CourseMemberController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\AcademyCourseController;
use App\Http\Controllers\AcademyMemberController;
use App\Http\Controllers\TopicQuestionController;
use App\Http\Controllers\CourseQuestionController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LessonQuestionController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuestionOptionController;
use App\Http\Controllers\AssignmentImageController;
use App\Http\Controllers\TopicAssignmentController;
use App\Http\Controllers\AssignmentAnswerController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\CourseAssignmentController;
use App\Http\Controllers\CourseAttendanceController;
use App\Http\Controllers\CourseQuizResultController;
use App\Http\Controllers\LessonAssignmentController;
use App\Http\Controllers\CourseGroupMemberController;
use App\Http\Controllers\CourseQuizQuestionController;
use App\Http\Controllers\UserAnswerQuestionController;
use App\Http\Controllers\PostCommentReactionController;
use App\Http\Controllers\UserController;


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

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () { return Inertia::render('Dashboard'); })->name('dashboard');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () { 
    // route resource /users
    // Route::delete('/users/{user}', [UserController::class, 'destroy']);
});


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/academies')->group(function () {
    Route::get('/', [AcademyController::class, 'index'])->name('academies');
    Route::post('/', [AcademyController::class, 'store'])->name('academies.store');
    Route::get('/create', [AcademyController::class, 'create'])->name('academy.create');
    Route::get('/{academy}', [AcademyController::class, 'show'])->name('academy.show');

    Route::get('/{academy}/courses', [AcademyCourseController::class, 'index'])->name('academy.courses.index');
    Route::get('/{academy}/courses/create', [AcademyCourseController::class, 'create'])->name('academy.courses.create');
    Route::post('/{academy}/courses', [AcademyCourseController::class, 'store'])->name('academy.courses.store');

    Route::patch('/{academy}/update', [AcademyController::class, 'update'])->name('academy.update');

    Route::post('/{academy}/members', [AcademyMemberController::class, 'storemember']);
    Route::post('/{academy}/unmembers', [AcademyMemberController::class, 'unmember']);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/courses')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('courses');
    Route::post('/', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/create', [CourseController::class, 'create'])->name('courses.create');
    Route::get('/{course}', [CourseController::class, 'show'])->name('course.show');
    Route::put('/{course}', [CourseController::class, 'update'])->name('course.update');
    Route::patch('/{course}', [CourseController::class, 'update'])->name('course.part.update');
    Route::delete('/{course}', [CourseController::class, 'destroy'])->name('course.destroy');
    Route::get('/{course}/progress', [CourseController::class, 'progress'])->name('course.progress');
    
    // Route::patch('/{course}/update', [CourseController::class, 'update'])->name('course.cover.update');

    Route::get('/users/{user}', [CourseController::class, 'getUserCourses'])->name('user.courses');
    Route::get('/users/{user}/member', [CourseController::class, 'getAuthMemberCourses'])->name('auth.member.courses');

    Route::resource('/{course}/lessons', LessonController::class);

    Route::get('/{course}/members/{member}/progress', [CourseMemberController::class, 'show'])->name('course.member.show');
    Route::post('/{course}/members', [CourseMemberController::class, 'storemember']);
    Route::delete('/{course}/members/{member}', [CourseMemberController::class, 'destroy']);
    Route::delete('/{course}/members/{member}/delete', [CourseMemberController::class, 'deleteCourseMember']);
    Route::post('/{course}/members/{member}/set-active-tab', [CourseMemberController::class, 'setActiveTab']);
    Route::post('/{course}/members/{member}/set-active-group-tab', [CourseMemberController::class, 'setActiveGroupTab']);
    Route::patch('/{course}/members/{member}/update', [CourseMemberController::class, 'update']);
    Route::patch('/{course}/members/{member}/bonus-points', [CourseMemberController::class, 'updateBonusPoints']);

    Route::get('/{course}/groups', [CourseGroupController::class, 'show']);
    Route::post('/{course}/groups', [CourseGroupController::class, 'store'])->name('course.groups.store');
    Route::patch('/{course}/groups/{group}', [CourseGroupController::class, 'update'])->name('course.groups.update');
    Route::delete('/{course}/groups/{group}', [CourseGroupController::class, 'destroy'])->name('course.groups.destroy');

    Route::post('/{course}/groups/{group}/members', [CourseGroupMemberController::class, 'store']);
    Route::delete('/{course}/members/groups/{group}', [CourseGroupMemberController::class, 'unMemberGroup']);
    Route::delete('/{course}/groups/{group}/members/{member}', [CourseGroupMemberController::class, 'unMemberGroup']);

    Route::resource('/{course}/assignments', CourseAssignmentController::class);
    
    Route::resource('/{course}/quizzes', CourseQuizController::class);
    Route::resource('/{course}/quizzes/{quiz}/questions', CourseQuizQuestionController::class);

    Route::resource('/{course}/quizzes/{quiz}/results', CourseQuizResultController::class);

    Route::get('/{course}/groups/{group}/attendances', [CourseAttendanceController::class, 'getCourseGroupAttendances'])->name('course.groups.attendances');
    Route::post('/{course}/groups/{group}/attendances', [CourseAttendanceController::class, 'store'])->name('course.groups.attendances.store');

});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/api/courses')->group(function () {
    Route::get('/', [CourseController::class, 'getMoreCourses'])->name('api.courses.all');
    Route::get('/users/{user}/my-courses', [CourseController::class, 'getMyCourses'])->name('api.courses.my-courses');
    Route::get('/users/{user}/membered', [CourseController::class, 'getAuthMemberedCourses'])->name('api.courses.member');

    Route::resource('/{course}/lessons', LessonController::class);
});


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::resource('/lessons', LessonController::class);
    Route::resource('/lessons/{lesson}/images', LessonImageController::class);
    Route::resource('/lessons/{lesson}/assignments', LessonAssignmentController::class);
    Route::resource('/lessons/{lesson}/questions', LessonQuestionController::class);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::resource('/assignments', AssignmentController::class);
    Route::resource('/asmimages', AssignmentImageController::class);
    Route::resource('/assignments/{assignment}/answers', AssignmentAnswerController::class);
    Route::post('/assignments/{assignment}/answers/{answer}/set-points', [AssignmentAnswerController::class, 'setAnswerPoints'])->name('assignments.answers.setAnswerPoints');
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
    Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('forgot-pasword');
    Route::post('/forgot-password/getuser', [ForgotPasswordController::class, 'getUser'])->name('forgot-pasword.get-user');
    Route::post('/forgot-password/reset/{user}', [ForgotPasswordController::class, 'resetPassword'])->name('forgot-pasword.reset');
    Route::post('/forgot-password/exchange/{user}', [ForgotPasswordController::class, 'exchangeMoney'])->name('forgot-pasword.exchange');
    Route::delete('/forgot-password/users/{user}', [ForgotPasswordController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    // Route::resource('supports', SupportController::class);
    Route::get('/supports/loan', [SupportController::class, 'loan'])->name('support.loan');
    Route::post('/supports/loan', [SupportController::class, 'storeLoan'])->name('support.store.loan');

    Route::get('/supports/advertise', [SupportController::class, 'advertise'])->name('support.advert');
    Route::post('/supports/advertise', [SupportController::class, 'storeAdvertise'])->name('support.store.advertise');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/attendances/{attendance}/details', [AttendanceDetailController::class, 'index'])->name('attendances.details.index');
    Route::post('/attendances/{attendance}/details', [AttendanceDetailController::class, 'store'])->name('attendances.details.store');
    Route::patch('/attendances/details/{detail}', [AttendanceDetailController::class, 'update'])->name('attendance.details.update');
});

Route::get('/mental-math', [MentalMathController::class, 'index'])->name('mental-math');
