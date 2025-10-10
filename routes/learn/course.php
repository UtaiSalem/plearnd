<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CoursePostController;
use App\Http\Controllers\CourseQuizController;
use App\Http\Controllers\TopicImageController;
use App\Http\Controllers\CourseGroupController;
use App\Http\Controllers\LessonImageController;
use App\Http\Controllers\CourseLessonController;
use App\Http\Controllers\CourseMemberController;
use App\Http\Controllers\LessonCommentController;
use App\Http\Controllers\QuestionImageController;
use App\Http\Controllers\CourseActivityController;
use App\Http\Controllers\LessonQuestionController;
use App\Http\Controllers\QuestionAnswerController;
use App\Http\Controllers\QuestionOptionController;
use App\Http\Controllers\AssignmentImageController;
use App\Http\Controllers\CoursePostImageController;
use App\Http\Controllers\AssignmentAnswerController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\CourseAssignmentController;
use App\Http\Controllers\CourseAttendanceController;
use App\Http\Controllers\CourseQuizResultController;
use App\Http\Controllers\LessonAssignmentController;
use App\Http\Controllers\CourseGroupMemberController;
use App\Http\Controllers\CoursePostCommentController;
use App\Http\Controllers\CoursePostReactionController;
use App\Http\Controllers\CourseQuizQuestionController;
use App\Http\Controllers\UserAnswerQuestionController;
use App\Http\Controllers\CourseLessonReactionController;
use App\Http\Controllers\LessonCommentReactionController;
use App\Http\Controllers\CoursePostImageCommentController;
use App\Http\Controllers\CoursePostCommentReactionController;
use App\Http\Controllers\CoursePostImageCommentReactionController;


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/courses/{course}/settings', [CourseController::class, 'settings'])->name('course.settings.page.show');
    Route::get('/courses/{course}/basic-info', [CourseController::class, 'basicInfo'])->name('course.basic-info.page.show');
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
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->prefix('/courses/{course}/groups')->group(function () {
    Route::resource('/', CourseGroupController::class);
    // Route::get('/', [CourseGroupController::class, 'show']);
    // Route::post('/', [CourseGroupController::class, 'store'])->name('course.groups.store');
    Route::patch('/{group}', [CourseGroupController::class, 'update'])->name('course.groups.update');
    // Route::delete('/{group}', [CourseGroupController::class, 'destroy'])->name('course.groups.destroy');
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

    Route::post('/{lesson}/likes', [CourseLessonReactionController::class,'toggleLike'])->name('course.lessons.like.toggle');
    Route::post('/{lesson}/dislikes', [CourseLessonReactionController::class,'toggleDislike'])->name('course.lessons.dislike.toggle');

});


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/lessons/{lesson}')->group(function () {
    Route::resource('/comments', LessonCommentController::class);

    Route::post('/comments/{comment}/like', [LessonCommentReactionController::class, 'toggleLike'])->name('lesson.comments.like.toggle');
    Route::post('/comments/{comment}/dislike', [LessonCommentReactionController::class, 'toggleDislike'])->name('lesson.comments.dislike.toggle');

});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/lessons')->group(function () {
    Route::resource('/', LessonController::class);
    Route::resource('/{lesson}/images', LessonImageController::class);
    Route::resource('/{lesson}/assignments', LessonAssignmentController::class);
    Route::resource('/{lesson}/questions', LessonQuestionController::class);
    
    Route::resource('/{lesson}/topics', TopicController::class);
    
});
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('/topics')->group(function () {
    Route::resource('/{topic}/images', TopicImageController::class);
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
    Route::post('/quizzes/{quiz}/efficiency', [CourseQuizController::class, 'calculateSumQuizsEfficiency'])->name('course.member.quiz.efficiency');

    Route::resource('/quizzes/{quiz}/questions', CourseQuizQuestionController::class);
    Route::resource('/quizzes/{quiz}/results', CourseQuizResultController::class);

    // Route::get('/quizzes/get-quizzes', [CourseQuizController::class, 'getQuizzes'])->name('course.quizzes.getQuizzes');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/quizzes/get-quizzes', [CourseQuizController::class, 'getQuizzes'])->name('course.quizzes.getQuizzes');
    Route::post('/quizzes/{quiz}/duplicate', [CourseQuizController::class, 'duplicateQuiz'])->name('course.quizzes.duplicateQuizzes');
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
    Route::get('/{member}/admin/progress', [CourseMemberController::class, 'memberProgress'])->name('course.admin.member.progress.show');

    Route::patch('/{member}/order-number', [CourseMemberController::class, 'updateOrderNumber'])->name('course.member.order-number.update');

});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::resource('/questions', QuestionController::class);
    Route::post('/questions/{question}/duplicate', [QuestionController::class, 'duplicateQuestion']);
    Route::patch('/questions/{question}/set_correct_option', [QuestionController::class, 'set_correct_option'])->name('questions.set_correct_option');

    Route::get('/user/questions', [QuestionController::class, 'getUserQuestions'])->name('user.get.questions');

    Route::resource('/questions/{question}/images', QuestionImageController::class);
    
    Route::resource('/questions/{question}/options', QuestionOptionController::class);
    Route::resource('/options', QuestionOptionController::class);
    
    Route::resource('/questions/{question}/answers', QuestionAnswerController::class);
    // Route::resource('/users/{user}/answers/{answer}/questions', UserAnswerQuestionController::class);
    // Route::resource('/users/{user}/questions/{question}/answers', UserAnswerQuestionController::class);
    Route::resource('/quizs/{quiz}/questions/{question}/answers', UserAnswerQuestionController::class);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/courses/{course}/attendances', [CourseAttendanceController::class, 'index'])->name('attendances.index');
    // Route::post('/attendances', [CourseAttendanceController::class, 'store'])->name('attendances.store');
    // Route::get('/attendances/{attendance}', [CourseAttendanceController::class, 'show'])->name('attendances.show');
    Route::patch('/attendances/{attendance}', [CourseAttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('/attendances/{attendance}', [CourseAttendanceController::class, 'destroy'])->name('attendances.destroy');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/attendances/{attendance}/details', [AttendanceDetailController::class, 'index'])->name('attendances.details.index');
    Route::post('/attendances/{attendance}/details', [AttendanceDetailController::class, 'store'])->name('attendances.details.store');
    Route::patch('/attendances/details/{detail}', [AttendanceDetailController::class, 'update'])->name('attendance.details.update');
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
