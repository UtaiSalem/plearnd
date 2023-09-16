<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;
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

        // $comment = new PostComment();
        // $comment->post_id = $request->input('post_id');
        // $comment->user_id = auth()->id();
        // $comment->content = $request->input('content');

        // Set other attributes for the comment as needed

        // $comment->save();
        
        $newComment = $post->postComments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);
        $post->increment('comments', 1);
        // $newComment = PostComments::create([
        //     'user_id' => auth()->id(),
        //     'post_id' => $request->post_id,
        //     'content' => $request->content
        // ]);
        return response()->json([
            'success' => true,
            'comment' => new PostCommentResource(PostComment::find($newComment->id)),
            // 'comment' => PostComment::find($newComment->id),
            // 'comment' => $newComment
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
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
