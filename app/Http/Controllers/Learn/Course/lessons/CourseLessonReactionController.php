<?php

namespace App\Http\Controllers\Learn\Course\Lessons;

use App\Models\Learn\Course\info\Course;
use App\Models\Learn\Course\lessons\Lesson;
use Illuminate\Support\Facades\DB;

class CourseLessonReactionController extends Controller
{
    private const REACTION_COST = 24;
    private const REACTION_REWARD = 12;

    public function toggleReaction(Course $course, Lesson $lesson, string $reactionType)
    {
        if (auth()->user()->pp < self::REACTION_COST) {
            return $this->insufficientPointsResponse($reactionType);
        }

        return DB::transaction(function () use ($course, $lesson, $reactionType) {
            $relationMethod = $reactionType . 'Lesson';
            $countColumn = $reactionType . '_count';

            $isReacted = $lesson->$relationMethod()->toggle(auth()->id());
            $wasToggled = !empty($isReacted['attached']);

            if ($wasToggled) {
                $lesson->increment($countColumn);
                auth()->user()->decrement('pp', self::REACTION_COST);
                $lesson->user()->increment('pp', self::REACTION_REWARD);
                $course->increment('points', self::REACTION_REWARD);
            } else {
                $lesson->decrement($countColumn);
                auth()->user()->decrement('pp', self::REACTION_REWARD);
                $course->increment('points', self::REACTION_REWARD);
            }

            return response()->json(['success' => true]);
        });
    }

    public function toggleLike(Course $course, Lesson $lesson)
    {
        return $this->toggleReaction($course, $lesson, 'like');
    }

    public function toggleDislike(Course $course, Lesson $lesson)
    {
        return $this->toggleReaction($course, $lesson, 'dislike');
    }

    private function insufficientPointsResponse(string $reactionType)
    {
        $action = $reactionType === 'liked' ? 'ถูกใจ' : 'ไม่ถูกใจ';
        return response()->json([
            'success' => false,
            'message' => "คุณมีพอยต์ไม่เพียงพอในการกด{$action}บทเรียนนี้ กรุณาสะสมแต้มเพิ่มเติม",
        ], 403);
    }
}
