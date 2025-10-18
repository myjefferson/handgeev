<?php

namespace App\Http\Middleware\Concerns;

use Illuminate\Http\Request;
use App\Models\Workspace;
use Log;

trait HandlesWorkspaceRequest
{
    /**
     * Obtém o workspace da requisição
     */
    private function getWorkspaceFromRequest(Request $request): ?Workspace
    {
        // Tentar pegar do request attributes (se outro middleware já definiu)
        if ($request->attributes->has('workspace')) {
            $workspace = $request->attributes->get('workspace');
            if ($workspace instanceof Workspace) {
                return $workspace;
            }
        }

        // Buscar por workspaceId na rota
        if ($workspaceId = $request->route('workspaceId')) {
            return Workspace::find($workspaceId);
        }

        // Buscar por workspace na rota
        if ($workspace = $request->route('workspace')) {
            if ($workspace instanceof Workspace) {
                return $workspace;
            }
        }

        // Buscar por ID em outros parâmetros comuns
        $possibleIdParams = ['workspace', 'workspace_id', 'workspaceId', 'id'];
        foreach ($possibleIdParams as $param) {
            if ($id = $request->route($param)) {
                if (is_numeric($id)) {
                    $found = Workspace::find($id);
                    if ($found) return $found;
                }
            }
        }

        // Para rotas de fields e topics, buscar workspace relacionado
        if ($fieldId = $request->route('fieldId')) {
            $field = \App\Models\Field::with('topic.workspace')->find($fieldId);
            return $field->topic->workspace ?? null;
        }

        if ($topicId = $request->route('topicId')) {
            $topic = \App\Models\Topic::with('workspace')->find($topicId);
            return $topic->workspace ?? null;
        }

        // Para rota shared API com hash
        if ($globalHash = $request->route('global_key_api')) {
            $user = \App\Models\User::where('global_key_api', $globalHash)->first();
            $workspaceHash = $request->route('workspace_key_api');
            
            if ($user && $workspaceHash) {
                return Workspace::where('workspace_key_api', $workspaceHash)
                              ->where('user_id', $user->id)
                              ->first();
            }
        }

        // Tentar extrair do path da URL
        $path = $request->path();
        if (preg_match('/workspaces?\/(\d+)/', $path, $matches)) {
            return Workspace::find($matches[1]);
        }

        Log::warning('Could not extract workspace from request', [
            'path' => $request->path(),
            'route_parameters' => $request->route()?->parameters(),
            'method' => $request->method()
        ]);

        return null;
    }

    /**
     * Verifica se é uma rota API
     */
    private function isApiRoute(Request $request): bool
    {
        return $request->is('api/*') || 
               $request->routeIs('api.*') ||
               str_contains($request->path(), 'api/');
    }

    /**
     * Rotas API públicas que funcionam mesmo com verificação de domínio
     */
    private function isPublicApiRoute(Request $request): bool
    {
        $publicApiRoutes = [
            'api.auth.login.token',
            'api.health',
            'api.shared.public',
            'api.health.check',
            'auth.token',
            'health',
            'health.check'
        ];

        $currentRoute = $request->route()?->getName();

        if (in_array($currentRoute, $publicApiRoutes)) {
            return true;
        }

        // Verificar por padrões de rotas públicas
        if ($currentRoute) {
            return str_contains($currentRoute, 'health') ||
                   str_contains($currentRoute, 'auth.token') ||
                   str_contains($currentRoute, 'shared.public');
        }

        return false;
    }
}