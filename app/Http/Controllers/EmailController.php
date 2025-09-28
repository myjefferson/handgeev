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
            
            return redirect()->route('login.show')->with('status', 'Enviamos um link de recuperação para seu e-mail!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erro ao enviar email. Tente novamente.']);
        }
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

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard.home');
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

        // Marcar email como verificado
        $user->update([
            'email_verified' => true,
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_sent_at' => null,
        ]);

        return redirect()->route('dashboard.home')
            ->with(['success' => 'Email verificado com sucesso! Bem-vindo ao Handgeev.']);
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
