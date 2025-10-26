<?php

namespace App\Http\Controllers\Play\Game;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GameController extends Controller
{
    public function guessing_number_game()
    {
        return Inertia::render('Play/Games/GuessingNumberGame');
    }
    public function xo_game()
    {
        return Inertia::render('Play/Games/XOGame');
    }
    public function snake_game()
    {
        return Inertia::render('Play/Games/SnakeGame');
    }
    
    public function mental_match_game()
    {
        return Inertia::render('Play/Games/MentalMatch');
    }
}
