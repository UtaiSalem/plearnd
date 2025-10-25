<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Play\Game\GameController;
use App\Http\Controllers\Play\Game\MentalMathController;


    
// Game Dashboard
Route::get('/play/games', [GameController::class, 'index'])->name('game.dashboard');

Route::get('/play/games/guessing-number', [GameController::class, 'guessing_number_game'])->name('game.quessing_number');
Route::get('/play/games/xo', [GameController::class, 'xo_game'])->name('game.xo');
Route::get('/play/games/snake', [GameController::class, 'snake_game'])->name('game.snake');

// API routes for Mental Math Game
Route::prefix('play/games/mental-math')->group(function () {
    Route::get('/', [MentalMathController::class, 'index'])->name('mental-math');
    Route::get('/api/question', [MentalMathController::class, 'generateQuestion']);
    Route::post('/api/score', [MentalMathController::class, 'saveScore']);
    Route::get('/api/leaderboard', [MentalMathController::class, 'getLeaderboard']);
    Route::get('/api/stats', [MentalMathController::class, 'getPlayerStats']);
    Route::get('/api/daily-challenge', [MentalMathController::class, 'getDailyChallenge']);
});