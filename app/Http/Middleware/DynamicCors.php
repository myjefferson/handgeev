<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Workspace;

class DynamicCors
{
    public function handle(Request $request, Closure $next)
    {
        // Handle preflight requests
        if ($request->isMethod('OPTIONS')) {
            return $this->handlePreflight($request);
        }

        $origin = $request->headers->get('Origin');
        $response = $next($request);

        // Se não tem Origin, não é CORS
        if (!$origin) {
            return $response;
        }

        // 1º: Sempre permitir localhost para desenvolvimento
        if ($this->isLocalhost($origin)) {
            return $this->setCorsHeaders($response, $origin);
        }

        // 2º: SEMPRE permitir domínios do Handgeev (seu próprio sistema)
        if ($this->isHandgeevDomain($origin)) {
            return $this->setCorsHeaders($response, $origin);
        }

        // 3º: PARA API COM BEARER TOKEN: permitir qualquer origem
        // A autenticação será feita depois pelo middleware 'api.auth_token'
        if ($this->isApiRequest($request)) {
            return $this->setCorsHeaders($response, $origin);
        }

        // 4º: Para outras rotas não-API, aplica a lógica de domínios personalizados
        $workspace = $this->getWorkspaceFromRequest($request);
        
        if (!$workspace) {
            return $this->blockOrigin($origin);
        }

        if (!$workspace->api_enabled) {
            return response()->json([
                'error' => 'API disabled for this workspace'
            ], 403);
        }

        if ($workspace->api_domain_restriction) {
            if ($this->isOriginAllowed($origin, $workspace)) {
                return $this->setCorsHeaders($response, $origin);
            }
            
            return $this->blockOrigin($origin, 'Your domain is not in the allowed list for this workspace API.');
        }

        return $this->setCorsHeaders($response, $origin);
    }

    private function isApiRequest($request): bool
    {
        // Verifica se é uma rota da API que requer autenticação Bearer
        $apiPaths = [
            'api/workspaces/',
            'api/topics/',
            'api/fields/',
            'api/auth/login/token',
        ];

        $path = $request->path();

        foreach ($apiPaths as $apiPath) {
            if (str_starts_with($path, $apiPath)) {
                return true;
            }
        }

        return false;
    }

    private function isHandgeevDomain($origin): bool
    {
        $handgeevDomains = [
            'https://handgeev.com',
            'https://www.handgeev.com', 
            'https://app.handgeev.com',
            'https://api.handgeev.com',
            'http://handgeev.com',
            'http://www.handgeev.com',
        ];

        $originHost = parse_url($origin, PHP_URL_HOST);
        
        foreach ($handgeevDomains as $domain) {
            $domainHost = parse_url($domain, PHP_URL_HOST);
            if ($originHost === $domainHost) {
                return true;
            }
        }

        if (str_ends_with($originHost, '.handgeev.com')) {
            return true;
        }

        return false;
    }

    private function isLocalhost($origin): bool
    {
        $devOrigins = [
            'http://localhost:3000', 
            'http://127.0.0.1:3000', 
            'http://localhost:5174',
            'http://localhost:5173',
            'http://localhost:8000',
            'http://localhost:8080'
        ];

        return in_array($origin, $devOrigins);
    }

    private function handlePreflight(Request $request)
    {
        $origin = $request->headers->get('Origin');
        
        if (!$origin) {
            return response()->noContent();
        }

        // Para preflight da API, sempre permitir (a autenticação vem depois)
        if ($this->isApiRequest($request)) {
            $headers = [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Origin, Content-Type, Authorization, Accept, X-Requested-With, X-Workspace-Id, X-API-Key',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age' => '86400',
            ];

            return response()->noContent(200, $headers);
        }

        // Para preflight não-API, aplica verificação normal
        $workspace = $this->getWorkspaceFromRequest($request);
        
        if ($workspace && $workspace->api_enabled && $workspace->api_domain_restriction) {
            if (!$this->isOriginAllowed($origin, $workspace)) {
                return response()->noContent(403);
            }
        }

        return response()->noContent(200, [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Authorization, Accept, X-Requested-With, X-Workspace-Id, X-API-Key',
            'Access-Control-Allow-Credentials' => 'true',
        ]);
    }

    private function getWorkspaceFromRequest(Request $request)
    {
        $workspaceId = $request->route('workspace') ?? 
                      $request->route('workspaceId') ?? 
                      $request->input('workspace_id') ??
                      $request->header('X-Workspace-Id');

        if ($workspaceId) {
            return Workspace::with(['allowedDomains' => function($query) {
                $query->where('is_active', true);
            }])->find($workspaceId);
        }

        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');
        if ($apiKey) {
            return Workspace::where('workspace_key_api', $apiKey)
                ->with(['allowedDomains' => function($query) {
                    $query->where('is_active', true);
                }])
                ->first();
        }

        return null;
    }

    private function isOriginAllowed($origin, Workspace $workspace): bool
    {
        $originHost = parse_url($origin, PHP_URL_HOST);
        return $workspace->isDomainAllowed($originHost);
    }

    private function blockOrigin($origin, $message = 'Origin not allowed')
    {
        return response()->json([
            'error' => 'Origin not allowed',
            'message' => $message
        ], 403)->withHeaders([
            'Access-Control-Allow-Origin' => $origin,
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