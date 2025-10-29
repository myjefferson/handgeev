<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CheckAllowedDomain
{
    use Concerns\HandlesWorkspaceRequest;

    /**
     * Middleware principal: validação de domínio, HTTPS e acesso do workspace.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ignorar rotas não pertencentes à API
        if (!$this->isApiRoute($request)) {
            return $next($request);
        }

        /**
         * 1. Identificação do workspace (precisamos disso primeiro para verificar a configuração HTTPS)
         */
        $workspace = $this->getWorkspaceFromRequest($request);

        if (!$workspace) {
            return response()->json([
                'error' => 'Workspace not found',
                'message' => 'The requested workspace does not exist'
            ], 404);
        }

        /**
         * 🔒 2. Segurança HTTPS — configurável por workspace
         */
        $httpsRequired = $workspace->api_https_required ?? true;

        if ($httpsRequired) {
            // Verificar se a conexão é segura
            if (!$request->isSecure()) {
                return response()->json([
                    'error' => 'Insecure connection',
                    'message' => 'Only HTTPS requests are allowed for this workspace.'
                ], 403);
            }

            // Verificar também o header Origin/Referer
            $originHeader = $request->header('Origin') ?? $request->header('Referer');
            if ($originHeader && !str_starts_with($originHeader, 'https://')) {
                return response()->json([
                    'error' => 'Insecure origin',
                    'message' => 'Requests must originate from a secure (HTTPS) domain for this workspace.'
                ], 403);
            }

            Log::debug('HTTPS requirement enabled for workspace', [
                'workspace_id' => $workspace->id,
                'https_required' => $httpsRequired
            ]);
        } else {
            Log::debug('HTTPS requirement disabled for workspace - allowing HTTP', [
                'workspace_id' => $workspace->id,
                'https_required' => $httpsRequired
            ]);
        }

        // 👑 Proprietário do workspace sempre pode acessar (mesmo se HTTPS estiver desabilitado)
        if (Auth::check() && Auth::id() === $workspace->user_id) {
            Log::debug('Owner access - bypassing all restrictions', [
                'workspace_id' => $workspace->id,
                'user_id' => Auth::id()
            ]);
            return $next($request);
        }

        /**
         * 3. Checar se API está habilitada
         */
        if (!$workspace->api_enabled) {
            return response()->json([
                'error' => 'API disabled',
                'message' => 'The API for this workspace is currently disabled'
            ], 403);
        }

        /**
         * 4. Checar se há restrição de domínio
         */
        if (!$workspace->api_domain_restriction) {
            Log::debug('Domain restriction disabled - allowing all domains', [
                'workspace_id' => $workspace->id
            ]);
            return $next($request);
        }

        /**
         * 5. Buscar domínios permitidos
         */
        $allowedDomains = $workspace->allowedDomains()
            ->where('is_active', true)
            ->get();

        Log::debug('Domain restriction ENABLED - checking domains', [
            'workspace_id' => $workspace->id,
            'allowed_domains_count' => $allowedDomains->count(),
            'https_required' => $httpsRequired
        ]);

        if ($allowedDomains->isEmpty()) {
            return response()->json([
                'error' => 'No allowed domains configured',
                'message' => 'Domain restriction is enabled but no domains are configured.',
                'workspace_id' => $workspace->id,
                'settings_url' => url("/workspace/{$workspace->id}/api#settings-tab")
            ], 403);
        }

        /**
         * 6. Obter domínio de origem (com porta, se existir)
         */
        $originDomain = $this->getOriginDomain($request);

        if (empty($originDomain)) {
            return response()->json([
                'error' => 'Origin not detected',
                'message' => 'Requests must include a valid Origin or Referer header.'
            ], 403);
        }

        /**
         * 7. Validar domínio permitido
         */
        $isDomainAllowed = $this->isDomainAllowed($originDomain, $allowedDomains);

        if (!$isDomainAllowed) {
            return response()->json([
                'error' => 'Domain not allowed',
                'message' => 'Your domain is not authorized to access this API',
                'origin' => $originDomain,
                'workspace_id' => $workspace->id,
                'workspace_title' => $workspace->title,
                'https_required' => $httpsRequired
            ], 403);
        }

        Log::debug('Request allowed', [
            'workspace_id' => $workspace->id,
            'origin_domain' => $originDomain,
            'https_required' => $httpsRequired
        ]);

        return $next($request);
    }

    /**
     * Extrai o domínio de origem da requisição (mantendo porta).
     */
    private function getOriginDomain(Request $request): string
    {
        Log::debug('Domain Check Headers', [
            'origin' => $request->header('Origin'),
            'referer' => $request->header('Referer'),
            'host' => $request->header('Host')
        ]);

        $origin = $request->header('Origin');
        if ($origin && $this->isValidUrl($origin)) {
            return $this->getHostWithPort($origin);
        }

        $referer = $request->header('Referer');
        if ($referer && $this->isValidUrl($referer)) {
            return $this->getHostWithPort($referer);
        }

        return '';
    }

    /**
     * Retorna host + porta (ex: localhost:8000)
     */
    private function getHostWithPort(string $url): string
    {
        $parsed = parse_url($url);
        if (!isset($parsed['host'])) {
            return '';
        }

        $host = strtolower($parsed['host']);
        if (isset($parsed['port'])) {
            return "{$host}:{$parsed['port']}";
        }

        return $host;
    }

    /**
     * Verifica se a URL é válida.
     */
    private function isValidUrl(string $url): bool
    {
        $parsed = parse_url($url);
        return $parsed && isset($parsed['host']);
    }

    /**
     * Verifica se o domínio está permitido (com suporte a porta e wildcard).
     */
    private function isDomainAllowed(string $requestDomain, $allowedDomains): bool
    {
        foreach ($allowedDomains as $allowedDomain) {
            if ($this->matchesDomainPattern($requestDomain, $allowedDomain->domain)) {
                Log::debug('Domain matched', [
                    'request_domain' => $requestDomain,
                    'allowed_domain' => $allowedDomain->domain
                ]);
                return true;
            }
        }

        Log::warning('Domain not in allowed list', [
            'request_domain' => $requestDomain,
            'allowed_domains' => $allowedDomains->pluck('domain')
        ]);

        return false;
    }

    /**
     * Suporte a wildcard, subdomínios e comparação com porta.
     */
    private function matchesDomainPattern(string $requestDomain, string $allowedDomain): bool
    {
        $requestDomain = strtolower(trim($requestDomain));
        $allowedDomain = strtolower(trim($allowedDomain));

        // Igualdade direta
        if ($requestDomain === $allowedDomain) {
            return true;
        }

        // Wildcard (*.dominio.com)
        if (strpos($allowedDomain, '*') === 0) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($allowedDomain, '/')) . '$/';
            return preg_match($pattern, $requestDomain) === 1;
        }

        // 🔹 Permitir qualquer porta local se permitido "localhost"
        if (str_starts_with($requestDomain, 'localhost') && $allowedDomain === 'localhost') {
            return true;
        }

        // Subdomínios (api.dominio.com → dominio.com)
        return str_ends_with($requestDomain, '.' . $allowedDomain);
    }

    /**
     * Verifica se é uma rota API.
     */
    private function isApiRoute(Request $request): bool
    {
        return str_starts_with($request->path(), 'api/');
    }
}