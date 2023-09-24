<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostRequest;
// use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StorePostRequest $request)
    {
        $validatedData = $request->validated();
        // $imagePath = $request->file('image')->store('post_images', 'public');

        $post = new Post();
        $post->user_id = auth()->user()->id;
        $post->title = $validatedData['title'];
        $post->content = $validatedData['content'];
        // $post->image = $imagePath;
        $post->save();

        $activity = new Activity();
        $activity->user_id = $post->user_id;
        $activity->activity_type = 'เขียนโพสต์';
        $activity->activityable()->associate($post);
        $activity->save();

        auth()->user()->decrement('pp', 1);
        // return redirect()->back()->with('success', 'Post created successfully.');

        return to_route('newsfeed');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $activity = $post->activities;

        $post->delete();
        $activity->delete();

        auth()->user()->decrement('pp', 1);

        return response()->json([
            'success' => true,
        ], 200);

    }
}
