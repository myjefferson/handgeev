<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckDeactivatedAccount
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se há sessão especial de conta desativada
        if ($request->session()->has('deactivated_account_access')) {
            $deactivatedData = $request->session()->get('deactivated_account_access');
            
            // Verificar se o período expirou
            $deletedAt = Carbon::parse($deactivatedData['deleted_at']);
            if ($deletedAt->diffInDays(now()) > 30) {
                $request->session()->forget('deactivated_account_access');
                return redirect()->route('login.show')->withErrors([
                    'email' => 'O período de recuperação de 30 dias expirou.'
                ]);
            }

            // Permitir acesso à página de conta desativada
            return $next($request);
        }

        if (Auth::check()) {
            $user = Auth::user();
        
            return redirect()->route('dashboard.home')->with('info', 'Sua conta está ativa.');
        }

        return redirect()->route('login.show')->withErrors([
            'email' => 'Acesso não autorizado.'
        ]);
    }
}