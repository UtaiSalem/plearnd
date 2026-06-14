<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id
            || $user->isAdmin()
            || ($user->isStaff() && $user->canReview($booking));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can attempt to create a booking
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        // Admin can update anything. 
        // Requester can update only if it's their booking and it's still pending.
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $booking->user_id && $booking->status === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can review (approve/reject) the booking.
     */
    public function review(User $user, Booking $booking): bool
    {
        return $user->canReview($booking);
    }

    /**
     * Determine whether the user can cancel the booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Requester can cancel their own pending or approved booking
        return $user->id === $booking->user_id && in_array($booking->status, ['pending', 'approved']);
    }
}
