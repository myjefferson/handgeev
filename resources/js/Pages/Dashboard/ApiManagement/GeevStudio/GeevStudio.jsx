import React, { useState, useEffect, useMemo } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import { StudioApiSettingsTab } from './StudioApiSettingsTab';
import { StudioStatisticsTab } from './StudioStatisticsTab';
import { StudioWorkspaceTab } from './StudioWorkspaceTab';
import { StudioCodeExamplesTab } from './StudioCodeExamplesTab';
import { StudioInputConnectionsTab } from './StudioInputConnectionsTab';

const GeevStudio = () => {
    const { 
        auth, 
        workspace, 
        rateLimitInfo, 
        global_key_api, 
        workspace_key_api,
        apiStats = {} // Adicione esta prop no backend
    } = usePage().props;

    const [activeTab, setActiveTab] = useState('workspace');
    const [activeLanguage, setActiveLanguage] = useState('javascript');
    const [searchTerm, setSearchTerm] = useState('');
    const [viewMode, setViewMode] = useState('normal'); // 'normal' ou 'json'
    const [expandedTopics, setExpandedTopics] = useState(new Set());
    const [connections, setConnections] = useState([]);
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
    const [showApiKeyModal, setShowApiKeyModal] = useState(false);
    const [apiKey, setApiKey] = useState(''); // Simulação de chave API

    // Estatísticas de exemplo (substitua pelos dados reais do backend)
    const [statistics, setStatistics] = useState({
        requestsToday: 143,
        totalRequests: 5248,
        popularEndpoint: '/api/v1/workspace',
        averageResponseTime: '245ms',
        errorRate: '0.8%',
        uniqueVisitors: 89,
        peakHour: '14:00-15:00'
    });

    // Dados de exemplo para gráficos
    const [usageData] = useState({
        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
        data: [1200, 1900, 1500, 2200, 1800, 2500]
    });

    // Inicializar tópicos expandidos
    useEffect(() => {
        if (workspace?.topics?.length > 0) {
            setExpandedTopics(new Set([workspace.topics[0]?.id]));
        }
    }, [workspace]);

    // Gerar API key aleatória (simulação)
    useEffect(() => {
        if (showApiKeyModal && !apiKey) {
            const generatedKey = `gee_${Math.random().toString(36).substr(2, 9)}_${Math.random().toString(36).substr(2, 9)}`;
            setApiKey(generatedKey);
        }
    }, [showApiKeyModal, apiKey]);

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
            console.error('Erro ao copiar: ', err);
            alert('Erro ao copiar');
        }
    };

    const generateApiUrl = (topicId) => {
        return `${window.location.origin}/api/v1/topics/${topicId}`;
    };

    const toggleApiStatus = () => {
        setApiConfig(prev => ({
            ...prev,
            enabled: !prev.enabled
        }));
    };

    const saveApiConfig = () => {
        // Aqui você faria a chamada API para salvar as configurações
        alert('Configurações salvas com sucesso!');
    };

    const resetApiKey = () => {
        setApiKey('');
        setShowApiKeyModal(true);
    };

    // Filtro de pesquisa
    const filteredTopics = useMemo(() => {
        if (!workspace?.topics) return [];

        return workspace.topics.filter(topic => {
            if (!searchTerm.trim()) return true;

            const searchLower = searchTerm.toLowerCase();
            const topicMatches = topic.title.toLowerCase().includes(searchLower);
            
            // Verificar campos (se existirem)
            const fieldMatches = topic.fields?.some(field =>
                field.key_name?.toLowerCase().includes(searchLower) ||
                field.value?.toString().toLowerCase().includes(searchLower)
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
            {/* <Head>
                <title>GeevStudio - {workspace.title}</title>
                <meta name="description" content={`Workspace ${workspace.title} no Handgeev Studio`} />
            </Head> */}

            <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
                {/* Header */}
                <header className="shadow">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
                <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                            expandedTopics={expandedTopics}
                            viewMode={viewMode}
                            filteredTopics={filteredTopics}
                            workspace={workspace}
                            copyToClipboard={copyToClipboard}
                        />

                        {/* Aba: Estatísticas */}
                        <StudioStatisticsTab
                            activeTab={activeTab}
                            statistics={statistics}
                            rateLimitInfo={rateLimitInfo}
                            usageData={usageData}
                        />

                        {/* Aba: API Config */}
                        <StudioApiSettingsTab 
                            activeTab={activeTab}
                            apiConfig={apiConfig}
                            toggleApiStatus={toggleApiStatus}
                            saveApiConfig={saveApiConfig}
                            global_key_api={global_key_api}
                        />

                        {/* Aba: Code Examples */}
                        <StudioCodeExamplesTab
                            activeTab={activeTab}
                            activeLanguage={activeLanguage}
                            global_key_api={global_key_api}
                            apiConfig={apiConfig}
                            setActiveLanguage={setActiveLanguage}
                        />

                        {}
                        <StudioInputConnectionsTab
                            activeTab={activeTab}
                            workspace={workspace}
                        />
                    </div>
                </main>
            </div>

            {/* Modal: Nova API Key */}
            {showApiKeyModal && (
                <div className="fixed inset-0 z-50 overflow-y-auto">
                    <div className="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div className="fixed inset-0 transition-opacity" aria-hidden="true">
                            <div className="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <div className="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <div className="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div className="sm:flex sm:items-start">
                                    <div className="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-teal-100 dark:bg-teal-900 sm:mx-0 sm:h-10 sm:w-10">
                                        <i className="fas fa-key text-teal-600 dark:text-teal-400"></i>
                                    </div>
                                    <div className="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 className="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                            Nova API Key Gerada
                                        </h3>
                                        <div className="mt-2">
                                            <p className="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                                Sua nova chave de API foi gerada. Guarde-a em um local seguro, pois ela não poderá ser recuperada novamente.
                                            </p>
                                            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                                <code className="text-sm text-gray-900 dark:text-gray-100 break-all">
                                                    {apiKey}
                                                </code>
                                            </div>
                                            <p className="text-xs text-red-500 dark:text-red-400 mt-2">
                                                <i className="fas fa-exclamation-triangle mr-1"></i>
                                                Esta chave será exibida apenas uma vez
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button
                                    type="button"
                                    onClick={() => {
                                        copyToClipboard(apiKey, 'API Key copiada!');
                                        setShowApiKeyModal(false);
                                    }}
                                    className="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    <i className="fas fa-copy mr-2"></i>
                                    Copiar e Fechar
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setShowApiKeyModal(false)}
                                    className="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
};

export default GeevStudio;