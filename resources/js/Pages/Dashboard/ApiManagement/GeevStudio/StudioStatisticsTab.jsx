import React, { useMemo, useState } from 'react';

export const StudioStatisticsTab = ({
    activeTab, 
    statistics, 
    loadingStats, 
    statsError,
    rateLimitInfo,
    usageByPeriod,
    methodsDistribution,
    statusDistribution,
    refreshStatistics
}) => {
    
    const [timeRange, setTimeRange] = useState('7d'); // 7d, 30d, 90d

    // Função para formatar números
    const formatNumber = (num) => {
        if (!num && num !== 0) return '0';
        if (num >= 1000000) return `${(num / 1000000).toFixed(1)}M`;
        if (num >= 1000) return `${(num / 1000).toFixed(1)}K`;
        return num.toString();
    };

    // Função para calcular porcentagem
    const calculatePercentage = (value, total) => {
        if (!total || total === 0) return 0;
        return Math.round((value / total) * 100);
    };

    // Dados processados para cards principais
    const mainMetrics = useMemo(() => {
        if (!statistics) {
            return {
                requestsToday: 0,
                totalRequests: 0,
                averageResponseTime: '0ms',
                errorRate: '0%',
                peakRequestsPerHour: 0,
                uniqueUsers: 0,
                successRate: '100%',
                dataTransfer: '0 MB'
            };
        }
        
        const { api_usage = {}, performance_metrics = {}, workspace_stats = {} } = statistics;
        
        return {
            requestsToday: api_usage?.today || 0,
            totalRequests: api_usage?.total || 0,
            averageResponseTime: performance_metrics?.average_response_time || '0ms',
            errorRate: performance_metrics?.error_rate || '0%',
            peakRequestsPerHour: workspace_stats?.peak_hour_requests || 0,
            uniqueUsers: workspace_stats?.unique_users || 0,
            successRate: performance_metrics?.success_rate || '100%',
            dataTransfer: workspace_stats?.data_transfer || '0 MB'
        };
    }, [statistics]);

    // Processar distribuição de métodos HTTP - CORRIGIDO
    const processedMethods = useMemo(() => {
        // Garantir que seja um array
        const methods = Array.isArray(methodsDistribution) ? methodsDistribution : [];
        
        if (methods.length === 0) {
            return [
                { method: 'GET', count: 0, color: 'bg-green-500', percentage: 0 },
                { method: 'POST', count: 0, color: 'bg-blue-500', percentage: 0 },
                { method: 'PUT', count: 0, color: 'bg-yellow-500', percentage: 0 },
                { method: 'DELETE', count: 0, color: 'bg-red-500', percentage: 0 },
                { method: 'PATCH', count: 0, color: 'bg-purple-500', percentage: 0 }
            ];
        }

        const total = methods.reduce((sum, item) => sum + (item.count || 0), 0);
        
        return methods.map(item => ({
            method: item.method || 'GET',
            count: item.count || 0,
            percentage: calculatePercentage(item.count || 0, total),
            color: (item.method || 'GET') === 'GET' ? 'bg-green-500' :
                   (item.method || 'POST') === 'POST' ? 'bg-blue-500' :
                   (item.method || 'PUT') === 'PUT' ? 'bg-yellow-500' :
                   (item.method || 'DELETE') === 'DELETE' ? 'bg-red-500' :
                   'bg-purple-500'
        }));
    }, [methodsDistribution]);

    // Processar distribuição de status HTTP - CORRIGIDO
    const processedStatus = useMemo(() => {
        // Garantir que seja um array
        const status = Array.isArray(statusDistribution) ? statusDistribution : [];
        
        if (status.length === 0) {
            return [
                { status: '2xx', count: 0, color: 'bg-green-500', percentage: 0 },
                { status: '3xx', count: 0, color: 'bg-blue-500', percentage: 0 },
                { status: '4xx', count: 0, color: 'bg-yellow-500', percentage: 0 },
                { status: '5xx', count: 0, color: 'bg-red-500', percentage: 0 }
            ];
        }

        const total = status.reduce((sum, item) => sum + (item.count || 0), 0);
        
        return status.map(item => ({
            status: `${item.status_code || 2}xx`,
            count: item.count || 0,
            percentage: calculatePercentage(item.count || 0, total),
            color: (item.status_code || 2) === 2 ? 'bg-green-500' :
                   (item.status_code || 3) === 3 ? 'bg-blue-500' :
                   (item.status_code || 4) === 4 ? 'bg-yellow-500' :
                   'bg-red-500'
        }));
    }, [statusDistribution]);

    // Calcular o máximo para normalização dos gráficos
    const maxUsageValue = useMemo(() => {
        const usage = Array.isArray(usageByPeriod) ? usageByPeriod : [];
        if (usage.length === 0) return 100;
        const maxValue = Math.max(...usage.map(item => item.value || 0));
        return maxValue > 0 ? maxValue : 100;
    }, [usageByPeriod]);

    if (activeTab !== 'statistics') return null;

    // Loading state
    if (loadingStats) {
        return (
            <div className="space-y-6 animate-fadeIn">
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div className="flex items-center justify-center h-64">
                        <div className="text-center">
                            <i className="fas fa-spinner fa-spin text-3xl text-teal-500 mb-4"></i>
                            <p className="text-gray-600 dark:text-gray-400">Carregando estatísticas...</p>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    // Error state
    if (statsError) {
        return (
            <div className="space-y-6 animate-fadeIn">
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div className="flex items-center justify-center h-64">
                        <div className="text-center">
                            <i className="fas fa-exclamation-triangle text-3xl text-red-500 mb-4"></i>
                            <p className="text-gray-600 dark:text-gray-400 mb-4">{statsError}</p>
                            <button
                                onClick={refreshStatistics}
                                className="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700"
                            >
                                <i className="fas fa-redo mr-2"></i>Tentar novamente
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="space-y-6 animate-fadeIn">
            {/* Cabeçalho com botão de refresh */}
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 className="text-xl font-bold text-gray-900 dark:text-white">
                            <i className="fas fa-chart-bar mr-2 text-teal-500"></i>
                            Estatísticas da API
                        </h2>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Dados atualizados em tempo real do uso da sua API
                        </p>
                    </div>
                    <div className="flex items-center space-x-4">
                        <select
                            value={timeRange}
                            onChange={(e) => setTimeRange(e.target.value)}
                            className="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300"
                        >
                            <option value="7d">Últimos 7 dias</option>
                            <option value="30d">Últimos 30 dias</option>
                            <option value="90d">Últimos 90 dias</option>
                        </select>
                        <button
                            onClick={refreshStatistics}
                            className="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"
                        >
                            <i className="fas fa-redo mr-2"></i>Atualizar
                        </button>
                    </div>
                </div>
            </div>

            {/* Cards de Métricas Principais */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {/* Requisições Hoje */}
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div className="flex items-start justify-between">
                        <div>
                            <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Requisições Hoje</p>
                            <p className="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                {formatNumber(mainMetrics?.requestsToday || 0)}
                            </p>
                            <div className="flex items-center mt-2">
                                <i className="fas fa-bolt text-blue-500 mr-2"></i>
                                <span className="text-sm text-gray-600 dark:text-gray-400">
                                    {statistics?.rate_limit_stats?.remaining_daily || rateLimitInfo?.remaining_daily || 0} restantes hoje
                                </span>
                            </div>
                        </div>
                        <div className="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <i className="fas fa-bolt text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                {/* Total de Requisições */}
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div className="flex items-start justify-between">
                        <div>
                            <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Total de Requisições</p>
                            <p className="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                {formatNumber(mainMetrics?.totalRequests || 0)}
                            </p>
                            <div className="flex items-center mt-2">
                                <i className="fas fa-chart-line text-green-500 mr-2"></i>
                                <span className="text-sm text-gray-600 dark:text-gray-400">
                                    Desde {new Date(statistics?.workspace_stats?.created_at || Date.now()).toLocaleDateString('pt-BR')}
                                </span>
                            </div>
                        </div>
                        <div className="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <i className="fas fa-chart-line text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                {/* Tempo Médio de Resposta */}
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div className="flex items-start justify-between">
                        <div>
                            <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Tempo Médio</p>
                            <p className="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                {mainMetrics?.averageResponseTime || '0ms'}
                            </p>
                            <div className="flex items-center mt-2">
                                <i className="fas fa-tachometer-alt text-purple-500 mr-2"></i>
                                <span className="text-sm text-gray-600 dark:text-gray-400">
                                    Performance ótima
                                </span>
                            </div>
                        </div>
                        <div className="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <i className="fas fa-tachometer-alt text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                {/* Taxa de Sucesso */}
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div className="flex items-start justify-between">
                        <div>
                            <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Taxa de Sucesso</p>
                            <p className="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                {mainMetrics?.successRate || '100%'}
                            </p>
                            <div className="flex items-center mt-2">
                                <i className="fas fa-check-circle text-green-500 mr-2"></i>
                                <span className="text-sm text-gray-600 dark:text-gray-400">
                                    Erros: {mainMetrics?.errorRate || '0%'}
                                </span>
                            </div>
                        </div>
                        <div className="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <i className="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {/* Gráficos e Detalhes */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Gráfico de Uso */}
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div className="flex items-center justify-between mb-6">
                        <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                            <i className="fas fa-chart-line mr-2"></i>
                            Uso da API (últimos {timeRange === '7d' ? '7' : timeRange === '30d' ? '30' : '90'} dias)
                        </h3>
                        <div className="text-sm text-gray-600 dark:text-gray-400">
                            Total: {usageByPeriod.reduce((sum, item) => sum + (item.value || 0), 0).toLocaleString()}
                        </div>
                    </div>
                    <div className="h-64">
                        {usageByPeriod.length > 0 ? (
                            <div className="h-full flex items-end space-x-1">
                                {usageByPeriod.map((item, index) => (
                                    <div key={index} className="flex-1 flex flex-col items-center">
                                        <div
                                            className="w-full bg-teal-500 rounded-t-lg transition-all hover:bg-teal-600 cursor-pointer"
                                            style={{ 
                                                height: `${((item.value || 0) / maxUsageValue) * 100}%`,
                                                minHeight: '2px'
                                            }}
                                            title={`${item.label}: ${item.value} requisições`}
                                        ></div>
                                        <span className="text-xs text-gray-500 dark:text-gray-400 mt-2 truncate w-full text-center">
                                            {item.label}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="h-full flex items-center justify-center">
                                <p className="text-gray-500 dark:text-gray-400">Nenhum dado disponível</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Distribuição de Métodos */}
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                        <i className="fas fa-exchange-alt mr-2"></i>
                        Distribuição por Método HTTP
                    </h3>
                    <div className="space-y-4">
                        {processedMethods.map((item, index) => (
                            <div key={index} className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center space-x-3">
                                        <span className={`inline-flex items-center px-3 py-1 rounded text-xs font-medium text-white ${item.color}`}>
                                            {item.method}
                                        </span>
                                        <span className="text-sm text-gray-700 dark:text-gray-300">
                                            {item.count.toLocaleString()}
                                        </span>
                                    </div>
                                    <span className="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        {item.percentage}%
                                    </span>
                                </div>
                                <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div 
                                        className={`h-2 rounded-full ${item.color}`}
                                        style={{ width: `${item.percentage}%` }}
                                    ></div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Distribuição de Status e Limites */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Distribuição de Status */}
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                        <i className="fas fa-server mr-2"></i>
                        Distribuição por Status HTTP
                    </h3>
                    <div className="space-y-4">
                        {processedStatus.map((item, index) => (
                            <div key={index} className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center space-x-3">
                                        <span className={`inline-flex items-center px-3 py-1 rounded text-xs font-medium text-white ${item.color}`}>
                                            {item.status}
                                        </span>
                                        <span className="text-sm text-gray-700 dark:text-gray-300">
                                            {item.count.toLocaleString()}
                                        </span>
                                    </div>
                                    <span className="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        {item.percentage}%
                                    </span>
                                </div>
                                <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div 
                                        className={`h-2 rounded-full ${item.color}`}
                                        style={{ width: `${item.percentage}%` }}
                                    ></div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Limites da API */}
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <div className="flex items-center justify-between mb-6">
                        <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                            <i className="fas fa-tachometer-alt mr-2"></i>
                            Limites da API
                        </h3>
                        <span className="text-sm text-gray-600 dark:text-gray-400">
                            Plano: {statistics?.plan_info?.name || rateLimitInfo?.plan || 'Free'}
                        </span>
                    </div>
                    
                    <div className="space-y-4">
                        {/* Limite por Minuto */}
                        <div>
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Por Minuto</span>
                                <span className="text-sm font-bold text-blue-600 dark:text-blue-400">
                                    {statistics?.rate_limit_stats?.remaining_minute || rateLimitInfo?.remaining_minute || 0}/{statistics?.rate_limit_stats?.limit_per_minute || rateLimitInfo?.limit_per_minute || 60}
                                </span>
                            </div>
                            <div className="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div 
                                    className="bg-blue-500 h-2 rounded-full" 
                                    style={{ 
                                        width: `${calculatePercentage(
                                            statistics?.rate_limit_stats?.remaining_minute || rateLimitInfo?.remaining_minute || 0,
                                            statistics?.rate_limit_stats?.limit_per_minute || rateLimitInfo?.limit_per_minute || 60
                                        )}%` 
                                    }}
                                ></div>
                            </div>
                        </div>

                        {/* Limite por Dia */}
                        <div>
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Por Dia</span>
                                <span className="text-sm font-bold text-green-600 dark:text-green-400">
                                    {statistics?.rate_limit_stats?.remaining_daily || rateLimitInfo?.remaining_daily || 0}/{statistics?.rate_limit_stats?.limit_per_day || rateLimitInfo?.limit_per_day || 1000}
                                </span>
                            </div>
                            <div className="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div 
                                    className="bg-green-500 h-2 rounded-full" 
                                    style={{ 
                                        width: `${calculatePercentage(
                                            statistics?.rate_limit_stats?.remaining_daily || rateLimitInfo?.remaining_daily || 0,
                                            statistics?.rate_limit_stats?.limit_per_day || rateLimitInfo?.limit_per_day || 1000
                                        )}%` 
                                    }}
                                ></div>
                            </div>
                        </div>

                        {/* Limite Mensal */}
                        <div>
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Por Mês</span>
                                <span className="text-sm font-bold text-purple-600 dark:text-purple-400">
                                    {statistics?.api_usage?.monthly || 0}/{statistics?.plan_info?.limits?.monthly || 50000}
                                </span>
                            </div>
                            <div className="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div 
                                    className="bg-purple-500 h-2 rounded-full" 
                                    style={{ 
                                        width: `${calculatePercentage(
                                            statistics?.api_usage?.monthly || 0,
                                            statistics?.plan_info?.limits?.monthly || 50000
                                        )}%` 
                                    }}
                                ></div>
                            </div>
                        </div>
                    </div>

                    {/* Dica de Otimização */}
                    <div className="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <p className="text-sm text-blue-800 dark:text-blue-200">
                            <i className="fas fa-lightbulb mr-2"></i>
                            <strong>Dica:</strong> Use cache e otimize suas consultas para reduzir o consumo de requisições.
                        </p>
                    </div>
                </div>
            </div>

            {/* Informações Adicionais */}
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i className="fas fa-info-circle mr-2"></i>
                    Informações Detalhadas
                </h3>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="space-y-2">
                        <h4 className="font-medium text-gray-700 dark:text-gray-300">Endpoint Mais Popular</h4>
                        <div className="flex items-center space-x-2">
                            <i className="fas fa-link text-gray-400"></i>
                            <code className="text-sm text-gray-600 dark:text-gray-400 truncate">
                                {statistics?.workspace_stats?.most_accessed_endpoint || '/api/v1/workspace'}
                            </code>
                        </div>
                        <p className="text-xs text-gray-500 dark:text-gray-400">
                            {statistics?.workspace_stats?.endpoint_usage_percentage || '42'}% do tráfego total
                        </p>
                    </div>
                    
                    <div className="space-y-2">
                        <h4 className="font-medium text-gray-700 dark:text-gray-300">Horário de Pico</h4>
                        <div className="flex items-center space-x-2">
                            <i className="fas fa-clock text-gray-400"></i>
                            <span className="text-sm text-gray-600 dark:text-gray-400">
                                {statistics?.workspace_stats?.peak_hour || '14:00-15:00'}
                            </span>
                        </div>
                        <p className="text-xs text-gray-500 dark:text-gray-400">
                            {statistics?.workspace_stats?.peak_requests || '250'} requisições/hora
                        </p>
                    </div>
                    
                    <div className="space-y-2">
                        <h4 className="font-medium text-gray-700 dark:text-gray-300">Usuários Únicos</h4>
                        <div className="flex items-center space-x-2">
                            <i className="fas fa-users text-gray-400"></i>
                            <span className="text-sm text-gray-600 dark:text-gray-400">
                                {mainMetrics?.uniqueUsers || '0'} usuários
                            </span>
                        </div>
                        <p className="text-xs text-gray-500 dark:text-gray-400">
                            {statistics?.workspace_stats?.unique_ips || '0'} IPs únicos
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
};