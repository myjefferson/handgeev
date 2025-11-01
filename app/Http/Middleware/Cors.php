<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Workspace;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Cors
{
    public function handle(Request $request, Closure $next)
    {
        // Pega a origem da requisição
        $origin = $request->headers->get('Origin');
        
        // Se não tem origin, usa * (para Postman, curl, etc)
        if (!$origin) {
            $origin = '*';
        }

        // Para requisições OPTIONS (preflight), responde IMEDIATAMENTE
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization, Accept, X-Requested-With, X-Workspace-Id, X-API-Key')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        // Para outras requisições, processa e adiciona headers na resposta
        $response = $next($request);

        // Adiciona headers CORS na resposta
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization, Accept, X-Requested-With, X-Workspace-Id, X-API-Key');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }

    private function handlePreflight(Request $request, $origin, $originHost)
    {
        $workspace = $this->getWorkspaceFromRequest($request);
        
        if (!$workspace) {
            Log::warning('CORS Preflight: Workspace não encontrado');
            return response()->json(['error' => 'Workspace not found'], 404);
        }

        // Owner sempre passa
        if (Auth::check() && Auth::id() === $workspace->user_id) {
            return $this->buildPreflightResponse($origin);
        }

        // API desabilitada
        if (!$workspace->api_enabled) {
            Log::warning('CORS Preflight: API desabilitada', ['workspace_id' => $workspace->id]);
            return response()->json(['error' => 'API disabled'], 403);
        }

        // HTTPS obrigatório
        $httpsRequired = $workspace->api_https_required ?? true;
        if ($httpsRequired && !str_starts_with($origin, 'https://')) {
            Log::warning('CORS Preflight: HTTPS obrigatório', ['origin' => $origin]);
            return response()->json(['error' => 'Only HTTPS allowed'], 403);
        }

        // Se não tem restrição de domínio, permite
        if (!$workspace->api_domain_restriction) {
            Log::debug('CORS Preflight: Sem restrição, permitindo', ['origin' => $origin]);
            return $this->buildPreflightResponse($origin);
        }

        // Valida domínios permitidos
        $allowedDomains = $workspace->allowedDomains()->where('is_active', true)->get();
        
        if ($allowedDomains->isEmpty()) {
            return response()->json(['error' => 'No domains configured'], 403);
        }

        if (!$this->isDomainAllowed($originHost, $allowedDomains)) {
            Log::warning('CORS Preflight: Domínio não permitido', [
                'origin' => $origin,
                'allowed' => $allowedDomains->pluck('domain')
            ]);
            return response()->json(['error' => 'Domain not allowed'], 403);
        }

        return $this->buildPreflightResponse($origin);
    }

    private function buildPreflightResponse($origin)
    {
        return response()->noContent(200, [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Authorization, Accept, X-Requested-With, X-Workspace-Id, X-API-Key',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
        ]);
    }

    private function getWorkspaceFromRequest(Request $request)
    {
        // 1. Tenta pelo ID na rota
        $workspaceId = $request->route('workspaceId') ?? 
                      $request->route('workspace') ?? 
                      $request->header('X-Workspace-Id');

        if ($workspaceId) {
            return Workspace::with(['allowedDomains' => function($query) {
                $query->where('is_active', true);
            }])->find($workspaceId);
        }

        // 2. Tenta pelo Bearer Token
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = str_replace('Bearer ', '', $authHeader);
            return Workspace::where('workspace_key_api', $token)
                ->with(['allowedDomains' => function($query) {
                    $query->where('is_active', true);
                }])
                ->first();
        }

        // 3. Tenta pelo X-API-Key
        $apiKey = $request->header('X-API-Key');
        if ($apiKey) {
            return Workspace::where('workspace_key_api', $apiKey)
                ->with(['allowedDomains' => function($query) {
                    $query->where('is_active', true);
                }])
                ->first();
        }

        // 4. Tenta pelo topicId
        $topicId = $request->route('topicId');
        if ($topicId) {
            $topic = \App\Models\Topic::find($topicId);
            if ($topic) {
                return Workspace::with(['allowedDomains' => function($query) {
                    $query->where('is_active', true);
                }])->find($topic->workspace_id);
            }
        }

        // 5. Tenta pelo fieldId
        $fieldId = $request->route('fieldId');
        if ($fieldId) {
            $field = \App\Models\Field::with('topic')->find($fieldId);
            if ($field && $field->topic) {
                return Workspace::with(['allowedDomains' => function($query) {
                    $query->where('is_active', true);
                }])->find($field->topic->workspace_id);
            }
        }

        return null;
    }

    private function getHostWithPort(string $url): string
    {
        $parsed = parse_url($url);
        if (!isset($parsed['host'])) {
            return '';
        }

        $host = strtolower($parsed['host']);
        
        // Adiciona porta se existir e não for padrão (80 ou 443)
        if (isset($parsed['port'])) {
            $scheme = $parsed['scheme'] ?? 'http';
            $defaultPort = ($scheme === 'https') ? 443 : 80;
            
            if ($parsed['port'] != $defaultPort) {
                return "{$host}:{$parsed['port']}";
            }
        }

        return $host;
    }

    private function isDomainAllowed(string $originHost, $allowedDomains): bool
    {
        foreach ($allowedDomains as $allowedDomain) {
            $domain = strtolower(trim($allowedDomain->domain));

            // Match exato
            if ($originHost === $domain) {
                return true;
            }

            // Wildcard: *.exemplo.com
            if (str_starts_with($domain, '*.')) {
                $baseDomain = substr($domain, 2);
                
                // Verifica se é subdomínio do domínio base
                if ($originHost === $baseDomain || str_ends_with($originHost, '.' . $baseDomain)) {
                    return true;
                }
            }

            // Localhost com porta (localhost:3000 → localhost)
            if (str_starts_with($originHost, 'localhost') && $domain === 'localhost') {
                return true;
            }

            // Subdomínio match (app.exemplo.com → exemplo.com)
            if (str_ends_with($originHost, '.' . $domain)) {
                return true;
            }
        }

        return false;
    }

    private function blockOrigin($origin, $message = 'Origin not allowed')
    {
        return response()->json([
            'error' => 'CORS Error',
            'message' => $message,
            'origin' => $origin
        ], 403)->withHeaders([
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Credentials' => 'true',
        ]);
    }

    private function setCorsHeaders($response, $origin)
    {
        return $response
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization, Accept, X-Requested-With, X-Workspace-Id, X-API-Key')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}