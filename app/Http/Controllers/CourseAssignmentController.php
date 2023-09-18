<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AssignmentResource;

class CourseAssignmentController extends Controller
{
    public function store(Course $course, Request $request)
    {
        $assignment = $course->assignments()->create([
            'title' => $request->title,
            'points' => $request->points,
        ]);

        if($request->hasFile('images')) {
            $images = $request->file('images');
            $fileNames = [];
            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = Storage::disk('public')->putFileAs('images/courses/assignments', $image, $fileName);
                $fileNames[] = $fileName;

                $assignment->images()->create([
                    'image_url' => $image_url
                ]);
            }
        }
        return response()->json([
            'assignment' => new AssignmentResource($assignment),
        ], 200);
    }

    public function update(Course $course, Assignment $assignment, Request $request)
    {
        $assignment->update([
            'title' => $request->title,
            'points' => $request->points,
        ]);

        if($request->hasFile('images')) {
            $images = $request->file('images');

            foreach ($images as $image) {
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_url = Storage::disk('public')->putFileAs('images/courses/assignments', $image, $fileName);

                $assignment->images()->create([
                    'image_url' => $image_url
                ]);
            }
        }
        return response()->json([
            'assignment' => new AssignmentResource($assignment),
        ], 200);
    }
}
