<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to allow truncation
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        
        \App\Models\User::truncate();
        \App\Models\Unit::truncate();
        
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $units = [
            'DIREKSI', 'SDM', 'IT', 'SEKRETARIAT', 'MARKETING', 
            'KEPERAWATAN', 'SDM & DIKLAT', 'KEUANGAN', 'REKAM MEDIS'
        ];

        $defaultShifts = [
            ['name' => 'Shift Pagi', 'start_time' => '07:00', 'end_time' => '14:00'],
            ['name' => 'Shift Sore', 'start_time' => '14:00', 'end_time' => '21:00'],
            ['name' => 'Shift Malam', 'start_time' => '21:00', 'end_time' => '07:00'],
        ];

        foreach ($units as $unitName) {
            \App\Models\Unit::create([
                'name' => $unitName,
                'available_shifts' => $defaultShifts
            ]);
        }

        $users = [
            ['name' => 'dr. YOHANA DENYKA KURNIAWATI, MPH.,MQM', 'unit' => 'DIREKSI'],
            ['name' => 'Dra MARGARETHA MULYONO, MPH.', 'unit' => 'DIREKSI'],
            ['name' => 'Dra. ENDANG DWI NINGSIH, M.M.', 'unit' => 'DIREKSI'],
            ['name' => 'PURYANI', 'unit' => 'SDM'],
            ['name' => 'SEPTIANUS AJI NUGROHO, S.Kom.', 'unit' => 'IT'],
            ['name' => 'FERRA DYAH KRISTIANA, S.M.', 'unit' => 'SEKRETARIAT'],
            ['name' => 'BHARATA YOGA PERMANA', 'unit' => 'MARKETING'],
            ['name' => 'ROSALIA NIEKE ARWATI, A.Md.Keb.', 'unit' => 'KEPERAWATAN'],
            ['name' => 'ALVANIA CLARESTA SARAH CHRISTIAN, S.Psi.', 'unit' => 'SDM & DIKLAT'],
            ['name' => 'TIARA SASOTYANINGTYAS, SE.', 'unit' => 'KEUANGAN'],
            ['name' => 'DIMAS WAHYU SULISTYO, Amd.Kom.', 'unit' => 'IT'],
            ['name' => 'DESSY LUCIA IRAWATI, A.Md.Kes.', 'unit' => 'REKAM MEDIS'],
            ['name' => 'SRIYANI MUGIARSIH, S.Kep.,Ns.', 'unit' => 'KEPERAWATAN'],
        ];

        $password = \Illuminate\Support\Facades\Hash::make('rsabunda123');

        foreach ($users as $index => $userData) {
            $nip = str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            \App\Models\User::create([
                'nip' => $nip,
                'name' => $userData['name'],
                'unit' => $userData['unit'],
                'password' => $password,
                'is_initial_password' => true,
            ]);
        }
    }
}
