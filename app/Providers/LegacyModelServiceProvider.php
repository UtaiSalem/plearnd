<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Play\Post;
use App\Models\Learn\AcademyPost;
use App\Models\Learn\CoursePost;
use App\Models\Earn\Donate;
use App\Models\Earn\Support;
use App\Models\Earn\DonateRecipient;

class LegacyModelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Create class aliases for old namespaces to maintain compatibility with existing data
        class_alias(Post::class, 'App\Models\Post');
        class_alias(AcademyPost::class, 'App\Models\AcademyPost');
        class_alias(CoursePost::class, 'App\Models\CoursePost');
        class_alias(Donate::class, 'App\Models\Donate');
        class_alias(Support::class, 'App\Models\Support');
        class_alias(DonateRecipient::class, 'App\Models\DonateRecipient');
    }
}
