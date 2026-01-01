<?php

namespace App\Policies;

use App\Models\CourseMember;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CourseMemberPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CourseMember $member): bool
    {
        return $user->id === $member->user_id || 
               $member->course->user_id === $user->id ||
               $member->course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }

    /**
     * Determine whether the user can update the model (general update).
     */
    public function update(User $user, CourseMember $member): bool
    {
        return $member->course->user_id === $user->id ||
               $member->course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CourseMember $member): bool
    {
        return $member->course->user_id === $user->id ||
               $member->course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }

    /**
     * Determine whether the user can update their own order number.
     */
    public function updateOrderNumber(User $user, CourseMember $member): bool
    {
        return $user->id === $member->user_id || 
               $member->course->user_id === $user->id ||
               $member->course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }
}
