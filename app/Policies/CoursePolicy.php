<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->user_id || 
               $course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->user_id || 
               $course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }

    /**
     * Determine whether the user can view course progress/grades.
     */
    public function viewProgress(User $user, Course $course): bool
    {
        return $user->id === $course->user_id || 
               $course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }

    /**
     * Determine whether the user can manage course members.
     */
    public function manageMembers(User $user, Course $course): bool
    {
        return $user->id === $course->user_id || 
               $course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        //
    }
}
