<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $bookings = Booking::with('room')
            ->where('user_id', $user->id)
            ->orderByDesc('start_at')
            ->get();

        $summary = [
            'pending' => $bookings->where('status', 'pending')->count(),
            'approved' => $bookings->where('status', 'approved')->count(),
            'rejected' => $bookings->where('status', 'rejected')->count(),
        ];

        $next = $bookings
            ->where('status', '!=', 'rejected')
            ->where('start_at', '>=', now())
            ->sortBy('start_at')
            ->first();

        $rooms = Room::orderBy('name')->limit(3)->get();

        return view('dashboard', compact('bookings', 'summary', 'next', 'rooms'));
    }
}
