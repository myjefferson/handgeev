<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plan;
use App\Services\HashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Auth;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['store']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id_user = Auth::user()->id;
        $user = User::where(['id' => $id_user])->first();
        return view('pages.dashboard.user.index', compact(['user']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar os dados
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            // 'timezone' => 'nullable|string|max:50',
            // 'language' => 'nullable|string|max:10',
            // 'phone' => 'nullable|string|max:20',
        ]);

        // Obter o plano free padrão
        $freePlan = Plan::where('name', 'free')->first();
        
        if (!$freePlan) {
            throw new \Exception('Plano free não configurado no sistema');
        }
        
        // Criar o usuário com todos os campos necessários
        $user = User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            // 'timezone' => $validated['timezone'] ?? 'UTC',
            'timezone' => 'UTC',
            // 'language' => $validated['language'] ?? 'pt_BR',
            'language' => $validated['language'] ?? 'pt_BR',
            // 'phone' => $validated['phone'] ?? null,
            'phone' => null,
            'current_plan_id' => $freePlan->id,
            'plan_expires_at' => null,
            'status' => 'active',
            'email_verified_at' => now(), // Ou null se precisar verificação por email
        ]);

        // // Atribuir role free ao usuário
        // $freeRole = Role::where('name', 'free')->first();
        // if ($freeRole) {
        //     $user->assignRole($freeRole);
        // }

        $user->assignRole('free');

        // Gerar hashes API se necessário
        $user->update([
            'primary_hash_api' =>  HashService::generateUniqueHash(),
            'secondary_hash_api' => HashService::generateUniqueHash()
        ]);

        // Logar o usuário
        Auth::login($user);

        return redirect()->route('dashboard.personal-data.edit')
            ->with(['success' => 'Conta criada com sucesso!']);
        try {

        } catch (\Exception $e) {
            // return redirect()->route('register.index')
            //     ->with(['error' => 'Ocorreu um erro ao criar a conta: ' . $e->getMessage()])
            //     ->withInput();
            return dd($e->error());
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
            'current_plan_id' => $request->plan_id,
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
            'premium_users' => User::whereHas('plan', function($query) {
                $query->where('name', 'premium');
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
