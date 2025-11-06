<?php

namespace App\Http\Controllers\Play;

use Illuminate\Http\Request;
use App\Models\Play\PostImageComment;

class PostImageCommentReactionController extends \App\Http\Controllers\Controller
{
    public function toggleLikePostImageComment(PostImageComment $post_image_comment)
    {
        try {
            if(auth()->user()->pp > 0){
                
                $toggled = $post_image_comment->liked()->toggle(auth()->id());

                if(empty($toggled['detached'])){
                    $post_image_comment->increment('likes');
                    auth()->user()->decrement('pp');
                    $post_image_comment->user()->increment('pp');
                }else{
                    $post_image_comment->decrement('likes');
                    auth()->user()->decrement('pp');
                }

                return response()->json([
                    'success' => true,
                ]);
                
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have enough points to like this image \'s comment.  / คุณไม่มีพ้อยท์เพียงพอในการกดถูกใจความคิดเห็นนี้ กรุณาสะสมแต้มเพิ่มเติม',
                ]);
            }

        } catch (\Throwable $th) {
            throw $th;
        }

    }

    public function toggleDislikePostImageComment(PostImageComment $post_image_comment)
    {
        try {
            if(auth()->user()->pp > 3){
                
                $toggled = $post_image_comment->disliked()->toggle(auth()->id());

                if(empty($toggled['detached'])){
                    $post_image_comment->increment('dislikes');
                    auth()->user()->decrement('pp');
                    $post_image_comment->user()->decrement('pp');
                }else{
                    $post_image_comment->decrement('dislikes');
                }

                return response()->json([
                    'success' => true,
                ]);
                
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have enough points to dislike this image \'s comment.  / คุณไม่มีพ้อยท์เพียงพอในการกดไม่ถูกใจความคิดเห็นนี้ กรุณาสะสมแต้มเพิ่มเติม',
                ]);
            }
            
        } catch (\Throwable $th) {
            throw $th;
        }

    }
}
