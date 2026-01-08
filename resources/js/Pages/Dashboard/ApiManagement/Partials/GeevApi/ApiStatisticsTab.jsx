// resources/js/Pages/ApiManagement/Partials/StatisticsTab.jsx
import React, { useState, useEffect, useRef } from 'react';
import { usePage, router } from '@inertiajs/react';
import Chart from 'chart.js/auto';

export default function ApiStatisticsTab({ workspace }) {
    const { auth } = usePage().props;
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [statistics, setStatistics] = useState(null);
    const [endpointStats, setEndpointStats] = useState([]);
    const [refreshing, setRefreshing] = useState(false);
    
    // Refs para os gráficos
    const requestsChartRef = useRef(null);
    const methodsChartRef = useRef(null);
    const statusChartRef = useRef(null);
    
    // Instâncias dos gráficos
    const [requestsChart, setRequestsChart] = useState(null);
    const [methodsChart, setMethodsChart] = useState(null);
    const [statusChart, setStatusChart] = useState(null);

    // Carregar estatísticas
    const loadStatistics = async () => {
        try {
            setLoading(true);
            setError(null);
            
            const response = await fetch(route('api.get.statistics', {
                global_key_api: auth.user.global_key_api,
                workspace_key_api: workspace.workspace_key_api
            }));
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || 'Erro ao carregar estatísticas');
            }
            
            setStatistics(data);
            setLoading(false);
        } catch (error) {
            console.error('Erro ao carregar estatísticas:', error);
            setError(error.message);
            setLoading(false);
        }
    };

    // Carregar estatísticas de endpoints
    const loadEndpointStatistics = async () => {
        try {
            const response = await fetch(route('api.get.endpoint-statistics', {
                global_key_api: auth.user.global_key_api,
                workspace_key_api: workspace.workspace_key_api
            }));
            
            const data = await response.json();
            
            if (data.success && data.endpoints) {
                setEndpointStats(data.endpoints.slice(0, 5)); // Top 5 endpoints
            }
        } catch (error) {
            console.error('Erro ao carregar estatísticas de endpoints:', error);
        }
    };

    // Atualizar estatísticas
    const refreshStatistics = async () => {
        setRefreshing(true);
        await Promise.all([loadStatistics(), loadEndpointStatistics()]);
        setRefreshing(false);
    };

    // Atualizar gráficos
    const updateCharts = () => {
        if (!statistics) return;
        
        updateRequestsChart(statistics.usage_by_period || []);
        updateMethodsChart(statistics.methods_distribution || {});
        updateStatusChart(statistics.status_distribution || {});
    };

    // Gráfico de tendência de requisições
    const updateRequestsChart = (usageData) => {
        const ctx = document.getElementById('requestsChart');
        if (!ctx) return;
        
        // Destruir gráfico anterior se existir
        if (requestsChart) {
            requestsChart.destroy();
        }
        
        // Preparar dados
        const labels = usageData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
        });
        
        const requests = usageData.map(item => item.total_requests);
        
        // Criar novo gráfico
        const newChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Requisições por Dia',
                    data: requests,
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#06b6d4',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { 
                            color: '#cbd5e1',
                            font: { size: 12 }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { 
                            color: 'rgba(100, 116, 139, 0.2)',
                            borderColor: 'rgba(100, 116, 139, 0.5)'
                        },
                        ticks: { 
                            color: '#cbd5e1',
                            font: { size: 11 }
                        }
                    },
                    y: {
                        grid: { 
                            color: 'rgba(100, 116, 139, 0.2)',
                            borderColor: 'rgba(100, 116, 139, 0.5)'
                        },
                        ticks: { 
                            color: '#cbd5e1',
                            font: { size: 11 },
                            beginAtZero: true
                        }
                    }
                }
            }
        });
        
        setRequestsChart(newChart);
    };

    // Gráfico de distribuição por método HTTP
    const updateMethodsChart = (methodsData) => {
        const ctx = document.getElementById('methodsChart');
        if (!ctx) return;
        
        // Destruir gráfico anterior se existir
        if (methodsChart) {
            methodsChart.destroy();
        }
        
        // Dados padrão caso não tenha dados reais
        const defaultMethods = {
            'GET': 60,
            'POST': 25,
            'PUT': 10,
            'DELETE': 4,
            'PATCH': 1
        };
        
        const methods = Object.keys(methodsData).length > 0 ? methodsData : defaultMethods;
        const labels = Object.keys(methods);
        const data = Object.values(methods);
        
        // Criar novo gráfico
        const newChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#10b981', // GET - verde
                        '#3b82f6', // POST - azul  
                        '#f59e0b', // PUT - amarelo
                        '#ef4444', // DELETE - vermelho
                        '#8b5cf6'  // PATCH - roxo
                    ],
                    borderWidth: 2,
                    borderColor: '#1e293b'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            color: '#cbd5e1',
                            font: { size: 11 },
                            padding: 15
                        }
                    }
                },
                cutout: '60%'
            }
        });
        
        setMethodsChart(newChart);
    };

    // Gráfico de códigos de status
    const updateStatusChart = (statusData) => {
        const ctx = document.getElementById('statusChart');
        if (!ctx) return;
        
        // Destruir gráfico anterior se existir
        if (statusChart) {
            statusChart.destroy();
        }
        
        // Dados padrão caso não tenha dados reais
        const defaultStatus = {
            '2xx': 85,
            '4xx': 10,
            '5xx': 5
        };
        
        const status = Object.keys(statusData).length > 0 ? statusData : defaultStatus;
        const labels = Object.keys(status);
        const data = Object.values(status);
        
        // Criar novo gráfico
        const newChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels.map(label => `${label} (${statusData[label] || 0}%)`),
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#10b981', // 2xx - verde
                        '#f59e0b', // 4xx - amarelo
                        '#ef4444'  // 5xx - vermelho
                    ],
                    borderWidth: 2,
                    borderColor: '#1e293b'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            color: '#cbd5e1',
                            font: { size: 11 },
                            padding: 15
                        }
                    }
                }
            }
        });
        
        setStatusChart(newChart);
    };

    // Atualizar barras de progresso do rate limit
    const RateLimitProgress = ({ period, data }) => {
        if (!data) return null;
        
        const percentage = (data.remaining / data.limit) * 100;
        let color = 'bg-green-500';
        
        if (percentage < 20) color = 'bg-red-500';
        else if (percentage < 50) color = 'bg-yellow-500';
        
        // Formatar tempo de reset
        const formatResetTime = (seconds) => {
            if (seconds <= 0) return 'Reset: agora';
            
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `Reset em: ${minutes}m ${remainingSeconds}s`;
        };
        
        return (
            <div>
                <div className="flex justify-between text-sm text-slate-300 mb-1">
                    <span>Por {period === 'minute' ? 'Minuto' : period === 'hour' ? 'Hora' : 'Dia'}</span>
                    <span>{data.remaining}/{data.limit}</span>
                </div>
                <div className="w-full bg-slate-700 rounded-full h-2">
                    <div 
                        className={`${color} h-2 rounded-full transition-all duration-300`}
                        style={{ width: `${percentage}%` }}
                    />
                </div>
                <div className="text-xs text-slate-400 mt-1">
                    {formatResetTime(data.reset_in)}
                </div>
            </div>
        );
    };

    // Componente de Loading
    const LoadingState = () => (
        <div className="text-center py-8">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-cyan-400 mx-auto" />
            <p className="text-slate-300 mt-4">Carregando estatísticas...</p>
        </div>
    );

    // Componente de Erro
    const ErrorState = () => (
        <div className="text-center py-8">
            <div className="text-red-400 text-6xl mb-4">
                <i className="fas fa-exclamation-triangle" />
            </div>
            <h4 className="text-lg font-semibold text-white mb-2">Erro ao carregar estatísticas</h4>
            <p className="text-slate-300 mb-4">{error || 'Não foi possível carregar as estatísticas da API.'}</p>
            <button 
                onClick={loadStatistics}
                className="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors"
            >
                <i className="fas fa-redo mr-2" />
                Tentar Novamente
            </button>
        </div>
    );

    // Cartões de estatísticas
    const StatCard = ({ icon, iconColor, title, value, bgColor = "bg-blue-500/20" }) => (
        <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
            <div className="flex items-center">
                <div className={`p-2 ${bgColor} rounded-lg mr-3`}>
                    <i className={`${icon} ${iconColor} text-lg`} />
                </div>
                <div>
                    <p className="text-sm text-slate-400">{title}</p>
                    <p className="text-2xl font-bold text-white">{value}</p>
                </div>
            </div>
        </div>
    );

    // Inicializar quando o componente montar
    useEffect(() => {
        loadStatistics();
        loadEndpointStatistics();
        
        // Limpar gráficos quando o componente desmontar
        return () => {
            if (requestsChart) requestsChart.destroy();
            if (methodsChart) methodsChart.destroy();
            if (statusChart) statusChart.destroy();
        };
    }, []);

    // Atualizar gráficos quando statistics mudar
    useEffect(() => {
        if (statistics) {
            updateCharts();
        }
    }, [statistics]);

    if (loading) return <LoadingState />;
    if (error) return <ErrorState />;

    const apiUsage = statistics?.api_usage || {};
    const performanceMetrics = statistics?.performance_metrics || {};
    const rateLimitStats = statistics?.rate_limit_stats || {};
    const workspaceStats = statistics?.workspace_stats || {};
    const planInfo = statistics?.plan_info || {};

    return (
        <div className="p-6 rounded-lg bg-slate-800/50 border border-slate-700">
            <div className="flex justify-between items-center mb-6">
                <h3 className="text-xl font-semibold text-white">
                    <i className="fas fa-chart-bar mr-2 text-cyan-400" />
                    Estatísticas da API
                </h3>
                <button 
                    onClick={refreshStatistics}
                    disabled={refreshing}
                    className="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm transition-colors flex items-center disabled:opacity-50"
                >
                    <i className={`fas fa-sync-alt mr-2 ${refreshing ? 'animate-spin' : ''}`} />
                    {refreshing ? 'Atualizando...' : 'Atualizar'}
                </button>
            </div>

            <div className="space-y-6">
                {/* Cartões de Resumo */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <StatCard 
                        icon="fas fa-exchange-alt"
                        iconColor="text-blue-400"
                        bgColor="bg-blue-500/20"
                        title="Total de Requisições"
                        value={apiUsage.total_requests?.toLocaleString() || '0'}
                    />
                    
                    <StatCard 
                        icon="fas fa-check-circle"
                        iconColor="text-green-400"
                        bgColor="bg-green-500/20"
                        title="Taxa de Sucesso"
                        value={`${apiUsage.success_rate || '0'}%`}
                    />
                    
                    <StatCard 
                        icon="fas fa-clock"
                        iconColor="text-purple-400"
                        bgColor="bg-purple-500/20"
                        title="Tempo Médio"
                        value={apiUsage.average_response_time || '0ms'}
                    />
                    
                    <StatCard 
                        icon="fas fa-calendar-day"
                        iconColor="text-amber-400"
                        bgColor="bg-amber-500/20"
                        title="Requisições Hoje"
                        value={apiUsage.requests_today?.toLocaleString() || '0'}
                    />
                </div>

                {/* Grid Principal */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Rate Limits */}
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                        <h4 className="text-lg font-semibold text-white mb-4">
                            <i className="fas fa-tachometer-alt mr-2 text-green-400" />
                            Rate Limits
                        </h4>
                        <div className="space-y-4">
                            <RateLimitProgress period="minute" data={rateLimitStats.minute} />
                            <RateLimitProgress period="hour" data={rateLimitStats.hour} />
                            <RateLimitProgress period="day" data={rateLimitStats.day} />
                        </div>
                    </div>

                    {/* Estatísticas do Workspace */}
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                        <h4 className="text-lg font-semibold text-white mb-4">
                            <i className="fas fa-folder-open mr-2 text-blue-400" />
                            Conteúdo do Workspace
                        </h4>
                        <div className="space-y-3">
                            <div className="flex justify-between items-center py-2 border-b border-slate-700">
                                <span className="text-slate-300">Total de Tópicos</span>
                                <span className="text-white font-semibold">{workspaceStats.total_topics || '0'}</span>
                            </div>
                            <div className="flex justify-between items-center py-2 border-b border-slate-700">
                                <span className="text-slate-300">Total de Campos</span>
                                <span className="text-white font-semibold">{workspaceStats.total_fields || '0'}</span>
                            </div>
                            <div className="flex justify-between items-center py-2 border-b border-slate-700">
                                <span className="text-slate-300">Campos Visíveis</span>
                                <span className="text-white font-semibold">{workspaceStats.visible_fields || '0'}</span>
                            </div>
                            <div className="flex justify-between items-center py-2">
                                <span className="text-slate-300">Plano Atual</span>
                                <span className="px-2 py-1 bg-cyan-500/20 text-cyan-300 rounded text-sm font-semibold">
                                    {planInfo.name || 'Free'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Informações de Uso */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Endpoint Mais Usado */}
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                        <h4 className="text-lg font-semibold text-white mb-4">
                            <i className="fas fa-fire mr-2 text-red-400" />
                            Endpoint Mais Popular
                        </h4>
                        <div className="flex space-x-3 items-center justify-between">
                            <code className="text-cyan-300 bg-slate-800 px-3 py-2 rounded text-sm overflow-auto">
                                {apiUsage.most_used_endpoint || '/endpoint'}
                            </code>
                            <span className="text-slate-300 text-sm whitespace-nowrap">
                                {(apiUsage.most_used_endpoint_count || 0).toLocaleString()} requisições
                            </span>
                        </div>
                    </div>

                    {/* Horário de Pico */}
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                        <h4 className="text-lg font-semibold text-white mb-4">
                            <i className="fas fa-chart-line mr-2 text-amber-400" />
                            Horário de Pico
                        </h4>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-amber-400">
                                {apiUsage.peak_usage_hour || '--:--'}
                            </div>
                            <p className="text-slate-400 text-sm mt-1">Maior volume de requisições</p>
                        </div>
                    </div>
                </div>

                {/* Métricas de Performance */}
                <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <h4 className="text-lg font-semibold text-white mb-4">
                        <i className="fas fa-bolt mr-2 text-yellow-400" />
                        Performance
                    </h4>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div className="text-center">
                            <div className="text-2xl font-bold text-green-400">
                                {performanceMetrics.uptime || '100'}%
                            </div>
                            <p className="text-xs text-slate-400 mt-1">Uptime</p>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-blue-400">
                                {performanceMetrics.p95_response_time || '0ms'}
                            </div>
                            <p className="text-xs text-slate-400 mt-1">P95 Response</p>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-purple-400">
                                {performanceMetrics.unique_ips || '0'}
                            </div>
                            <p className="text-xs text-slate-400 mt-1">IPs Únicos</p>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-amber-400">
                                {performanceMetrics.active_days || '0'}
                            </div>
                            <p className="text-xs text-slate-400 mt-1">Dias Ativos</p>
                        </div>
                    </div>
                </div>

                {/* Gráfico de Tendência */}
                <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <h4 className="text-lg font-semibold text-white mb-4">
                        <i className="fas fa-chart-line mr-2 text-green-400" />
                        Tendência dos Últimos 7 Dias
                    </h4>
                    <div className="h-64">
                        <canvas id="requestsChart" />
                    </div>
                </div>

                {/* Gráficos de Métodos e Status */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                        <h4 className="text-lg font-semibold text-white mb-4">
                            <i className="fas fa-code-branch mr-2 text-blue-400" />
                            Distribuição por Método HTTP
                        </h4>
                        <div className="h-48">
                            <canvas id="methodsChart" />
                        </div>
                    </div>
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                        <h4 className="text-lg font-semibold text-white mb-4">
                            <i className="fas fa-list-alt mr-2 text-purple-400" />
                            Códigos de Status
                        </h4>
                        <div className="h-48">
                            <canvas id="statusChart" />
                        </div>
                    </div>
                </div>

                {/* Endpoints Mais Usados */}
                <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div className="flex justify-between items-center mb-4">
                        <h4 className="text-lg font-semibold text-white">
                            <i className="fas fa-list-ol mr-2 text-cyan-400" />
                            Top 5 Endpoints
                        </h4>
                        <button 
                            onClick={loadEndpointStatistics}
                            className="text-slate-400 hover:text-white text-sm"
                        >
                            <i className="fas fa-sync-alt mr-1" />
                            Atualizar
                        </button>
                    </div>
                    <div className="overflow-auto">
                        {endpointStats.length > 0 ? (
                            endpointStats.map((endpoint, index) => (
                                <div key={index} className="flex items-center justify-between py-3 border-b border-slate-700 last:border-b-0">
                                    <div className="flex-1">
                                        <div className="flex items-center space-x-2">
                                            <span className="px-2 py-1 bg-slate-700 text-slate-300 text-xs rounded font-mono">
                                                {endpoint.method}
                                            </span>
                                            <code className="text-sm text-cyan-300 truncate">
                                                {endpoint.endpoint}
                                            </code>
                                        </div>
                                        <div className="flex items-center space-x-4 mt-2 text-xs text-slate-400">
                                            <span>{endpoint.total_requests.toLocaleString()} req</span>
                                            <span>{endpoint.success_rate}% sucesso</span>
                                            <span>{endpoint.avg_response_time}</span>
                                        </div>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <div className="text-center py-4 text-slate-400">
                                Nenhuma requisição registrada ainda
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}