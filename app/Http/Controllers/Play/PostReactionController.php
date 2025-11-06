<?php

namespace App\Http\Controllers\Play;

use App\Models\Play\Post;
use App\Models\Shared\User;

class PostReactionController extends \App\Http\Controllers\Controller
{
    public function toggleLikePost(Post $post)
    {
        if(auth()->user()->pp > 3){
            $post->likedPost()->toggle(auth()->id());
            if($post->likedPost()->where('user_id', auth()->id())->exists()){
                $post->increment('likes');
                auth()->user()->decrement('pp');
                $post->author()->increment('pp');
            }
            else{
                $post->decrement('likes');
                auth()->user()->decrement('pp');
                $post->author()->decrement('pp');
            };

            return response()->json([
                'success' => true,
            ]);
        }else{
            // return redirect()->back()->with('error', 'You do not have enough PP to like this post');
            return response()->json([
                'success' => false,
                'message' => 'You do not have enough points to like this post. / คุณไม่มีพ้อยท์เพียงพอในการกดถูกใจโพสต์นี้ กรุณาสะสมแต้มเพิ่มเติม',
            ]);
        }
        
        // return redirect()->back();
    }

    public function toggleDislikePost(Post $post)
    {
        if(auth()->user()->pp > 2){
            $post->dislikedPost()->toggle(auth()->id());
            if($post->dislikedPost()->where('user_id', auth()->id())->exists()){
                $post->increment('dislikes');
                auth()->user()->decrement('pp');
                $post->author()->decrement('pp');
                User::find(1)->increment('pp', 2);
            }
            else{
                $post->decrement('dislikes');
                auth()->user()->decrement('pp');
                $post->author()->decrement('pp');
            };

            return response()->json([
                'success' => true,
            ]);
        }else{
            // return redirect()->back()->with('error', 'You do not have enough PP to like this post');
            return response()->json([
                'success' => false,
                'message' => 'You do not have enough points to dislike this post. / คุณไม่มีพ้อยท์เพียงพอในการกดไม่ถูกใจโพสต์นี้ กรุณาสะสมแต้มเพิ่มเติม',
            ]);
        }

        // return redirect()->back();
    }
}
