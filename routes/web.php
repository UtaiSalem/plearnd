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
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\NewsfeedController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\TopicImageController;
use App\Http\Controllers\LessonImageController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\CourseMemberController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\AcademyMemberController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\TopicQuestionController;
use App\Http\Controllers\CourseQuestionController;
use App\Http\Controllers\LessonQuestionController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuestionOptionController;
use App\Http\Controllers\AssignmentImageController;
use App\Http\Controllers\TopicAssignmentController;
use App\Http\Controllers\AssignmentAnswerController;
use App\Http\Controllers\CourseAssignmentController;
use App\Http\Controllers\LessonAssignmentController;
use App\Http\Controllers\UserAnswerQuestionController;
use App\Http\Controllers\PostCommentReactionController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', ['profilePath' => '/../']);
        // return redirect('/newsfeed');
    })->name('dashboard');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'),'verified',])->group(function () {
    Route::get('/newsfeed', [NewsfeedController::class, 'index'] )->name('newsfeed');
    Route::get('/myfeed/{user}', [NewsfeedController::class, 'show'] )->name('myfeed');

    Route::resource('/activities', ActivityController::class);
    Route::get('users/{user}/activities', [ ActivityController::class,'show'])->name('user.activities');

    Route::resource('/posts', PostController::class);

    Route::resource('/courses', CourseController::class);
    Route::get('courses/{courses}', [CourseController::class, 'show'])->name('courses.show');


    Route::resource('/lessons', LessonController::class);
    Route::resource('/topics', TopicController::class);

    Route::resource('/topic_images', TopicImageController::class);
    Route::delete('/topics/{topic}/images', [TopicImageController::class, 'destroy']);

    Route::post('/topics/{topic}/assignments', [TopicController::class, 'assignmentsStore'])->name('topic.assignment.store');
    Route::put('/topics/{topic}/assignments/{assignment}', [TopicAssignmentController::class, 'update']);
    // Route::delete('/topics/{topic}/assignments/{assignment}', [TopicAssignmentController::class, 'destroy']);

    Route::resource('/topics/{topic}/questions', TopicQuestionController::class);

    Route::resource('/assignments', AssignmentController::class);
    Route::resource('/asmimages', AssignmentImageController::class);
    Route::resource('/assignments/{assignment}/answers', AssignmentAnswerController::class);

    Route::resource('/questions', QuestionController::class);
    Route::resource('/questions/{question}/options', QuestionOptionController::class);
    Route::resource('/options', QuestionOptionController::class);

    Route::resource('/questions/{question}/answers', QuestionAnswerController::class);
    Route::resource('/users/{user}/answers/{answer}/questions', UserAnswerQuestionController::class);

    Route::resource('/lessons/{lesson}/images', LessonImageController::class);
    Route::resource('/lessons/{lesson}/assignments', LessonAssignmentController::class);
    Route::resource('/lessons/{lesson}/questions', LessonQuestionController::class);

    Route::resource('/courses/{course}/assignments', CourseAssignmentController::class);
    Route::resource('/courses/{course}/questions', CourseQuestionController::class);
    // Route::patch('/courses/{course}/update', [CourseController::class, 'update'])->name('course.update');
    // Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])->name('course.lesson.store');
    Route::resource('/courses/{course}/lessons', LessonController::class);

    Route::get('users/{user}/courses', [CourseController::class, 'getUserCourses'])->name('user.courses');

    Route::post('/posts/{post}/like', [PostReactionController::class, 'toggleLikePost'])->name('toggle.like.post');
    Route::post('/posts/{post}/dislike', [PostReactionController::class, 'toggleDislikePost'])->name('toggle.dislike.post');

    Route::post('/post_comments/{post_comment}/like', [PostCommentReactionController::class, 'toggleLikePostComment'])->name('toggle.like.post.comment');
    Route::post('/post_comments/{post_comment}/dislike', [PostCommentReactionController::class, 'toggleDislikePostComment'])->name('toggle.dislike.post.comment');

    Route::get('/posts/{post}/comments', [PostCommentController::class, 'index'])->name('post.comments.index');
    Route::post('/posts/{post}/comments', [PostCommentController::class, 'store'])->name('post.comments.store');

    Route::patch('/academies/{academy}/update', [AcademyController::class, 'update'])->name('academy.update');
    Route::get('/academies/{academy}', [AcademyController::class, 'show'])->name('academy.show');
    Route::post('/academies/upload',[AcademyController::class, 'upload'])->name('academy.upload');
    Route::get('/user/{user}/academies',[AcademyController::class, 'getAuthAcademies'])->name('user.academy.getAcademies');

    Route::resource('/academies/{academy}/members', AcademyMemberController::class);
    Route::resource('/courses/{course}/members', CourseMemberController::class);

    Route::get('test', [TestController::class, 'index']);
    Route::post('test/upload', [TestController::class, 'upload'])->name('upload');

    Route::resource('supports', SupportController::class);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('forgot-pasword');
    Route::post('/forgot-password/getuser', [ForgotPasswordController::class, 'getUser'])->name('forgot-pasword.get-user');
    Route::post('/forgot-password/reset/{user}', [ForgotPasswordController::class, 'resetPassword'])->name('forgot-pasword.reset');
    Route::post('/forgot-password/exchange/{user}', [ForgotPasswordController::class, 'exchangeMoney'])->name('forgot-pasword.exchange');

});



