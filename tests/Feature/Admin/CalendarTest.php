<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_calendar_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/calendar');

        $response->assertStatus(200);
        $response->assertViewIs('admin.calendar.index');
    }

    public function test_calendar_events_endpoint_returns_json_in_range(): void
    {
        $admin = User::factory()->admin()->create();
        
        $startOfWeek = Carbon::now()->startOfWeek();
        
        // In range
        Booking::factory()->approved()->create([
            'start_at' => $startOfWeek->copy()->addDay()->setTime(10, 0),
            'end_at' => $startOfWeek->copy()->addDay()->setTime(12, 0),
        ]);
        
        // Out of range (next month)
        Booking::factory()->approved()->create([
            'start_at' => $startOfWeek->copy()->addMonth()->setTime(10, 0),
            'end_at' => $startOfWeek->copy()->addMonth()->setTime(12, 0),
        ]);

        $response = $this->actingAs($admin)->getJson('/admin/calendar/events?' . http_build_query([
            'start' => $startOfWeek->toIso8601String(),
            'end' => $startOfWeek->copy()->endOfWeek()->toIso8601String(),
        ]));

        $response->assertStatus(200);
        
        $events = $response->json();
        $this->assertCount(1, $events);
        $this->assertArrayHasKey('id', $events[0]);
        $this->assertArrayHasKey('title', $events[0]);
        $this->assertArrayHasKey('start', $events[0]);
        $this->assertArrayHasKey('end', $events[0]);
        $this->assertArrayHasKey('extendedProps', $events[0]);
    }
}
