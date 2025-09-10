<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run()
    {
        $plans = [
            [
                'name' => 'free',
                'price' => 0.00,
                'max_workspaces' => 1,
                'max_topics' => 3,
                'max_fields' => 10,
                'can_export' => false,
                'can_use_api' => false,
                'is_active' => true
            ],
            [
                'name' => 'premium',
                'price' => 29.00,
                'max_workspaces' => 5,
                'max_topics' => 0, // 0 = ilimitado
                'max_fields' => 0, // 0 = ilimitado
                'can_export' => true,
                'can_use_api' => true,
                'is_active' => true
            ],
            [
                'name' => 'admin',
                'price' => 0.00,
                'max_workspaces' => 0, // 0 = ilimitado
                'max_topics' => 0, // 0 = ilimitado
                'max_fields' => 0, // 0 = ilimitado
                'can_export' => true,
                'can_use_api' => true,
                'is_active' => true
            ]
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(
                ['name' => $planData['name']],
                $planData
            );
        }
    }
}