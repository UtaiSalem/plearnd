<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\CoursePost;

class CoursePostReactionController extends Controller
{
    public $plearndAdmin = null;

    public function __construct()
    {
        $this->plearndAdmin = User::find(1);
    }

    public function toggleLike(Course $course, CoursePost $post)
    {
        if(auth()->user()->pp > 24){
            $post->likedPost()->toggle(auth()->id());
            if($post->likedPost()->where('user_id', auth()->id())->exists())
            {
                $post->increment('likes');
                auth()->user()->decrement('pp', 24);
                $post->user()->increment('pp',12);
                $this->plearndAdmin->increment('pp', 12);
            }
            else
            {
                $post->decrement('likes');
                auth()->user()->decrement('pp', 12);
                $this->plearndAdmin->increment('pp', 12);
            };

            return response()->json([
                'success' => true,
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'You do not have enough points to like this post. / คุณไม่มีพ้อยท์เพียงพอในการกดถูกใจโพสต์นี้ กรุณาสะสมแต้มเพิ่มเติม',
            ]);
        }
        
    }

    public function toggleDislike(Course $course, CoursePost $post)
    {
        if(auth()->user()->pp > 24){
            $post->dislikedPost()->toggle(auth()->id());
            if($post->dislikedPost()->where('user_id', auth()->id())->exists()){
                $post->increment('dislikes');
                auth()->user()->decrement('pp', 12);
                $post->user()->decrement('pp', 12);
                $this->plearndAdmin->increment('pp', 24);
            }
            else{
                $post->decrement('dislikes');
                auth()->user()->decrement('pp', 12);
                $this->plearndAdmin->increment('pp', 12);
            };

            return response()->json([
                'success' => true,
            ]);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'You do not have enough points to dislike this post. / คุณไม่มีพ้อยท์เพียงพอในการกดไม่ถูกใจโพสต์นี้ กรุณาสะสมแต้มเพิ่มเติม',
            ]);
        }
    }
}
