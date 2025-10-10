<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PostComment;
// use Illuminate\Http\Request;

class PostCommentReactionController extends Controller
{
    public function toggleLikePostComment(PostComment $post_comment)
    {
        try {
            if(auth()->user()->pp > 0){
                
                $post_comment->likedPostComment()->toggle(auth()->id());

                if($post_comment->likedPostComment()->where('user_id', auth()->id())->exists()){
                    $post_comment->increment('likes');
                    auth()->user()->decrement('pp');
                    $post_comment->user()->increment('pp');
                }else{
                    $post_comment->decrement('likes');
                    // auth()->user()->increment('pp');
                    $post_comment->user()->decrement('pp');
                }
                return response()->json([
                    'success' => true,
                ]);
                
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have enough points to like this post \'s comment.  / คุณไม่มีพ้อยท์เพียงพอในการกดถูกใจความคิดเห็นนี้ กรุณาสะสมแต้มเพิ่มเติม',
                ]);
            }
            
            // return redirect()->back();

        } catch (\Throwable $th) {
            throw $th;
        }

    }

    public function toggleDislikePostComment(PostComment $post_comment)
    {
        try {
            if(auth()->user()->pp > 3){
                
                $post_comment->dislikedPostComment()->toggle(auth()->id());

                if($post_comment->dislikedPostComment()->where('user_id', auth()->id())->exists()){
                    $post_comment->increment('dislikes');
                    auth()->user()->decrement('pp');
                    $post_comment->user()->decrement('pp');
                    User::find(1)->increment('pp', 2);
                }else{
                    $post_comment->decrement('dislikes');
                    // auth()->user()->decrement('pp');
                    // $post_comment->user()->increment('pp');
                };

                return response()->json([
                    'success' => true,
                ]);

            }else{
                // return redirect()->back()->with('error', 'You do not have enough PP to like this post');
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have enough points to dislike this post\'s comment. / คุณไม่มีพ้อยท์เพียงพอในการกดไม่ถูกใจความคิดเห็นนี้ กรุณาสะสมแต้มเพิ่มเติม',
                ]);
            }
            
            // return redirect()->back();

        } catch (\Throwable $th) {
            throw $th;
        }

    }
}
