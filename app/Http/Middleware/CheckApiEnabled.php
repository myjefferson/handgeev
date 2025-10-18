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
        // Para rotas que têm workspaceId na URL
        $workspaceId = $request->route('workspaceId') ?? 
                      $request->route('workspace')?->id;

        if ($workspaceId) {
            $workspace = Workspace::find($workspaceId);
            
            if ($workspace && !$workspace->api_enabled) {
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