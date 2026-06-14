<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = now();
        $today = now()->startOfDay();

        // Pending Bookings
        $pendingCount = Booking::where('status', 'pending')
            ->whereNull('cancelled_at')
            ->count();

        // Approved Today
        $approvedTodayCount = Booking::where('status', 'approved')
            ->whereNull('cancelled_at')
            ->whereDate('reviewed_at', $today)
            ->count();

        // In-use now
        $inUseCount = Booking::where('status', 'approved')
            ->whereNull('cancelled_at')
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->count();

        // Rooms by Status
        $roomsByStatus = Room::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')->toArray();

        // Users by Role
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role')->toArray();

        // 7-day trend (Bookings created per day)
        $sevenDaysAgo = now()->subDays(6)->startOfDay();
        $trendRaw = Booking::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', $sevenDaysAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')->toArray();

        $trend = [];
        for ($i = 0; $i < 7; $i++) {
            $dateStr = $sevenDaysAgo->copy()->addDays($i)->format('Y-m-d');
            $trend[$dateStr] = $trendRaw[$dateStr] ?? 0;
        }

        // Top 5 Rooms this month
        $startOfMonth = now()->startOfMonth();
        $topRooms = Room::withCount(['bookings' => function ($query) use ($startOfMonth) {
                $query->where('status', 'approved')
                      ->whereNull('cancelled_at')
                      ->where('start_at', '>=', $startOfMonth);
            }])
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();

        // Latest pending bookings
        $latestPending = Booking::with(['room', 'user'])
            ->where('status', 'pending')
            ->whereNull('cancelled_at')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'pendingCount',
            'approvedTodayCount',
            'inUseCount',
            'roomsByStatus',
            'usersByRole',
            'trend',
            'topRooms',
            'latestPending'
        ));
    }
}
