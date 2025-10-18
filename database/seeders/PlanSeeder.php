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
                'max_domains' => 1,
                'can_export' => false,
                'can_use_api' => false,
                'api_requests_per_minute' => 30,
                'api_requests_per_hour' => 500,
                'api_requests_per_day' => 2000,
                'burst_requests' => 5,
                'is_active' => true
            ],
            [
                'name' => 'start',
                'price' => 29.90,
                'max_workspaces' => 3,
                'max_topics' => 10,
                'max_fields' => 50,
                'max_domains' => 5,
                'can_export' => true,
                'can_use_api' => true,
                'api_requests_per_minute' => 60,
                'api_requests_per_hour' => 2000,
                'api_requests_per_day' => 10000,
                'burst_requests' => 15,
                'is_active' => true
            ],
            [
                'name' => 'pro',
                'price' => 79.90,
                'max_workspaces' => 10,
                'max_topics' => 30,
                'max_fields' => 200,
                'max_domains' => 50,
                'can_export' => true,
                'can_use_api' => true,
                'api_requests_per_minute' => 120,
                'api_requests_per_hour' => 5000,
                'api_requests_per_day' => 50000,
                'burst_requests' => 25,
                'is_active' => true
            ],
            [
                'name' => 'premium',
                'price' => 249.90,
                'max_workspaces' => null, // ilimitado
                'max_topics' => null, // ilimitado
                'max_fields' => null, // ilimitado
                'max_domains' => 100,
                'can_export' => true,
                'can_use_api' => true,
                'api_requests_per_minute' => 250,
                'api_requests_per_hour' => 25000,
                'api_requests_per_day' => 250000,
                'burst_requests' => 50,
                'is_active' => true
            ],
            [
                'name' => 'admin',
                'price' => 0.00,
                'max_workspaces' => null,
                'max_topics' => null,
                'max_fields' => null,
                'max_domains' => null,
                'can_export' => true,
                'can_use_api' => true,
                'api_requests_per_minute' => 1000,
                'api_requests_per_hour' => null,
                'api_requests_per_day' => null,
                'burst_requests' => 200,
                'is_active' => true
            ]
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}