<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\GlobalSetting::upsert([
            [
                'key' => 'office_location',
                'value' => '-7.2954701,108.2057908',
                'description' => 'Koordinat Kantor Pusat (Latitude, Longitude)',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'office_radius',
                'value' => '500',
                'description' => 'Radius Maksimal Presensi (Meter)',
                'type' => 'number',
                'is_active' => true,
            ],
            [
                'key' => 'attendance_message',
                'value' => 'Selamat datang di Sistem Presensi RS Asa Bunda. Tetap semangat melayani!',
                'description' => 'Pesan Pengumuman di Halaman Presensi',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'emergency_mode',
                'value' => 'false',
                'description' => 'Mode Darurat (Tampilkan Tombol Darurat)',
                'type' => 'boolean',
                'is_active' => false,
            ],
            [
                'key' => 'emergency_link',
                'value' => 'https://forms.gle/emergency-form',
                'description' => 'Link Form Presensi Darurat',
                'type' => 'text',
                'is_active' => true,
            ],
            [
                'key' => 'external_links',
                'value' => json_encode([
                    ['label' => 'Form Cuti Online', 'url' => 'https://trello.com/b/cymDIrip/it-asa-bunda']
                ]), 
                'description' => 'Daftar Tombol Link Eksternal',
                'type' => 'json',
                'is_active' => true,
            ],
        ], ['key'], ['value', 'description', 'type', 'is_active']);
        
        \App\Models\GlobalSetting::whereIn('key', ['external_link_label', 'external_link_url'])->delete();
    }
}
