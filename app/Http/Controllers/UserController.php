<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Plan;
use App\Models\RecordFieldValue;
use App\Services\HashService;
use App\Services\SubscriptionService;
use App\Mail\VerificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{
    use AuthorizesRequests;

    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id_user = Auth::user()->id;
        $user = User::where(['id' => $id_user])->first();
        $fieldsCount = RecordFieldValue::whereHas('record.topic.workspace', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        $userStats = [
            'workspaces' => $user->workspaces()->count(),
            'topics' => $user->topics()->count(),
            'fields' => $fieldsCount,
        ];
        return Inertia::render('Dashboard/Profile/Profile', [
            'lang' => __('profile'),
            'user' => $user,   
            'userStats' => $userStats,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function storeProfile(Request $request)
    {
        // Validar os dados com mensagens traduzidas
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'terms' => 'required|accepted',
        ], [
            'name.required' => __('register.validation.name_required'),
            'surname.required' => __('register.validation.surname_required'),
            'email.required' => __('register.validation.email_required'),
            'email.email' => __('register.validation.email_email'),
            'email.unique' => __('register.validation.email_unique'),
            'password.required' => __('register.validation.password_required'),
            'password.min' => __('register.validation.password_min'),
            'terms.required' => __('register.validation.terms_required'),
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'surname' => $validated['surname'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'timezone' => 'UTC',
                'language' => app()->getLocale(),
                'phone' => null,
                'status' => 'active',
                'email_verified' => false,
                'email_verification_code' => null,
                'email_verification_sent_at' => null,
            ]);

            // Atribuir role free ao usuário
            $user->assignRole(User::ROLE_FREE);

            // Gerar hashes API
            $user->update([
                'global_key_api' => HashService::generateUniqueHash()
            ]);

            // Gerar código de verificação
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            $user->update([
                'email_verification_code' => $verificationCode,
                'email_verification_sent_at' => now(),
            ]);

            // VERIFICAR SE TEM PLANO PENDENTE NA SESSÃO
            $pendingPlan = session('pending_subscription_plan');
            if ($pendingPlan) {
                session(['pending_verification_user' => $user->id]);
                \Log::info("Usuário associado ao plano pendente:", [
                    'user_id' => $user->id,
                    'plan' => $pendingPlan,
                    'session_id' => session()->getId()
                ]);
            }

            // Enviar email de verificação
            Mail::to($user->email)->queue(new VerificationEmail($user, $verificationCode));

            // Logar o usuário
            Auth::login($user);

            // Redirecionar para página de verificação
            return redirect()->route('verify.code.email.show')
                ->with([
                    'success' => __('register.messages.success'),
                    'email' => $user->email
                ]);

        } catch (\Exception $e) {
            \Log::error("Erro ao criar usuário: " . $e->getMessage());
            
            return redirect()->back()
                ->with(['error' => __('register.messages.error')])
                ->withInput();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Validação dos dados usando validate() diretamente
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
            ], [
                'name.required' => 'O nome é obrigatório.',
                'name.max' => 'O nome não pode ter mais de 255 caracteres.',
                'surname.required' => 'O sobrenome é obrigatório.',
                'surname.max' => 'O sobrenome não pode ter mais de 255 caracteres.',
                'email.required' => 'O email é obrigatório.',
                'email.email' => 'Digite um email válido.',
                'email.unique' => 'Este email já está em uso.',
                'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            ]);

            // Preparar dados para atualização
            $updateData = [
                'name' => $validated['name'],
                'surname' => $validated['surname'],
                'phone' => $validated['phone'] ?? null,
            ];

            // Atualizar usuário
            $user->update($updateData);

            return redirect()->route('user.profile')
                ->with('success', 'Perfil atualizado com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Por favor, corrija os erros abaixo.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar perfil: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Atualizar senha do usuário
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ], [
                'current_password.required' => 'A senha atual é obrigatória.',
                'new_password.required' => 'A nova senha é obrigatória.',
                'new_password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
                'new_password.confirmed' => 'A confirmação da senha não coincide.',
            ]);

            // Verificar senha atual
            if (!Hash::check($validated['current_password'], $user->password)) {
                return redirect()->back()
                    ->with('error', 'A senha atual está incorreta.')
                    ->with('tab', 'password');
            }

            // Verificar se a nova senha é diferente da atual
            if (Hash::check($validated['new_password'], $user->password)) {
                return redirect()->back()
                    ->with('error', 'A nova senha deve ser diferente da senha atual.')
                    ->with('tab', 'password');
            }

            // Atualizar senha
            $user->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            return redirect()->route('user.profile')
                ->with('success', 'Senha atualizada com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->with('tab', 'password')
                ->with('error', 'Por favor, corrija os erros abaixo.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar senha: ' . $e->getMessage())
                ->with('tab', 'password');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Este método agora requer autenticação
        $user = $request->user();
        $user->load('plan');
        
        return response()->json([
            'user' => $user,
            'subscription_active' => $user->hasActiveSubscription(),
            'can_export' => $user->canExportData(),
            'can_use_api' => $user->canUseApi(),
            'can_create_workspace' => $user->canCreateWorkspace()
        ]);
    }

    public function updatePlan(Request $request, User $user)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'expires_at' => 'nullable|date'
        ]);

        $user->update([
            // 'current_plan_id' => $request->plan_id,
            'plan_expires_at' => $request->expires_at
        ]);

        return response()->json(['message' => 'User plan updated successfully']);
    }

    public function stats()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereHas('status', function($query) {
                $query->where('name', 'active');
            })->count(),
            'pro_users' => User::whereHas('plan', function($query) {
                $query->where('name', 'pro');
            })->count(),
            'admin_users' => User::whereHas('plan', function($query) {
                $query->where('name', 'admin');
            })->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $user = User::where(['id' => Auth::user()->id])->first();
        return view('pages.dashboard.user.form', compact(['user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'surname' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'current_password' => 'required_with:password',
            'password' => 'sometimes|confirmed|min:8',
            'timezone' => 'sometimes|string|max:50',
            'language' => 'sometimes|string|max:10',
            'phone' => 'sometimes|string|max:20',
        ]);

        // Verificar senha atual se estiver alterando a senha
        if (isset($validated['password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json(['error' => 'Senha atual incorreta'], 422);
            }
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    // UpgradeController.php
    public function upgradeToPro(User $user)
    {
        // Apenas muda a role!
        $user->syncRoles([User::ROLE_PRO]);
        
        // Envia email de confirmação, etc...
    }

    // AdminController.php  
    public function changeUserPlan(Request $request, User $user)
    {
        $validated = $request->validate([
            'plan' => 'required|in:free,pro'
        ]);

        // Apenas muda a role!
        $user->syncRoles([$validated['plan']]);
        
        return back()->with('success', 'Plano atualizado!');
    }

    /**
     * Redirecionar para assinatura após verificação de email
     */
    public function redirectToSubscription(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login.show');
        }

        // Verificar se o email foi verificado
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verify.code.email.show')
                ->with('error', 'Por favor, verifique seu email antes de escolher um plano.');
        }

        // Se já tem assinatura ativa, redirecionar para dashboard
        if ($user->hasActiveStripeSubscription()) {
            return redirect()->route('dashboard.home')
                ->with('success', 'Você já possui uma assinatura ativa.');
        }

        // Redirecionar para página de planos
        return redirect()->route('subscription.pricing')
            ->with('info', 'Escolha o plano que melhor atende suas necessidades.');
    }
}
