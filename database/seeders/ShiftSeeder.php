<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Shift::create(['name' => 'Pagi (Shift 1)', 'start_time' => '07:00', 'end_time' => '14:00']);
        \App\Models\Shift::create(['name' => 'Siang (Shift 2)', 'start_time' => '14:00', 'end_time' => '21:00']);
        \App\Models\Shift::create(['name' => 'Malam (Shift 3)', 'start_time' => '21:00', 'end_time' => '07:00']);
        \App\Models\Shift::create(['name' => 'Full Day', 'start_time' => '08:00', 'end_time' => '16:00']);
    }
}
