<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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
            Log::info('Google redirect iniciado');
            
            return Socialite::driver('google')
                ->redirectUrl(config('services.google.redirect'))
                ->stateless() // ← ADICIONE ESTA LINHA!
                ->redirect();
        } catch (\Exception $e) {
            Log::error('Erro no redirect do Google: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google' => 'Erro ao redirecionar para Google.'
            ]);
        }
    }

    public function callback()
    {
        Log::info('=== INICIANDO CALLBACK GOOGLE ===');
        
        try {
            // Verificar se há erro na requisição
            if (request()->has('error')) {
                $error = request('error');
                Log::error('Erro no callback do Google: ' . $error);
                return redirect('/login')->withErrors([
                    'google' => 'Erro na autenticação: ' . $error
                ]);
            }

            // Obter usuário do Google com tratamento de estado
            Log::info('Tentando obter usuário do Google...');
            $googleUser = Socialite::driver('google')
                ->redirectUrl(config('services.google.redirect'))
                ->stateless() // ← ADICIONE ESTA LINHA!
                ->user();
            
            Log::info('Usuário Google obtido', [
                'email' => $googleUser->getEmail(),
                'id' => $googleUser->getId()
            ]);
            
            if (!$googleUser->getEmail()) {
                throw new \Exception('Email não fornecido pelo Google');
            }

            // Buscar usuário incluindo os deletados
            $user = User::withTrashed()->where('email', $googleUser->getEmail())->first();
            
            Log::info('Usuário encontrado no banco?', ['existe' => $user ? 'sim' : 'não']);
            
            if (!$user) {
                Log::info('Criando novo usuário...');
                
                // CRIAR USUÁRIO CORRETAMENTE
                $userData = [
                    'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Usuário Google',
                    'surname' => '',
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt(Str::random(24)),
                    'email_verified' => true,
                    'email_verified_at' => now(),
                    'provider_name' => 'google',
                    'timezone' => 'UTC',
                    'language' => app()->getLocale(),
                    'status' => 'active',
                    'remember_token' => Str::random(60),
                ];
                
                Log::info('Dados do novo usuário:', $userData);
                
                // CRIAR usando create() que respeita $fillable
                $user = User::create($userData);
                
                Log::info('Usuário criado com ID: ' . $user->id);

                // Atribuir role free
                $user->assignRole(User::ROLE_FREE);
                Log::info('Role atribuída');

                // Gerar hashes API
                $user->update([
                    'global_key_api' => HashService::generateUniqueHash()
                ]);

                // Criar estruturas padrão
                $this->createDefaultStructures($user);

            } else {
                Log::info('Usuário já existe, atualizando...');
                
                // Se o usuário foi deletado, restaurar
                if ($user->trashed()) {
                    Log::info('Restaurando usuário deletado...');
                    $user->restore();
                    $user->update([
                        'status' => 'active',
                        'deleted_at' => null,
                        'remember_token' => Str::random(60),
                    ]);
                }

                // Atualizar google_id se não tiver
                if (empty($user->google_id)) {
                    Log::info('Atualizando google_id...');
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                        'provider_name' => 'google',
                        'email_verified' => true,
                        'email_verified_at' => now(),
                        'remember_token' => Str::random(60),
                    ]);
                }
                
                // Garantir remember_token
                if (empty($user->remember_token)) {
                    Log::info('Gerando remember_token...');
                    $user->update([
                        'remember_token' => Str::random(60),
                    ]);
                }
            }
            
            Log::info('Tentando fazer login...');
            
            // IMPORTANTE: Tente login de forma SEGURA
            try {
                // Primeiro, limpar sessão atual
                session()->flush();
                
                // Fazer login SEM remember inicialmente (menos propenso a erros)
                Auth::login($user);
                
                // Depois, regenerar a sessão
                request()->session()->regenerate();
                
                Log::info('Login realizado com sucesso');
                
            } catch (\Exception $authError) {
                Log::error('Erro no Auth::login: ' . $authError->getMessage());
                throw $authError;
            }
            
            // Atualizar último login
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
            
            Log::info('Redirecionando para dashboard...');
            
            // Redirecionar de forma segura
            return redirect()->route('dashboard.home');
            
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error('InvalidStateException: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google' => 'Sessão expirada. Tente novamente.'
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('ClientException: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'google' => 'Erro de conexão com Google. Verifique suas credenciais.'
            ]);
        } catch (\Exception $e) {
            Log::error('=== ERRO COMPLETO NO LOGIN GOOGLE ===');
            Log::error('Mensagem: ' . $e->getMessage());
            Log::error('Arquivo: ' . $e->getFile());
            Log::error('Linha: ' . $e->getLine());
            Log::error('Trace: ' . $e->getTraceAsString());
            
            return redirect('/login')->withErrors([
                'google' => 'Falha na autenticação. Detalhes: ' . $e->getMessage()
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