<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Course::class => \App\Policies\CoursePolicy::class,
        \App\Models\CourseMember::class => \App\Policies\CourseMemberPolicy::class,
        \App\Models\AssignmentAnswer::class => \App\Policies\AssignmentAnswerPolicy::class,
        \App\Models\UserAnswerQuestion::class => \App\Policies\UserAnswerQuestionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
