<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use App\Mail\VerificationEmail;

class EmailController extends Controller
{
    /**
     * Envia email de recuperação de senha
     */
    public function sendRecoveryEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Este e-mail não está cadastrado em nosso sistema.'
        ]);

        $user = User::where('email', $request->email)->first();

        // Verifica se o usuário está ativo
        if ($user->status !== 'active') {
            return redirect()->back()->withErrors(['email' => 'Não é possível recuperar a senha de uma conta inativa ou suspensa.']);
        }

        // Verifica se já existe um token recente (evita spam)
        $recentToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('created_at', '>', now()->subMinutes(5))
            ->first();

        if ($recentToken) {
            return redirect()->back()->withErrors(['email' => 'Um link de recuperação já foi enviado recentemente. Verifique seu email ou aguarde 5 minutos.']);
        }

        // Gera token
        $token = Str::random(64);
        
        // Deleta tokens antigos
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        
        // Insere novo token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // Envia email
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $token));
            
            // Log para auditoria
            \Log::info('Email de recuperação enviado', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('login.show')->with('status', 'Enviamos um link de recuperação para seu e-mail! O link expira em 60 minutos.');
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email de recuperação', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->withErrors(['error' => 'Erro ao enviar email. Tente novamente em alguns minutos.']);
        }
    }

    /**
     * Verifica se um token de recuperação é válido
     */
    public function checkTokenValidity($token)
    {
        $resetData = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$resetData) {
            return response()->json(['valid' => false, 'message' => 'Token inválido']);
        }

        // Verifica se o token expirou (60 minutos)
        $tokenExpired = now()->subMinutes(60)->gt($resetData->created_at);
        if ($tokenExpired) {
            DB::table('password_reset_tokens')->where('token', $token)->delete();
            return response()->json(['valid' => false, 'message' => 'Token expirado']);
        }

        return response()->json([
            'valid' => true,
            'email' => $resetData->email,
            'created_at' => $resetData->created_at
        ]);
    }

    /**
     * Mostra a página de verificação de email
     */
    public function showVerifyEmail()
    {
        if(Auth::check()){
            $user = Auth::user();
            
            if ($user->hasVerifiedEmail()) {
                return redirect()->route('dashboard.home');
            }
    
            return view('pages.auth.verify-email', [
                'email' => $user->email
            ]);
        }
        return redirect()->route('login.show');
    }

    /**
     * Processa a verificação do código
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login.show')
            ->with('error', 'Sessão expirada. Faça login novamente.');
        }

        // Verificar se o código está correto
        if ($user->email_verification_code !== $request->code) {
            return redirect()->back()
            ->withErrors(['code' => 'Código inválido. Tente novamente.']);
        }

        // Verificar se o código expirou (30 minutos)
        if ($user->email_verification_sent_at->addMinutes(30)->isPast()) {
            return redirect()->back()
            ->withErrors(['code' => 'Código expirado. Solicite um novo código.']);
        }
            
        try {
            // Marcar email como verificado
            $user->update([
                'email_verified' => true,
                'email_verified_at' => now(),
                'email_verification_code' => null,
                'email_verification_sent_at' => null,
            ]);

            \Log::info("Email verificado com sucesso: {$user->email}");

            // VERIFICAR SE TEM PLANO PENDENTE
            $pendingPlan = session('pending_subscription_plan');
            $pendingUser = session('pending_verification_user');

            if ($pendingPlan && $pendingUser && $pendingUser == $user->id) {
                // Limpar sessão ANTES do redirecionamento
                session()->forget(['pending_subscription_plan', 'pending_verification_user']);

                // Redirecionar para checkout
                return redirect()->route('subscription.checkout.redirect', ['price_id' => $pendingPlan])
                    ->with('success', 'Email verificado com sucesso! Agora você pode finalizar sua assinatura.');
            }

            return redirect()->route('dashboard.home')
                ->with(['success' => 'Email verificado com sucesso! Bem-vindo ao Handgeev.']);
        } catch (\Exception $e) {
            \Log::error("Erro ao verificar email: " . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erro ao verificar email. Tente novamente.')
                ->withInput();
        }
    }
    
    /**
     * Reenvia o código de verificação
     */
    public function resendVerifyEmail()
    {
        $user = Auth::user();

        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard.home');
        }
        
        try {
            // Enviar o código por email
            $this->sendVerificationCode($user);

            return redirect()->back()->with('status', 'Código de verificação enviado para seu email!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao enviar email: ' . $e->getMessage()]);
        }
    }

    /**
     * Altera o email (caso o usuário tenha digitado errado)
     */
    public function updateVerifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id()
        ]);

        $user = Auth::user();

        try {
            $user->update([
                'email' => $request->email,
                'email_verified' => false,
                'email_verification_code' => null,
                'email_verification_sent_at' => null,
            ]);

            // Enviar novo código para o novo email
            $this->sendVerificationCode($user);

            return redirect()->back()
                ->with('status', 'Email atualizado! Um novo código foi enviado.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao atualizar email: ' . $e->getMessage()]);
        }
    }

    private function sendVerificationCode(User $user)
    {
        // Gerar novo código
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Atualizar usuário com novo código
        $user->update([
            'email_verification_code' => $verificationCode,
            'email_verification_sent_at' => now(),
        ]);
        // Enviar email
        Mail::to($user->email)->send(new VerificationEmail($user, $verificationCode));

        // Log para debug (remova em produção)
        \Log::info("Código de verificação para {$user->email}: {$verificationCode}");
    }
}
