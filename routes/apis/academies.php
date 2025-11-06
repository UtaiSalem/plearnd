<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AcademyController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->prefix('academies')->group(function () {
    Route::get('/', [AcademyController::class, 'index'])->name('academies');
    Route::get('/create', [AcademyController::class, 'create'])->name('academy.create');
});