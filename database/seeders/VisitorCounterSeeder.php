<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VisitorCounter;

class VisitorCounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VisitorCounter::create([
            'id' => 1,
            'visitor_counter' => 0,
            'login_counter' => 0
        ]);
    }
}
