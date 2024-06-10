<?php

namespace App\Http\Controllers;

use App\Models\TopicImage;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreTopicImageRequest;
use App\Http\Requests\UpdateTopicImageRequest;

class TopicImageController extends Controller
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
    public function store(StoreTopicImageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TopicImage $topicImage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TopicImage $topicImage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTopicImageRequest $request, TopicImage $topicImage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TopicImage $topic_image)
    {
        // $topic = Topic::find($topicId);
        // $image = TopicImage::find($topicImage->id);

        // if (!$image) {
        //     return response()->json(['message' => 'Image not found'], 404);
        // }

        Storage::disk('public')->delete($topic_image->image_url);
        $topic_image->delete();

        return response()->json(['message' => 'Image deleted'], 200);
    }
}
