<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role_name' => 'Student',                'access_level' => 1],
            ['role_name' => 'Lecturer',               'access_level' => 2],
            ['role_name' => 'Course Adviser',         'access_level' => 3],
            ['role_name' => 'Head of Department',     'access_level' => 4],
            ['role_name' => 'Dean',                   'access_level' => 5],
            ['role_name' => 'Senate Committee',       'access_level' => 6],
            ['role_name' => 'Data Protection Officer','access_level' => 7],
            ['role_name' => 'System Administrator',   'access_level' => 8],
        ];

        DB::table('roles')->insert($roles);
    }
}