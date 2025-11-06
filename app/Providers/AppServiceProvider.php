<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\CourseGroup;
use App\Models\CourseQuiz;
use App\Models\CourseMember;
use App\Observers\CourseGroupObserver;
use App\Observers\CourseQuizObserver;
use App\Observers\CourseMemberObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        CourseGroup::observe(CourseGroupObserver::class);
        CourseQuiz::observe(CourseQuizObserver::class);
        CourseMember::observe(CourseMemberObserver::class);
    }
}
