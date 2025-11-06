<?php

namespace App\Http\Controllers;

use App\Models\Post;

class LikePostController extends Controller
{
    public function toggleLikePost(Post $post)
    {
        $post->likes()->toggle(auth()->id());
        if($post->likedPost()->where('user_id', auth()->id())->exists()){
            $post->decrement('likes');
        }else{
            $post->increment('likes');
        };
        return redirect()->back();
    }
}
