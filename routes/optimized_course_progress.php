<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OptimizedCourseProgressController;

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Optimized course progress routes
    Route::get('/courses/{course}/progress/optimized', [OptimizedCourseProgressController::class, 'index'])->name('courses.progress.optimized');
    Route::get('/api/courses/{course}/members/paginated', [OptimizedCourseProgressController::class, 'getPaginatedMembers'])->name('api.courses.members.paginated');
    Route::get('/api/courses/{course}/export/excel', [OptimizedCourseProgressController::class, 'exportToExcel'])->name('api.courses.export.excel');
    
    // Member update routes
    Route::patch('/courses/{course}/members/{member}/bonus-points', [OptimizedCourseProgressController::class, 'updateBonusPoints'])->name('courses.members.bonus-points.update');
    Route::patch('/courses/{course}/members/{member}/grade_progress', [OptimizedCourseProgressController::class, 'updateGradeProgress'])->name('courses.members.grade-progress.update');
    Route::patch('/courses/{course}/members/{member}/order-number', [OptimizedCourseProgressController::class, 'updateOrderNumber'])->name('courses.members.order-number.update');
    Route::patch('/courses/{course}/members/{member}/notes_comments', [OptimizedCourseProgressController::class, 'updateNotesComments'])->name('courses.members.notes-comments.update');
    Route::post('/courses/{course}/members/{member}/set-active-group-tab', [OptimizedCourseProgressController::class, 'setActiveGroupTab'])->name('courses.members.set-active-group-tab');
});