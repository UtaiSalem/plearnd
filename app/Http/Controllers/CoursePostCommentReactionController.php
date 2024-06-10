<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CoursePostComment;

class CoursePostCommentReactionController extends Controller
{
    public $plearndAdmin = null;

    public function __construct()
    {
        $this->plearndAdmin = User::find(1);
    }

    public function toggleLikeComment(CoursePostComment $comment)
    {
        if(auth()->user()->pp > 24){
            $comment->comment_likes()->toggle(auth()->id());
            if($comment->comment_likes()->where('user_id', auth()->id())->exists())
            {
                $comment->increment('likes');
                auth()->user()->decrement('pp', 24);
                $comment->user()->increment('pp',12);
                $this->plearndAdmin->increment('pp', 12);
            }
            else
            {
                $comment->decrement('likes');
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

    public function toggleDislikeComment(CoursePostComment $comment)
    {
        if(auth()->user()->pp > 24){
            $comment->comment_dislikes()->toggle(auth()->id());
            if($comment->comment_dislikes()->where('user_id', auth()->id())->exists()){
                $comment->increment('dislikes');
                auth()->user()->decrement('pp', 12);
                $comment->user()->decrement('pp', 12);
                $this->plearndAdmin->increment('pp', 24);
            }
            else{
                $comment->decrement('dislikes');
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
