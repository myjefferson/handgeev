<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSuspendedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se o usuário está autenticado
        if (Auth::check()) {
            $user = Auth::user();
            
            // Verificar se o usuário está suspenso
            if($user->status === 'suspended'){
                Auth::logout();
                // Limpar a sessão
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('account.suspended')
                    ->withErrors([
                        'error' => __('login.messages.account_suspended')
                    ]);
            }
            
            // Verificar outros status que podem restringir acesso
            if (in_array($user->status, ['inactive', 'past_due', 'unpaid', 'incomplete'])) {
                // Para esses status, podemos mostrar um aviso mas permitir acesso limitado
                if (!$request->session()->has('show_status_warning')) {
                    $request->session()->flash('warning', $this->getStatusMessage($user->status));
                    $request->session()->put('show_status_warning', true);
                }
            }
        }

        return $next($request);
    }
    
    /**
     * Obter mensagem personalizada baseada no status
     */
    private function getStatusMessage(string $status): string
    {
        $messages = [
            'inactive' => 'Sua conta está inativa. Algumas funcionalidades podem estar limitadas.',
            'past_due' => 'Seu pagamento está pendente. Regularize sua situação para evitar limitações.',
            'unpaid' => 'Há pendências financeiras em sua conta. Entre em contato com o suporte.',
            'incomplete' => 'Seu cadastro está incompleto. Complete suas informações para acesso total.',
        ];
        
        return $messages[$status] ?? 'Seu status de conta requer atenção.';
    }
}