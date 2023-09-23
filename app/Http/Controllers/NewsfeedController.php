<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityResource;

class NewsfeedController extends Controller
{
    public function index()
    {

        $activities = ActivityResource::collection(Activity::latest()->paginate());

        return Inertia::render('Newsfeed', [
            'activities' => $activities,
            'profilePath' => ''
        ]);
    }

    public function show(User $user){

        $myActivities = ActivityResource::collection($user->activities()->latest()->paginate());

        return Inertia::render('Myfeed', [
            'myActivity' => $myActivities,
        ], 200);
    }
}
