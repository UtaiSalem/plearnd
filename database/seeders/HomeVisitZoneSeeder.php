<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HomeVisitZone;

class HomeVisitZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            [
                'zone_name' => 'โซนเหนือ',
                'zone_code' => 'NORTH',
                'description' => 'พื้นที่ทางตอนเหนือของเขตพื้นที่บริการ',
                'color' => '#3B82F6',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'zone_name' => 'โซนใต้',
                'zone_code' => 'SOUTH',
                'description' => 'พื้นที่ทางตอนใต้ของเขตพื้นที่บริการ',
                'color' => '#EF4444',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'zone_name' => 'โซนตะวันออก',
                'zone_code' => 'EAST',
                'description' => 'พื้นที่ทางตอนตะวันออกของเขตพื้นที่บริการ',
                'color' => '#10B981',
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'zone_name' => 'โซนตะวันตก',
                'zone_code' => 'WEST',
                'description' => 'พื้นที่ทางตอนตะวันตกของเขตพื้นที่บริการ',
                'color' => '#F59E0B',
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'zone_name' => 'โซนกลาง',
                'zone_code' => 'CENTER',
                'description' => 'พื้นที่ตัวเมืองและย่านกลาง',
                'color' => '#8B5CF6',
                'is_active' => true,
                'display_order' => 5,
            ],
        ];

        foreach ($zones as $zone) {
            HomeVisitZone::updateOrCreate(
                ['zone_code' => $zone['zone_code']],
                $zone
            );
        }
    }
}

