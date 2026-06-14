<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Notifications\BookingApproved;
use App\Notifications\BookingRejected;
use App\Notifications\BookingSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with('room')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('start_at')
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request): View
    {
        $rooms = Room::orderBy('name')->get();
        $user = $request->user();
        return view('bookings.create', compact('rooms', 'user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isStudent = $user->user_type === 'student';

        $data = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'attendees' => ['required', 'integer', 'min:1'],
            'purpose' => ['required', 'string', 'max:1000'],
            'requirements' => ['nullable', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:30'],
            'advisor_name' => [$isStudent ? 'required' : 'nullable', 'string', 'max:120'],
        ]);

        $room = Room::findOrFail($data['room_id']);
        if ($room->status === 'maintenance') {
            throw ValidationException::withMessages(['room_id' => 'ห้องนี้ปิดปรับปรุง ไม่สามารถจองได้']);
        }

        $startAt = Carbon::parse($data['date'].' '.$data['start_time']);
        $endAt = Carbon::parse($data['date'].' '.$data['end_time']);

        if (Booking::hasConflict($room->id, $startAt->toDateTimeString(), $endAt->toDateTimeString())) {
            throw ValidationException::withMessages(['start_time' => 'ช่วงเวลานี้มีการจองห้องอื่นซ้อนทับอยู่แล้ว']);
        }

        $booking = Booking::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'requester_name' => $user->name,
            'requester_type' => $user->user_type ?? 'employee',
            'requester_identifier' => $user->identifier(),
            'faculty' => $user->faculty,
            'department' => $user->department,
            'phone' => $data['phone'],
            'advisor_name' => $data['advisor_name'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'attendees' => $data['attendees'],
            'purpose' => $data['purpose'],
            'requirements' => $data['requirements'] ?? null,
            'status' => 'pending',
            'staff_status' => 'scheduled',
        ]);

        $this->notifyApprovers($booking->fresh('room'));

        return redirect()->route('bookings.index')
            ->with('status', 'ส่งคำขอจองห้องเรียบร้อยแล้ว ระบบได้แจ้งผู้รับผิดชอบห้องทางอีเมลแล้ว');
    }

    public function show(Request $request, Booking $booking): View
    {
        $user = $request->user();
        $canSee = $user->id === $booking->user_id
            || $user->isAdmin()
            || ($user->isStaff() && $user->canReview($booking));
        abort_unless($canSee, 403);

        $booking->load(['room.manager', 'reviewer']);

        return view('bookings.show', compact('booking'));
    }

    public function review(Request $request, Booking $booking): RedirectResponse
    {
        $user = $request->user();
        $booking->load('room', 'user');
        abort_unless($user->canReview($booking), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $booking->update([
            'status' => $data['status'],
            'rejection_reason' => $data['status'] === 'rejected'
                ? ($data['reason'] ?: 'ไม่ระบุเหตุผล')
                : null,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        $notification = $data['status'] === 'approved'
            ? new BookingApproved($booking)
            : new BookingRejected($booking);

        $booking->user->notify($notification);

        return back()->with('status', $data['status'] === 'approved'
            ? 'อนุมัติคำขอเรียบร้อย และแจ้งผู้ขอใช้ทางอีเมลแล้ว'
            : 'ปฏิเสธคำขอเรียบร้อย และแจ้งผู้ขอใช้ทางอีเมลแล้ว');
    }

    public function updateStaffStatus(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('review', $booking);

        $data = $request->validate([
            'staff_status' => ['required', Rule::in(['scheduled', 'ready', 'in_use', 'cleanup', 'issue'])],
        ]);

        $booking->update(['staff_status' => $data['staff_status']]);

        return back()->with('status', 'อัปเดตสถานะห้องเรียบร้อย');
    }

    private function notifyApprovers(Booking $booking): void
    {
        $recipients = collect();

        if ($manager = $booking->room->manager) {
            $recipients->push($manager);
        }

        $admins = User::where('role', 'admin')->get();
        $recipients = $recipients->merge($admins)->unique('id')->values();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new BookingSubmitted($booking));
    }
}
