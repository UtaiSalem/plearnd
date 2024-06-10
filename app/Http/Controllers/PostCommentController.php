<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;
use App\Models\PostCommentImage;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostCommentResource;

class PostCommentController extends Controller
{
    public function index(Post $post)
    {
        $postComments = PostComment::where('post_id', $post->id)->get();
        $postCommentResource = PostCommentResource::collection($postComments);

        // $post->increment('comments', 1);

        return response()->json([
            'success' => true,
            'comments' => $postCommentResource

        ], 200);
    }

    /**
     * Store a newly created comment in storage.
     *
     * @param  \App\Http\Requests\StoreCommentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Post $post, Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $newComment = $post->postComments()->create([
            'user_id' => auth()->id(),
            'content' => $validatedData['content']
        ]);

        if($request->hasFile('images')) {
            $post_comment_images = $request->file('images');
            foreach ($post_comment_images as $image) {
                $fileName = $post->id . uniqid() . '.' . $image->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('images/posts/comments', $image, $fileName);
                // PostCommentImage::create([
                //     'post_id' => $post->id,
                //     'post_comment_id' => $newComment->id,
                //     'filename' => $fileName,
                // ]);
                $newComment->postCommentImages()->create([
                    'post_id' => $post->id,
                    'filename' => $fileName,
                ]);
            }
        }

        $post->increment('comments', 1);

        return response()->json([
            'success' => true,
            'comment' => new PostCommentResource(PostComment::find($newComment->id)),
        ]);
    }

    /**
     * Update the specified comment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->content = $request->input('content');
        // Update other attributes for the comment as needed

        $comment->save();

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ]);
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post, PostComment $comment)
    {
        // $comment = Comment::findOrFail($id);
        $comment_images = $comment->postCommentImages;
        if ($comment_images->count() > 0) {
            foreach ($comment_images as $image) {
                Storage::disk('public')->delete('images/posts/comments/' . $image->filename);
                $image->delete();
            }
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}
