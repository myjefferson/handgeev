import React, { useState, useEffect, useMemo } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { StudioApiSettingsTab } from './StudioApiSettingsTab';
import { StudioStatisticsTab } from './StudioStatisticsTab';
import { StudioWorkspaceTab } from './StudioWorkspaceTab';
import { StudioCodeExamplesTab } from './StudioCodeExamplesTab';
import StudioInputConnectionsTab from './StudioInputConnectionsTab';

const GeevStudio = () => {
    const { 
        auth, 
        workspace, 
        rateLimitInfo, 
        global_key_api, 
        workspace_key_api,
        connections = []
    } = usePage().props;

    const [activeTab, setActiveTab] = useState('workspace');
    const [activeLanguage, setActiveLanguage] = useState('javascript');
    const [searchTerm, setSearchTerm] = useState('');
    const [viewMode, setViewMode] = useState('normal');
    const [expandedTopics, setExpandedTopics] = useState(new Set());
    const [apiConfig, setApiConfig] = useState({
        enabled: true,
        requireHttps: true,
        rateLimitPerMinute: 60,
        rateLimitPerDay: 1000,
        allowCors: true,
        enableWebhooks: false,
        requireApiKey: true,
        logRequests: true
    });
    
    // Estados para estatísticas
    const [statistics, setStatistics] = useState(null);
    const [loadingStats, setLoadingStats] = useState(false);
    const [statsError, setStatsError] = useState(null);
    const [usageByPeriod, setUsageByPeriod] = useState([]);
    const [methodsDistribution, setMethodsDistribution] = useState([]);
    const [statusDistribution, setStatusDistribution] = useState([]);

    // Inicializar tópicos expandidos
    useEffect(() => {
        if (workspace?.topics?.length > 0) {
            setExpandedTopics(new Set([workspace.topics[0]?.id]));
        }
    }, [workspace]);

    // Buscar estatísticas quando a aba for ativada
    useEffect(() => {
        if (activeTab === 'statistics' && !loadingStats) {
            fetchStatistics();
        }
    }, [activeTab]);

    // Função para buscar estatísticas
    const fetchStatistics = async () => {
        setLoadingStats(true);
        setStatsError(null);
        
        try {
            const response = await fetch(`/api/workspace/${global_key_api}/${workspace_key_api}/statistics`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${global_key_api}`
                }
            });
            
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                setStatistics(data);
                
                // Processar dados para gráficos
                if (data.usage_by_period && Array.isArray(data.usage_by_period)) {
                    const processedUsage = data.usage_by_period.map(item => ({
                        label: new Date(item.date).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' }),
                        value: item.count || 0
                    }));
                    setUsageByPeriod(processedUsage);
                } else {
                    setUsageByPeriod([]);
                }
                
                if (data.methods_distribution && Array.isArray(data.methods_distribution)) {
                    setMethodsDistribution(data.methods_distribution);
                } else {
                    setMethodsDistribution([]);
                }
                
                if (data.status_distribution && Array.isArray(data.status_distribution)) {
                    setStatusDistribution(data.status_distribution);
                } else {
                    setStatusDistribution([]);
                }
            } else {
                throw new Error(data.error || 'Erro ao buscar estatísticas');
            }
        } catch (error) {
            console.error('Erro ao carregar estatísticas:', error);
            setStatsError(error.message);
            // Inicializar arrays vazios em caso de erro
            setUsageByPeriod([]);
            setMethodsDistribution([]);
            setStatusDistribution([]);
        } finally {
            setLoadingStats(false);
        }
    };

    // Atualizar estatísticas periodicamente
    useEffect(() => {
        let interval;
        if (activeTab === 'statistics') {
            // Atualizar a cada 30 segundos enquanto na aba
            interval = setInterval(() => {
                if (!loadingStats) {
                    fetchStatistics();
                }
            }, 30000);
        }
        
        return () => {
            if (interval) clearInterval(interval);
        };
    }, [activeTab, loadingStats]);

    // Funções auxiliares
    const toggleTopic = (topicId) => {
        setExpandedTopics(prev => {
            const newSet = new Set(prev);
            if (newSet.has(topicId)) {
                newSet.delete(topicId);
            } else {
                newSet.add(topicId);
            }
            return newSet;
        });
    };

    const copyToClipboard = async (text, message) => {
        try {
            await navigator.clipboard.writeText(text);
            alert(message || 'Copiado!');
        } catch (err) {
            console.error('Erro ao copiar:', err);
            alert('Erro ao copiar');
        }
    };

    const generateApiUrl = (topicId) => {
        return `${window.location.origin}/api/v1/topics/${topicId}`;
    };

    // Filtro de pesquisa
    const filteredTopics = useMemo(() => {
        if (!workspace?.topics) return [];

        return workspace.topics.filter(topic => {
            if (!searchTerm.trim()) return true;

            const searchLower = searchTerm.toLowerCase();
            const topicMatches = topic.title.toLowerCase().includes(searchLower);
            
            // Verificar campos nos registros
            const fieldMatches = topic.records?.some(record => 
                record.values?.some(field =>
                    field.key_name?.toLowerCase().includes(searchLower) ||
                    field.value?.toString().toLowerCase().includes(searchLower)
                )
            );

            return topicMatches || fieldMatches;
        });
    }, [workspace, searchTerm]);

    if (!workspace) {
        return (
            <div className="min-h-screen bg-slate-900 dark:bg-gray-900 flex items-center justify-center">
                <div className="text-white">Carregando...</div>
            </div>
        );
    }

    return (
        <DashboardLayout>
            <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
                {/* Header */}
                <header className="shadow">
                    <div className="max-w-7xl mx-auto px-4 sm:px-3 lg:px-3 py-3">
                        <button
                            onClick={() => window.history.back()}
                            className="flex items-center text-cyan-400 hover:text-cyan-300 transition-colors cursor-pointer mb-5"
                        >
                            <i className="fas fa-arrow-left mr-2"></i>
                            Voltar
                        </button>
                        <div className="flex items-center justify-between">
                            <div>
                                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                    <i className="fas fa-code mr-2 text-teal-500"></i>
                                    GeevStudio
                                </h1>
                                <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    Gerenciamento avançado da API para <span className="font-semibold">{workspace.title}</span>
                                </p>
                            </div>
                            <div className="flex items-center space-x-4">
                                <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <i className="fas fa-key mr-1"></i>
                                    API Key: {global_key_api.substring(0, 8)}...
                                </span>
                                <button 
                                    onClick={() => copyToClipboard(global_key_api, 'API Key copiada!')}
                                    className="inline-flex items-center px-3 py-1 text-sm bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600"
                                >
                                    <i className="fas fa-copy mr-1"></i>Copiar Key
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                {/* Main Content */}
                <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-3 py-3">
                    {/* Tabs Navigation */}
                    <div className="border-b border-gray-200 dark:border-gray-700 mb-8">
                        <nav className="-mb-px flex space-x-8">
                            {[
                                { id: 'workspace', label: 'Workspace', icon: 'fa-layer-group' },
                                { id: 'statistics', label: 'Estatísticas', icon: 'fa-chart-bar' },
                                { id: 'api-config', label: 'API Config', icon: 'fa-cog' },
                                { id: 'code-examples', label: 'Code Examples', icon: 'fa-code' },
                                { id: 'input-connections', label: 'Conexões de Entrada', icon: 'fa-plug' }
                            ].map((tab) => (
                                <button
                                    key={tab.id}
                                    className={`py-4 px-1 border-b-2 font-medium text-sm flex items-center cursor-pointer ${
                                        activeTab === tab.id
                                            ? 'border-teal-500 text-teal-600 dark:text-teal-400'
                                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                                    }`}
                                    onClick={() => setActiveTab(tab.id)}
                                >
                                    <i className={`fas ${tab.icon} mr-2`}></i>
                                    {tab.label}
                                </button>
                            ))}
                        </nav>
                    </div>

                    {/* Tabs Content */}
                    <div>
                        {/* Aba: Workspace */}
                        <StudioWorkspaceTab
                            activeTab={activeTab}
                            searchTerm={searchTerm}
                            setSearchTerm={setSearchTerm}
                            viewMode={viewMode}
                            setViewMode={setViewMode}
                            filteredTopics={filteredTopics}
                            expandedTopics={expandedTopics}
                            toggleTopic={toggleTopic}
                            copyToClipboard={copyToClipboard}
                            generateApiUrl={generateApiUrl}
                            workspace={workspace}
                        />

                        {/* Aba: Estatísticas */}
                        <StudioStatisticsTab
                            activeTab={activeTab}
                            statistics={statistics}
                            loadingStats={loadingStats}
                            statsError={statsError}
                            rateLimitInfo={rateLimitInfo}
                            usageByPeriod={usageByPeriod}
                            methodsDistribution={methodsDistribution}
                            statusDistribution={statusDistribution}
                            refreshStatistics={fetchStatistics}
                        />

                        {/* Aba: API Config */}
                        <StudioApiSettingsTab 
                            activeTab={activeTab}
                            apiConfig={apiConfig}
                            setApiConfig={setApiConfig}
                        />

                        {/* Aba: Code Examples */}
                        <StudioCodeExamplesTab
                            activeTab={activeTab}
                            activeLanguage={activeLanguage}
                            global_key_api={global_key_api}
                            workspace_key_api={workspace_key_api}
                            apiConfig={apiConfig}
                            setActiveLanguage={setActiveLanguage}
                            copyToClipboard={copyToClipboard}
                        />

                        <StudioInputConnectionsTab
                            activeTab={activeTab}
                            workspace={workspace}
                            connections={connections}
                        />
                    </div>
                </main>
            </div>
        </DashboardLayout>
    );
};

export default GeevStudio;