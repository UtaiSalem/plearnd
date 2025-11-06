<?php

namespace App\Observers;

use App\Models\CourseMember;
use App\Models\Course;

class CourseMemberObserver
{
    /**
     * Handle the CourseMember "created" event.
     */
    public function created(CourseMember $courseMember): void
    {
        // Only count active students (status = 1 and role = 1 or 2)
        if ($courseMember->course_member_status == 1 && in_array($courseMember->role, [1, 2])) {
            $course = $courseMember->course;
            if ($course) {
                $course->increment('enrolled_students');
            }
        }
    }

    /**
     * Handle the CourseMember "updated" event.
     */
    public function updated(CourseMember $courseMember): void
    {
        $course = $courseMember->course;
        if (!$course) return;

        // Check if status or role changed
        $wasActiveStudent = $courseMember->getOriginal('course_member_status') == 1 && 
                           in_array($courseMember->getOriginal('role'), [1, 2]);
        $isActiveStudent = $courseMember->course_member_status == 1 && 
                          in_array($courseMember->role, [1, 2]);

        // If changed from active student to not active student
        if ($wasActiveStudent && !$isActiveStudent) {
            $course->decrement('enrolled_students');
        }
        // If changed from not active student to active student
        elseif (!$wasActiveStudent && $isActiveStudent) {
            $course->increment('enrolled_students');
        }
    }

    /**
     * Handle the CourseMember "deleted" event.
     */
    public function deleted(CourseMember $courseMember): void
    {
        // Only decrement if it was an active student
        if ($courseMember->course_member_status == 1 && in_array($courseMember->role, [1, 2])) {
            $course = $courseMember->course;
            if ($course && $course->enrolled_students > 0) {
                $course->decrement('enrolled_students');
            }
        }
    }

    /**
     * Handle the CourseMember "restored" event.
     */
    public function restored(CourseMember $courseMember): void
    {
        $this->created($courseMember);
    }

    /**
     * Handle the CourseMember "force deleted" event.
     */
    public function forceDeleted(CourseMember $courseMember): void
    {
        $this->deleted($courseMember);
    }
}