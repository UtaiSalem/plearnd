<?php

namespace App\Http\Controllers\AIControllers;

use App\Models\Post;
use App\Models\Poll;

class ActivityController extends Controller
{
    public function index()
    {
        // Retrieve activities associated with both posts and polls
        $posts = Post::with('activities')->get();
        $polls = Poll::with('activities')->get();

        // Merge the activities into a single collection
        $activities = $posts->flatMap->activities->concat($polls->flatMap->activities);

        return $activities;
    }

    public function index2()
    {
        $activities = Activity::whereIn('activityable_type', [Post::class, Poll::class])
            ->with('activityable')
            ->orderByDesc('created_at')
            ->get();

        return ActivityResource::collection($activities);
    }
}

?>