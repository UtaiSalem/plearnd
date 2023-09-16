<?php

namespace App\Http\Controllers\AIControllers;

use App\Models\Activity;
use App\Models\Post;



class PostController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request and create the post
        $request->validate([
            // Validation rules
        ]);

        $post = new Post();
        // Set the post attributes

        $post->save();

        // Store activity data
        $this->storeActivity($post, 'create');

        // Redirect or return a response
    }

    public function update(Request $request, Post $post)
    {
        // Validate the request and update the post
        $request->validate([
            // Validation rules
        ]);

        // Update the post attributes

        $post->save();

        // Store activity data
        $this->storeActivity($post, 'update');

        // Redirect or return a response
    }

    private function storeActivity(Post $post, string $action)
    {
        $activity = new Activity();
        $activity->user_id = Auth::id();
        $activity->action = $action;
        $activity->description = "Performed {$action} action on post";
        $post->activities()->save($activity);
    }

    public function storePostWithImages(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post = new Post();
        $post->title = $request->input('title');
        $post->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $imagePath = $imageFile->store('post_images', 'public');

                $image = new Image();
                $image->post_id = $post->id;
                $image->filename = $imagePath;
                $image->save();
            }
        }

        return redirect()->back()->with('success', 'Post created successfully.');
    }
}



?>