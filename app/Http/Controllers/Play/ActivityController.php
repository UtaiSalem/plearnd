<?php

namespace App\Http\Controllers\Play;

use App\Models\Shared\User;
use App\Models\Play\Activity;
use App\Http\Resources\Play\ActivityResource;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;

class ActivityController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $activities = Activity::all()->paginate(15);
        $activities = ActivityResource::collection(Activity::latest()->paginate());

        return response()->json([
            'success'    => true,
            'activities' => $activities,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request)
    {

            // $activity = new Activity();
            // $activity->user_id = Auth::id();
            // $activity->action = 'like';
            // $activity->activityable()->associate($post);
            // $activity->save();

            // dd($activity);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // $activities = ActivityResource::collection(Activity::where('')->latest()->paginate());
        $activities = ActivityResource::collection($user->activities()->latest()->paginate());

        return response()->json([
            'success'    => true,
            'activities' => $activities,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        $activity->activityable()->delete();
        $activity->delete();
        
        auth()->user()->decrement('pp', 1);

        return response()->json([
            'success' => true,
        ], 200);
    }


    public function fetchActivities()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Fetch activities with conditions
        $activities = Activity::where(function ($query) use ($user) {
            // Include public activities
            $query->where('privacy', 'public');

            // Include private activities if the poster is a friend of the authenticated user
            $query->orWhere(function ($query) use ($user) {
                $query->where('privacy', 'private')
                      ->whereIn('poster_id', $user->friends()->pluck('id'));
            });
        })
        ->where(function ($query) use ($user) {
            // Filter activities for the authenticated user
            $query->where('user_id', $user->id);
        })
        ->with(['subject' => function ($query) {
            // Eager load the associated post or poll
            $query->with(['post', 'poll']);
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        // Return the activities
        return $activities;
    }
}
