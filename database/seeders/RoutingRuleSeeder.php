<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutingRuleSeeder extends Seeder
{
    public function run(): void
    {
        // Role IDs (matching RoleSeeder order):
        // 1=Student, 2=Lecturer, 3=Course Adviser,
        // 4=HOD, 5=Dean, 6=Senate, 7=DPO, 8=Admin

        // Campus IDs (matching CampusSeeder order):
        // 1=Nkpolu, 2=Emuoha, 3=Ahoada, 4=Etche, 5=Sakpenwa

        // Category IDs (matching CategorySeeder order):
        // 1=Academic, 2=Welfare & Security, 3=Infrastructure,
        // 4=Financial, 5=General Guidance

        $rules = [];
        $campuses   = [1, 2, 3, 4, 5];
        $urgencies  = ['low', 'medium', 'high', 'critical'];

        foreach ($campuses as $campus) {
            foreach ($urgencies as $urgency) {

                // --- Academic complaints ---
                $rules[] = [
                    'category_id'                => 1,
                    'campus_id'                  => $campus,
                    'urgency_level'              => $urgency,
                    'primary_handler_role_id'    => 3, // Course Adviser
                    'escalation_handler_role_id' => 4, // HOD
                    'secondary_escalation_role_id' => 5, // Dean
                    'sla_hours'                  => match($urgency) {
                        'low'      => 72,
                        'medium'   => 48,
                        'high'     => 24,
                        'critical' => 6,
                    },
                    'notify_central_admin' => $urgency === 'critical',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // --- Welfare & Security complaints ---
                $rules[] = [
                    'category_id'                => 2,
                    'campus_id'                  => $campus,
                    'urgency_level'              => $urgency,
                    'primary_handler_role_id'    => 4, // HOD
                    'escalation_handler_role_id' => 5, // Dean
                    'secondary_escalation_role_id' => 6, // Senate
                    'sla_hours'                  => match($urgency) {
                        'low'      => 48,
                        'medium'   => 24,
                        'high'     => 12,
                        'critical' => 3,
                    },
                    'notify_central_admin' => in_array($urgency, ['high', 'critical']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // --- Infrastructure complaints ---
                $rules[] = [
                    'category_id'                => 3,
                    'campus_id'                  => $campus,
                    'urgency_level'              => $urgency,
                    'primary_handler_role_id'    => 4, // HOD
                    'escalation_handler_role_id' => 5, // Dean
                    'secondary_escalation_role_id' => 8, // Admin
                    'sla_hours'                  => match($urgency) {
                        'low'      => 96,
                        'medium'   => 72,
                        'high'     => 48,
                        'critical' => 12,
                    },
                    'notify_central_admin' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // --- Financial complaints ---
                $rules[] = [
                    'category_id'                => 4,
                    'campus_id'                  => $campus,
                    'urgency_level'              => $urgency,
                    'primary_handler_role_id'    => 3, // Course Adviser
                    'escalation_handler_role_id' => 4, // HOD
                    'secondary_escalation_role_id' => 5, // Dean
                    'sla_hours'                  => match($urgency) {
                        'low'      => 72,
                        'medium'   => 48,
                        'high'     => 24,
                        'critical' => 8,
                    },
                    'notify_central_admin' => $urgency === 'critical',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // --- General Guidance ---
                $rules[] = [
                    'category_id'                => 5,
                    'campus_id'                  => $campus,
                    'urgency_level'              => $urgency,
                    'primary_handler_role_id'    => 3, // Course Adviser
                    'escalation_handler_role_id' => 4, // HOD
                    'secondary_escalation_role_id' => null,
                    'sla_hours'                  => match($urgency) {
                        'low'      => 96,
                        'medium'   => 72,
                        'high'     => 48,
                        'critical' => 24,
                    },
                    'notify_central_admin' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('routing_rules')->insert($rules);
    }
}