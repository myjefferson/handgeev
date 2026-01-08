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
                'structures' => 3,
                'workspaces' => 1,
                'topics' => 3,
                'fields' => 10,
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
                'structures' => 10,
                'workspaces' => 3,
                'topics' => 10,
                'fields' => 50,
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
                'structures' => 20,
                'workspaces' => 10,
                'topics' => 30,
                'fields' => 200,
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
                'structures' => 999,
                'workspaces' => 999,
                'topics' => 999,
                'fields' => 999,
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
                'structures' => 9999,
                'workspaces' => 9999,
                'topics' => 9999,
                'fields' => 9999,
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