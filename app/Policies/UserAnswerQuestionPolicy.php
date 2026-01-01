<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAnswerQuestion;
use Illuminate\Auth\Access\Response;

class UserAnswerQuestionPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserAnswerQuestion $userAnswerQuestion): bool
    {
        return $user->id === $userAnswerQuestion->user_id || 
               $userAnswerQuestion->course->user_id === $user->id ||
               $userAnswerQuestion->course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
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
    public function update(User $user, UserAnswerQuestion $userAnswerQuestion): bool
    {
        return $user->id === $userAnswerQuestion->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserAnswerQuestion $userAnswerQuestion): bool
    {
        return $user->id === $userAnswerQuestion->user_id || 
               $userAnswerQuestion->course->user_id === $user->id ||
               $userAnswerQuestion->course->courseMembers()
                      ->where('user_id', $user->id)
                      ->where('role', 4)
                      ->exists();
    }
}
