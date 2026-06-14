<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admin can update anyone except they should probably not be able to 
        // disable themselves if they are the last admin (logic will be in controller or custom check)
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Prevent deleting self
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can change the role of a user.
     */
    public function changeRole(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can reset the password of a user.
     */
    public function resetPassword(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
}
