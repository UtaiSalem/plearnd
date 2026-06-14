<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_all_bookings(): void
    {
        $admin = User::factory()->admin()->create();
        Booking::factory(3)->create();

        $response = $this->actingAs($admin)->get('/admin/bookings');

        $response->assertStatus(200);
        $response->assertViewIs('admin.bookings.index');
        $response->assertViewHas('bookings');
    }

    public function test_admin_can_approve_booking(): void
    {
        $admin = User::factory()->admin()->create();
        $booking = Booking::factory()->pending()->create();

        $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/approve");

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'approved',
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_admin_can_reject_booking(): void
    {
        $admin = User::factory()->admin()->create();
        $booking = Booking::factory()->pending()->create();

        $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/reject", [
            'reason' => 'ห้องไม่ว่าง'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'rejected',
            'rejection_reason' => 'ห้องไม่ว่าง',
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_admin_can_cancel_booking(): void
    {
        $admin = User::factory()->admin()->create();
        $booking = Booking::factory()->approved()->create();

        $response = $this->actingAs($admin)->post("/admin/bookings/{$booking->id}/cancel", [
            'reason' => 'ปิดปรับปรุงด่วน'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'cancelled_by' => $admin->id,
            'admin_note' => 'ปิดปรับปรุงด่วน',
        ]);
        
        $this->assertNotNull($booking->fresh()->cancelled_at);
    }

    public function test_admin_can_update_booking_with_no_conflict(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::factory()->create();
        $booking = Booking::factory()->pending()->create(['room_id' => $room->id]);

        $newStart = Carbon::tomorrow()->setTime(8, 0)->format('Y-m-d\TH:i');
        $newEnd = Carbon::tomorrow()->setTime(10, 0)->format('Y-m-d\TH:i');

        $response = $this->actingAs($admin)->put("/admin/bookings/{$booking->id}", [
            'room_id' => $room->id,
            'start_at' => $newStart,
            'end_at' => $newEnd,
            'attendees' => 10,
            'purpose' => 'Updated Purpose',
        ]);

        $response->assertRedirect(route('admin.bookings.show', $booking));
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'purpose' => 'Updated Purpose',
            'attendees' => 10,
        ]);
    }

    public function test_admin_cannot_update_booking_with_conflict(): void
    {
        $admin = User::factory()->admin()->create();
        $room = Room::factory()->create();
        
        // Existing booking
        Booking::factory()->approved()->create([
            'room_id' => $room->id,
            'start_at' => Carbon::tomorrow()->setTime(9, 0),
            'end_at' => Carbon::tomorrow()->setTime(12, 0),
        ]);

        // Target booking to update
        $booking = Booking::factory()->pending()->create(['room_id' => $room->id]);

        // Try to overlap with existing booking
        $newStart = Carbon::tomorrow()->setTime(10, 0)->format('Y-m-d\TH:i');
        $newEnd = Carbon::tomorrow()->setTime(11, 0)->format('Y-m-d\TH:i');

        $response = $this->actingAs($admin)->put("/admin/bookings/{$booking->id}", [
            'room_id' => $room->id,
            'start_at' => $newStart,
            'end_at' => $newEnd,
            'attendees' => 10,
            'purpose' => 'Updated Purpose',
        ]);

        $response->assertSessionHasErrors(['error']);
        $this->assertDatabaseMissing('bookings', [
            'id' => $booking->id,
            'purpose' => 'Updated Purpose',
        ]);
    }
}
