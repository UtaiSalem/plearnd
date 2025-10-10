<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonComment;
use Illuminate\Support\Facades\DB;

class LessonCommentReactionController extends Controller
{
    private const REACTION_COST = 12;
    private const REACTION_REWARD = 6;

    public function toggleReaction(Course $course, Lesson $lesson, LessonComment $comment, string $reactionType)
    {
        if (auth()->user()->pp < self::REACTION_COST) {
            return $this->insufficientPointsResponse($reactionType);
        }

        return DB::transaction(function () use ($course, $lesson, $comment, $reactionType) {
            $relationMethod = $reactionType . 'Comment';
            $countColumn = $reactionType . 's';

            $isReacted = $comment->$relationMethod()->toggle(auth()->id());
            $wasToggled = !empty($isReacted['attached']);

            if ($wasToggled) {
                $comment->increment($countColumn);
                auth()->user()->decrement('pp', self::REACTION_COST);
                $comment->user()->increment('pp', self::REACTION_REWARD);
                $course->increment('points', self::REACTION_REWARD);
            } else {
                $comment->decrement($countColumn);
                auth()->user()->increment('pp', self::REACTION_REWARD);
                $comment->user()->decrement('pp', self::REACTION_REWARD);
            }

            return response()->json(['success' => true]);
        });
    }

    public function toggleLike(Course $course, Lesson $lesson, LessonComment $comment)
    {
        return $this->toggleReaction($course, $lesson, $comment, 'like');
    }

    public function toggleDislike(Course $course, Lesson $lesson, LessonComment $comment)
    {
        return $this->toggleReaction($course, $lesson, $comment, 'dislike');
    }

    private function insufficientPointsResponse(string $reactionType)
    {
        $action = $reactionType === 'liked' ? 'ถูกใจ' : 'ไม่ถูกใจ';
        return response()->json([
            'success' => false,
            'message' => "คุณมีพอยต์ไม่เพียงพอในการกด{$action}ความคิดเห็นนี้ กรุณาสะสมแต้มเพิ่มเติม",
        ], 403);
    }
}
