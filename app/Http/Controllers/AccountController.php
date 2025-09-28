<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    
    public function indexLogin()
    {
        return view('pages.auth.login');
    }

    /**
     * Authentication Login
     */
    public function authLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user){
            return redirect()->route('login.show')->withErrors(['error' => 'Email ou senha inválidos']);
        }
        
        //check password
        if(!Hash::check($request->password, $user->password)){
            return redirect()->route('login.show')->withErrors(['error' => 'Email ou senha inválidos']);
        }

        // Verificar se o email foi confirmado
        if (!$user->email_verified) {
            Auth::login($user);
            return redirect()->route('verification.show')
                ->withErrors(['error' => 'Por favor, verifique seu email antes de acessar o dashboard.']);
        }

        //redirect account inactive
        if($user->status === 'inactive'){
            return redirect()->route('account.inactive');
        }
        
        //redirect account suspended
        if($user->status === 'suspended'){
            return redirect()->route('account.suspended');
        }

        Auth::login($user);

        return redirect()->route('dashboard.home')->with(['success' => 'Você entrou!']);
    }
    
    public function indexRegister()
    {
        return view('pages.auth.register');
    }
    
    public function showRecovery(){
        return view('pages.auth.recovery-account');
    }

    /**
     * Mostra formulário de redefinição de senha
     */
    public function showResetForm($token)
    {
        return view('pages.auth.reset-password', ['token' => $token]);
    }

    /**
     * Processa a redefinição de senha
     */
    public function updatePasswordRecovery(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

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

        // Atualiza a senha do usuário
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Deleta o token usado
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login.show')->with('status', 'Senha redefinida com sucesso! Faça login com sua nova senha.');
    }

    
    /**
     * Remove the specified resource from storage.
     */
    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login.show');
    }
}