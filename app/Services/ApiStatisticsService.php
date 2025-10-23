<?php

namespace App\Services;

use App\Models\Workspace;
use App\Models\ApiRequestLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiStatisticsService
{
    /**
     * Obter estatísticas reais de uso da API
     */
    public static function getRealApiUsageStatistics(Workspace $workspace): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $lastWeek = Carbon::today()->subWeek();

        // Total de requisições
        $totalRequests = ApiRequestLog::where('workspace_id', $workspace->id)->count();

        // Requisições hoje
        $requestsToday = ApiRequestLog::where('workspace_id', $workspace->id)
            ->whereDate('created_at', $today)
            ->count();

        // Requisições ontem (para comparação)
        $requestsYesterday = ApiRequestLog::where('workspace_id', $workspace->id)
            ->whereDate('created_at', $yesterday)
            ->count();

        // Taxa de sucesso (códigos 2xx são sucesso)
        $successfulRequests = ApiRequestLog::where('workspace_id', $workspace->id)
            ->whereBetween('response_code', [200, 299])
            ->count();

        // Requisições com erro (códigos 4xx e 5xx)
        $failedRequests = ApiRequestLog::where('workspace_id', $workspace->id)
            ->where(function($query) {
                $query->whereBetween('response_code', [400, 499])
                      ->orWhereBetween('response_code', [500, 599]);
            })
            ->count();

        // Tempo médio de resposta
        $avgResponseTime = ApiRequestLog::where('workspace_id', $workspace->id)
            ->where('response_time', '>', 0)
            ->avg('response_time');

        // Endpoint mais usado
        $mostUsedEndpoint = ApiRequestLog::where('workspace_id', $workspace->id)
            ->select('endpoint', DB::raw('COUNT(*) as count'))
            ->groupBy('endpoint')
            ->orderByDesc('count')
            ->first();

        // Horário de pico (agrupar por hora)
        $peakHour = ApiRequestLog::scopePeakHour($workspace->id, $lastWeek)->first();

        // Última requisição
        $lastRequest = ApiRequestLog::where('workspace_id', $workspace->id)
            ->latest('created_at')
            ->first();

        return [
            'total_requests' => $totalRequests,
            'successful_requests' => $successfulRequests,
            'failed_requests' => $failedRequests,
            'success_rate' => $totalRequests > 0 ? round(($successfulRequests / $totalRequests) * 100, 2) : 0,
            'average_response_time' => round($avgResponseTime ?? 0, 2) . 'ms',
            'most_used_endpoint' => $mostUsedEndpoint->endpoint ?? '/workspaces/' . $workspace->id,
            'most_used_endpoint_count' => $mostUsedEndpoint->count ?? 0,
            'requests_today' => $requestsToday,
            'requests_yesterday' => $requestsYesterday,
            'daily_change' => self::calculateDailyChange($requestsToday, $requestsYesterday),
            'peak_usage_hour' => $peakHour ? sprintf('%02d:00-%02d:00', $peakHour->hour, $peakHour->hour + 1) : '14:00-15:00',
            'peak_usage_count' => $peakHour->count ?? 0,
            'last_request' => $lastRequest ? $lastRequest->created_at->toISOString() : null,
            'last_request_endpoint' => $lastRequest ? $lastRequest->endpoint : null,
        ];
    }

    /**
     * Estatísticas detalhadas por endpoint
     */
    public static function getEndpointStatistics(Workspace $workspace): array
    {
        $lastWeek = Carbon::today()->subWeek();

        return ApiRequestLog::where('workspace_id', $workspace->id)
            ->whereDate('created_at', '>=', $lastWeek)
            ->select([
                'endpoint',
                'method',
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('SUM(CASE WHEN response_code BETWEEN 200 AND 299 THEN 1 ELSE 0 END) as successful_requests'),
                DB::raw('SUM(CASE WHEN response_code BETWEEN 400 AND 599 THEN 1 ELSE 0 END) as failed_requests'),
                DB::raw('AVG(response_time) as avg_response_time'),
                DB::raw('MAX(response_time) as max_response_time'),
                DB::raw('MIN(response_time) as min_response_time'),
                DB::raw('STDDEV(response_time) as std_response_time'),
            ])
            ->groupBy('endpoint', 'method')
            ->orderByDesc('total_requests')
            ->get()
            ->map(function($log) {
                return [
                    'endpoint' => $log->endpoint,
                    'method' => $log->method,
                    'total_requests' => $log->total_requests,
                    'successful_requests' => $log->successful_requests,
                    'failed_requests' => $log->failed_requests,
                    'success_rate' => $log->total_requests > 0 ? round(($log->successful_requests / $log->total_requests) * 100, 2) : 0,
                    'avg_response_time' => round($log->avg_response_time ?? 0, 2) . 'ms',
                    'max_response_time' => round($log->max_response_time ?? 0, 2) . 'ms',
                    'min_response_time' => round($log->min_response_time ?? 0, 2) . 'ms',
                    'std_response_time' => round($log->std_response_time ?? 0, 2) . 'ms', // Adicionar ao resultado
                ];
            })
            ->toArray();
    }

    /**
     * Estatísticas de uso por período (últimos 7 dias)
     */
    public static function getUsageByPeriod(Workspace $workspace, int $days = 7): array
    {
        $startDate = Carbon::today()->subDays($days);

        return ApiRequestLog::where('workspace_id', $workspace->id)
            ->whereDate('created_at', '>=', $startDate)
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('SUM(CASE WHEN response_code BETWEEN 200 AND 299 THEN 1 ELSE 0 END) as successful_requests'),
                DB::raw('AVG(response_time) as avg_response_time'),
            ])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(function($log) {
                return [
                    'date' => $log->date,
                    'total_requests' => $log->total_requests,
                    'successful_requests' => $log->successful_requests,
                    'success_rate' => $log->total_requests > 0 ? round(($log->successful_requests / $log->total_requests) * 100, 2) : 0,
                    'avg_response_time' => round($log->avg_response_time ?? 0, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Métricas de performance
     */
    public static function getPerformanceMetrics(Workspace $workspace): array
    {
        $lastWeek = Carbon::today()->subWeek();

        $metrics = ApiRequestLog::where('workspace_id', $workspace->id)
            ->whereDate('created_at', '>=', $lastWeek)
            ->select([
                DB::raw('AVG(response_time) as overall_avg_response_time'),
                DB::raw('MAX(response_time) as max_response_time'),
                DB::raw('MIN(response_time) as min_response_time'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_ips'),
                DB::raw('COUNT(DISTINCT DATE(created_at)) as active_days'),
                // Aproximação para percentis usando funções PostgreSQL
                DB::raw('(SELECT response_time FROM api_request_logs 
                        WHERE workspace_id = ' . $workspace->id . ' 
                        AND created_at >= \'' . $lastWeek->format('Y-m-d') . '\'
                        ORDER BY response_time LIMIT 1 OFFSET FLOOR(0.95 * (SELECT COUNT(*) FROM api_request_logs 
                        WHERE workspace_id = ' . $workspace->id . ' 
                        AND created_at >= \'' . $lastWeek->format('Y-m-d') . '\'))) as p95_approx'),
                DB::raw('(SELECT response_time FROM api_request_logs 
                        WHERE workspace_id = ' . $workspace->id . ' 
                        AND created_at >= \'' . $lastWeek->format('Y-m-d') . '\'
                        ORDER BY response_time LIMIT 1 OFFSET FLOOR(0.99 * (SELECT COUNT(*) FROM api_request_logs 
                        WHERE workspace_id = ' . $workspace->id . ' 
                        AND created_at >= \'' . $lastWeek->format('Y-m-d') . '\'))) as p99_approx'),
            ])
            ->first();

        return [
            'overall_avg_response_time' => round($metrics->overall_avg_response_time ?? 0, 2) . 'ms',
            'p95_response_time' => round($metrics->p95_approx ?? 0, 2) . 'ms',
            'p99_response_time' => round($metrics->p99_approx ?? 0, 2) . 'ms',
            'max_response_time' => round($metrics->max_response_time ?? 0, 2) . 'ms',
            'min_response_time' => round($metrics->min_response_time ?? 0, 2) . 'ms',
            'unique_ips' => $metrics->unique_ips ?? 0,
            'active_days' => $metrics->active_days ?? 0,
            'uptime' => self::calculateUptime($workspace),
        ];
    }

    /**
     * Métricas de performance simplificadas (mais rápida e confiável)
     */
    public static function getPerformanceMetricsSimple(Workspace $workspace): array
    {
        $lastWeek = Carbon::today()->subWeek();

        $metrics = ApiRequestLog::where('workspace_id', $workspace->id)
            ->whereDate('created_at', '>=', $lastWeek)
            ->select([
                DB::raw('AVG(response_time) as overall_avg_response_time'),
                DB::raw('MAX(response_time) as max_response_time'),
                DB::raw('MIN(response_time) as min_response_time'),
                DB::raw('COUNT(DISTINCT ip_address) as unique_ips'),
                DB::raw('COUNT(DISTINCT DATE(created_at)) as active_days'),
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('SUM(CASE WHEN response_code BETWEEN 200 AND 299 THEN 1 ELSE 0 END) as successful_requests'),
            ])
            ->first();

        // Calcular percentis de forma aproximada
        $responseTimeStats = self::getResponseTimeStats($workspace, $lastWeek);

        return [
            'overall_avg_response_time' => round($metrics->overall_avg_response_time ?? 0, 2) . 'ms',
            'p95_response_time' => $responseTimeStats['p95'] . 'ms',
            'p99_response_time' => $responseTimeStats['p99'] . 'ms',
            'max_response_time' => round($metrics->max_response_time ?? 0, 2) . 'ms',
            'min_response_time' => round($metrics->min_response_time ?? 0, 2) . 'ms',
            'unique_ips' => $metrics->unique_ips ?? 0,
            'active_days' => $metrics->active_days ?? 0,
            'total_requests' => $metrics->total_requests ?? 0,
            'successful_requests' => $metrics->successful_requests ?? 0,
            'uptime' => $metrics->total_requests > 0 ? 
                round(($metrics->successful_requests / $metrics->total_requests) * 100, 2) : 100,
        ];
    }

    /**
     * Estatísticas de response time usando aproximações
     */
    private static function getResponseTimeStats(Workspace $workspace, Carbon $startDate): array
    {
        // Para grandes volumes de dados, use aproximações
        $stats = ApiRequestLog::where('workspace_id', $workspace->id)
            ->whereDate('created_at', '>=', $startDate)
            ->where('response_time', '>', 0)
            ->select([
                DB::raw('AVG(response_time) as avg_time'),
                DB::raw('STDDEV(response_time) as std_time'),
            ])
            ->first();

        $avg = $stats->avg_time ?? 0;
        $std = $stats->std_time ?? 0;

        // Aproximação para percentis baseada na distribuição normal
        // P95 ≈ média + 1.645 * desvio padrão
        // P99 ≈ média + 2.326 * desvio padrão
        return [
            'p95' => round($avg + (1.645 * $std), 2),
            'p99' => round($avg + (2.326 * $std), 2),
        ];
    }

    /**
     * Distribuição por método HTTP
     */
    public static function getMethodsDistribution(Workspace $workspace): array
    {
        $distribution = ApiRequestLog::where('workspace_id', $workspace->id)
            ->select('method', DB::raw('COUNT(*) as count'))
            ->groupBy('method')
            ->get()
            ->pluck('count', 'method')
            ->toArray();

        // Garantir que todos os métodos principais existam
        $allMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        $result = [];
        
        foreach ($allMethods as $method) {
            $result[$method] = $distribution[$method] ?? 0;
        }

        return $result;
    }

    /**
     * Distribuição por código de status
     */
    public static function getStatusDistribution(Workspace $workspace): array
    {
        $distribution = ApiRequestLog::where('workspace_id', $workspace->id)
            ->select(
                DB::raw('CASE 
                    WHEN response_code BETWEEN 200 AND 299 THEN \'2xx\'
                    WHEN response_code BETWEEN 400 AND 499 THEN \'4xx\' 
                    WHEN response_code BETWEEN 500 AND 599 THEN \'5xx\'
                    ELSE \'other\'
                END as status_group'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status_group')
            ->get()
            ->pluck('count', 'status_group')
            ->toArray();

        return [
            '2xx' => $distribution['2xx'] ?? 0,
            '4xx' => $distribution['4xx'] ?? 0,
            '5xx' => $distribution['5xx'] ?? 0,
            'other' => $distribution['other'] ?? 0,
        ];
    }

    /**
     * Calcular percentis manualmente para MySQL
     */
    private static function calculatePercentiles(Workspace $workspace, Carbon $startDate): array
    {
        // Buscar todos os response times ordenados
        $responseTimes = ApiRequestLog::where('workspace_id', $workspace->id)
            ->whereDate('created_at', '>=', $startDate)
            ->where('response_time', '>', 0)
            ->orderBy('response_time')
            ->pluck('response_time')
            ->toArray();

        $total = count($responseTimes);
        
        if ($total === 0) {
            return ['p95' => 0, 'p99' => 0];
        }

        // Calcular índices dos percentis
        $p95Index = (int) ceil($total * 0.95) - 1;
        $p99Index = (int) ceil($total * 0.99) - 1;

        // Garantir que os índices estão dentro dos limites
        $p95Index = min($p95Index, $total - 1);
        $p99Index = min($p99Index, $total - 1);

        return [
            'p95' => round($responseTimes[$p95Index] ?? 0, 2),
            'p99' => round($responseTimes[$p99Index] ?? 0, 2),
        ];
    }

    /**
     * Calcular mudança percentual diária
     */
    private static function calculateDailyChange(int $today, int $yesterday): float
    {
        if ($yesterday === 0) {
            return $today > 0 ? 100 : 0;
        }

        return round((($today - $yesterday) / $yesterday) * 100, 2);
    }

    /**
     * Calcular uptime (taxa de sucesso)
     */
    private static function calculateUptime(Workspace $workspace): float
    {
        $total = ApiRequestLog::where('workspace_id', $workspace->id)->count();
        $successful = ApiRequestLog::where('workspace_id', $workspace->id)
            ->whereBetween('response_code', [200, 299])
            ->count();

        return $total > 0 ? round(($successful / $total) * 100, 2) : 100;
    }
}