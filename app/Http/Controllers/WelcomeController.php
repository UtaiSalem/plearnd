<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\VisitorCounter;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

class WelcomeController extends Controller
{
    public function index(){
        // $users_count = User::count();
        VisitorCounter::first()->increment('counter');
        // $visitorCounter = VisitorCounter::first();
        $visitorCounter = VisitorCounter::pluck('counter')->first();

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,

            'usersCount' => User::count(),
            'coursesCount' => Course::count(),
            'lessonsCount' => Lesson::count(),
            'postsCount' => Post::count(),
            // 'visitorCounter' => $visitorCounter->increment('counter'),
            'visitorCounter' => $visitorCounter,

            'ceo'   => new UserResource(User::find(1)),

        ]);
    }
}