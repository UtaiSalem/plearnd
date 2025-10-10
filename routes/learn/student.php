<?php

use App\Http\Controllers\Learn\StudentCardController;

Route::prefix('student')->group(function () {
    Route::get('card', [StudentCardController::class, 'index'])->name('student-card');
    Route::put('card/{student}', [StudentCardController::class, 'update'])->name('student-card.update');
    
    Route::delete('card/{student}/photo', [StudentCardController::class, 'destroyPhoto'])->name('student-card.photo.destroy');
    
    Route::get('card/{level}/{room}', [StudentCardController::class, 'getStudentByRoom'])->name('student-card.get-by-room');

    Route::patch('upload-photo/{student_card}', [StudentCardController::class, 'updateImage'])->name('student-card.update-image');

    Route::patch('update-code/{student_card}', [StudentCardController::class, 'updateStudentID'])->name('student-card.update-student-id');
    Route::patch('update-name-th/{student_card}', [StudentCardController::class, 'updateStudentNameTh'])->name('student-card.update-student-name-th');
    Route::patch('update-name-en/{student_card}', [StudentCardController::class, 'updateStudentNameEn'])->name('student-card.update-student-name-en');

});

Route::prefix('admin')->group(function () {
    Route::get('/student/card', [StudentCardController::class, 'adminIndex'])->name('admin.student-card');
    Route::get('student/card/{level}/{room}', [StudentCardController::class, 'adminGetStudentByRoom'])->name('admin.student-card.get-by-room');
});