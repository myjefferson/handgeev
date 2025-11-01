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

        // 3º: Buscar workspace da requisição
        $workspace = $this->getWorkspaceFromRequest($request);
        
        if (!$workspace) {
            // Se não encontrou workspace, BLOQUEIA por segurança
            return $this->blockOrigin($origin, 'Workspace not found');
        }

        // Verifica se a API está habilitada para este workspace
        if (!$workspace->api_enabled) {
            return response()->json([
                'error' => 'API disabled for this workspace'
            ], 403);
        }

        // 4º: SE restrição de domínio está ATIVA, verificar domínios permitidos
        if ($workspace->api_domain_restriction) {
            if ($this->isOriginAllowed($origin, $workspace)) {
                return $this->setCorsHeaders($response, $origin);
            }
            
            // Domínio não permitido
            return $this->blockOrigin($origin, 'Your domain is not in the allowed list for this workspace API.');
        }

        // 5º: SE restrição de domínio está DESABILITADA, permite QUALQUER origem
        return $this->setCorsHeaders($response, $origin);
    }

    private function handlePreflight(Request $request)
    {
        $origin = $request->headers->get('Origin');
        
        if (!$origin) {
            return response()->noContent();
        }

        // Para preflight, verificamos a mesma lógica
        $workspace = $this->getWorkspaceFromRequest($request);
        
        if ($workspace) {
            // Se API não está habilitada
            if (!$workspace->api_enabled) {
                return response()->noContent(403);
            }

            // Se restrição de domínio está ativa E a origem não é permitida
            if ($workspace->api_domain_restriction && !$this->isOriginAllowed($origin, $workspace)) {
                return response()->noContent(403);
            }

            // Se chegou aqui, permite a origem (restrição desativada ou origem permitida)
            return $this->buildPreflightResponse($origin);
        }

        // Se não encontrou workspace, permite por segurança (ou bloqueia, dependendo da sua preferência)
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

    private function getWorkspaceFromRequest(Request $request)
    {
        // Tenta obter workspace de várias formas possíveis
        $workspaceId = $request->route('workspace') ?? 
                      $request->route('workspaceId') ?? 
                      $request->input('workspace_id') ??
                      $request->header('X-Workspace-Id');

        if ($workspaceId) {
            return Workspace::with(['allowedDomains' => function($query) {
                $query->where('is_active', true);
            }])->find($workspaceId);
        }

        // Tenta pegar pela chave da API do Bearer Token
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $token = str_replace('Bearer ', '', $authHeader);
            return Workspace::where('workspace_key_api', $token)
                ->with(['allowedDomains' => function($query) {
                    $query->where('is_active', true);
                }])
                ->first();
        }

        // Tenta pela chave da API via header customizado
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
            'Access-Control-Allow-Origin' => $origin, // Para o frontend ler o erro
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