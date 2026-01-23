// resources/js/Pages/ApiManagement/GeevApi.jsx
import React, { useState, useEffect } from 'react';
import { Head, usePage, router, useForm } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import ApiStatisticsTab from './ApiStatisticsTab';
import ApiEndpointsTab from './ApiEndpointsTab';
import ApiDocumentationTab from './ApiDocumentationTab';
import ApiPermissionsTab from './ApiPermissionsTab';
import ApiSettingsTab from './ApiSettingsTab';
import Alert from '@/Components/Alerts/Alert';
import ConfirmModal from '@/Components/Workspace/ApiManagement/GeevApi/Modals/ConfirmModal';
import JsonExampleModal from '@/Components/Workspace/ApiManagement/GeevApi/Modals/JsonExampleModal';
import DomainManagementModal from '@/Components/Workspace/ApiManagement/GeevApi/Modals/DomainManagementModal';
import ExportDocumentationModal from '@/Components/Workspace/ApiManagement/GeevApi/Modals/ExportDocumentationModal';
import RateLimitModal from '@/Components/Workspace/ApiManagement/GeevApi/Modals/RateLimitModal';

export default function GeevApi({ workspace, rateLimitData }) {
    const { flash } = usePage().props;
    const [activeTab, setActiveTab] = useState('statistics');
    const [showJsonModal, setShowJsonModal] = useState(false);
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const [showDomainModal, setShowDomainModal] = useState(false);
    const [showExportModal, setShowExportModal] = useState(false);
    const [showRateLimitModal, setShowRateLimitModal] = useState(false);
    const [jsonContent, setJsonContent] = useState('');
    const [modalTitle, setModalTitle] = useState('');
    const [modalConfig, setModalConfig] = useState({});

    const tabs = [
        { id: 'statistics', name: 'Estatísticas', icon: 'chart-bar' },
        { id: 'endpoints', name: 'Endpoints', icon: 'satellite-dish' },
        { id: 'documentation', name: 'Documentação', icon: 'book' },
        // { id: 'permissions', name: 'Permissões', icon: 'lock' },
        { id: 'settings', name: 'Configurações', icon: 'cog' }
    ];

    const showJsonExample = (jsonString, title = 'Exemplo de Resposta') => {
        try {
            const jsonObj = JSON.parse(jsonString);
            setJsonContent(JSON.stringify(jsonObj, null, 2));
        } catch (e) {
            setJsonContent(jsonString);
        }
        setModalTitle(title);
        setShowJsonModal(true);
    };

    const copyToClipboard = async (text) => {
        try {
            await navigator.clipboard.writeText(text);
            // Você pode adicionar um toast de sucesso aqui
        } catch (err) {
            console.error('Erro ao copiar:', err);
        }
    };

    const toggleApiStatus = (enabled) => {
        setModalConfig({
            title: enabled ? 'Ativar API' : 'Desativar API',
            message: enabled 
                ? 'Tem certeza que deseja ativar o acesso à API? Isso permitirá requisições externas.'
                : 'Tem certeza que deseja desativar o acesso à API? Isso bloqueará todas as requisições externas.',
            action: () => {
                router.put(`/access/api/${workspace.id}/toggle`, { enabled: enabled });
                setShowConfirmModal(false);
            },
            type: enabled ? 'success' : 'warning'
        });
        setShowConfirmModal(true);
    };

    const handleBack = () => {
        // Usar window.history para voltar ou redirecionar para uma rota específica
        if (window.history.length > 1) {
            window.history.back();
        } else {
            // Se não houver histórico, redirecionar para a página de workspaces
            router.visit('/workspaces');
        }
    };

    const renderTabContent = () => {
        switch (activeTab) {
            case 'statistics':
                return <ApiStatisticsTab workspace={workspace} />;
            case 'endpoints':
                return <ApiEndpointsTab 
                    workspace={workspace} 
                    showJsonExample={showJsonExample} 
                    copyToClipboard={copyToClipboard}
                />;
            case 'documentation':
                return <ApiDocumentationTab 
                    workspace={workspace} 
                    onExportClick={() => setShowExportModal(true)}
                />;
            case 'permissions':
                return <ApiPermissionsTab workspace={workspace} />;
            case 'settings':
                return <ApiSettingsTab 
                    workspace={workspace} 
                    setShowConfirmModal={setShowConfirmModal}
                    onDomainManagement={() => setShowDomainModal(true)}
                    onRateLimitClick={() => setShowRateLimitModal(true)}
                    toggleApiStatus={toggleApiStatus}
                />;
            default:
                return <ApiStatisticsTab workspace={workspace} />;
        }
    };

    return (
        <>
            <DashboardLayout>
                <Head title={`API ${workspace.title}`} />

                <div className="min-h-screen">
                    <div className="max-w-7xl mx-auto p-0 sm:p-0 md:p-6">
                        
                        {/* Header */}
                        <div className="flex justify-between items-center mb-8">
                            <div>
                                <h1 className="text-3xl font-bold text-white">
                                    Geev API - {workspace.title}
                                </h1>
                                <p className="text-slate-400 mt-2">
                                    Gerencie e integre seus dados através de API
                                </p>
                            </div>
                            <div className="flex items-center space-x-4">
                                {/* Botão para abrir modal de Rate Limit */}
                                <button 
                                    onClick={() => setShowRateLimitModal(true)}
                                    className="flex items-center text-cyan-400 hover:text-cyan-300 transition-colors text-sm cursor-pointer"
                                >
                                    <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Limites de Uso
                                </button>
                                <button 
                                    onClick={handleBack}
                                    className="flex items-center text-cyan-400 hover:text-cyan-300 transition-colors cursor-pointer"
                                >
                                    <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Voltar
                                </button>
                            </div>
                        </div>

                        {/* Alert */}
                        <Alert />

                        {/* API Status Cards */}
                        <div className="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                            {/* Status Card */}
                            <div className="bg-slate-800 rounded-xl p-6 border border-cyan-500/20">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <h3 className="text-lg font-semibold text-white">Status da API</h3>
                                        {workspace.api_enabled ? (
                                            <p className="text-green-400 flex items-center mt-1">
                                                <span className="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                                API Ativa
                                            </p>
                                        ) : (
                                            <p className="text-red-400 flex items-center mt-1">
                                                <span className="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                                API Desativada
                                            </p>
                                        )}
                                    </div>
                                    <div className="p-3 bg-cyan-500/10 rounded-lg">
                                        <svg className="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {/* Base URL Card */}
                            <div className="bg-slate-800 rounded-xl p-6 border border-cyan-500/20">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <h3 className="text-lg font-semibold text-white">Base URL</h3>
                                        <p className="text-slate-300 font-mono text-sm mt-1">
                                            {window.location.origin}/api/v1
                                        </p>
                                    </div>
                                    <button 
                                        onClick={() => copyToClipboard(`${window.location.origin}/api`)}
                                        className="p-2 text-cyan-400 hover:text-cyan-300 transition-colors cursor-pointer"
                                    >
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {/* Workspace Key Card */}
                            <div className={`bg-slate-800 rounded-xl p-6 border ${workspace.api_jwt_required ? 'border-amber-500/20' : 'border-cyan-500/20'}`}>
                                <div className="flex items-center justify-between">
                                    <div>
                                        <div className="flex items-center space-x-2 mb-1">
                                            <h3 className="text-lg font-semibold text-white">
                                                {workspace.api_jwt_required ? 'JWT Authentication' : 'Workspace Key'}
                                            </h3>
                                        </div>
                                        
                                        {workspace.api_jwt_required ? (
                                            <p className="text-slate-300 text-sm">Use seu login para gerar tokens JWT</p>
                                        ) : (
                                            <p className="text-slate-300 font-mono text-sm mt-1">
                                                {workspace.workspace_key_api 
                                                    ? `••••••••${workspace.workspace_key_api.slice(-8)}`
                                                    : 'Não gerada'
                                                }
                                            </p>
                                        )}
                                    </div>
                                    
                                    <div className="flex space-x-2">
                                        {!workspace.api_jwt_required && (
                                            <>
                                                {workspace.workspace_key_api ? (
                                                    <button 
                                                        onClick={() => {
                                                            setModalConfig({
                                                                title: 'Regenerar Workspace Key',
                                                                message: 'Tem certeza? Isso invalidará a chave atual e todas as aplicações usando esta chave precisarão ser atualizadas.',
                                                                action: () => {
                                                                    router.post(`/api/${workspace.id}/generate-api-key`);
                                                                    setShowConfirmModal(false);
                                                                },
                                                                type: 'warning'
                                                            });
                                                            setShowConfirmModal(true);
                                                        }}
                                                        className="p-2 text-yellow-400 hover:text-yellow-300"
                                                        title="Regenerar Key"
                                                    >
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                    </button>
                                                ) : (
                                                    <button 
                                                        onClick={() => {
                                                            setModalConfig({
                                                                title: 'Gerar Workspace Key',
                                                                message: 'Deseja gerar uma nova Workspace Key? Esta chave será usada para autenticar todas as requisições à API.',
                                                                action: () => {
                                                                    router.post(`/api/v1/${workspace.id}/generate-api-key`);
                                                                    setShowConfirmModal(false);
                                                                },
                                                                type: 'info'
                                                            });
                                                            setShowConfirmModal(true);
                                                        }}
                                                        className="p-2 text-green-400 hover:text-green-300"
                                                        title="Gerar Key"
                                                    >
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                        </svg>
                                                    </button>
                                                )}
                                                <button 
                                                    onClick={() => copyToClipboard(workspace.workspace_key_api)}
                                                    className={`p-2 ${workspace.api_jwt_required ? 'text-amber-400 hover:text-amber-300' : 'text-cyan-400 hover:text-cyan-300'}`}
                                                    title={workspace.api_jwt_required ? 'Copiar Workspace Hash' : 'Copiar Workspace Key'}
                                                >
                                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                </button>
                                            </>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Tabs Navigation */}
                        <div className="mb-8 border-b border-slate-700">
                            <ul className="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
                                {tabs.map((tab) => (
                                    <li key={tab.id} className="mr-2" role="presentation">
                                        <button
                                            className={`inline-block cursor-pointer p-4 border-b-2 rounded-t-lg flex items-center transition-colors ${
                                                activeTab === tab.id
                                                    ? 'text-cyan-400 border-cyan-400'
                                                    : 'text-slate-400 border-transparent hover:text-slate-300 hover:border-slate-300'
                                            }`}
                                            onClick={() => setActiveTab(tab.id)}
                                            type="button"
                                            role="tab"
                                        >
                                            <i className={`fas fa-${tab.icon} mr-2`}></i>
                                            {tab.name}
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        </div>

                        {/* Tab Content */}
                        <div>
                            {renderTabContent()}
                        </div>
                    </div>
                </div>

            </DashboardLayout>
            
            {/* Modals */}
            <ConfirmModal
                show={showConfirmModal}
                onClose={() => setShowConfirmModal(false)}
                onConfirm={modalConfig.action}
                title={modalConfig.title}
                message={modalConfig.message}
                type={modalConfig.type}
                confirmText="Confirmar"
                cancelText="Cancelar"
            />

            <JsonExampleModal
                show={showJsonModal}
                onClose={() => setShowJsonModal(false)}
                title={modalTitle}
                jsonContent={jsonContent}
                onCopy={() => {
                    // Mostrar toast de sucesso
                    // console.log('JSON copiado!');
                }}
            />

            <DomainManagementModal
                show={showDomainModal}
                onClose={() => setShowDomainModal(false)}
                workspace={workspace}
                domains={workspace.allowed_domains || []}
                onDomainAdded={() => {
                    router.reload();
                }}
                onDomainRemoved={(domainId) => {
                    // Implementar remoção via API
                    // console.log('Remover domínio:', domainId);
                }}
            />

            <ExportDocumentationModal
                show={showExportModal}
                onClose={() => setShowExportModal(false)}
                workspace={workspace}
                onExport={(config) => {
                    // console.log('Exportar com configuração:', config);
                    // Implementar lógica de exportação
                }}
            />

            <RateLimitModal
                show={showRateLimitModal}
                onClose={() => setShowRateLimitModal(false)}
                rateLimitData={rateLimitData || {}}
            />
        </>
    );
}