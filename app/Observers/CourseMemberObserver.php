<?php

namespace App\Observers;

use App\Models\CourseMember;
use App\Models\CourseGroupMember;
use App\Models\Course;

class CourseMemberObserver
{
    /**
     * Handle the CourseMember "created" event.
     * 
     * นับสมาชิกเฉพาะที่:
     * - course_member_status = 1 (หรือ status = 1)
     * - role = 1 หรือ 2 (นักเรียนหรือหัวหน้ากลุ่ม)
     * - มี record ใน course_group_members ที่ status = '1'
     */
    public function created(CourseMember $courseMember): void
    {
        // Only count active students (course_member_status = 1 and role = 1 or 2)
        // Use course_member_status as the primary check
        $status = $courseMember->course_member_status ?? $courseMember->status;
        
        if ($status == 1 && in_array($courseMember->role, [1, 2])) {
            // Check if has group membership (cross table record)
            $hasGroupMembership = CourseGroupMember::where('course_id', $courseMember->course_id)
                ->where('user_id', $courseMember->user_id)
                ->exists();
            
            if ($hasGroupMembership) {
                $course = $courseMember->course;
                if ($course) {
                    $course->increment('enrolled_students');
                }
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

        // Check both status and course_member_status for compatibility
        $oldStatus = $courseMember->getOriginal('course_member_status') ?? $courseMember->getOriginal('status');
        $newStatus = $courseMember->course_member_status ?? $courseMember->status;
        
        $oldRole = $courseMember->getOriginal('role');
        $newRole = $courseMember->role;

        // Check if has group membership (cross table record)
        $hasGroupMembership = CourseGroupMember::where('course_id', $courseMember->course_id)
            ->where('user_id', $courseMember->user_id)
            ->exists();

        // Determine if was/is active student (with group membership)
        $wasActiveStudent = ($oldStatus == 1) && in_array($oldRole, [1, 2]) && $hasGroupMembership;
        $isActiveStudent = ($newStatus == 1) && in_array($newRole, [1, 2]) && $hasGroupMembership;

        // If changed from active student to not active student
        if ($wasActiveStudent && !$isActiveStudent) {
            if ($course->enrolled_students > 0) {
                $course->decrement('enrolled_students');
            }
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
        // Only decrement if it was an active student with group membership
        $status = $courseMember->course_member_status ?? $courseMember->status;
        
        if ($status == 1 && in_array($courseMember->role, [1, 2])) {
            // Check if had group membership (cross table record)
            $hasGroupMembership = CourseGroupMember::where('course_id', $courseMember->course_id)
                ->where('user_id', $courseMember->user_id)
                ->exists();
            
            if ($hasGroupMembership) {
                $course = $courseMember->course;
                if ($course && $course->enrolled_students > 0) {
                    $course->decrement('enrolled_students');
                }
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