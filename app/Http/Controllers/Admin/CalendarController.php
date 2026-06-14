<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Booking::class);
        $rooms = Room::orderBy('code')->get();
        return view('admin.calendar.index', compact('rooms'));
    }

    public function events(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Booking::class);

        $start = $request->input('start');
        $end = $request->input('end');

        if (!$start || !$end) {
            return response()->json([]);
        }

        $query = Booking::with('room')
            ->whereNull('cancelled_at')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_at', [$start, $end])
                  ->orWhereBetween('end_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_at', '<=', $start)
                         ->where('end_at', '>=', $end);
                  });
            });

        if ($request->filled('room_id')) {
            $query->whereIn('room_id', (array) $request->input('room_id'));
        }

        if ($request->filled('status')) {
            $query->whereIn('status', (array) $request->input('status'));
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($booking) {
            $color = match ($booking->status) {
                'pending' => '#eab308', // yellow-500
                'approved' => $booking->room->color ?? '#3b82f6', // room color or blue-500
                'rejected' => '#ef4444', // red-500
                default => '#9ca3af', // gray-400
            };

            return [
                'id' => $booking->id,
                'title' => $booking->room->code . ': ' . $booking->purpose,
                'start' => $booking->start_at->toIso8601String(),
                'end' => $booking->end_at->toIso8601String(),
                'color' => $color,
                'extendedProps' => [
                    'room' => $booking->room->name,
                    'requester' => $booking->requester_name,
                    'status' => $booking->status,
                    'staff_status' => $booking->staff_status,
                ],
            ];
        });

        return response()->json($events);
    }
}
