<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Models\AssignmentAnswer;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AssignmentAnswerResource;

class AssignmentAnswerController extends Controller
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
    public function store(Assignment $assignment, Request $request)
    {
        $answer = $assignment->answers()->create([
            'user_id'   => auth()->id(),
            'content' => $request->content,
        ]);

        if($request->hasFile('images')) {
            $images = $request->file('images');
            $fileNames = [];
            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = Storage::disk('public')->putFileAs('images/topics/assignments/answers', $image, $fileName);
                $fileNames[] = $fileName;

                $answer->images()->create([
                    'image_url' => $image_url
                ]);
            }
        }
        return response()->json([
            'success' => true,
            'newAnswer' => new AssignmentAnswerResource($answer),
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(AssignmentAnswer $assignmentAnswer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssignmentAnswer $assignmentAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Assignment $assignment, AssignmentAnswer $answer, Request $request, )
    {
        $answer->update([
            'points' => $request->points
        ]);

        return response()->json([
            'success' => true,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment, AssignmentAnswer $answer)
    {
        foreach ($answer->images as $image) {
            Storage::disk('public')->delete($image->image_url);
        }
        $answer->images()->delete();
        $answer->delete();
    }
}
