<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiRequestLog;
use Illuminate\Support\Facades\Auth;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        // Só logar requisições da API
        if (!$this->isApiRequest($request)) {
            return;
        }

        try {
            $user = Auth::user();
            $workspace = $this->getWorkspaceFromRequest($request);

            ApiRequestLog::create([
                'user_id' => $user?->id,
                'workspace_id' => $workspace?->id,
                'ip_address' => $request->ip(),
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'response_code' => $response->getStatusCode(),
                'response_time' => $this->calculateResponseTime(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log do erro sem interromper a aplicação
            \Log::error('Failed to log API request: ' . $e->getMessage());
        }
    }

    private function isApiRequest(Request $request): bool
    {
        return $request->is('api/*') || str_contains($request->path(), 'api/');
    }

    private function getWorkspaceFromRequest(Request $request): ?\App\Models\Workspace
    {
        $workspaceId = $request->route('workspaceId') ?? 
                      $request->route('workspace')?->id ??
                      $request->input('workspace_id');

        if ($workspaceId) {
            return \App\Models\Workspace::find($workspaceId);
        }

        return null;
    }

    private function calculateResponseTime(): int
    {
        if (defined('LARAVEL_START')) {
            return (int) ((microtime(true) - LARAVEL_START) * 1000);
        }

        return 0;
    }
}