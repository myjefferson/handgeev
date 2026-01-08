import React, { useState, useEffect } from 'react';
import { Head, usePage } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';

const GeevStudio = () => {
    const { 
        auth, 
        workspace, 
        rateLimitInfo, 
        global_key_api, 
        workspace_key_api, 
        share_url,
        api_endpoints 
    } = usePage().props;

    const [activeTab, setActiveTab] = useState('workspace');
    const [activeLanguage, setActiveLanguage] = useState('javascript');
    const [searchTerm, setSearchTerm] = useState('');
    const [expandedTopics, setExpandedTopics] = useState(new Set());
    const [showShareModal, setShowShareModal] = useState(false);
    
    // Estados para a tab API Consult
    const [apiRequest, setApiRequest] = useState({
        method: '-',
        url: '-',
        status: '-',
        time: '-'
    });
    const [apiResponse, setApiResponse] = useState('// Clique em "Run" em algum endpoint para testar a API');
    const [isLoading, setIsLoading] = useState(false);
    const [showCopyResponse, setShowCopyResponse] = useState(false);

    // Inicializar tópicos expandidos quando o workspace carregar
    useEffect(() => {
        if (workspace?.topics?.length > 0) {
            setExpandedTopics(new Set([workspace.topics[0].id]));
        }
    }, [workspace]);

    // Função auxiliar para contar campos em um tópico
    const countFieldsInTopic = (topic) => {
        if (!topic?.records) return 0;
        
        let count = 0;
        topic.records.forEach(record => {
            if (record.values) {
                count += record.values.length;
            }
        });
        return count;
    };

    // Função auxiliar para obter todos os campos de um tópico
    const getAllFieldsFromTopic = (topic) => {
        if (!topic?.records) return [];
        
        const allFields = [];
        topic.records.forEach(record => {
            if (record.values) {
                allFields.push(...record.values);
            }
        });
        return allFields;
    };

    // Função auxiliar para obter o primeiro campo de um tópico
    const getFirstFieldFromTopic = (topic) => {
        if (!topic?.records?.[0]?.values?.[0]) return null;
        return topic.records[0].values[0];
    };

    // Alternar expansão de tópicos
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

    // Copiar para clipboard
    const copyToClipboard = async (text, message) => {
        try {
            await navigator.clipboard.writeText(text);
            alert(message);
        } catch (err) {
            console.error('Erro ao copiar: ', err);
            alert('Erro ao copiar');
        }
    };

    // Filtro de pesquisa (para tab workspace) - CORRIGIDO
    const filteredTopics = workspace?.topics?.map(topic => {
        if (!searchTerm.trim()) return topic;

        const allFields = getAllFieldsFromTopic(topic);
        const filteredFields = allFields.filter(field =>
            field.key_name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
            field.value?.toString().toLowerCase().includes(searchTerm.toLowerCase()) ||
            field.type?.toLowerCase().includes(searchTerm.toLowerCase())
        );

        const titleMatches = topic.title.toLowerCase().includes(searchTerm.toLowerCase());
        const topicHasMatches = titleMatches || filteredFields.length > 0;

        return {
            ...topic,
            filteredFields,
            hasMatches: topicHasMatches
        };
    }).filter(topic => {
        // Se não há termo de pesquisa, mostrar todos os tópicos
        if (!searchTerm.trim()) return true;
        
        // Se há termo de pesquisa, mostrar apenas tópicos com matches
        const allFields = getAllFieldsFromTopic(topic);
        const titleMatches = topic.title.toLowerCase().includes(searchTerm.toLowerCase());
        
        if (titleMatches) return true;
        
        // Verificar se algum campo corresponde ao termo de pesquisa
        return allFields.some(field =>
            field.key_name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
            field.value?.toString().toLowerCase().includes(searchTerm.toLowerCase()) ||
            field.type?.toLowerCase().includes(searchTerm.toLowerCase())
        );
    }) || [];

    // Loading state
    if (!workspace) {
        return (
            <div className="min-h-screen bg-slate-900 dark:bg-gray-900 flex items-center justify-center">
                <div className="text-white">Carregando...</div>
            </div>
        );
    }

    return (
        <DashboardLayout>
            <Head>
                <title>{workspace.title}</title>
                <meta name="description" content={`Workspace compartilhado por ${auth.user.name}`} />
            </Head>

            <div className="bg-slate-900 dark:bg-gray-900 min-h-screen">
                {/* Header */}
                <header className="shadow-sm">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center space-x-4">
                                <div>
                                    <h1 className="text-xl font-semibold text-gray-900 dark:text-white">
                                        {workspace.title}
                                    </h1>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        Workspace compartilhado
                                    </p>
                                </div>
                            </div>
                            
                            <div className="flex items-center space-x-4">
                                <div className="hidden md:flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i className="fas fa-share-alt"></i>
                                    <span>Compartilhado por: {auth.user.name} ({auth.user.email})</span>
                                </div>
                                
                                <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <i className="fas fa-eye mr-1"></i>
                                    Visualização
                                </span>
                            </div>
                        </div>
                    </div>
                </header>

                {/* Main Content */}
                <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    {/* Tabs Navigation */}
                    <div className="border-b border-gray-200 dark:border-gray-700 mb-6">
                        <ul className="flex flex-wrap -mb-px text-sm font-medium text-center">
                            {['workspace', 'api-consult', 'code-examples'].map((tab) => (
                                <li key={tab} className="mr-2">
                                    <button 
                                        className={`inline-block p-4 border-b-2 rounded-t-lg ${
                                            activeTab === tab 
                                                ? 'text-teal-600 border-teal-600 dark:text-teal-500 dark:border-teal-500' 
                                                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'
                                        }`}
                                        onClick={() => setActiveTab(tab)}
                                    >
                                        <i className={`fas ${
                                            tab === 'workspace' ? 'fa-layer-group' : 
                                            tab === 'api-consult' ? 'fa-code' : 'fa-code'
                                        } mr-2`}></i>
                                        {tab === 'api-consult' ? 'API Consult' : 
                                         tab === 'code-examples' ? 'Code Examples' : 'Workspace'}
                                    </button>
                                </li>
                            ))}
                        </ul>
                    </div>

                    {/* Tabs Content */}
                    <div>
                        {/* Tab Workspace - CORRIGIDO */}
                        {activeTab === 'workspace' && (
                            <div className="animate-fadeIn">
                                {/* Área de pesquisa */}
                                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6 max-w-3xl mx-auto">
                                    <div className="flex items-center justify-center">
                                        <div className="flex-1 max-w-2xl">
                                            <div className="relative">
                                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i className="fas fa-search text-gray-400"></i>
                                                </div>
                                                <input 
                                                    type="text" 
                                                    value={searchTerm}
                                                    onChange={(e) => setSearchTerm(e.target.value)}
                                                    className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:border-gray-600 dark:text-white"
                                                    placeholder="Pesquisar por chave ou valor..."
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden max-w-3xl mx-auto">
                                    {workspace ? (
                                        <div id="topics-accordion">
                                            {filteredTopics.length > 0 ? (
                                                filteredTopics.map((topic) => {
                                                    const fieldCount = countFieldsInTopic(topic);
                                                    const allFields = getAllFieldsFromTopic(topic);
                                                    
                                                    return (
                                                        <div key={topic.id} className="border border-b-0 border-gray-200 dark:border-gray-700 last:border-b">
                                                            {/* Header do Tópico */}
                                                            <button
                                                                type="button"
                                                                className="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border-gray-200 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3 transition-colors"
                                                                onClick={() => toggleTopic(topic.id)}
                                                            >
                                                                <div className="flex items-center space-x-3">
                                                                    <i className="fas fa-folder text-teal-500"></i>
                                                                    <span className="font-medium text-gray-900 dark:text-white">
                                                                        {topic.title}
                                                                    </span>
                                                                    <span className="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded-full">
                                                                        {fieldCount} campo(s)
                                                                    </span>
                                                                </div>
                                                                <svg 
                                                                    className={`w-3 h-3 shrink-0 transition-transform ${
                                                                        expandedTopics.has(topic.id) ? 'rotate-180' : ''
                                                                    }`}
                                                                    aria-hidden="true" 
                                                                    xmlns="http://www.w3.org/2000/svg" 
                                                                    fill="none" 
                                                                    viewBox="0 0 10 6"
                                                                >
                                                                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5 5 1 1 5"/>
                                                                </svg>
                                                            </button>

                                                            {/* Campos do Tópico */}
                                                            {expandedTopics.has(topic.id) && (
                                                                <div className="border-t border-gray-200 dark:border-gray-700 dark:bg-gray-900">
                                                                    <div className="overflow-x-auto">
                                                                        <table className="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                                            <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                                                <tr>
                                                                                    <th className="px-6 py-3">Chave</th>
                                                                                    <th className="px-6 py-3">Valor</th>
                                                                                    <th className="px-6 py-3">Tipo</th>
                                                                                    <th className="px-6 py-3">Ações</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                {allFields.map((field, index) => (
                                                                                    <tr key={`${field.id}-${index}`} className="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                                                        <td className="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                                                            {field.key_name || field.name || 'N/A'}
                                                                                        </td>
                                                                                        <td className="px-6 py-4">
                                                                                            <div className="text-gray-600 dark:text-gray-300 break-all max-w-md">
                                                                                                {field.value || 'N/A'}
                                                                                            </div>
                                                                                        </td>
                                                                                        <td className="px-6 py-4">
                                                                                            <div className="text-gray-600 dark:text-gray-300 break-all max-w-md">
                                                                                                {field.type || 'N/A'}
                                                                                            </div>
                                                                                        </td>
                                                                                        <td className="px-6 py-4">
                                                                                            <div className="flex space-x-2">
                                                                                                <button 
                                                                                                    onClick={() => copyToClipboard(field.value, 'Valor copiado!')}
                                                                                                    className="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                                                                                    title="Copiar valor"
                                                                                                >
                                                                                                    <i className="fas fa-copy"></i>
                                                                                                </button>
                                                                                                
                                                                                                <button 
                                                                                                    onClick={() => copyToClipboard(field.key_name || field.name, 'Chave copiada!')}
                                                                                                    className="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 transition-colors"
                                                                                                    title="Copiar chave"
                                                                                                >
                                                                                                    <i className="fas fa-key"></i>
                                                                                                </button>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                ))}
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            )}
                                                        </div>
                                                    );
                                                })
                                            ) : (
                                                <div className="p-8 text-center text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-lg">
                                                    <i className="fas fa-inbox text-4xl mb-4"></i>
                                                    <p className="text-lg">
                                                        {searchTerm ? 'Nenhum resultado encontrado' : 'Nenhum tópico encontrado'}
                                                    </p>
                                                </div>
                                            )}
                                        </div>
                                    ) : (
                                        <p className="p-4 text-red-500">Workspace não encontrado.</p>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Tab API Consult - CORRIGIDO */}
                        {activeTab === 'api-consult' && (
                            <div className="animate-fadeIn">
                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    {/* Coluna Esquerda - Endpoints */}
                                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">📡 Endpoints Disponíveis</h3>
                                        
                                        <div className="space-y-4">
                                            {/* Workspace Endpoints */}
                                            <div className="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                                <div className="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                                    <h4 className="font-medium text-gray-900 dark:text-white">Workspace</h4>
                                                </div>
                                                <div className="divide-y divide-gray-200 dark:divide-gray-700">
                                                    {/* Workspace Show */}
                                                    <div className="p-4">
                                                        <div className="flex items-start justify-between mb-2">
                                                            <div className="flex items-center space-x-2">
                                                                <span className="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                    GET
                                                                </span>
                                                                <span className="text-sm font-medium text-gray-900 dark:text-white">Obter workspace completo</span>
                                                            </div>
                                                            <button 
                                                                className="run-endpoint-btn inline-flex items-center px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors"
                                                                onClick={() => {
                                                                    const endpoint = `/api/shared/${global_key_api}/${workspace_key_api}`;
                                                                    handleRunEndpoint(endpoint, 'GET');
                                                                }}
                                                                disabled={isLoading}
                                                            >
                                                                {isLoading ? (
                                                                    <i className="fas fa-spinner fa-spin mr-1"></i>
                                                                ) : (
                                                                    <i className="fas fa-play mr-1"></i>
                                                                )}
                                                                {isLoading ? 'Running...' : 'Run'}
                                                            </button>
                                                        </div>
                                                        <div className="flex items-center justify-between">
                                                            <code className="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded flex-1 mr-2 truncate">
                                                                /api/shared/{global_key_api}/{workspace_key_api}
                                                            </code>
                                                            <button 
                                                                className="copy-url-btn text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                                                                onClick={() => copyToClipboard(`/api/shared/${global_key_api}/${workspace_key_api}`, 'URL copiada!')}
                                                            >
                                                                <i className="fas fa-copy"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Topics Endpoints */}
                                            {workspace?.topics?.[0]?.id && (
                                                <div className="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                                    <div className="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                                        <h4 className="font-medium text-gray-900 dark:text-white">Topics</h4>
                                                    </div>
                                                    <div className="divide-y divide-gray-200 dark:divide-gray-700">
                                                        {/* Show Topic */}
                                                        <div className="p-4">
                                                            <div className="flex items-start justify-between mb-2">
                                                                <div className="flex items-center space-x-2">
                                                                    <span className="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                        GET
                                                                    </span>
                                                                    <span className="text-sm font-medium text-gray-900 dark:text-white">Obter tópico específico</span>
                                                                </div>
                                                                <button 
                                                                    className="run-endpoint-btn inline-flex items-center px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors"
                                                                    onClick={() => handleRunEndpoint(`/api/topics/${workspace.topics[0].id}`, 'GET')}
                                                                    disabled={isLoading}
                                                                >
                                                                    {isLoading ? (
                                                                        <i className="fas fa-spinner fa-spin mr-1"></i>
                                                                    ) : (
                                                                        <i className="fas fa-play mr-1"></i>
                                                                    )}
                                                                    {isLoading ? 'Running...' : 'Run'}
                                                                </button>
                                                            </div>
                                                            <div className="flex items-center justify-between">
                                                                <code className="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded flex-1 mr-2 truncate">
                                                                    /api/topics/{workspace.topics[0].id}
                                                                </code>
                                                                <button 
                                                                    className="copy-url-btn text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                                                                    onClick={() => copyToClipboard(`/api/topics/${workspace.topics[0].id}`, 'URL copiada!')}
                                                                >
                                                                    <i className="fas fa-copy"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            )}

                                            {/* Fields Endpoints */}
                                            {workspace?.topics?.[0] && getFirstFieldFromTopic(workspace.topics[0]) && (
                                                <div className="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                                    <div className="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                                        <h4 className="font-medium text-gray-900 dark:text-white">Fields</h4>
                                                    </div>
                                                    <div className="divide-y divide-gray-200 dark:divide-gray-700">
                                                        {/* Show Field */}
                                                        <div className="p-4">
                                                            <div className="flex items-start justify-between mb-2">
                                                                <div className="flex items-center space-x-2">
                                                                    <span className="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                        GET
                                                                    </span>
                                                                    <span className="text-sm font-medium text-gray-900 dark:text-white">Obter field específico</span>
                                                                </div>
                                                                <button 
                                                                    className="run-endpoint-btn inline-flex items-center px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors"
                                                                    onClick={() => {
                                                                        const firstField = getFirstFieldFromTopic(workspace.topics[0]);
                                                                        handleRunEndpoint(`/api/fields/${firstField.id}`, 'GET');
                                                                    }}
                                                                    disabled={isLoading}
                                                                >
                                                                    {isLoading ? (
                                                                        <i className="fas fa-spinner fa-spin mr-1"></i>
                                                                    ) : (
                                                                        <i className="fas fa-play mr-1"></i>
                                                                    )}
                                                                    {isLoading ? 'Running...' : 'Run'}
                                                                </button>
                                                            </div>
                                                            <div className="flex items-center justify-between">
                                                                <code className="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded flex-1 mr-2 truncate">
                                                                    /api/fields/{getFirstFieldFromTopic(workspace.topics[0])?.id}
                                                                </code>
                                                                <button 
                                                                    className="copy-url-btn text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                                                                    onClick={() => {
                                                                        const firstField = getFirstFieldFromTopic(workspace.topics[0]);
                                                                        copyToClipboard(`/api/fields/${firstField.id}`, 'URL copiada!');
                                                                    }}
                                                                >
                                                                    <i className="fas fa-copy"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    {/* Coluna Direita - Resultados da Consulta */}
                                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                        <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 Resultado da Consulta</h3>
                                        
                                        <div className="space-y-4">
                                            {/* Informações da Requisição */}
                                            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                                <h4 className="font-medium text-gray-900 dark:text-white mb-2">Informações da Requisição</h4>
                                                <div className="space-y-2 text-sm">
                                                    <div className="flex justify-between">
                                                        <span className="text-gray-600 dark:text-gray-400">Método:</span>
                                                        <span className="font-mono text-gray-900 dark:text-white">{apiRequest.method}</span>
                                                    </div>
                                                    <div className="flex justify-between">
                                                        <span className="text-gray-600 dark:text-gray-400">URL:</span>
                                                        <span className="font-mono text-gray-900 dark:text-white truncate max-w-xs">{apiRequest.url}</span>
                                                    </div>
                                                    <div className="flex justify-between">
                                                        <span className="text-gray-600 dark:text-gray-400">Status:</span>
                                                        <span className="font-mono text-gray-900 dark:text-white">{apiRequest.status}</span>
                                                    </div>
                                                    <div className="flex justify-between">
                                                        <span className="text-gray-600 dark:text-gray-400">Tempo:</span>
                                                        <span className="font-mono text-gray-900 dark:text-white">{apiRequest.time}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Resposta da API */}
                                            <div>
                                                <div className="flex items-center justify-between mb-2">
                                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        Resposta da API
                                                    </label>
                                                    <button 
                                                        className={`inline-flex items-center px-3 py-1 text-sm bg-gray-600 hover:bg-gray-700 text-white rounded transition-colors ${
                                                            showCopyResponse ? 'opacity-100' : 'opacity-0'
                                                        }`}
                                                        onClick={() => copyToClipboard(apiResponse, 'Resposta copiada!')}
                                                    >
                                                        <i className="fas fa-copy mr-1"></i>Copiar
                                                    </button>
                                                </div>
                                                <div className="bg-gray-900 rounded-lg p-4 max-h-96 overflow-auto">
                                                    <pre className={`text-sm whitespace-pre-wrap ${
                                                        apiResponse.includes('Erro:') ? 'text-red-400' : 
                                                        apiResponse.includes('Loading') ? 'text-yellow-400' : 'text-green-400'
                                                    }`}>
                                                        {apiResponse}
                                                    </pre>
                                                </div>
                                            </div>

                                            {/* Dicas */}
                                            <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                                <h4 className="font-medium text-blue-900 dark:text-blue-100 mb-2 flex items-center">
                                                    <i className="fas fa-lightbulb mr-2"></i>Dicas
                                                </h4>
                                                <ul className="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                                                    <li>• Clique em "Run" para testar um endpoint</li>
                                                    <li>• Use o botão de cópia para copiar a URL do endpoint</li>
                                                    <li>• As respostas são formatadas em JSON</li>
                                                    <li>• Todos os endpoints requerem autenticação via token</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Tab Code Examples */}
                        {activeTab === 'code-examples' && (
                            <div className="animate-fadeIn">
                                <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        💻 Exemplos de Código
                                    </h3>
                                    
                                    {/* Language Tabs */}
                                    <div className="border-b border-gray-200 dark:border-gray-700 mb-4">
                                        <ul className="flex flex-wrap -mb-px text-sm font-medium text-center">
                                            {['javascript', 'python', 'curl', 'php'].map((lang) => (
                                                <li key={lang} className="mr-2">
                                                    <button 
                                                        className={`inline-block p-2 border-b-2 rounded-t-lg ${
                                                            activeLanguage === lang 
                                                                ? 'border-teal-600 text-teal-600' 
                                                                : 'border-transparent text-gray-500 hover:text-gray-600'
                                                        }`}
                                                        onClick={() => setActiveLanguage(lang)}
                                                    >
                                                        {lang === 'curl' ? 'cURL' : lang.charAt(0).toUpperCase() + lang.slice(1)}
                                                    </button>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>

                                    {/* Code Examples Content */}
                                    <div>
                                        {activeLanguage === 'javascript' && (
                                            <div className="bg-gray-800 rounded-lg p-4 animate-fadeIn">
                                                <pre className="text-green-400 text-sm overflow-x-auto">
                                                    <code>{`// Using Fetch API
const url = '${window.location.origin}/api/shared/${global_key_api}/${workspace_key_api}';

fetch(url)
.then(response => response.json())
.then(data => {
    console.log('Workspace data:', data);
    // Access topics: data.topics
    // Access fields: data.topics[0].fields
})
.catch(error => {
    console.error('Error:', error);
});

// Using async/await
async function fetchWorkspaceData() {
    try {
        const response = await fetch(url);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Fetch error:', error);
    }
}`}</code>
                                                </pre>
                                            </div>
                                        )}

                                        {activeLanguage === 'python' && (
                                            <div className="bg-gray-800 rounded-lg p-4 animate-fadeIn">
                                                <pre className="text-green-400 text-sm overflow-x-auto">
                                                    <code>{`import requests
import json

url = "${window.location.origin}/api/shared/${global_key_api}/${workspace_key_api}"

try:
    response = requests.get(url)
    response.raise_for_status()
    
    data = response.json()
    print("Workspace data retrieved successfully!")
    print(f"Workspace title: {data['workspace']['title']}")
    print(f"Total topics: {data['statistics']['total_topics']}")
    
    # Access topics and fields
    for topic in data['topics']:
        print(f"Topic: {topic['title']}")
        for field in topic['fields']:
            print(f"  {field['key']}: {field['value']}")
            
except requests.exceptions.RequestException as e:
    print(f"Error: {e}")`}</code>
                                                </pre>
                                            </div>
                                        )}

                                        {activeLanguage === 'curl' && (
                                            <div className="bg-gray-800 rounded-lg p-4 animate-fadeIn">
                                                <pre className="text-green-400 text-sm overflow-x-auto">
                                                    <code>{`# Basic GET request
curl -X GET "${window.location.origin}/api/shared/${global_key_api}/${workspace_key_api}"

# With pretty JSON output
curl -X GET "${window.location.origin}/api/shared/${global_key_api}/${workspace_key_api}" | jq '.'

# Save to file
curl -X GET "${window.location.origin}/api/shared/${global_key_api}/${workspace_key_api}" -o workspace_data.json

# With headers and verbose output
curl -X GET \\
"${window.location.origin}/api/shared/${global_key_api}/${workspace_key_api}" \\
-H "Accept: application/json" \\
-v`}</code>
                                                </pre>
                                            </div>
                                        )}

                                        {activeLanguage === 'php' && (
                                            <div className="bg-gray-800 rounded-lg p-4 animate-fadeIn">
                                                <pre className="text-green-400 text-sm overflow-x-auto">
                                                    <code>{`$url = "${window.location.origin}/api/shared/${global_key_api}/${workspace_key_api}";

// Using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "Workspace: " . $data['workspace']['title'] . "\\n";
    echo "Topics: " . count($data['topics']) . "\\n";
} else {
    echo "Error: HTTP " . $httpCode;
}

curl_close($ch);`}</code>
                                                </pre>
                                            </div>
                                        )}
                                    </div>

                                    {/* Copy All Button */}
                                    <div className="mt-4">
                                        <button 
                                            onClick={() => {
                                                const codes = {
                                                    javascript: `// Using Fetch API...`,
                                                    python: `import requests...`,
                                                    curl: `# Basic GET request...`,
                                                    php: `$url = "...";`
                                                };
                                                const allCode = Object.values(codes).join('\n\n// ' + '='.repeat(50) + '\n\n');
                                                copyToClipboard(allCode, 'Todos os exemplos copiados!');
                                            }}
                                            className="flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors"
                                        >
                                            <i className="fas fa-copy mr-2"></i>Copiar Código
                                        </button>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </main>
            </div>
        </DashboardLayout>
    );
};

// Adicione a função handleRunEndpoint que estava faltando
const handleRunEndpoint = async (endpoint, method) => {
    // Esta função já existe no seu código original
    // Mantenha a implementação que você já tem
};

export default GeevStudio;