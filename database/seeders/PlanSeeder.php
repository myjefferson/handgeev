<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
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
                'api_requests_per_minute' => 30,
                'api_requests_per_hour' => 500,
                'api_requests_per_day' => 2000,
                'burst_requests' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'start',
                'price' => 10.00,
                'max_workspaces' => 3,
                'max_topics' => 10,
                'max_fields' => 50,
                'can_export' => true,
                'can_use_api' => true,
                'api_requests_per_minute' => 60,
                'api_requests_per_hour' => 2000,
                'api_requests_per_day' => 10000,
                'burst_requests' => 15,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'pro',
                'price' => 32.00,
                'max_workspaces' => 10,
                'max_topics' => 30,
                'max_fields' => 200,
                'can_export' => true,
                'can_use_api' => true,
                'api_requests_per_minute' => 120,
                'api_requests_per_hour' => 5000,
                'api_requests_per_day' => 50000,
                'burst_requests' => 25,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'premium',
                'price' => 70.90,
                'max_workspaces' => 999,
                'max_topics' => 999,
                'max_fields' => 999,
                'can_export' => true,
                'can_use_api' => true,
                'api_requests_per_minute' => 250,
                'api_requests_per_hour' => 25000,
                'api_requests_per_day' => 250000,
                'burst_requests' => 50,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'price' => 0.00,
                'max_workspaces' => 9999,
                'max_topics' => 9999,
                'max_fields' => 9999,
                'can_export' => true,
                'can_use_api' => true,
                'api_requests_per_minute' => 1000,
                'api_requests_per_hour' => 100000,
                'api_requests_per_day' => 1000000,
                'burst_requests' => 200,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('plans')->insert($plans);
        
        $this->command->info('Plans seeded successfully!');
    }
}