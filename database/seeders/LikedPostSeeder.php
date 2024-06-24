<?php

namespace Database\Seeders;

use App\Models\LikePost;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LikedPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LikedPost::factory(30)->create();
    }
}
