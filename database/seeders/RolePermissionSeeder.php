<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        try {
            // Reset cached roles and permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            $this->command->info('Criando permissões...');
            
            // Criar permissões
            $permissions = [
                'workspace.create',
                'workspace.view',
                'workspace.edit', 
                'workspace.delete',
                'workspace.export',
                'api.access',
                'user.manage',
                'plan.manage',
                'system.monitor',
            ];

            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web'
                ]);
                $this->command->info("Permissão criada: {$permission}");
            }

            $this->command->info('Criando roles...');
            
            // Criar roles e atribuir permissões
            $roles = [
                'free' => [
                    'workspace.create',
                    'workspace.view',
                    'workspace.edit',
                    'workspace.delete'
                ],
                'pro' => [
                    'workspace.create',
                    'workspace.view',
                    'workspace.edit',
                    'workspace.delete',
                    'workspace.export',
                    'api.access'
                ],
                'admin' => [
                    'workspace.create',
                    'workspace.view',
                    'workspace.edit',
                    'workspace.delete',
                    'workspace.export',
                    'api.access',
                    'user.manage',
                    'plan.manage',
                    'system.monitor'
                ]
            ];

            foreach ($roles as $roleName => $rolePermissions) {
                // Criar role
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web'
                ]);
                
                $this->command->info("Role criada: {$roleName}");
                
                // Atribuir permissões
                $role->syncPermissions($rolePermissions);
                $this->command->info("Permissões atribuídas à role {$roleName}: " . implode(', ', $rolePermissions));
            }

            $this->command->info('Seeder de roles e permissões executado com sucesso!');

        } catch (\Exception $e) {
            $this->command->error('Erro no seeder: ' . $e->getMessage());
            Log::error('Erro no RolePermissionSeeder: ' . $e->getMessage());
        }
    }
}