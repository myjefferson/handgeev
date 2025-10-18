<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AccountController extends Controller
{
    
    public function indexLogin()
    {
        if(Auth::check()){
            return redirect()->route('dashboard.home');
        }
        return view('pages.auth.login');
    }

    /**
     * Authentication Login
     */
    public function authLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return redirect()->route('login.show')->withErrors([
                'error' => __('login.messages.error')
            ]);
        }
        
        // Check password
        if(!Hash::check($request->password, $user->password)){
            return redirect()->route('login.show')->withErrors([
                'error' => __('login.messages.error')
            ]);
        }

        // Verificar se o email foi confirmado
        if (!$user->email_verified) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('verification.show')
                ->withErrors([
                    'error' => __('login.messages.email_not_verified')
                ]);
        }

        // Redirect account inactive
        if($user->status === 'inactive'){
            return redirect()->route('account.inactive')
                ->withErrors([
                    'error' => __('login.messages.account_inactive')
                ]);
        }
        
        // Redirect account suspended
        if($user->status === 'suspended'){
            return redirect()->route('account.suspended')
                ->withErrors([
                    'error' => __('login.messages.account_suspended')
                ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard.home')->with([
            'success' => __('login.messages.success')
        ]);
    }
    
    public function indexRegister(Request $request)
    {
        if(Auth::check()){
            return redirect()->route('dashboard.home');
        }

        $selectedPlan = $request->get('plan', 'free');
        
        // Se veio com um plano, salvar na sessão imediatamente
        if ($selectedPlan !== 'free') {
            $priceId = $this->getPriceIdByPlan($selectedPlan);
            if ($priceId) {
                session(['pending_subscription_plan' => $priceId]);
                \Log::info("Plano salvo na sessão no registro:", [
                    'plan_name' => $selectedPlan,
                    'price_id' => $priceId,
                    'session_id' => session()->getId()
                ]);
            }
        }
        
        return view('pages.auth.register');
    }

    private function getPriceIdByPlan($planName)
    {
        $priceIds = [
            'start' => config('services.stripe.prices.start'),
            'pro' => config('services.stripe.prices.pro'),
            'premium' => config('services.stripe.prices.premium'),
        ];
        
        return $priceIds[$planName] ?? null;
    }
    
    public function showRecovery(){
        return view('pages.auth.recovery-account');
    }

    /**
     * Mostra formulário de redefinição de senha
     */
    public function showResetForm($token)
    {
        // Verifica se o token é válido
        $resetData = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$resetData) {
            return redirect()->route('login.show')
                ->with('error', 'Link de recuperação inválido ou expirado.');
        }

        // Verifica se o token expirou (60 minutos)
        $tokenExpired = now()->subMinutes(60)->gt($resetData->created_at);
        if ($tokenExpired) {
            DB::table('password_reset_tokens')->where('token', $token)->delete();
            return redirect()->route('login.show')
                ->with('error', 'Link de recuperação expirado. Solicite uma nova recuperação.');
        }

        return view('pages.auth.reset-password', [
            'token' => $token,
            'email' => $resetData->email
        ]);
    }

    /**
     * Processa a redefinição de senha
     */
    public function updatePasswordRecovery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        ], [
            'password.regex' => 'A senha deve conter pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $resetData = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])->first();

        if (!$resetData) {
            return redirect()->back()->withErrors(['email' => 'Token inválido ou expirado.']);
        }

        // Verifica se o token expirou (60 minutos)
        $tokenExpired = now()->subMinutes(60)->gt($resetData->created_at);
        if ($tokenExpired) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect()->back()->withErrors(['email' => 'Token expirado. Solicite uma nova recuperação.']);
        }

        // Encontra o usuário
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Usuário não encontrado.']);
        }

        // Verifica se a nova senha é diferente da atual
        if (Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['password' => 'A nova senha deve ser diferente da senha atual.']);
        }

        try {
            // Atualiza a senha do usuário
            $user->password = Hash::make($request->password);
            $user->save();

            // Deleta o token usado
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Log da ação
            \Log::info('Senha redefinida com sucesso', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return redirect()->route('login.show')
                ->with('success', 'Senha redefinida com sucesso! Faça login com sua nova senha.');

        } catch (\Exception $e) {
            \Log::error('Erro ao redefinir senha', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Erro ao redefinir senha. Tente novamente.']);
        }
    }

    /**
     * API para verificar validade do token (para AJAX)
     */
    public function checkToken(Request $request, $token)
    {
        $resetData = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$resetData) {
            return response()->json([
                'valid' => false,
                'message' => 'Token inválido'
            ]);
        }

        $tokenExpired = now()->subMinutes(60)->gt($resetData->created_at);
        if ($tokenExpired) {
            DB::table('password_reset_tokens')->where('token', $token)->delete();
            return response()->json([
                'valid' => false,
                'message' => 'Token expirado'
            ]);
        }

        return response()->json([
            'valid' => true,
            'email' => $resetData->email
        ]);
    }

    
    /**
     * Remove the specified resource from storage.
     */
    public function logout()
    {
        $user = Auth::user();
        
        \Log::info('Usuário fez logout', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
        
        Auth::logout();
        Session::flush();
        
        return redirect()->route('login.show')
            ->with('status', 'Logout realizado com sucesso!');
    }
}