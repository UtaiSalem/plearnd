<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function queue(Request $request): View
    {
        $user = $request->user();

        $bookingsQuery = Booking::with(['room', 'user'])->orderByDesc('start_at');
        $roomsQuery = Room::query();

        if (! $user->isAdmin()) {
            $bookingsQuery->whereHas('room', fn ($q) => $q->where('manager_user_id', $user->id));
            $roomsQuery->where('manager_user_id', $user->id);
        }

        $bookings = $bookingsQuery->get();
        $pending = $bookings->where('status', 'pending')->values();

        $today = now()->toDateString();
        $todayApproved = $bookings->filter(
            fn ($b) => $b->status === 'approved' && $b->start_at->toDateString() === $today
        )->values();

        $roomCount = $roomsQuery->count();

        return view('staff.queue', compact('pending', 'todayApproved', 'roomCount'));
    }

    public function rooms(Request $request): View
    {
        $user = $request->user();
        $roomsQuery = Room::query()->orderBy('name');

        if (! $user->isAdmin()) {
            $roomsQuery->where('manager_user_id', $user->id);
        }

        $rooms = $roomsQuery->get();
        return view('staff.rooms', compact('rooms'));
    }
}
