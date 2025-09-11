<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Criar as roles
        $roles = [
            User::ROLE_FREE,
            User::ROLE_PRO, 
            User::ROLE_ADMIN
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web'
            ]);
        }

        // Atribuir role free para todos os usuários existentes
        $freeRole = Role::where('name', User::ROLE_FREE)->first();
        if ($freeRole) {
            $users = User::all();
            foreach ($users as $user) {
                if (!$user->hasAnyRole($roles)) {
                    $user->assignRole($freeRole);
                }
            }
        }
    }
}