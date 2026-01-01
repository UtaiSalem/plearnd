<?php

namespace App\Policies;

use App\Models\AssignmentAnswer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssignmentAnswerPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AssignmentAnswer $assignmentAnswer): bool
    {
        return $user->id === $assignmentAnswer->user_id || 
               $assignmentAnswer->assignment->course->user_id === $user->id ||
               $assignmentAnswer->assignment->course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Usually controlled by course membership, handled in controller/middleware
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AssignmentAnswer $assignmentAnswer): bool
    {
        return $user->id === $assignmentAnswer->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssignmentAnswer $assignmentAnswer): bool
    {
        return $user->id === $assignmentAnswer->user_id || 
               $assignmentAnswer->assignment->course->user_id === $user->id ||
               $assignmentAnswer->assignment->course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }
}
