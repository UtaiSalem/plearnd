<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\CourseMember;
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
        
        $answer = $assignment->answers()->where('user_id', auth()->id())->first();
        if( $answer ){
            $oldPoints = $answer->points ?? 0;
            $courseMember = CourseMember::where('course_id', $request->course_id)->where('user_id', $answer->user_id)->first();
            $courseMember->achieved_score -= $oldPoints;

            $answer->update([
                'content' => $request->content, 
                'points' => 0
            ]);
            $answer->images()->delete();
        }else{
            $answer = $assignment->answers()->create([
                'user_id' => auth()->id(),
                'content' => $request->content, 
                'points' => 0
            ]);

        }

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
    public function update(Assignment $assignment, AssignmentAnswer $answer, Request $request)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment, AssignmentAnswer $answer, Request $request)
    {
        $courseMember = CourseMember::where('course_id', $request->course_id)->where('user_id', $answer->user_id)->first();
        $courseMember->achieved_score -= $answer->points;
        $courseMember->save();

        foreach ($answer->images as $image) {
            Storage::disk('public')->delete($image->image_url);
        }
        
        $answer->images()->delete();
        $answer->delete();
    }

    public function setAnswerPoints(Assignment $assignment, AssignmentAnswer $answer, Request $request)
    {
        $courseMember = CourseMember::where('course_id', $request->course_id)->where('user_id', $answer->user_id)->first();
        $oldAnswer = $assignment->answers()->where('user_id', $answer->user_id)->orderBy('updated_at', 'desc')->get();
        if (count($oldAnswer) > 1) {
            $oldPoints = $oldAnswer[0]->points;
            $newPoints = $request->points ?? 0;
        }else{
            $oldPoints = $answer->points ?? 0;
            $newPoints = $request->points ?? 0;      
        }

        $courseMember->achieved_score -= $oldPoints;
        $courseMember->achieved_score += $newPoints;
        $courseMember->save();

        $answer->update([
            'points' => $request->points
        ]);

        return response()->json([
            'success' => true,
        ], 200);
    }
}
