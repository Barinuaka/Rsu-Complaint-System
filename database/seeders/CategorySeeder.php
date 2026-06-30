<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'category_name'        => 'Academic',
                'category_description' => 'Grade disputes, exam irregularities, course registration issues, academic victimisation',
                'parent_category_id'   => null,
                'is_active'            => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'category_name'        => 'Welfare and Security',
                'category_description' => 'Sexual harassment, extortion, security threats, health and safety concerns',
                'parent_category_id'   => null,
                'is_active'            => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'category_name'        => 'Infrastructure',
                'category_description' => 'Facility failures, hostel issues, classroom conditions, utility problems',
                'parent_category_id'   => null,
                'is_active'            => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'category_name'        => 'Financial',
                'category_description' => 'Scholarship issues, fee disputes, bursary queries, financial aid problems',
                'parent_category_id'   => null,
                'is_active'            => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'category_name'        => 'General Guidance',
                'category_description' => 'Course advice, registration guidance, career counselling, general information requests',
                'parent_category_id'   => null,
                'is_active'            => true,
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}