<?php

namespace App\Http\Controllers\Play;

use App\Models\Play\Post;
use Inertia\Inertia;
use App\Models\Play\Activity;
use App\Models\Play\PostImage;
use Illuminate\Http\Request;
use App\Http\Resources\Play\PostResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Play\ActivityResource;
// use App\Http\Requests\UpdatePostRequest;

class PostController extends \App\Http\Controllers\Controller
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
        try {

            if (auth()->user()->pp < 180) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'You need at least 180 points to create a post. / คุณมีแต้มสะสมไม่พอสำหรับการโพสต์ กรุณาสะสมแต้มสะสมอย่างน้อย 180 แต้ม',
                ], 403);
            }

            $validatedData = $request->validated();
        
            $validatedData['content'] = preg_replace('/\s+/', ' ', $validatedData['content']);
            
            $content = $validatedData['content'] ?? '';
            $hashtags = $this->extractHashtags($content);
    
            $post = new Post();
            $post->user_id = auth()->user()->id;
            $post->content = $validatedData['content'] ?? '';
            $post->privacy_settings = $validatedData['privacy_settings'];
            $post->status = 1;
            $post->hashtags = json_encode($hashtags);
            $post->save();
            
            if($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $image) {
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('images/posts', $image, $fileName);
                    $post->postImages()->create([
                        'filename' => $fileName,
                    ]);
                }
            }
    
            $activity = new Activity();
            $activity->user_id = $post->user_id;
            $activity->activity_type = 'createpost';
            $activity->activityable()->associate($post);
            $activity->save();
    
            auth()->user()->decrement('pp',180);
    
            // return to_route('newsfeed');
            return response()->json([
                'success'   => true,
                'activity'  => new ActivityResource($activity),
            ], 201);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return Inertia::render('Post', [
            'post_id' => $post->id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return Inertia::render('EditPost', [
            'post_id' => $post->id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validatedData = $request->validate([
            'content'           => 'required|string|max:5000',
            'privacy_settings'  => 'required|integer|in:1,2,3',
            'images.*'          => 'image|mimes:jpeg,png,jpg,gif,svg|max:4048|nullable',
        ]);

        $content = $validatedData['content'];
        $hashtags = $this->extractHashtags($content);

        $post->content = $validatedData['content'];
        $post->privacy_settings = $validatedData['privacy_settings'];
        $post->hashtags = json_encode($hashtags);
        $post->save();

        if($request->hasFile('images')) {
            $post_images = $request->file('images');
            foreach ($post_images as $image) {
                $fileName = $post->id . uniqid() . '.' . $image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/posts', $image, $fileName);
                PostImage::create([
                    'post_id' => $post->id,
                    'filename' => $fileName,
                ]);
            }
        }

        $post->refresh();

        return response()->json([
            'success'   => true,
            'post'      => new PostResource($post),
            'request'   => $request->all(),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {

        // $activity = $post->activities;

        //get post's images
        $post_images = $post->postImages;
        //loop each images
        if ($post_images->count() > 0) {
            //delete images
            foreach ($post_images as $image) {
                //get image's comments
                // $image_comments = $image->comments;
                //loop each comments
                // foreach ($image_comments as $image_comment) {
                    //delete comment
                    // $image_comment->delete();
                // }
                //delete image from storage
                Storage::disk('public')->delete('images/posts/' . $image->filename);
                //delete image from database
                $image->delete();
            }
        }

        //get post's comments
        $comments = $post->postComments;
        //loop each comments
        if ($comments->count() > 0) {
            //delete comments
            foreach ($comments as $comment) {
                //delete comment
                $comment->delete();
            }
        }

        // $activity[0]->delete();
        $post->activity()->delete();
        $post->delete();

        return response()->json([
            'success' => true,
        ], 200);

    }

    /**
     * Extract hashtags from post content.
     *
     * @param string $content The post content.
     * @return array An array of extracted hashtags.
     */
    private function extractHashtags($content)
    {
        // Regular expression to match hashtags (e.g., #laravel, #webdev)
        $pattern = '/#\w+/';

        preg_match_all($pattern, $content, $matches);

        // Extract hashtags from the matches
        $hashtags = [];
        foreach ($matches[0] as $match) {
            // Remove the '#' symbol
            $tag = str_replace('#', '', $match);
            $hashtags[] = $tag;
        }

        return $hashtags;
    }

    public function getPostActivity(Post $post)
    {
        $post->increment('views');

        $activity = $post->activity;
        // $activityResource = ActivityResource::collection($activity);
        $activityResource = new ActivityResource($activity);

        return response()->json([
            'success'       => true,
            'activity'      => $activityResource,
        ], 200);
    }
}
