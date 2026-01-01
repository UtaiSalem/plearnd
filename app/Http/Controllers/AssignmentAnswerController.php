<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\CourseMember;
use Illuminate\Http\Request;
use App\Models\AssignmentAnswer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AssignmentAnswerResource;

class AssignmentAnswerController extends Controller
{

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
            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = Storage::disk('public')->putFileAs('images/courses/assignments/answers/', $image, $fileName);
                $answer->images()->create([
                    'filename' => $fileName
                ]);
            }
        }

        // Load relationships for the resource
        $answer->load(['user', 'assignment.assignmentable', 'images']);

        return response()->json([
            'success' => true,
            'newAnswer' => new AssignmentAnswerResource($answer),
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Assignment $assignment, AssignmentAnswer $answer, Request $request)
    {
        // Verify that the user owns this answer or is an admin
        $this->authorize('update', $answer);

        try {
            DB::beginTransaction();

            // Update content
            $answer->update([
                'content' => $request->content,
            ]);

            // Handle existing images - delete ones not in the existing_images array
            $existingImageIds = $request->input('existing_images', []);
            $imagesToDelete = $answer->images()->whereNotIn('id', $existingImageIds)->get();
            
            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete('images/courses/assignments/answers/' . $image->filename);
                $image->delete();
            }

            // Handle new images upload
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $image) {
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $image_url = Storage::disk('public')->putFileAs('images/courses/assignments/answers/', $image, $fileName);
                    $answer->images()->create([
                        'filename' => $fileName
                    ]);
                }
            }

            // Reset points if content changed (optional - depends on your business logic)
            if ($answer->wasChanged('content')) {
                $oldPoints = $answer->points ?? 0;
                if ($oldPoints > 0) {
                    $courseMember = CourseMember::where('course_id', $request->course_id)
                        ->where('user_id', $answer->user_id)
                        ->first();
                    if ($courseMember) {
                        $courseMember->achieved_score -= $oldPoints;
                        $courseMember->save();
                    }
                    $answer->update(['points' => 0]);
                }
            }

            DB::commit();

            // Load relationships for the resource
            $answer = $answer->fresh(['user', 'assignment.assignmentable', 'images']);

            return response()->json([
                'success' => true,
                'answer' => new AssignmentAnswerResource($answer),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating assignment answer: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update assignment answer'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assignment $assignment, AssignmentAnswer $answer, Request $request)
    {
        try {
            DB::beginTransaction();
            
            $courseMember = CourseMember::where('course_id', $request->course_id)
                ->where('user_id', $answer->user_id)
                ->firstOrFail();
            
            $courseMember->achieved_score -= $answer->points;
            $courseMember->save();
    
            foreach ($answer->images as $image) {
                Storage::disk('public')->delete('images/courses/assignments/answers/'.$image->filename);
            }
    
            $answer->images()->delete();
            $answer->delete();
    
            DB::commit();
            
            return response()->json([
                'success' => true,
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting assignment answer: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete assignment answer'], 500);
        }
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
