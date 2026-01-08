<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Workspace;

class CheckApiEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $workspace = $request->route('workspace');

        // Se vier como ID (fallback de segurança)
        if (is_numeric($workspace)) {
            $workspace = Workspace::find($workspace);
        }

        // Se for um model válido
        if ($workspace instanceof Workspace) {
            if (!$workspace->api_enabled) {
                return response()->json([
                    'error' => 'API disabled',
                    'message' => 'The API for this workspace is currently disabled'
                ], 403);
            }
        }

        return $next($request);
    }

    /**
     * Verifica se é uma rota API (não web)
     */
    private function isApiRoute(Request $request): bool
    {
        return $request->is('api/*') || 
               $request->routeIs('api.*') ||
               str_contains($request->path(), 'api/');
    }

    /**
     * Rotas API públicas que funcionam mesmo com API desabilitada
     */
    private function isPublicApiRoute(Request $request): bool
    {
        $publicApiRoutes = [
            'api.auth.login.token',
            'api.health',
            'api.shared.public'
        ];

        return in_array($request->route()->getName(), $publicApiRoutes) ||
               $request->routeIs('health') ||
               $request->routeIs('auth.token');
    }
}