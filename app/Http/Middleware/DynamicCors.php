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

        // Sempre permitir localhost para desenvolvimento
        if ($this->isLocalhost($origin)) {
            return $this->setCorsHeaders($response, $origin);
        }

        // SEMPRE permitir domínios do Handgeev
        if ($this->isHandgeevDomain($origin)) {
            return $this->setCorsHeaders($response, $origin);
        }

        // Buscar workspace da requisição
        $workspace = $this->getWorkspaceFromRequest($request);
        
        if (!$workspace) {
            // Se não encontrou workspace, aplica política global
            return $this->blockOrigin($origin);
        }

        // Verifica se a API está habilitada para este workspace
        if (!$workspace->api_enabled) {
            return response()->json([
                'error' => 'API disabled for this workspace'
            ], 403);
        }

        // Se restrição de domínio está ativa, verificar domínios permitidos
        if ($workspace->api_domain_restriction) {
            if ($this->isOriginAllowed($origin, $workspace)) {
                return $this->setCorsHeaders($response, $origin);
            }
            
            // Domínio não permitido
            return response()->json([
                'error' => 'Origin not allowed for this workspace',
                'message' => 'Your domain is not in the allowed list for this workspace API. Please contact the workspace administrator to add your domain.'
            ], 403)->withHeaders([
                'Access-Control-Allow-Origin' => $origin, // Importante para o frontend ler o erro
            ]);
        }

        // Se restrição não está ativa, permite qualquer origem
        return $this->setCorsHeaders($response, $origin);
    }

    private function handlePreflight(Request $request)
    {
        $origin = $request->headers->get('Origin');
        
        if (!$origin) {
            return response()->noContent();
        }

        // Para preflight, verificamos se a origem seria permitida
        $workspace = $this->getWorkspaceFromRequest($request);
        
        if ($workspace && $workspace->api_enabled && $workspace->api_domain_restriction) {
            if (!$this->isOriginAllowed($origin, $workspace)) {
                return response()->noContent(403);
            }
        }

        $headers = [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Authorization, Accept, X-Requested-With, X-Workspace-Id, X-API-Key',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
        ];

        return response()->noContent(200, $headers);
    }

    private function isHandgeevDomain($origin): bool
    {
        $handgeevDomains = [
            'https://handgeev.com',
            'https://www.handgeev.com', 
            'https://app.handgeev.com',
            'https://*.handgeev.com'
        ];

        foreach ($handgeevDomains as $domain) {
            if (str_starts_with($domain, '*')) {
                $pattern = str_replace('*.', '', $domain);
                if (str_contains($origin, $pattern)) {
                    return true;
                }
            } elseif ($origin === $domain) {
                return true;
            }
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

    private function blockOrigin($origin, $message = 'Origin not allowed')
    {
        return response()->json([
            'error' => 'Origin not allowed',
            'message' => $message
        ], 403)->withHeaders([
            'Access-Control-Allow-Origin' => $origin, // Para o frontend ler o erro
        ]);
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

        // Tenta pegar pela chave da API
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');
        if ($apiKey) {
            return Workspace::where('workspace_key_api', $apiKey)
                ->with(['allowedDomains' => function($query) {
                    $query->where('is_active', true);
                }])
                ->first();
        }

        // Tenta pelo token JWT (se estiver usando)
        if ($request->bearerToken()) {
            // Aqui você pode adicionar lógica para extrair workspace do JWT se necessário
        }

        return null;
    }

    private function isOriginAllowed($origin, Workspace $workspace): bool
    {
        $originHost = parse_url($origin, PHP_URL_HOST);
        return $workspace->isDomainAllowed($originHost);
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