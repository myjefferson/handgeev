<div class="hidden p-6 rounded-lg bg-slate-800/50 border border-slate-700" id="statistics-tab" role="tabpanel">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold text-white">
            <i class="fas fa-chart-bar mr-2 text-cyan-400"></i>
            Estatísticas da API
        </h3>
        <button onclick="refreshStatistics()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm transition-colors flex items-center">
            <i class="fas fa-sync-alt mr-2"></i>
            Atualizar
        </button>
    </div>

    <div id="statistics-content">
        <!-- Loading -->
        <div id="statistics-loading" class="text-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-cyan-400 mx-auto"></div>
            <p class="text-slate-300 mt-4">Carregando estatísticas...</p>
        </div>

        <!-- Conteúdo das estatísticas -->
        <div id="statistics-data" class="hidden space-y-6">
            <!-- Cartões de Resumo -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total de Requisições -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-500/20 rounded-lg mr-3">
                            <i class="fas fa-exchange-alt text-blue-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Total de Requisições</p>
                            <p class="text-2xl font-bold text-white" id="total-requests">0</p>
                        </div>
                    </div>
                </div>

                <!-- Taxa de Sucesso -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-500/20 rounded-lg mr-3">
                            <i class="fas fa-check-circle text-green-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Taxa de Sucesso</p>
                            <p class="text-2xl font-bold text-white" id="success-rate">0%</p>
                        </div>
                    </div>
                </div>

                <!-- Tempo Médio de Resposta -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-500/20 rounded-lg mr-3">
                            <i class="fas fa-clock text-purple-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Tempo Médio</p>
                            <p class="text-2xl font-bold text-white" id="avg-response-time">0ms</p>
                        </div>
                    </div>
                </div>

                <!-- Requisições Hoje -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-amber-500/20 rounded-lg mr-3">
                            <i class="fas fa-calendar-day text-amber-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Requisições Hoje</p>
                            <p class="text-2xl font-bold text-white" id="requests-today">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Principal -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Rate Limits -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <h4 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-tachometer-alt mr-2 text-green-400"></i>
                        Rate Limits
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm text-slate-300 mb-1">
                                <span>Por Minuto</span>
                                <span id="minute-remaining">0</span>/<span id="minute-limit">0</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-2">
                                <div id="minute-progress" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <div class="text-xs text-slate-400 mt-1" id="minute-reset">Reset em: --</div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm text-slate-300 mb-1">
                                <span>Por Hora</span>
                                <span id="hour-remaining">0</span>/<span id="hour-limit">0</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-2">
                                <div id="hour-progress" class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <div class="text-xs text-slate-400 mt-1" id="hour-reset">Reset em: --</div>
                        </div>

                        <div>
                            <div class="flex justify-between text-sm text-slate-300 mb-1">
                                <span>Por Dia</span>
                                <span id="day-remaining">0</span>/<span id="day-limit">0</span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-2">
                                <div id="day-progress" class="bg-purple-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <div class="text-xs text-slate-400 mt-1" id="day-reset">Reset em: --</div>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas do Workspace -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <h4 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-folder-open mr-2 text-blue-400"></i>
                        Conteúdo do Workspace
                    </h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-slate-700">
                            <span class="text-slate-300">Total de Tópicos</span>
                            <span class="text-white font-semibold" id="total-topics">0</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-700">
                            <span class="text-slate-300">Total de Campos</span>
                            <span class="text-white font-semibold" id="total-fields">0</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-700">
                            <span class="text-slate-300">Campos Visíveis</span>
                            <span class="text-white font-semibold" id="visible-fields">0</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-slate-300">Plano Atual</span>
                            <span class="px-2 py-1 bg-cyan-500/20 text-cyan-300 rounded text-sm font-semibold" id="current-plan">Free</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações de Uso -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Endpoint Mais Usado -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <h4 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-fire mr-2 text-red-400"></i>
                        Endpoint Mais Popular
                    </h4>
                    <div class="flex space-x-3 items-center justify-between">
                        <code class="text-cyan-300 bg-slate-800 px-3 py-2 rounded text-sm overflow-scroll" id="popular-endpoint">/endpoint</code>
                        <span class="text-slate-300 text-sm" id="endpoint-usage">-- requisições</span>
                    </div>
                </div>

                <!-- Horário de Pico -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <h4 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-chart-line mr-2 text-amber-400"></i>
                        Horário de Pico
                    </h4>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-amber-400" id="peak-hour">--:--</div>
                        <p class="text-slate-400 text-sm mt-1">Maior volume de requisições</p>
                    </div>
                </div>
            </div>

            <!-- Métricas de Performance -->
            <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                <h4 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-bolt mr-2 text-yellow-400"></i>
                    Performance
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-400" id="uptime">100%</div>
                        <p class="text-xs text-slate-400 mt-1">Uptime</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-400" id="p95-response">0ms</div>
                        <p class="text-xs text-slate-400 mt-1">P95 Response</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-400" id="unique-ips">0</div>
                        <p class="text-xs text-slate-400 mt-1">IPs Únicos</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-amber-400" id="active-days">0</div>
                        <p class="text-xs text-slate-400 mt-1">Dias Ativos</p>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                <h4 class="text-lg font-semibold text-white mb-4">
                    <i class="fas fa-chart-line mr-2 text-green-400"></i>
                    Tendência dos Últimos 7 Dias
                </h4>
                <div class="h-64">
                    <canvas id="requestsChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <h4 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-code-branch mr-2 text-blue-400"></i>
                        Distribuição por Método HTTP
                    </h4>
                    <div class="h-48">
                        <canvas id="methodsChart"></canvas>
                    </div>
                </div>
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <h4 class="text-lg font-semibold text-white mb-4">
                        <i class="fas fa-list-alt mr-2 text-purple-400"></i>
                        Códigos de Status
                    </h4>
                    <div class="h-48">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Endpoints Mais Usados -->
            <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-white">
                        <i class="fas fa-list-ol mr-2 text-cyan-400"></i>
                        Top 5 Endpoints
                    </h4>
                    <button onclick="loadEndpointStatistics()" class="text-slate-400 hover:text-white text-sm">
                        <i class="fas fa-sync-alt mr-1"></i>
                        Atualizar
                    </button>
                </div>
                <div id="endpoints-stats" class="overflow-scroll">
                    <div class="text-center py-4 text-slate-400">
                        Carregando endpoints...
                    </div>
                </div>
            </div>
        </div>

        <!-- Erro -->
        <div id="statistics-error" class="hidden text-center py-8">
            <div class="text-red-400 text-6xl mb-4">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h4 class="text-lg font-semibold text-white mb-2">Erro ao carregar estatísticas</h4>
            <p class="text-slate-300 mb-4" id="error-message">Não foi possível carregar as estatísticas da API.</p>
            <button onclick="loadStatistics()" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                <i class="fas fa-redo mr-2"></i>
                Tentar Novamente
            </button>
        </div>
    </div>
</div>

<script>
    // Variáveis globais para os gráficos
    let requestsChart, methodsChart, statusChart;

    // Carregar estatísticas da API
    async function loadStatistics() {
        const loadingEl = document.getElementById('statistics-loading');
        const dataEl = document.getElementById('statistics-data');
        const errorEl = document.getElementById('statistics-error');

        // Mostrar loading
        loadingEl.classList.remove('hidden');
        dataEl.classList.add('hidden');
        errorEl.classList.add('hidden');

        try {
            const response = await fetch("{{ route('api.get.statistics', ['global_key_api' => auth()->user()->global_key_api, 'workspace_key_api' => $workspace->workspace_key_api]) }}");
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erro ao carregar estatísticas');
            }

            // Atualizar UI com os dados
            updateStatisticsUI(data);
            
            // Mostrar dados
            loadingEl.classList.add('hidden');
            dataEl.classList.remove('hidden');

        } catch (error) {
            console.error('Erro ao carregar estatísticas:', error);
            loadingEl.classList.add('hidden');
            errorEl.classList.remove('hidden');
            document.getElementById('error-message').textContent = error.message;
        }
    }

    // Atualizar UI com dados das estatísticas
    function updateStatisticsUI(data) {
        const apiUsage = data.api_usage;
        const performanceMetrics = data.performance_metrics || {};
        
        // Cartões de resumo com dados reais
        document.getElementById('total-requests').textContent = apiUsage.total_requests.toLocaleString();
        document.getElementById('success-rate').textContent = apiUsage.success_rate + '%';
        document.getElementById('avg-response-time').textContent = apiUsage.average_response_time;
        document.getElementById('requests-today').textContent = apiUsage.requests_today.toLocaleString();

        // Rate Limits
        updateRateLimitProgress('minute', data.rate_limit_stats.minute);
        updateRateLimitProgress('hour', data.rate_limit_stats.hour);
        updateRateLimitProgress('day', data.rate_limit_stats.day);

        // Workspace stats
        document.getElementById('total-topics').textContent = data.workspace_stats.total_topics;
        document.getElementById('total-fields').textContent = data.workspace_stats.total_fields;
        document.getElementById('visible-fields').textContent = data.workspace_stats.visible_fields;
        document.getElementById('current-plan').textContent = data.plan_info.name;

        // Uso da API com dados reais
        document.getElementById('popular-endpoint').textContent = apiUsage.most_used_endpoint;
        document.getElementById('endpoint-usage').textContent = apiUsage.most_used_endpoint_count.toLocaleString() + ' requisições';
        document.getElementById('peak-hour').textContent = apiUsage.peak_usage_hour;

        // Métricas de performance
        document.getElementById('uptime').textContent = performanceMetrics.uptime + '%';
        document.getElementById('p95-response').textContent = performanceMetrics.p95_response_time;
        document.getElementById('unique-ips').textContent = performanceMetrics.unique_ips;
        document.getElementById('active-days').textContent = performanceMetrics.active_days;

        // Atualizar gráficos
        updateCharts(data);
    }

    // Atualizar gráficos
    function updateCharts(data) {
        updateRequestsChart(data.usage_by_period || []);
        updateMethodsChart(data.methods_distribution || {});
        updateStatusChart(data.status_distribution || {});
    }

    // Gráfico de tendência de requisições
    function updateRequestsChart(usageData) {
        const ctx = document.getElementById('requestsChart').getContext('2d');
        
        // Preparar dados para o gráfico
        const labels = usageData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
        });
        
        const requests = usageData.map(item => item.total_requests);

        if (requestsChart) {
            requestsChart.destroy();
        }

        requestsChart = new Chart(ctx, {
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
    }

    // Gráfico de distribuição por método HTTP
    function updateMethodsChart(methodsData) {
        const ctx = document.getElementById('methodsChart').getContext('2d');
        
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

        if (methodsChart) {
            methodsChart.destroy();
        }

        methodsChart = new Chart(ctx, {
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
    }

    // Gráfico de códigos de status
    function updateStatusChart(statusData) {
        const ctx = document.getElementById('statusChart').getContext('2d');
        
        // Dados padrão caso não tenha dados reais
        const defaultStatus = {
            '2xx': 85,
            '4xx': 10,
            '5xx': 5
        };
        
        const status = Object.keys(statusData).length > 0 ? statusData : defaultStatus;
        const labels = Object.keys(status);
        const data = Object.values(status);

        if (statusChart) {
            statusChart.destroy();
        }

        statusChart = new Chart(ctx, {
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
    }

    // Atualizar barras de progresso do rate limit
    function updateRateLimitProgress(period, data) {
        const percentage = (data.remaining / data.limit) * 100;
        const progressBar = document.getElementById(`${period}-progress`);
        const remainingEl = document.getElementById(`${period}-remaining`);
        const limitEl = document.getElementById(`${period}-limit`);
        const resetEl = document.getElementById(`${period}-reset`);

        // Cor baseada no uso
        let color = 'bg-green-500';
        if (percentage < 20) color = 'bg-red-500';
        else if (percentage < 50) color = 'bg-yellow-500';

        progressBar.className = `${color} h-2 rounded-full transition-all duration-300`;
        progressBar.style.width = `${percentage}%`;

        remainingEl.textContent = data.remaining;
        limitEl.textContent = data.limit;
        
        // Formatar tempo de reset
        if (data.reset_in > 0) {
            const minutes = Math.floor(data.reset_in / 60);
            const seconds = data.reset_in % 60;
            resetEl.textContent = `Reset em: ${minutes}m ${seconds}s`;
        } else {
            resetEl.textContent = 'Reset: agora';
        }
    }

    // Carregar estatísticas de endpoints
    async function loadEndpointStatistics() {
        try {
            const response = await fetch("{{ route('api.get.endpoint-statistics', ['global_key_api' => auth()->user()->global_key_api, 'workspace_key_api' => $workspace->workspace_key_api]) }}");
            const data = await response.json();

            if (data.success && data.endpoints.length > 0) {
                updateEndpointStatisticsUI(data.endpoints);
            } else {
                document.getElementById('endpoints-stats').innerHTML = 
                    '<div class="text-center py-4 text-slate-400">Nenhuma requisição registrada ainda</div>';
            }
        } catch (error) {
            console.error('Erro ao carregar estatísticas de endpoints:', error);
            document.getElementById('endpoints-stats').innerHTML = 
                '<div class="text-center py-4 text-red-400">Erro ao carregar endpoints</div>';
        }
    }

    // Atualizar UI de estatísticas de endpoints
    function updateEndpointStatisticsUI(endpoints) {
        const container = document.getElementById('endpoints-stats');
        
        if (endpoints.length === 0) {
            container.innerHTML = '<div class="text-center py-4 text-slate-400">Nenhuma requisição registrada ainda</div>';
            return;
        }

        const topEndpoints = endpoints.slice(0, 5); // Top 5 endpoints
        
        container.innerHTML = topEndpoints.map(endpoint => `
            <div class="flex items-center justify-between py-3 border-b border-slate-700 last:border-b-0">
                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-slate-700 text-slate-300 text-xs rounded font-mono">
                            ${endpoint.method}
                        </span>
                        <code class="text-sm text-cyan-300 truncate">${endpoint.endpoint}</code>
                    </div>
                    <div class="flex items-center space-x-4 mt-2 text-xs text-slate-400">
                        <span>${endpoint.total_requests.toLocaleString()} req</span>
                        <span>${endpoint.success_rate}% sucesso</span>
                        <span>${endpoint.avg_response_time}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Atualizar estatísticas
    function refreshStatistics() {
        loadStatistics();
    }

    // Carregar estatísticas quando a aba for aberta
    document.addEventListener('DOMContentLoaded', function() {
        // Observar mudanças de tab para carregar estatísticas quando aberta
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = mutation.target;
                    if (target.id === 'statistics-tab' && !target.classList.contains('hidden')) {
                        loadStatistics();
                        loadEndpointStatistics();
                    }
                }
            });
        });

        const statisticsTab = document.getElementById('statistics-tab');
        if (statisticsTab) {
            observer.observe(statisticsTab, { attributes: true });
        }
    });
</script>