<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Services\HashService;
use App\Models\Structure;
use App\Models\StructureField;
use Inertia\Inertia;

class GoogleController extends Controller
{
    public function redirect()
    {
        try {
            Log::info('Google redirect iniciado');
            
            // Armazenar URL de retorno se estiver autenticado
            if (Auth::check()) {
                Session::put('google_connect_return', url()->previous());
            }
            
            return Socialite::driver('google')
                ->redirectUrl(config('services.google.redirect'))
                ->stateless()
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
        // Configuração especial para desenvolvimento
        if (app()->environment('local')) {
            $client = new \GuzzleHttp\Client([
                'verify' => false,
                'timeout' => 30,
                'connect_timeout' => 30,
                'http_errors' => false,
            ]);
            Socialite::driver('google')->setHttpClient($client);
        }
        
        try {
            // Verificar se há erro na requisição
            if (request()->has('error')) {
                $error = request('error');
                Log::error('Erro no callback do Google: ' . $error);
                return $this->handleError('Erro na autenticação: ' . $error);
            }

            // Obter usuário do Google
            Log::info('Tentando obter usuário do Google...');
            $googleUser = Socialite::driver('google')
                ->redirectUrl(config('services.google.redirect'))
                ->stateless()
                ->user();
            
            Log::info('Usuário Google obtido', [
                'email' => $googleUser->getEmail(),
                'id' => $googleUser->getId()
            ]);
            
            if (!$googleUser->getEmail()) {
                throw new \Exception('Email não fornecido pelo Google');
            }

            // Verificar se já existe usuário com este google_id
            $existingGoogleUser = User::where('google_id', $googleUser->getId())->first();
            
            // Buscar usuário por email (incluindo deletados)
            $existingEmailUser = User::withTrashed()->where('email', $googleUser->getEmail())->first();
            
            // CASO 1: Usuário já está logado e quer conectar conta Google
            if (Auth::check()) {
                return $this->handleConnectedUser(Auth::user(), $googleUser, $existingGoogleUser);
            }
            
            // CASO 2: Usuário NÃO está logado
            return $this->handleNonLoggedUser($googleUser, $existingGoogleUser, $existingEmailUser);
            
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::error('InvalidStateException: ' . $e->getMessage());
            return $this->handleError('Sessão expirada. Tente novamente.');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('ClientException: ' . $e->getMessage());
            return $this->handleError('Erro de conexão com Google. Verifique suas credenciais.');
        } catch (\Exception $e) {
            Log::error('=== ERRO COMPLETO NO LOGIN GOOGLE ===', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->handleError('Falha na autenticação: ' . $e->getMessage());
        }
    }

    /**
     * Lida com usuário JÁ LOGADO que quer conectar conta Google
     */
    private function handleConnectedUser(User $currentUser, $googleUser, $existingGoogleUser)
    {
        Log::info('Usuário logado quer conectar Google', ['user_id' => $currentUser->id]);
        
        // Verificar se este Google ID já está vinculado a OUTRA conta
        if ($existingGoogleUser && $existingGoogleUser->id !== $currentUser->id) {
            Log::warning('Google ID já vinculado a outra conta', [
                'current_user' => $currentUser->id,
                'linked_user' => $existingGoogleUser->id
            ]);
            
            Session::flash('error', 'Esta conta Google já está vinculada a outra conta em nosso sistema.');
            return $this->redirectToReturnUrl();
        }
        
        // Verificar se email do Google é DIFERENTE do email atual
        if ($googleUser->getEmail() !== $currentUser->email) {
            Log::info('Email diferente, perguntando ao usuário', [
                'current' => $currentUser->email,
                'google' => $googleUser->getEmail()
            ]);
            
            // Armazenar dados temporários
            Session::put('google_link_data', [
                'user_id' => $currentUser->id,
                'google_id' => $googleUser->getId(),
                'google_email' => $googleUser->getEmail(),
                'google_name' => $googleUser->getName(),
                'google_avatar' => $googleUser->getAvatar(),
                'action' => 'link_different_email'
            ]);
            
            return redirect()->route('google.confirm-email');
        }
        
        // Vincular conta Google à conta atual
        DB::beginTransaction();
        try {
            $this->linkGoogleAccount($currentUser, $googleUser);
            DB::commit();
            
            Session::flash('success', 'Conta Google vinculada com sucesso!');
            Log::info('Conta Google vinculada', ['user_id' => $currentUser->id]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao vincular conta Google: ' . $e->getMessage());
            Session::flash('error', 'Erro ao vincular conta Google.');
        }
        
        return $this->redirectToReturnUrl();
    }

    /**
     * Lida com usuário NÃO LOGADO
     */
    private function handleNonLoggedUser($googleUser, $existingGoogleUser, $existingEmailUser)
    {
        // CASO A: Já existe usuário com este google_id (login normal)
        if ($existingGoogleUser) {
            Log::info('Usuário encontrado por google_id', ['user_id' => $existingGoogleUser->id]);
            return $this->loginUser($existingGoogleUser, $googleUser);
        }
        
        // CASO B: Existe usuário com este email mas SEM google_id (associação)
        if ($existingEmailUser) {
            Log::info('Usuário encontrado por email sem google_id', [
                'user_id' => $existingEmailUser->id,
                'has_google_id' => !empty($existingEmailUser->google_id)
            ]);
            
            // Se o usuário já tem um google_id diferente, é um problema
            if (!empty($existingEmailUser->google_id) && $existingEmailUser->google_id !== $googleUser->getId()) {
                Log::error('Conflito de Google ID', [
                    'user_id' => $existingEmailUser->id,
                    'existing_google_id' => $existingEmailUser->google_id,
                    'new_google_id' => $googleUser->getId()
                ]);
                
                Session::flash('error', 
                    'Este email já está associado a outra conta Google. ' .
                    'Por favor, faça login com a conta Google original ou use email/senha.'
                );
                return redirect('/login');
            }
            
            // Perguntar se quer associar as contas
            if ($existingEmailUser->trashed()) {
                $existingEmailUser->restore();
            }
            
            Session::put('google_link_data', [
                'user_id' => $existingEmailUser->id,
                'google_id' => $googleUser->getId(),
                'google_email' => $googleUser->getEmail(),
                'google_name' => $googleUser->getName(),
                'google_avatar' => $googleUser->getAvatar(),
                'action' => 'link_existing_account'
            ]);
            
            return redirect()->route('google.confirm-link');
        }
        
        // CASO C: Não existe usuário (criar novo)
        Log::info('Criando novo usuário...');
        return $this->createNewUser($googleUser);
    }

    /**
     * Cria novo usuário com Google
     */
    private function createNewUser($googleUser)
    {
        DB::beginTransaction();
        try {
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
            
            $user = User::create($userData);
            
            // Atribuir role free
            $user->assignRole(User::ROLE_FREE);
            Log::info('Role atribuída');
            
            // Gerar hashes API
            $user->update([
                'global_key_api' => HashService::generateUniqueHash()
            ]);
            
            // Criar estruturas padrão
            $this->createDefaultStructures($user);
            
            DB::commit();
            
            Log::info('Usuário criado com ID: ' . $user->id);
            
            return $this->loginUser($user, $googleUser);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao criar usuário: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Realiza login do usuário
     */
    private function loginUser(User $user, $googleUser)
    {
        try {
            // Se o usuário foi deletado, restaurar
            if ($user->trashed()) {
                $user->restore();
                $user->update([
                    'status' => 'active',
                    'deleted_at' => null,
                ]);
            }
            
            // Atualizar dados do Google
            $updateData = [
                'avatar' => $googleUser->getAvatar(),
                'email_verified' => true,
                'email_verified_at' => now(),
                'provider_name' => 'google',
                'remember_token' => Str::random(60),
            ];
            
            // Atualizar google_id se estiver vazio
            if (empty($user->google_id)) {
                $updateData['google_id'] = $googleUser->getId();
            }
            
            $user->update($updateData);
            
            // Fazer login
            session()->flush();
            Auth::login($user, true);
            request()->session()->regenerate();
            
            Log::info('Login realizado com sucesso');
            
            // Atualizar último login
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
            
            // Redirecionar
            return redirect()->intended('/dashboard/home');
            
        } catch (\Exception $e) {
            Log::error('Erro no login: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vincula conta Google ao usuário atual
     */
    private function linkGoogleAccount(User $user, $googleUser)
    {
        $updateData = [
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'provider_name' => 'google',
            'email_verified' => true,
            'email_verified_at' => now(),
        ];
        
        $user->update($updateData);
        
        Log::info('Conta Google vinculada', [
            'user_id' => $user->id,
            'google_id' => $googleUser->getId()
        ]);
    }

    /**
     * Redireciona para URL de retorno ou dashboard
     */
    private function redirectToReturnUrl()
    {
        $returnUrl = Session::get('google_connect_return', '/dashboard/home');
        Session::forget('google_connect_return');
        
        return redirect($returnUrl);
    }

    /**
     * Manipula erro com flash message
     */
    private function handleError($message)
    {
        Session::flash('error', $message);
        return redirect('/login');
    }

    /**
     * Página de confirmação para vincular contas
    */
    public function showConfirmLink()
    {
        if (!Session::has('google_link_data')) {
            return redirect('/login');
        }
        
        $data = Session::get('google_link_data');
        
        // Buscar usuário para obter email
        $user = User::find($data['user_id']);
        
        if (!$user) {
            Session::forget('google_link_data');
            return redirect('/login');
        }
        
        return Inertia::render('Auth/GoogleConfirmLink', [
            'google_link_data' => [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'google_email' => $data['google_email'],
                'google_name' => $data['google_name'],
                'google_avatar' => $data['google_avatar'],
                'action' => $data['action']
            ]
        ]);
    }

    // No método updateEmail (para a view de atualização de email)
    public function showUpdateEmail()
    {
        if (!Session::has('google_link_data')) {
            return redirect('/login');
        }
        
        $data = Session::get('google_link_data');
        
        $user = User::find($data['user_id']);
        
        if (!$user) {
            Session::forget('google_link_data');
            return redirect('/login');
        }
        
        return Inertia::render('Auth/GoogleUpdateEmail', [
            'google_link_data' => [
                'user_id' => $user->id,
                'current_email' => $user->email,
                'google_email' => $data['google_email']
            ]
        ]);
    }

    /**
     * Confirma a vinculação de contas
     */
    public function confirmLink(Request $request)
    {
        if (!Session::has('google_link_data')) {
            return redirect('/login');
        }
        
        $data = Session::get('google_link_data');
        
        DB::beginTransaction();
        try {
            $user = User::findOrFail($data['user_id']);
            
            // Vincular conta
            $updateData = [
                'google_id' => $data['google_id'],
                'avatar' => $data['google_avatar'],
                'provider_name' => 'google',
                'email_verified' => true,
                'email_verified_at' => now(),
            ];
            
            $user->update($updateData);
            
            DB::commit();
            
            Session::forget('google_link_data');
            
            // Se o email for diferente, perguntar se quer atualizar
            if ($data['action'] === 'link_different_email' && $data['google_email'] !== $user->email) {
                Session::put('google_link_data', $data);
                return redirect()->route('google.update-email');
            }
            
            // Se o usuário não estava logado, fazer login
            if (!Auth::check()) {
                Auth::login($user, true);
                return redirect()->intended('/dashboard/home')
                    ->with('success', 'Conta vinculada com sucesso! Agora você pode fazer login com Google.');
            }
            
            return redirect()->route('dashboard.home')
                ->with('success', 'Conta Google vinculada com sucesso!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao confirmar vinculação: ' . $e->getMessage());
            
            return redirect('/login')
                ->with('error', 'Erro ao vincular conta Google: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza email do usuário para o email do Google
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'action' => 'required|in:update,keep'
        ]);
        
        if (!Session::has('google_link_data')) {
            return redirect('/login');
        }
        
        $data = Session::get('google_link_data');
        
        DB::beginTransaction();
        try {
            $user = User::findOrFail($data['user_id']);
            
            $updateData = [
                'google_id' => $data['google_id'],
                'avatar' => $data['google_avatar'],
                'provider_name' => 'google',
                'email_verified' => true,
                'email_verified_at' => now(),
            ];
            
            // Se escolheu atualizar email
            if ($request->action === 'update') {
                // Verificar se o novo email já existe
                $existingUser = User::where('email', $data['google_email'])
                    ->where('id', '!=', $user->id)
                    ->first();
                    
                if ($existingUser) {
                    throw new \Exception('Este email já está em uso por outra conta.');
                }
                
                $updateData['email'] = $data['google_email'];
            }
            
            $user->update($updateData);
            
            DB::commit();
            
            Session::forget('google_link_data');
            
            // Fazer login
            Auth::login($user, true);
            
            $message = $request->action === 'update' 
                ? 'Email atualizado e conta Google vinculada com sucesso!'
                : 'Conta Google vinculada com sucesso!';
            
            return redirect()->intended('/dashboard/home')
                ->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar email: ' . $e->getMessage());
            
            return back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    /**
     * Desvincula conta Google do usuário atual
     */
    public function unlink(Request $request)
    {
        $user = Auth::user();
        
        if (empty($user->google_id)) {
            return response()->json(['error' => 'Sua conta não está vinculada ao Google.'], 400);
        }
        
        try {
            $user->update([
                'google_id' => null,
                'provider_name' => null,
            ]);
            
            return response()->json(['success' => 'Conta Google desvinculada com sucesso!']);
            
        } catch (\Exception $e) {
            Log::error('Erro ao desvincular Google: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao desvincular conta Google.'], 500);
        }
    }

    /**
     * Cria estruturas padrão
     */
    private function createDefaultStructures(User $user): void
    {
        try {
            // 1. Estrutura de Key-Value
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
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar estruturas padrão: ' . $e->getMessage());
            // Não lança exceção para não quebrar o fluxo principal
        }
    }
}