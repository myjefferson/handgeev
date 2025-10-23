<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeViewsWorkspacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typeViews = [
            [
                'id' => 1,
                'description' => 'Interface da API',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'description' => 'API REST JSON',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('type_views_workspaces')->insert($typeViews);
        
        $this->command->info('Type views workspaces seeded successfully!');
    }
}