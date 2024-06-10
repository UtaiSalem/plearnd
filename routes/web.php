<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\PostController;

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\AcademyController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\NewsfeedController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\PostImageController;
use App\Http\Controllers\SuggesterController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CoursePostController;
use App\Http\Controllers\CourseQuizController;
use App\Http\Controllers\MentalMathController;
use App\Http\Controllers\AcademyPostController;
use App\Http\Controllers\CourseGroupController;
use App\Http\Controllers\LessonImageController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CourseLessonController;
use App\Http\Controllers\CourseMemberController;
use App\Http\Controllers\PostReactionController;
use App\Http\Controllers\AcademyCourseController;
use App\Http\Controllers\AcademyMemberController;
use App\Http\Controllers\CourseActivityController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LessonQuestionController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuestionOptionController;
use App\Http\Controllers\AcademyActivityController;
use App\Http\Controllers\AssignmentImageController;
use App\Http\Controllers\CoursePostImageController;
use App\Http\Controllers\AssignmentAnswerController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\CourseAssignmentController;
use App\Http\Controllers\CourseAttendanceController;
use App\Http\Controllers\CourseQuizResultController;
use App\Http\Controllers\LessonAssignmentController;
use App\Http\Controllers\Auth\ProviderAuthController;
use App\Http\Controllers\CourseGroupMemberController;
use App\Http\Controllers\CoursePostCommentController;
use App\Http\Controllers\PostImageReactionController;
use App\Http\Controllers\CoursePostReactionController;
use App\Http\Controllers\CourseQuizQuestionController;
use App\Http\Controllers\UserAnswerQuestionController;
use App\Http\Controllers\PostCommentReactionController;
use App\Http\Controllers\CoursePostImageCommentController;
use App\Http\Controllers\PostImageCommentReactionController;
use App\Http\Controllers\CoursePostCommentReactionController;
use App\Http\Controllers\CoursePostImageCommentReactionController;


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


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/academies')->group(function () {
    Route::get('/', [AcademyController::class, 'index'])->name('academies');
    Route::post('/', [AcademyController::class, 'store'])->name('academies.store');
    Route::get('/create', [AcademyController::class, 'create'])->name('academy.create');
    Route::get('/{academy:name}', [AcademyController::class, 'show'])->name('academy.show');

    Route::get('/{academy:name}/feeds', [AcademyActivityController::class, 'index'])->name('academy.feeds');

    Route::get('/{academy:name}/courses', [AcademyCourseController::class, 'index'])->name('academy.courses.index');
    Route::get('/{academy:name}/courses/create', [AcademyCourseController::class, 'create'])->name('academy.courses.create');
    Route::post('/{academy}/courses', [AcademyCourseController::class, 'store'])->name('academy.courses.store');

    Route::patch('/{academy}/update', [AcademyController::class, 'update'])->name('academy.update');

    // Route::get('/{academy}/members', [AcademyMemberController::class, 'index'])->name('academy.members');
    Route::get('/{academy}/members', [AcademyMemberController::class, 'index'])->name('academy.members');
    Route::post('/{academy}/members', [AcademyMemberController::class, 'storemember']);
    Route::post('/{academy}/unmembers', [AcademyMemberController::class, 'unmember']);

    Route::get('/{academy}/posts/{post}', [AcademyPostController::class, 'show'])->name('academy_post.show');

});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/api/academies')->group(function () {
    Route::get('/', [AcademyController::class, 'getMoreAcademies'])->name('api.academies.all');
    Route::get('/users/{user}/my-academies', [AcademyController::class, 'getMyAcademies'])->name('api.academies.my-academies');
    Route::get('/users/{user}/membered-academies', [AcademyController::class, 'getAuthMemberedAcademies'])->name('api.academies.membered');
    Route::get('/all-academies', [AcademyController::class, 'getAllAcademies'])->name('api.academies.all-academies');

    Route::get('/{academy}/courses', [AcademyCourseController::class, 'getAcademyCourses'])->name('api.academy.courses.getAcademyCourses');

    Route::post('/{academy}/posts', [AcademyPostController::class, 'store'])->name('api.academy.posts.store');
    
    Route::get('/{academy}/activities', [AcademyActivityController::class, 'getActivities'])->name('api.academy.activities.getActivities');
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
    
    Route::get('/users/{user}', [CourseController::class, 'getUserCourses'])->name('user.courses');
    Route::get('/users/{user}/member', [CourseController::class, 'getAuthMemberCourses'])->name('auth.member.courses');

    // Route::get('/{course}/groups', [CourseGroupController::class, 'show']);
    // Route::post('/{course}/groups', [CourseGroupController::class, 'store'])->name('course.groups.store');
    // Route::patch('/{course}/groups/{group}', [CourseGroupController::class, 'update'])->name('course.groups.update');
    // Route::delete('/{course}/groups/{group}', [CourseGroupController::class, 'destroy'])->name('course.groups.destroy');

    Route::post('/{course}/groups/{group}/members', [CourseGroupMemberController::class, 'store']);
    Route::delete('/{course}/members/groups/{group}', [CourseGroupMemberController::class, 'unMemberGroup']);
    Route::delete('/{course}/groups/{group}/members/{member}', [CourseGroupMemberController::class, 'unMemberGroup']);

    Route::resource('/{course}/quizzes/{quiz}/questions', CourseQuizQuestionController::class);
    Route::resource('/{course}/quizzes/{quiz}/results', CourseQuizResultController::class);

    Route::get('/{course}/groups/{group}/attendances', [CourseAttendanceController::class, 'getCourseGroupAttendances'])->name('course.groups.attendances');
    Route::post('/{course}/groups/{group}/attendances', [CourseAttendanceController::class, 'store'])->name('course.groups.attendances.store');

    Route::get('/{course}/feeds', [CourseActivityController::class, 'index'])->name('course.feeds');
    Route::get('/{course}/feeds/get-more-activities', [CourseActivityController::class, 'getActivities'])->name('course.feeds.getMoresActivities');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/api/courses')->group(function () {
    Route::get('/', [CourseController::class, 'getMoreCourses'])->name('api.courses.all');
    Route::get('/users/{user}', [CourseController::class, 'getMyCourses'])->name('api.courses.user-courses');
    Route::get('/users/{user}/my-courses', [CourseController::class, 'getMyCourses'])->name('api.courses.my-courses');
    Route::get('/users/{user}/membered', [CourseController::class, 'getAuthMemberedCourses'])->name('api.courses.member');

    // Route::resource('/{course}/lessons', LessonController::class);
});


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
    Route::get('/courses/{course}/settings', [CourseController::class, 'settings'])->name('course.settings.page.show');
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
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::post('/courses/posts/comments/{comment}/like', [CoursePostCommentReactionController::class, 'toggleLikeComment'])->name('toggle.like.course_post_comment');
    Route::post('/courses/posts/comments/{comment}/dislike', [CoursePostCommentReactionController::class, 'toggleDislikeComment'])->name('toggle.dislike.course_post_comment');
    
    Route::post('/courses/posts/images/comments/{comment}/like', [CoursePostImageCommentReactionController::class, 'toggleLikeComment'])->name('toggle.like.course_post_image_comment');
    Route::post('/courses/posts/images/comments/{comment}/dislike', [CoursePostImageCommentReactionController::class, 'toggleDislikeComment'])->name('toggle.dislike.course_post_image_comment');
});

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


// Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'plearnd_admin'])->prefix('/plearnd-admin')->group(function () {
//     Route::get('/donates', [DonateController::class, 'index'])->name('plearnd-admin.index');
// });
Route::get('/supports/donates/create', [DonateController::class, 'create'])->name('support.donate.create');
Route::post('/supports/donates', [DonateController::class, 'store'])->name('support.donate.store');
Route::get('/supports/donates/donor/{user:personal_code}', [DonateController::class, 'getDonor'])->name('donate.get-donor');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'plearnd_admin'])->prefix('/plearnd-admin/supports/donates')->group(function () {
    Route::get('/', [DonateController::class, 'index'])->name('admin.support.donate.index');
    Route::patch('/{donate}/recieve', [DonateController::class, 'recieve'])->name('admin.support.donate.recieve');
    Route::patch('/{donate}/reject', [DonateController::class, 'reject'])->name('admin.support.donate.reject');

});
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/donates/{donate}/get-donate', [DonateController::class, 'getDonate'])->name('donate.get-donate');
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
