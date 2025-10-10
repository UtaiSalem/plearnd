<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PostImage;
use Illuminate\Http\Request;

class PostImageReactionController extends Controller
{
    public function toggleLikePostImage(PostImage $post_image)
    {
        try {
            if(auth()->user()->pp > 0){
                
                $result = $post_image->likedPostImage()->toggle(auth()->id());

                if(empty($result['detached'])){
                    $post_image->increment('likes');
                    auth()->user()->decrement('pp');
                    $post = $post_image->post;
                    $post->user()->increment('pp');
                    User::find(1)->increment('pp', 2);
                }else{
                    $post_image->decrement('likes');
                    $post = $post_image->post;
                    $post->user()->decrement('pp');
                }

                return response()->json([
                    'success' => true,
                ]);
                
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have enough points to like this image.  / คุณไม่มีพ้อยท์เพียงพอในการกดถูกใจรูปนี้ กรุณาสะสมแต้มเพิ่มเติม',
                ]);
            }
            
            // return redirect()->back();

        } catch (\Throwable $th) {
            throw $th;
        }

    }

    public function toggleDislikePostImage(PostImage $post_image)
    {
        try {
            if(auth()->user()->pp > 3){
                
                $dislike = $post_image->dislikedPostImage()->toggle(auth()->id());

                if(empty($dislike['detached'])){
                    $post_image->increment('dislikes');
                    auth()->user()->decrement('pp');

                    $post = $post_image->post;
                    
                    $post->user()->decrement('pp');
                    User::find(1)->increment('pp', 2);
                }else{
                    $post_image->decrement('dislikes');
                };

                return response()->json([
                    'success' => true,
                ]);

            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have enough points to dislike this image. / คุณไม่มีพ้อยท์เพียงพอในการกดไม่ถูกใจรูปนี้ กรุณาสะสมแต้มเพิ่มเติม',
                ]);
            }

        } catch (\Throwable $th) {
            throw $th;
        }

    }
}
