<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePost;
use Illuminate\Http\Request;
use App\Models\CoursePostComment;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CoursePostCommentResource;

class CoursePostCommentController extends Controller
{
    public function index(Course $course, CoursePost $post)
    {
        try {
            // $comments = $post->post_comments()->latest()->paginate();
    
            // $post_comments = $this->getPostComments($post);
            $post_comments = CoursePostComment::where('course_post_id', $post->id)->latest()->paginate();
    
            return response()->json([
                'success' => true,
                // 'comments' => CoursePostCommentResource::collection($comments),
                'comments' => CoursePostCommentResource::collection($post_comments),
                'post_comments' => $post_comments,
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    
    /**
     * Store a newly created comment in storage.
     *
     * @param  \App\Http\Requests\StoreCommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Course $course, CoursePost $post, Request $request)
    {
        try {

            $validatedData = $request->validate([
                'content' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $newComment = $post->post_comments()->create([
                'user_id' => auth()->id(),
                'content' => $validatedData['content']
            ]);

            if($request->hasFile('images')) {
                $post_comment_images = $request->file('images');
                foreach ($post_comment_images as $image) {
                    $fileName = $post->id . uniqid() . '.' . $image->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('images/courses/posts/comments', $image, $fileName);

                    $newComment->postCommentImages()->create([
                        'course_post_id' => $post->id,
                        'filename' => $fileName,
                    ]);
                }
            }

            $post->increment('comments', 1);

            return response()->json([
                'success' => true,
                'comment' => new CoursePostCommentResource($newComment),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param  \App\Models\CoursePostComment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course, CoursePost $post, CoursePostComment $comment)
    {
        try {
            
            $post_comment_images = $comment->postCommentImages;
            foreach ($post_comment_images as $image) {
                Storage::disk('public')->delete('images/courses/posts/comments/' . $image->filename);
                $image->delete();
            }

            $comment->delete();

            $post->decrement('comments', 1);

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 404);
        }
    }
}
