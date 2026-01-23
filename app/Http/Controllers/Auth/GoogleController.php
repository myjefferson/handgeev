<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Services\HashService;
use App\Models\Structure;
use App\Models\StructureField;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Buscar usuário incluindo os deletados (soft delete)
            $user = User::withTrashed()->where('email', $googleUser->getEmail())->first();
            
            if (!$user) {
                // Criar novo usuário SEM verificação de email
                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Usuário Google',
                    'surname' => '', // Deixe vazio ou extraia do nome se possível
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt(Str::random(24)),
                    'email_verified' => true, // IMPORTANTE: Marcar como verificado
                    'email_verified_at' => now(), // Preencher o timestamp
                    'provider_name' => 'google',
                    'timezone' => 'UTC',
                    'language' => app()->getLocale(),
                    'status' => 'active',
                ]);

                // Atribuir role free ao usuário
                $user->assignRole(User::ROLE_FREE);

                // Gerar hashes API
                $user->update([
                    'global_key_api' => HashService::generateUniqueHash()
                ]);

                // Criar estruturas padrão para o usuário
                $this->createDefaultStructures($user);

            } else {
                // Se o usuário foi deletado (soft delete), restaurar a conta
                if ($user->trashed()) {
                    $user->restore();
                    $user->update([
                        'status' => 'active',
                        'deleted_at' => null,
                    ]);
                }

                // Atualizar google_id e avatar se não tiver
                if (empty($user->google_id)) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'provider_name' => 'google',
                        'email_verified' => true, // Marcar como verificado
                        'email_verified_at' => now(),
                    ]);
                }
            }
            
            // Fazer login
            Auth::login($user, true);
            
            // Redirecionar para dashboard
            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            \Log::error('Erro no login com Google: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google' => 'Falha na autenticação com Google. Tente novamente.'
            ]);
        }
    }

    /**
     * Cria estruturas padrão para um novo usuário (copiado do UserController)
     */
    private function createDefaultStructures(User $user): void
    {
        // 1. Estrutura de Key-Value (Chave e Valor)
        $keyValueStructure = Structure::create([
            'user_id' => $user->id,
            'name' => 'Key-Value',
            'description' => 'Estrutura básica de pares chave-valor para armazenamento simples',
            'is_public' => false,
        ]);

        $keyValueFields = [
            ['name' => 'chave', 'type' => 'text', 'default_value' => '', 'is_required' => true, 'order' => 1],
            ['name' => 'valor', 'type' => 'text', 'default_value' => '', 'is_required' => false, 'order' => 2],
            ['name' => 'ativo', 'type' => 'boolean', 'default_value' => 'true', 'is_required' => true, 'order' => 6],
        ];

        foreach ($keyValueFields as $fieldData) {
            StructureField::create(array_merge($fieldData, ['structure_id' => $keyValueStructure->id]));
        }

        // Estrutura de Produtos
        $productStructure = Structure::create([
            'user_id' => $user->id,
            'name' => 'Produtos',
            'description' => 'Estrutura para gerenciamento de produtos',
            'is_public' => false,
        ]);

        $productFields = [
            ['name' => 'Nome', 'type' => 'text', 'is_required' => true, 'order' => 1],
            ['name' => 'Descrição', 'type' => 'text', 'is_required' => false, 'order' => 2],
            ['name' => 'Preço', 'type' => 'decimal', 'is_required' => true, 'order' => 3],
            ['name' => 'Estoque', 'type' => 'number', 'is_required' => true, 'order' => 4],
            ['name' => 'Ativo', 'type' => 'boolean', 'is_required' => true, 'order' => 5, 'default_value' => 'true'],
            ['name' => 'Data de Criação', 'type' => 'datetime', 'is_required' => false, 'order' => 6],
        ];

        foreach ($productFields as $field) {
            StructureField::create(array_merge($field, ['structure_id' => $productStructure->id]));
        }
    }
}