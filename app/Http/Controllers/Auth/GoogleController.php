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
        try {
            return Socialite::driver('google')
                ->redirectUrl(config('services.google.redirect'))
                ->redirect();
        } catch (\Exception $e) {
            \Log::error('Erro no redirect do Google: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google' => 'Erro ao redirecionar para Google.'
            ]);
        }
    }

    public function callback()
    {
        try {
            // Verificar se há erro na requisição
            if (request()->has('error')) {
                \Log::error('Erro no callback do Google: ' . request('error'));
                return redirect('/login')->withErrors([
                    'google' => 'Erro na autenticação: ' . request('error')
                ]);
            }

            // Obter usuário do Google
            $googleUser = Socialite::driver('google')
                ->redirectUrl(config('services.google.redirect'))
                ->user();
            
            if (!$googleUser->getEmail()) {
                throw new \Exception('Email não fornecido pelo Google');
            }

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
                    'remember_token' => Str::random(60), // ← ADICIONE ESTA LINHA
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
                        'remember_token' => Str::random(60), // ← ADICIONE ESTA LINHA
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
                        'remember_token' => Str::random(60), // ← ADICIONE ESTA LINHA
                    ]);
                }
                
                // Garantir que o remember_token existe
                if (empty($user->remember_token)) {
                    $user->update([
                        'remember_token' => Str::random(60),
                    ]);
                }
            }
            
            // Fazer login - use false se ainda estiver com problemas
            Auth::login($user, true);
            
            // Atualizar último login
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
            
            // Redirecionar para dashboard
            return redirect()->intended('/dashboard/home');
            
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            \Log::error('InvalidStateException no login com Google: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google' => 'Sessão expirada. Tente novamente.'
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            \Log::error('ClientException no login com Google: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google' => 'Erro de conexão com Google. Tente novamente.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro no login com Google: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
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