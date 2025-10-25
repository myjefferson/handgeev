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
use App\Mail\EmailChangeConfirmation;
use App\Mail\PasswordResetMail;
use App\Mail\VerificationEmail;

class EmailController extends Controller
{
    /**
     * Altera o email e envia link de confirmação
     */
    public function updateEmail(Request $request)
    {
        if(!Auth::check()){
            abort(403);
        }
        
        if(!isset($request->email_confirm)){
            $request->validate([
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'current_password' => 'required|current_password'
            ]);
        }
        
        $user = Auth::user();
        $newEmail = isset($request->email_confirm) ? $request->email_confirm : $request->email;
        
        try {
            // Gerar token de confirmação
            $token = Str::random(64);
            $tokenKey = 'email_change_' . $user->id;
            
            // Deletar tokens antigos
            DB::table('password_reset_tokens')
            ->where('email', $tokenKey)
            ->delete();
            
            DB::table('password_reset_tokens')->insert([
                'email' => $tokenKey,
                'token' => $token . '|' . $newEmail,
                'created_at' => now()
            ]);
            
            Mail::to($newEmail)->queue(new EmailChangeConfirmation($user, $token, $newEmail));
            
            \Log::info('Link de confirmação de email enviado', [
                'user_id' => $user->id,
                'old_email' => $user->email,
                'new_email' => $newEmail
            ]);

            if(isset($request->email_confirm)){
                return response()->json([
                    'success' => 'Confirmação de email enviado.',
                    'message' => 'Enviamos um link de confirmação para seu novo email! O link expira em 24 horas.'
                ], 200);
            }

            return redirect()->back()
                ->with('status', 'Enviamos um link de confirmação para seu novo email! O link expira em 24 horas.');

        } catch (\Exception $e) {
            \Log::error('Erro ao enviar link de confirmação de email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            if(isset($request->email_confirm)){
                return response()->json([
                    'error' => 'Erro ao enviar link de confirmação.',
                    'message' => 'Erro ao enviar link de confirmação. Tente novamente.'
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Erro ao enviar link de confirmação. Tente novamente.']);
        }
    }

    /**
     * Confirma a alteração de email via token
     */
    public function confirmEmailChange($token)
    {
        // Buscar token na tabela password_reset_tokens
        $tokenData = DB::table('password_reset_tokens')
            ->where('token', 'like', $token . '%')
            ->first();

        if (!$tokenData) {
            return view('pages.auth.change-email-error', [
                'message' => 'Link de confirmação inválido ou expirado.'
            ]);
        }

        // Extrair user_id do email (formato: email_change_{user_id})
        if (!str_starts_with($tokenData->email, 'email_change_')) {
            return view('pages.auth.change-email-error', [
                'message' => 'Token inválido.'
            ]);
        }

        $userId = str_replace('email_change_', '', $tokenData->email);

        // Verificar se o token expirou (24 horas)
        $tokenExpired = now()->subHours(24)->gt($tokenData->created_at);
        if ($tokenExpired) {
            DB::table('password_reset_tokens')->where('token', $tokenData->token)->delete();
            return view('pages.auth.change-email-error', [
                'message' => 'Link de confirmação expirado. Solicite um novo link.'
            ]);
        }

        $user = User::find($userId);

        if (!$user) {
            return view('pages.auth.change-email-error', [
                'message' => 'Usuário não encontrado.'
            ]);
        }

        try {
            $tokenParts = explode('|', $tokenData->token);
            
            if (count($tokenParts) < 2) {
                return view('pages.auth.change-email-error', [
                    'message' => 'Token malformado. Solicite um novo link.'
                ]);
            }
            
            $newEmail = $tokenParts[1];

            if (!$newEmail) {
                return view('pages.auth.change-email-error', [
                    'message' => 'Email não encontrado no token.'
                ]);
            }

            // Verificar se o novo email ainda está disponível
            $emailExists = User::where('email', $newEmail)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($emailExists) {
                return view('pages.auth.change-email-error', [
                    'message' => 'Este email já está em uso por outro usuário.'
                ]);
            }

            // Atualizar email do usuário
            $oldEmail = $user->email;
            $user->update([
                'email' => $newEmail,
                'email_verified_at' => now(),
                'email_verified' => true,
                'email_verification_code' => null,
                'email_verification_sent_at' => null
            ]);

            // Deletar token usado
            DB::table('password_reset_tokens')->where('token', $tokenData->token)->delete();

            \Log::info('Email alterado com sucesso', [
                'user_id' => $user->id,
                'old_email' => $oldEmail,
                'new_email' => $newEmail
            ]);

            // Redirecionar para página de sucesso
            return view('pages.auth.change-email-success', [
                'newEmail' => $newEmail,
                'oldEmail' => $oldEmail
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao confirmar alteração de email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return view('pages.auth.change-email-error', [
                'message' => 'Erro ao confirmar alteração de email. Tente novamente.'
            ]);
        }
    }

    /**
     * Mostra formulário para confirmar novo email
     */
    public function showEmailConfirmForm()
    {
        $pendingChange = session('pending_email_change');
        
        $user = Auth::user();

        if (!$pendingChange && !$user) {
            return view('pages.auth.change-email-error', [
                'message' => 'Sessão expirada. Solicite um novo link de confirmação.'
            ]);
        }
        
        return view('email.email-change-confirmation', [
            'token' => $pendingChange['token'],
            'user' => $user,
            'newEmail' => $pendingChange['token']
        ]);
    }

    /**
     * Envia email de recuperação de senha
     */
    public function sendRecoveryAccountEmail(Request $request)
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
            Mail::to($user->email)->queue(new PasswordResetMail($user, $token));
            
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
    public function showVerifyCodeEmail()
    {
        if(Auth::check()){
            $user = Auth::user();
            
            if ($user->hasVerifiedEmail()) {
                return redirect()->route('dashboard.home');
            }

            $this->sendVerificationCode($user);
    
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
        Mail::to($user->email)->queue(new VerificationEmail($user, $verificationCode));

        // Log para debug (remova em produção)
        \Log::info("Código de verificação para {$user->email}: {$verificationCode}");
    }
}
