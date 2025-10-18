<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Workspace;
use App\Models\WorkspaceAllowedDomain;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CheckAllowedDomain
{

    use Concerns\HandlesWorkspaceRequest;
    /**
     * Handle an incoming request - Verificação completa de domínios permitidos
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isApiRoute($request)) {
            return $next($request);
        }

        // Para rotas API públicas - permitir sempre
        if ($this->isPublicApiRoute($request)) {
            return $next($request);
        }

        // Obter workspace da requisição
        $workspace = $this->getWorkspaceFromRequest($request);

        if (!$workspace) {
            return response()->json([
                'error' => 'Workspace not found',
                'message' => 'The requested workspace does not exist'
            ], 404);
        }
        
        //Se o usuário autenticado é o proprietário do workspace, permite o acesso
        if (Auth::check() && Auth::id() === $workspace->user_id) {
            \Log::debug('Owner access - bypassing domain restrictions', [
                'workspace_id' => $workspace->id,
                'user_id' => Auth::id()
            ]);
            return $next($request);
        }

        // Verificar se a API está habilitada no workspace
        if (!$workspace->api_enabled) {
            return response()->json([
                'error' => 'API disabled',
                'message' => 'The API for this workspace is currently disabled'
            ], 403);
        }

        // Se a restrição de domínio está desativada, permite acesso
        if (!$workspace->api_domain_restriction) {
            \Log::debug('Domain restriction disabled - allowing all domains', [
                'workspace_id' => $workspace->id
            ]);
            return $next($request);
        }

        $allowedDomains = $workspace->allowedDomains()
            ->where('is_active', true)
            ->get();

        \Log::debug('Domain restriction ENABLED - checking domains', [
            'workspace_id' => $workspace->id,
            'allowed_domains_count' => $allowedDomains->count() // ← AGORA $allowedDomains existe
        ]);

        if ($allowedDomains->isEmpty()) {
            return response()->json([
                'error' => 'No allowed domains configured',
                'message' => 'Domain restriction is enabled but no domains are configured. Please add domains in the workspace settings.',
                'workspace_id' => $workspace->id,
                'workspace_title' => $workspace->title,
                'settings_url' => url("/workspace/{$workspace->id}/api#settings-tab")
            ], 403);
        }

        // Obter domínio de origem da requisição
        $originDomain = $this->getOriginDomain($request);

        // Verificar se o domínio de origem está permitido
        $isDomainAllowed = $this->isDomainAllowed($originDomain, $allowedDomains);

        if (!$isDomainAllowed) {
            return response()->json([
                'error' => 'Domain not allowed',
                'message' => 'Your domain is not authorized to access this API',
                'workspace_id' => $workspace->id
            ], 403);
        }

        return $next($request);
    }

    /**
     * Extrai o domínio de origem da requisição
     */
    private function getOriginDomain(Request $request): string
    {
        // DEBUG: Log todos os headers relevantes
        \Log::debug('Domain Check Headers', [
            'origin' => $request->header('Origin'),
            'referer' => $request->header('Referer'),
            'host' => $request->header('Host'),
            'path' => $request->path(),
            'method' => $request->method()
        ]);

        // Tentar pegar do header Origin (mais confiável para CORS)
        $origin = $request->header('Origin');
        
        if ($origin && $this->isValidUrl($origin)) {
            $domain = parse_url($origin, PHP_URL_HOST);
            $port = parse_url($origin, PHP_URL_PORT);
            if ($domain) {
                $result = strtolower(trim($domain));
                if ($port) {
                    $result .= ':' . $port;
                }
                \Log::debug('Using Origin domain', ['domain' => $result]);
                return $result;
            }
        }

        // Tentar pegar do header Referer
        $referer = $request->header('Referer');
        if ($referer && $this->isValidUrl($referer)) {
            $domain = parse_url($referer, PHP_URL_HOST);
            if ($domain) {
                $result = strtolower(trim($domain));
                \Log::debug('Using Referer domain', ['domain' => $result]);
                return $result;
            }
        }

        // Host pode ser facilmente falsificado e não representa a origem real
        $result = '';
        \Log::debug('No valid origin domain found', ['result' => $result]);
        return $result;
    }

    /**
     * Verifica se uma URL é válida para extração de domínio
     */
    private function isValidUrl(string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        // Verificar se é uma URL válida
        $parsed = parse_url($url);
        if (!$parsed || !isset($parsed['host'])) {
            return false;
        }

        // Verificar formato básico de domínio
        return (bool) preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i', $parsed['host']);
    }

    /**
     * Verifica se o domínio está na lista de permitidos
     */
    private function isDomainAllowed(string $domain, $allowedDomains): bool
    {
        if (empty($domain)) {
            \Log::warning('Empty domain detected with domain restriction active');
            return false;
        }

        foreach ($allowedDomains as $allowedDomain) {
            if ($this->matchesDomainPattern($domain, $allowedDomain->domain)) {
                \Log::debug('Domain matched', [
                    'request_domain' => $domain,
                    'allowed_domain' => $allowedDomain->domain
                ]);
                return true;
            }
        }

        \Log::warning('Domain not in allowed list', [
            'request_domain' => $domain,
            'allowed_domains' => $allowedDomains->pluck('domain')
        ]);
        return false;
    }

    /**
     * Encontra qual domínio permitido correspondeu (para logging)
     */
    private function findMatchingDomain(string $domain, $allowedDomains): ?string
    {
        foreach ($allowedDomains as $allowedDomain) {
            if ($this->matchesDomainPattern($domain, $allowedDomain->domain)) {
                return $allowedDomain->domain;
            }
        }

        return null;
    }

    /**
     * Verifica se o domínio corresponde ao padrão permitido
     * Suporta:
     * - Correspondência exata: exemplo.com
     * - Wildcards: *.exemplo.com
     * - Subdomínios: sub.exemplo.com → exemplo.com
     */
    private function matchesDomainPattern(string $requestDomain, string $allowedDomain): bool
    {
        $requestDomain = strtolower(trim($requestDomain));
        $allowedDomain = strtolower(trim($allowedDomain));

        // 1. Se for exatamente igual
        if ($requestDomain === $allowedDomain) {
            return true;
        }

        // 2. Se o domínio permitido tem wildcard no início
        if (strpos($allowedDomain, '*') === 0) {
            // Converter *.exemplo.com em regex: /^.*\.exemplo\.com$/
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($allowedDomain, '/')) . '$/';
            return preg_match($pattern, $requestDomain) === 1;
        }

        // 3. Se o domínio permitido é um domínio base do request domain
        // Ex: api.exemplo.com → exemplo.com
        if ($this->getBaseDomain($requestDomain) === $allowedDomain) {
            return true;
        }

        // 4. Se o request domain é um subdomínio do allowed domain
        // Ex: sub.exemplo.com matches exemplo.com
        if (str_ends_with($requestDomain, '.' . $allowedDomain)) {
            return true;
        }

        return false;
    }

    /**
     * Extrai o domínio base (ex: sub.exemplo.com → exemplo.com)
     */
    private function getBaseDomain(string $domain): string
    {
        $parts = explode('.', $domain);
        
        // Para domínios com pelo menos 2 partes
        if (count($parts) >= 2) {
            // Para TLDs com 2 partes como .co.uk, .com.br, etc
            if ($this->isTwoPartTld($parts)) {
                return $parts[count($parts) - 3] . '.' . $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
            }
            
            // Para TLDs comuns
            return $parts[count($parts) - 2] . '.' . $parts[count($parts) - 1];
        }
        
        return $domain;
    }

    /**
     * Verifica se o TLD tem duas partes (como .co.uk, .com.br)
     */
    private function isTwoPartTld(array $domainParts): bool
    {
        $twoPartTlds = [
            'co.uk', 'com.br', 'org.uk', 'net.uk', 'ac.uk', 'gov.uk',
            'ltd.uk', 'plc.uk', 'me.uk', 'ne.jp', 'or.jp', 'go.jp',
            'ac.jp', 'ad.jp', 'ed.jp', 'gr.jp', 'lg.jp', 'geo.jp'
        ];

        if (count($domainParts) < 3) {
            return false;
        }

        $lastTwo = $domainParts[count($domainParts) - 2] . '.' . $domainParts[count($domainParts) - 1];
        return in_array($lastTwo, $twoPartTlds);
    }
}