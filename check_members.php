<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Course Members Data ===\n\n";

// Check course_group_members status values
echo "course_group_members status distribution:\n";
$cgmStatus = DB::table('course_group_members')
    ->select('status', DB::raw('COUNT(*) as count'))
    ->groupBy('status')
    ->get();

foreach ($cgmStatus as $row) {
    echo "  Status: '{$row->status}' - Count: {$row->count}\n";
}

echo "\n";

// Check course_members
echo "course_members distribution:\n";
$cmCount = DB::table('course_members')
    ->selectRaw("
        COUNT(*) as total,
        SUM(CASE WHEN course_member_status = 1 THEN 1 ELSE 0 END) as cms_1,
        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as s_1,
        SUM(CASE WHEN role = 1 THEN 1 ELSE 0 END) as role_1,
        SUM(CASE WHEN role = 2 THEN 1 ELSE 0 END) as role_2
    ")
    ->first();

echo "  Total: {$cmCount->total}\n";
echo "  course_member_status=1: {$cmCount->cms_1}\n";
echo "  status=1: {$cmCount->s_1}\n";
echo "  role=1: {$cmCount->role_1}\n";
echo "  role=2: {$cmCount->role_2}\n";

echo "\n";

// Check joined count
echo "Joined count (cm + cgm):\n";
$joinedCount = DB::table('course_members as cm')
    ->join('course_group_members as cgm', function($join) {
        $join->on('cm.course_id', '=', 'cgm.course_id')
             ->on('cm.user_id', '=', 'cgm.user_id');
    })
    ->count();

echo "  Total joined: {$joinedCount}\n";

$joinedActive = DB::table('course_members as cm')
    ->join('course_group_members as cgm', function($join) {
        $join->on('cm.course_id', '=', 'cgm.course_id')
             ->on('cm.user_id', '=', 'cgm.user_id');
    })
    ->where(function($query) {
        $query->where('cm.course_member_status', 1)
              ->orWhere('cm.status', 1);
    })
    ->where('cgm.status', '1')
    ->whereIn('cm.role', [1, 2])
    ->count();

echo "  Active (cms=1 AND cgm.status='1' AND role in [1,2]): {$joinedActive}\n";

$joinedActive2 = DB::table('course_members as cm')
    ->join('course_group_members as cgm', function($join) {
        $join->on('cm.course_id', '=', 'cgm.course_id')
             ->on('cm.user_id', '=', 'cgm.user_id');
    })
    ->where(function($query) {
        $query->where('cm.course_member_status', 1)
              ->orWhere('cm.status', 1);
    })
    ->where('cgm.status', 1) // Try integer
    ->whereIn('cm.role', [1, 2])
    ->count();

echo "  Active (cms=1 AND cgm.status=1 (int) AND role in [1,2]): {$joinedActive2}\n";

