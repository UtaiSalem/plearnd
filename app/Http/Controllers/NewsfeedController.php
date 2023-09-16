<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityResource;

class NewsfeedController extends Controller
{
    public function index()
    {
        $activities = ActivityResource::collection(Activity::latest()->get());

        return Inertia::render('Newsfeed', [
            'activities' => $activities,
            'profilePath' => ''
        ]);
    }
}
