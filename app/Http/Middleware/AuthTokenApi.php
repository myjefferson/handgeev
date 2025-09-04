<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

class AuthTokenApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {            
            if(!Auth::check() && !Auth::user()){
                $user = JWTAuth::parseToken()->authenticate();

                if (!$user && !Auth::check()) {
                    // Se o token for válido, mas o usuário não for encontrado no banco de dados.
                    return response()->json(['error' => 'Usuário não encontrado.'], 404);
                }
            }


        } catch (TokenExpiredException $e) {
            // Lida com o erro de token expirado.
            return response()->json(['error' => 'Token expirado.'], 401);
        } catch (TokenInvalidException $e) {
            // Lida com o erro de token inválido (por exemplo, assinado com chave errada).
            return response()->json(['error' => 'Token inválido.'], 401);
        } catch (TokenBlacklistedException $e) {
            // Lida com o erro de token na lista negra (revogado).
            return response()->json(['error' => 'Token na lista negra.'], 401);
        } catch (Exception $e) {
            // Lida com quaisquer outras exceções, como token não fornecido.
            return response()->json(['error' => 'Token de autenticação não fornecido ou inválido.'], 401);
        }

        // Se o código chegou aqui, o token foi validado e o usuário foi autenticado.
        return $next($request);
    }
}
