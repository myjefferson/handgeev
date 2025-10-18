<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Models\Workspace;
use Exception;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Illuminate\Support\Facades\RateLimiter;

class AuthTokenApi
{
    use Concerns\HandlesWorkspaceRequest;
    
    public function handle(Request $request, Closure $next): Response
    {
        // Aplicar rate limiting antes da autenticação
        if (!$this->checkPublicRateLimit($request)) {
            return response()->json([
                'error' => 'Rate limit exceeded',
                'message' => 'Too many requests. Please try again in a minute.'
            ], 429);
        }

        try {
            $workspace = $this->getWorkspaceFromRequest($request);
            
            //Se JWT é obrigatório, não permitir autenticação por workspace_key
            if ($workspace && $workspace->api_jwt_required) {
                // JWT obrigatório - só permite autenticação via JWT tradicional
                try {
                    $user = JWTAuth::parseToken()->authenticate();
                    if ($user) {
                        Auth::login($user);
                        return $this->handleRateLimit($request, $next);
                    }
                } catch (Exception $e) {
                    return response()->json([
                        'error' => 'JWT required',
                        'message' => 'This workspace requires JWT authentication. Please use /api/auth/login/token endpoint.',
                        // 'auth_endpoint' => url('/api/auth/login/token')
                    ], 401);
                }
            }
            if (Auth::check()) {
                // Já está autenticado, continuar
                return $this->handleRateLimit($request, $next);
            }

            // autenticar via workspace_key_api
            if ($this->authenticateViaWorkspaceHash($request)) {
                return $this->handleRateLimit($request, $next);
            }

            // Só então tentar JWT tradicional (para compatibilidade)
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if ($user) {
                    Auth::login($user);
                    return $this->handleRateLimit($request, $next);
                }
            } catch (Exception $e) {
                // Ignorar erro JWT, pois queremos priorizar workspace_key_api
            }

            // Se nenhum método de autenticação funcionou, retornar erro
            return response()->json(['error' => 'Token inválido.'], 401);

        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token expirado.'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido.'], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json(['error' => 'Token na lista negra.'], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Token de autenticação não fornecido ou inválido.'], 401);
        }
    }

    /**
     * Autenticar usando workspace_key_api como token (MÉTODO PRINCIPAL)
     */
    private function authenticateViaWorkspaceHash(Request $request): bool
    {
        $token = $this->getCleanBearerToken($request);
        
        if (!$token) {
            \Log::debug('Nenhum token Bearer fornecido');
            return false;
        }

        // Log para debug
        \Log::debug('Tentando autenticar com workspace_key_api', [
            'token_length' => strlen($token),
            'token_preview' => substr($token, 0, 10) . '...',
            'path' => $request->path()
        ]);

        // Buscar workspace pelo hash (com trim para garantir)
        $workspace = Workspace::where('workspace_key_api', trim($token))
                            ->where('api_enabled', true)
                            ->with('user')
                            ->first();

        if (!$workspace) {
            \Log::warning('Workspace não encontrado ou API desativada', [
                'token_length' => strlen($token),
                'token_preview' => substr($token, 0, 10) . '...'
            ]);
            
            // 🔥 DEBUG: Listar workspaces disponíveis para ajudar no diagnóstico
            $availableWorkspaces = Workspace::where('api_enabled', true)
                ->select('id', 'title', 'workspace_key_api')
                ->get()
                ->map(function($ws) {
                    return [
                        'id' => $ws->id,
                        'title' => $ws->title,
                        'hash_length' => strlen($ws->workspace_key_api),
                        'hash_preview' => substr($ws->workspace_key_api, 0, 10) . '...'
                    ];
                });
                
            \Log::debug('Workspaces disponíveis com API ativa', [
                'count' => $availableWorkspaces->count(),
                'workspaces' => $availableWorkspaces->toArray()
            ]);
            
            return false;
        }

        \Log::debug('Workspace encontrado', [
            'workspace_id' => $workspace->id,
            'workspace_title' => $workspace->title,
            'user_id' => $workspace->user_id
        ]);

        // Verificar se o workspace tem um user válido
        if (!$workspace->user) {
            \Log::error('Workspace sem usuário associado', [
                'workspace_id' => $workspace->id
            ]);
            return false;
        }

        // Autenticar como o dono do workspace
        Auth::login($workspace->user);
        
        \Log::debug('Autenticação bem-sucedida via workspace_key_api', [
            'user_id' => $workspace->user->id,
            'workspace_id' => $workspace->id
        ]);

        return true;
    }

    /**
     * 🔥 NOVO MÉTODO: Extrair e limpar o token Bearer
     * Remove espaços em branco e valida o formato
     */
    private function getCleanBearerToken(Request $request): ?string
    {
        $authorizationHeader = $request->header('Authorization');
        
        if (!$authorizationHeader) {
            return null;
        }

        \Log::debug('Authorization Header recebido', [
            'header' => $authorizationHeader,
            'header_length' => strlen($authorizationHeader)
        ]);

        // Verificar se começa com "Bearer"
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7); // Remove "Bearer "
        } else {
            // Tentar usar o header completo como token (para compatibilidade)
            $token = $authorizationHeader;
        }

        // Limpar o token: remover espaços em branco no início e fim
        $token = trim($token);
        
        // Remover possíveis aspas
        $token = trim($token, '"\'');
        
        // Verificar se o token não está vazio após limpeza
        if (empty($token)) {
            \Log::warning('Token vazio após limpeza', [
                'original_header' => $authorizationHeader
            ]);
            return null;
        }

        \Log::debug('Token após limpeza', [
            'token_length' => strlen($token),
            'token_preview' => substr($token, 0, 10) . '...'
        ]);

        return $token;
    }

    /**
     * Aplicar rate limiting após autenticação
     */
    private function handleRateLimit(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (!$this->checkUserRateLimit($request)) {
                return response()->json([
                    'error' => 'Rate limit exceeded',
                    'message' => 'Request limit exceeded for your plan. Please try again later.'
                ], 429);
            }
        }

        return $next($request);
    }

    /**
     * Rate limiting para requisições não autenticadas (por IP)
     */
    private function checkPublicRateLimit(Request $request): bool
    {
        $key = 'public_api:' . $request->ip();
        
        return RateLimiter::attempt(
            $key . ':minute', 
            30,
            function() {},
            60
        );
    }

    /**
     * Rate limiting baseado no plano do usuário
     */
    private function checkUserRateLimit(Request $request): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        $plan = $user->getPlan();
        
        if (!$plan || !$plan->can_use_api) {
            return false;
        }

        $limits = $this->getPlanLimits($plan);
        $userKey = 'user_api:' . $user->id;

        // Verificar limite por minuto
        if (!$this->checkRateLimit($userKey . ':minute', $limits['per_minute'], 60)) {
            return false;
        }
        
        // Verificar limite por hora
        if ($limits['per_hour'] > 0 && !$this->checkRateLimit($userKey . ':hour', $limits['per_hour'], 3600)) {
            return false;
        }
        
        // Verificar limite por dia
        if ($limits['per_day'] > 0 && !$this->checkRateLimit($userKey . ':day', $limits['per_day'], 86400)) {
            return false;
        }

        return true;
    }

    private function getPlanLimits($plan): array
    {
        if (!$plan) {
            return [
                'per_minute' => 60,
                'per_hour' => 1000,
                'per_day' => 10000,
            ];
        }
        
        return [
            'per_minute' => $plan->api_requests_per_minute ?? 60,
            'per_hour' => $plan->api_requests_per_hour ?? 1000,
            'per_day' => $plan->api_requests_per_day ?? 10000,
        ];
    }
    
    private function checkRateLimit(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        if ($maxAttempts === 0) return true;
        
        return RateLimiter::attempt(
            $key, 
            $maxAttempts, 
            function() {},
            $decaySeconds
        );
    }
}