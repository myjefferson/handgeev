<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiRequestLog;
use Carbon\Carbon;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000); // em milissegundos

        // Log apenas se for uma requisição da API
        if (str_starts_with($request->path(), 'api/')) {
            $this->logRequest($request, $response, $responseTime);
        }

        return $response;
    }

    private function logRequest(Request $request, Response $response, int $responseTime): void
    {
        try {
            $user = auth()->user();
            $workspaceId = $this->extractWorkspaceId($request);

            ApiRequestLog::create([
                'user_id' => $user?->id,
                'workspace_id' => $workspaceId,
                'ip_address' => $request->ip(),
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'response_code' => $response->getStatusCode(),
                'response_time' => $responseTime,
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Falha silenciosa - não quebrar a aplicação por causa do log
            \Log::error('Failed to log API request: ' . $e->getMessage());
        }
    }

    private function extractWorkspaceId(Request $request): ?int
    {
        // Extrair workspace_id da URL ou parâmetros
        if ($request->route('id')) {
            return $request->route('id');
        }

        return null;
    }
}