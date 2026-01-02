<?php

/**
 * Course SPA API Routes
 * 
 * JSON endpoints for Course SPA tab data loading.
 * Uses session-based auth (web middleware).
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CourseController,
    CourseLessonController,
    CourseMemberController,
    CourseAssignmentController,
    CourseAttendanceController,
    CourseQuizController,
    CourseActivityController
};
use App\Http\Controllers\Learn\Course\groups\CourseGroupController;

Route::middleware('auth')->prefix('api/courses/{course}')->group(function () {
    Route::get('/', [CourseController::class, 'apiShow'])->name('api.course.show');
    Route::get('/feeds', [CourseActivityController::class, 'getActivities'])->name('api.course.feeds');
    Route::get('/lessons', [CourseLessonController::class, 'apiIndex'])->name('api.course.lessons');
    Route::get('/members', [CourseMemberController::class, 'apiIndex'])->name('api.course.members');
    Route::get('/assignments', [CourseAssignmentController::class, 'apiIndex'])->name('api.course.assignments');
    Route::get('/attendances', [CourseAttendanceController::class, 'apiIndex'])->name('api.course.attendances');
    Route::get('/quizzes', [CourseQuizController::class, 'apiIndex'])->name('api.course.quizzes');
    Route::get('/groups', [CourseGroupController::class, 'index'])->name('api.course.groups');
    Route::get('/progress', [CourseController::class, 'apiProgress'])->name('api.course.progress');
});
