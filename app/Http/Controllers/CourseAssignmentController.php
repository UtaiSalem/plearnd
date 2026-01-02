<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\CourseResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AssignmentResource;


class CourseAssignmentController extends Controller
{
    public function index(Course $course)
    {
        return Inertia::render('Learn/Course/Course', [
            'course' => new CourseResource($course),
            'activeTab' => 'assignments',
        ]);
    }

    public function apiIndex(Course $course)
    {
        // Eager load relationships ที่จำเป็นทั้งหมด
        $assignments = $course->courseAssignments()
            ->with([
                'answers' => function($query) {
                    $query->with(['user', 'assignment.assignmentable']);
                },
                'images'
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => AssignmentResource::collection($assignments),
        ]);
    }
    
    public function store(Course $course, Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'points' => 'required',
            'due_date' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'target_groups' => 'nullable|array',
            'target_groups.*' => 'integer', // Ensure each target group ID is an integer
            'increase_points' => 'nullable',
            'decrease_points' => 'nullable',
            'status' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $assignment = $course->assignments()->create([
            'title'             => $validated['title'],
            'points'            => $validated['points'],
            'graded_score'      => $validated['points'],
            'due_date'          => Carbon::parse($validated['due_date'])->setTimezone('Asia/Bangkok'),
            'start_date'        => Carbon::parse($validated['start_date'])->setTimezone('Asia/Bangkok'),
            'end_date'          => Carbon::parse($validated['end_date'])->setTimezone('Asia/Bangkok'),
            'target_groups'     => $validated['target_groups'],
            'increase_points'   => $validated['increase_points'],
            'decrease_points'   => $validated['decrease_points'],
            'status'            => $validated['status'],
        ]);

        $course->increment('total_score', $validated['points']);
        $course->increment('assignments');

        if($request->hasFile('images')) {
            $images = $request->file('images');
            $fileNames = [];
            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = Storage::disk('public')->putFileAs('images/courses/assignments', $image, $fileName);
                $fileNames[] = $fileName;

                $assignment->images()->create([
                    'image_url' => $fileName
                ]);
            }
        }
        return response()->json([
            'assignment' => new AssignmentResource($assignment),
        ], 200);
    }

    public function update(Course $course, Assignment $assignment, Request $request)
    {
        $course->decrement('total_score', $assignment->points);

        $validated = $request->validate([
            'title' => 'required|string',
            'points' => 'required',
            'due_date' => 'nullable',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'target_groups' => 'nullable|array',
            'target_groups.*' => 'integer', // Ensure each target group ID is an integer
            'increase_points' => 'nullable',
            'decrease_points' => 'nullable',
            'status' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $assignment->update([
            'title'                 => $validated['title'],
            'points'                => $validated['points'],
            'graded_score'          => $validated['points'],
            'due_date'              => Carbon::parse($validated['due_date'])->setTimezone('Asia/Bangkok'),
            'start_date'            => Carbon::parse($validated['start_date'])->setTimezone('Asia/Bangkok'),
            'end_date'              => Carbon::parse($validated['end_date'])->setTimezone('Asia/Bangkok'),
            // 'target_groups'      => json_encode($validated['target_groups'])->toArray(),
            // 'target_groups'         => json_encode($validated['target_groups']),
            'target_groups'         => $validated['target_groups'],
            'increase_points'       => $validated['increase_points'],
            'decrease_points'       => $validated['decrease_points'],
            'status'                => $validated['status'],
        ]);
        
        $course->increment('total_score', $validated['points']);
            
        if($request->hasFile('images')) {
            $images = $request->file('images');

            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = Storage::disk('public')->putFileAs('images/courses/assignments', $image, $fileName);

                $assignment->images()->create([
                    // 'image_url' => $image_url
                    'image_url' => $fileName
                ]);
            }
        }
        return response()->json([
            'assignment' => new AssignmentResource($assignment),
        ], 200);
    }

    public function destroy(Assignment $assignment)
    {
        // $answers = $assignment->answers;
        foreach ( $assignment->answers as $answer) {            
            foreach ($answer->images as $image) {
                Storage::disk('public')->delete('images/courses/assignments/answers/'.$image->filename);
            }
            $answer->images()->delete();
        }

        foreach ($assignment->images as $image) {
            Storage::disk('public')->delete('images/courses/assignments/'.$image->image_url);
        }

        $course = $assignment->assignmentable;
        $course->decrement('total_score', $assignment->points);

        // $answers->delete();
        $assignment->answers()->delete();
        $assignment->images()->delete();
        $assignment->delete();

        return response()->json(['success' => true], 200);
    }
}
