<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            TypeViewsWorkspacesSeeder::class,
            TypeWorkspaceSeeder::class,
            RolesSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}