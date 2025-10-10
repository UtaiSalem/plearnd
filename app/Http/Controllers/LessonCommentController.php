<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Models\LessonComment;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\LessonCommentResource;

class LessonCommentController extends Controller
{
    //get all comments resource collections for a lesson by lesson id and paginate them
    public function index(Lesson $lesson)
    {
        return LessonCommentResource::collection($lesson->comments()->latest()->paginate(10));
    }

    public function store(Lesson $lesson, Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $newComment = $lesson->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validatedData['content']
        ]);
    
        if($request->hasFile('images')) {
            $lesson_comment_images = $request->file('images');
            foreach ($lesson_comment_images as $image) {
                $fileName = $lesson->id . uniqid() . '.' . $image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/courses/lessons/comments', $image, $fileName);
                $newComment->lessonCommentImages()->create([
                    'lesson_id' => $lesson->id,
                    'filename' => $fileName,
                ]);
            }
        }
    
        $lesson->increment('comment_count', 1);
    
        return response()->json([
            'success' => true,
            'comment' => new LessonCommentResource(LessonComment::find($newComment->id)),
        ]);
    }

    // destroy a comment by comment id
    public function destroy(Lesson $lesson, LessonComment $comment)
    {
        if ($comment->lessonCommentImages->count() > 0) {
            foreach ($comment->lessonCommentImages() as $image) {
                Storage::disk('public')->delete('images/courses/lessons/comments/'. $image->filename);
                $image->delete();
            }
        }
        $comment->likeComment()->detach();
        $comment->dislikeComment()->detach();

        $comment->delete();
        $lesson->decrement('comment_count', 1);

        return response()->json([
           'success' => true,
        ]);
    }

}
