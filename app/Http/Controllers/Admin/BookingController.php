<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CancelBookingRequest;
use App\Http\Requests\Admin\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Notifications\BookingApproved;
use App\Notifications\BookingCancelledByAdmin;
use App\Notifications\BookingRejected;
use App\Notifications\BookingUpdatedByAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Booking::class);

        $query = Booking::query()->with(['room', 'user', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('staff_status')) {
            $query->where('staff_status', $request->staff_status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('q')) {
            $search = '%' . $request->q . '%';
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', $search)
                  ->orWhere('requester_name', 'like', $search)
                  ->orWhere('requester_identifier', 'like', $search);
            });
        }

        $bookings = $query->orderBy('start_at', 'desc')->paginate(25)->withQueryString();
        $rooms = Room::orderBy('code')->get();

        return view('admin.bookings.index', compact('bookings', 'rooms'));
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);
        
        $booking->load(['room.manager', 'reviewer', 'canceller', 'updater']);
        
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking): View
    {
        $this->authorize('update', $booking);
        
        $rooms = Room::where('is_bookable', true)->orderBy('code')->get();
        
        return view('admin.bookings.edit', compact('booking', 'rooms'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('update', $booking);
        
        $data = $request->validated();
        
        if (Booking::hasConflict($data['room_id'], $data['start_at'], $data['end_at'], $booking->id)) {
            return back()->withErrors(['error' => 'เวลาดังกล่าวมีการจองห้องนี้แล้ว'])->withInput();
        }

        $data['updated_by'] = $request->user()->id;
        $booking->update($data);
        
        if ($booking->user) {
            $booking->user->notify(new BookingUpdatedByAdmin($booking));
        }

        return redirect()->route('admin.bookings.show', $booking)->with('status', 'อัปเดตข้อมูลการจองและแจ้งเตือนผู้ใช้แล้ว');
    }

    public function approve(Booking $booking): RedirectResponse
    {
        $this->authorize('review', $booking);

        if ($booking->cancelled_at) {
            return back()->withErrors(['error' => 'รายการนี้ถูกยกเลิกแล้ว ไม่สามารถอนุมัติได้']);
        }

        if ($booking->status !== 'pending') {
            return back()->withErrors(['error' => 'สามารถอนุมัติได้เฉพาะรายการที่รอพิจารณาเท่านั้น']);
        }
        
        $booking->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        
        if ($booking->user) {
            $booking->user->notify(new BookingApproved($booking));
        }

        return back()->with('status', 'อนุมัติการจองเรียบร้อยแล้ว');
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('review', $booking);

        if ($booking->cancelled_at) {
            return back()->withErrors(['error' => 'รายการนี้ถูกยกเลิกแล้ว']);
        }

        if ($booking->status !== 'pending') {
            return back()->withErrors(['error' => 'สามารถปฏิเสธได้เฉพาะรายการที่รอพิจารณาเท่านั้น']);
        }

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);
        
        $booking->update([
            'status' => 'rejected',
            'rejection_reason' => $data['reason'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        
        if ($booking->user) {
            $booking->user->notify(new BookingRejected($booking));
        }

        return back()->with('status', 'ปฏิเสธการจองเรียบร้อยแล้ว');
    }

    public function cancel(CancelBookingRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('cancel', $booking);
        
        if ($booking->cancelled_at) {
            return back()->withErrors(['error' => 'รายการนี้ถูกยกเลิกไปแล้ว']);
        }
        
        $data = $request->validated();
        
        $booking->update([
            'cancelled_at' => now(),
            'cancelled_by' => $request->user()->id,
            'admin_note' => $data['reason'],
        ]);
        
        if ($booking->user) {
            $booking->user->notify(new BookingCancelledByAdmin($booking));
        }

        return back()->with('status', 'ยกเลิกการจองเรียบร้อยแล้ว');
    }
}
