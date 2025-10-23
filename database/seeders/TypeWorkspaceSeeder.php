<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeWorkspaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'id' => 1,
                'description' => 'Tópico Único',
            ],
            [
                'id' => 2, 
                'description' => 'Um ou Mais Tópicos',
            ],
        ];

        foreach ($types as $type) {
            DB::table('type_workspaces')->updateOrInsert(
                ['id' => $type['id']], // Busca por ID
                $type // Dados para inserir/atualizar
            );
        }

        $this->command->info('Tipos de workspace inseridos com sucesso!');
    }
}