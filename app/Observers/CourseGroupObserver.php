<?php

namespace App\Observers;

use App\Models\CourseGroup;
use App\Models\Course;

class CourseGroupObserver
{
    /**
     * Handle the CourseGroup "created" event.
     */
    public function created(CourseGroup $courseGroup): void
    {
        $course = $courseGroup->course;
        if ($course) {
            $course->increment('groups');
        }
    }

    /**
     * Handle the CourseGroup "deleted" event.
     */
    public function deleted(CourseGroup $courseGroup): void
    {
        $course = $courseGroup->course;
        if ($course && $course->groups > 0) {
            $course->decrement('groups');
        }
    }

    /**
     * Handle the CourseGroup "restored" event.
     */
    public function restored(CourseGroup $courseGroup): void
    {
        $this->created($courseGroup);
    }

    /**
     * Handle the CourseGroup "force deleted" event.
     */
    public function forceDeleted(CourseGroup $courseGroup): void
    {
        $this->deleted($courseGroup);
    }
}