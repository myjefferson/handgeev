import React, { useState, useEffect } from 'react';
import { Head, Link, usePage, router } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import Alert from '@/Components/Alerts/Alert';

export default function MyApis({ workspaces }) {
    const { auth } = usePage().props;
    const [searchTerm, setSearchTerm] = useState('');
    const [statusFilter, setStatusFilter] = useState('all');
    const [filteredWorkspaces, setFilteredWorkspaces] = useState(workspaces);
    const [isLoading, setIsLoading] = useState(false); // Estado para controlar o loading
    const [currentTogglingId, setCurrentTogglingId] = useState(null); // ID do workspace sendo modificado

    // Estatísticas
    const totalApis = workspaces.length;
    const activeApis = workspaces.filter(w => w.is_published).length;
    const visibleFields = workspaces.reduce((sum, w) => sum + (w.visible_fields_count || 0), 0);

    // Filtragem
    useEffect(() => {
        const filtered = workspaces.filter(workspace => {
            const matchesSearch = workspace.title.toLowerCase().includes(searchTerm.toLowerCase());
            const matchesStatus = statusFilter === 'all' || 
                (statusFilter === 'active' && workspace.api_enabled) ||
                (statusFilter === 'inactive' && !workspace.api_enabled);
            
            return matchesSearch && matchesStatus;
        });
        setFilteredWorkspaces(filtered);
    }, [searchTerm, statusFilter, workspaces]);

    const copyApiUrl = (url) => {
        navigator.clipboard.writeText(url).then(() => {
            alert('URL copiada para a área de transferência!');
        }).catch(() => {
            alert('Erro ao copiar URL');
        });
    };

    const toggleApiStatus = (url, newStatus, workspaceId) => {
        if (!confirm(`Tem certeza que deseja ${newStatus ? 'ativar' : 'inativar'} esta API?`)) {
            return;
        }

        // Desabilita todos os toggles
        setIsLoading(true);
        setCurrentTogglingId(workspaceId);

        router.put(url, { api_enabled: newStatus }, {
            preserveScroll: true,
            onSuccess: () => {
                // Habilita os toggles novamente
                setIsLoading(false);
                setCurrentTogglingId(null);
            },
            onError: (errors) => {
                console.error(errors);
                alert("Erro ao atualizar status da API");
                // Habilita os toggles mesmo em caso de erro
                setIsLoading(false);
                setCurrentTogglingId(null);
            },
            onFinish: () => {
                // Garantir que os toggles sejam reativados ao final da operação
                setIsLoading(false);
                setCurrentTogglingId(null);
            }
        });
    };

    return (
        <DashboardLayout>
            <Head 
                title="Minhas APIs" 
                description={`Gerencie e visualize suas APIs no HandGeev`} 
            />

            <div className="min-h-screen max-w-7xl mx-auto">
                {/* Header */}
                <div className="p-0 sm:p-0 md:p-6">
                    <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 py-6">
                        <div className="w-full sm:w-auto">
                            <h1 className="text-xl sm:text-2xl font-bold text-white">Minhas APIs</h1>
                            <p className="text-slate-400 mt-1 text-sm sm:text-base">
                                Gerencie e visualize suas APIs ativas
                            </p>
                        </div>
                    </div>

                    {/* Stats Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                            <div className="flex items-center">
                                <div className="p-2 bg-blue-500/20 rounded-lg mr-4">
                                    <i className="fas fa-plug text-blue-400 text-lg"></i>
                                </div>
                                <div>
                                    <p className="text-sm text-slate-400">Total de APIs</p>
                                    <p className="text-2xl font-bold text-white">{totalApis}</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                            <div className="flex items-center">
                                <div className="p-2 bg-green-500/20 rounded-lg mr-4">
                                    <i className="fas fa-check-circle text-green-400 text-lg"></i>
                                </div>
                                <div>
                                    <p className="text-sm text-slate-400">APIs Ativas</p>
                                    <p className="text-2xl font-bold text-white">{activeApis}</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                            <div className="flex items-center">
                                <div className="p-2 bg-amber-500/20 rounded-lg mr-4">
                                    <i className="fas fa-code text-amber-400 text-lg"></i>
                                </div>
                                <div>
                                    <p className="text-sm text-slate-400">Campos Visíveis</p>
                                    <p className="text-2xl font-bold text-white">{visibleFields}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Barra de Pesquisa e Filtros */}
                    <div className="bg-slate-800/50 rounded-xl border border-slate-700 p-6 mb-6">
                        <div className="flex flex-col justify-center lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div className="flex flex-col sm:flex-row gap-4 flex-1 items-center justify-between">
                                {/* Barra de Pesquisa */}
                                <div className="relative flex-1 w-full max-w-md">
                                    <input
                                        type="text"
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                        className="w-full bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 pl-10 pr-4 py-2"
                                        placeholder="Buscar APIs por nome..."
                                    />
                                    <i className="fas fa-search absolute left-3 top-3 text-slate-400"></i>
                                </div>
                                {/* Filtros Rápidos */}
                                <div className="flex items-center gap-3">
                                    <div className="flex bg-slate-800 rounded-full p-1">
                                        <button
                                            type="button"
                                            className={`px-3 py-1 text-sm rounded-full ${
                                                statusFilter === 'all' 
                                                    ? 'bg-teal-600 text-white' 
                                                    : 'text-slate-400 hover:text-white'
                                            }`}
                                            onClick={() => setStatusFilter('all')}
                                        >
                                            Todas
                                        </button>
                                        <button
                                            type="button"
                                            className={`px-3 py-1 text-sm rounded-full ${
                                                statusFilter === 'active' 
                                                    ? 'bg-teal-600 text-white' 
                                                    : 'text-slate-400 hover:text-white'
                                            }`}
                                            onClick={() => setStatusFilter('active')}
                                        >
                                            Ativas
                                        </button>
                                        <button
                                            type="button"
                                            className={`px-3 py-1 text-sm rounded-full ${
                                                statusFilter === 'inactive' 
                                                    ? 'bg-teal-600 text-white' 
                                                    : 'text-slate-400 hover:text-white'
                                            }`}
                                            onClick={() => setStatusFilter('inactive')}
                                        >
                                            Inativas
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Alert/>

                    {/* Grid de APIs */}
                    <div id="apis-content">
                        {filteredWorkspaces.length > 0 ? (
                            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                                {filteredWorkspaces.map((workspace) => (
                                    <div
                                        key={workspace.id}
                                        className="api-card bg-slate-800 rounded-xl border border-slate-700 p-6 hover:border-teal-500/50 transition-all duration-300"
                                        data-status={workspace.api_enabled ? 'active' : 'inactive'}
                                        data-type={workspace.api_type?.toLowerCase().replace(' ', '-')}
                                    >
                                        {/* Header com Nome e Status */}
                                        <div className="flex justify-between items-center mb-6">
                                            <h3 className="text-lg font-semibold text-white truncate flex-1 pr-4">
                                                {workspace.title}
                                            </h3>
                                            
                                            {/* Status Badge */}
                                            <span
                                                className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border ${
                                                    workspace.api_enabled
                                                        ? 'bg-green-500/20 text-green-400 border-green-500/30'
                                                        : 'bg-red-500/20 text-red-400 border-red-500/30'
                                                }`}
                                            >
                                                <span
                                                    className={`w-2 h-2 rounded-full mr-2 ${
                                                        workspace.api_enabled ? 'bg-green-400' : 'bg-red-400'
                                                    }`}
                                                ></span>
                                                {workspace.api_enabled ? 'Ativa' : 'Inativa'}
                                            </span>
                                        </div>

                                        {/* Estatísticas Principais */}
                                        <div className="space-y-4 mb-6">
                                            {/* Total de Consultas */}
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center text-slate-400">
                                                    <i className="fas fa-chart-bar mr-3 text-sm"></i>
                                                    <span className="text-sm">Total de Consultas</span>
                                                </div>
                                                <span className="text-white font-semibold text-sm">
                                                    {workspace.api_requests_count || '0'}
                                                </span>
                                            </div>

                                            {/* Endpoint */}
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center text-slate-400">
                                                    <i className="fas fa-link mr-3 text-sm"></i>
                                                    <span className="text-sm">Endpoint</span>
                                                </div>
                                                <button
                                                    onClick={() =>
                                                        copyApiUrl(
                                                            route('workspace.shared.api', {
                                                                global_key_api: auth.user.global_key_api,
                                                                workspace_key_api: workspace.workspace_key_api,
                                                            })
                                                        )
                                                    }
                                                    className="text-cyan-400 hover:text-cyan-300 flex items-center text-sm"
                                                    title="Copiar URL da API"
                                                    disabled={isLoading} // Desabilita botão durante loading
                                                >
                                                    <i className="fas fa-copy mr-1"></i>
                                                    Copiar
                                                </button>
                                            </div>

                                            {/* Tipo de Visualização */}
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center text-slate-400">
                                                    <i
                                                        className={`fas ${
                                                            workspace.type_view_workspace_id == 1
                                                                ? 'fa-eye'
                                                                : 'fa-code'
                                                        } mr-3 text-sm`}
                                                    ></i>
                                                    <span className="text-sm">Tipo</span>
                                                </div>
                                                <span className="text-white font-semibold text-sm">
                                                    {workspace.type_view_workspace_id == 1
                                                        ? 'Geev Studio'
                                                        : 'Geev API'}
                                                </span>
                                            </div>
                                        </div>

                                        {/* Ações */}
                                        <div className="flex justify-between items-center pt-4 border-t border-slate-700">
                                            {/* Botão Gerenciar */}
                                            <Link
                                                href={
                                                    workspace.type_view_workspace_id == 1
                                                        ? route('workspace.shared-geev-studio.show', {
                                                              global_key_api: auth.user.global_key_api,
                                                              workspace_key_api: workspace.workspace_key_api,
                                                          })
                                                        : route('workspace.api-rest.show', {
                                                              global_key_api: auth.user.global_key_api,
                                                              workspace_key_api: workspace.workspace_key_api,
                                                          })
                                                }
                                                className="text-slate-300 hover:text-white text-sm flex items-center bg-slate-700 hover:bg-slate-600 px-3 py-2 rounded-lg transition-colors"
                                                disabled={isLoading} // Desabilita link durante loading
                                            >
                                                <i className="fas fa-cog mr-2"></i>
                                                Gerenciar{' '}
                                                {workspace.type_view_workspace_id == 1 ? 'Geev Studio' : 'Geev API'}
                                            </Link>
                                            
                                            {/* Botão Ativar/Inativar */}
                                            <button
                                                onClick={() =>
                                                    toggleApiStatus(
                                                        route('management.api.access.toggle', workspace.id),
                                                        !workspace.api_enabled,
                                                        workspace.id
                                                    )
                                                }
                                                disabled={isLoading} // Desabilita todos os toggles durante loading
                                                className={`relative inline-flex items-center h-6 rounded-full w-11 transition-colors ${
                                                    workspace.api_enabled ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600'
                                                } ${isLoading ? 'opacity-50 cursor-not-allowed' : ''}`}
                                                aria-label={workspace.api_enabled ? 'Desativar API' : 'Ativar API'}
                                            >
                                                <span
                                                    className={`inline-block w-4 h-4 transform bg-white rounded-full transition ${
                                                        workspace.api_enabled ? 'translate-x-6' : 'translate-x-1'
                                                    } ${currentTogglingId === workspace.id ? 'opacity-50' : ''}`}
                                                />
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            /* Estado Vazio */
                            <div className="text-center py-16 bg-slate-800/50 rounded-xl border border-slate-700">
                                <div className="text-slate-400 text-6xl mb-4">
                                    <i className="fas fa-plug"></i>
                                </div>
                                <h3 className="text-xl font-semibold text-white mb-2">Nenhuma API encontrada</h3>
                                <p className="text-slate-400 mb-6 max-w-md mx-auto">
                                    Você ainda não possui APIs configuradas. Crie um workspace e ative a API para começar.
                                </p>
                                <div className="flex flex-col sm:flex-row gap-3 justify-center">
                                    <Link
                                        href={route('workspaces.show')}
                                        className="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded-lg transition-colors flex items-center justify-center"
                                        disabled={isLoading} // Desabilita durante loading
                                    >
                                        <i className="fas fa-plus mr-2"></i>
                                        Criar Workspace
                                    </Link>
                                    <Link
                                        href={route('dashboard.home')}
                                        className="bg-slate-700 hover:bg-slate-600 text-white px-6 py-2 rounded-lg transition-colors flex items-center justify-center"
                                        disabled={isLoading} // Desabilita durante loading
                                    >
                                        <i className="fas fa-home mr-2"></i>
                                        Voltar ao Dashboard
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            <style>{`
                .line-clamp-2 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }

                .api-card {
                    transition: all 0.3s ease;
                }

                .api-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
                }

                /* Garantir que os ícones sejam exibidos corretamente */
                .fas, .fa {
                    font-family: 'Font Awesome 6 Free';
                    font-weight: 900;
                }

                /* Estilo para elementos desabilitados */
                a[disabled], button[disabled] {
                    pointer-events: none;
                    opacity: 0.6;
                    cursor: not-allowed;
                }
            `}</style>
        </DashboardLayout>
    );
}