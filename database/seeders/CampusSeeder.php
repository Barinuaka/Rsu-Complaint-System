<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampusSeeder extends Seeder
{
    public function run(): void
    {
        $campuses = [
            [
                'campus_name'    => 'Nkpolu-Oroworukwo',
                'campus_code'    => 'NKP',
                'is_main_campus' => true,
                'local_admin_id' => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'campus_name'    => 'Emuoha',
                'campus_code'    => 'EMU',
                'is_main_campus' => false,
                'local_admin_id' => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'campus_name'    => 'Ahoada',
                'campus_code'    => 'AHO',
                'is_main_campus' => false,
                'local_admin_id' => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'campus_name'    => 'Etche',
                'campus_code'    => 'ETC',
                'is_main_campus' => false,
                'local_admin_id' => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'campus_name'    => 'Sakpenwa',
                'campus_code'    => 'SAK',
                'is_main_campus' => false,
                'local_admin_id' => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];

        DB::table('campuses')->insert($campuses);
    }
}