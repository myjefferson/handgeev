import React, { useState, useEffect, useRef } from 'react';
import { Head, usePage, useForm, Link, router } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import Alert from '@/Components/Alerts/Alert';
import ModalCreateWorkspace from '@/Components/Modals/CreateWorkspaceModal';
import MyWorkspaceList from '@/Components/Lists/MyWorkspaceList';
import MyWorkspaceCard from '@/Components/Cards/MyWorkspaceCard';
import EmptyState from '@/Components/State/MyWorkspaceEmptyState';
import UpgradeButton from '@/Components/Buttons/UpgradeButton';

export default function MyWorkspaces({ workspaces, collaborations }){
    const { auth, flash } = usePage().props;

    // Estados
    const [currentView, setCurrentView] = useState('list');
    const [searchTerm, setSearchTerm] = useState('');
    const [filters, setFilters] = useState({
        status: 'all',
        sort: 'newest'
    });
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [filteredWorkspaces, setFilteredWorkspaces] = useState([]);
    const [hasResults, setHasResults] = useState(true);

    // Refs para menus dropdown
    const menuRefs = useRef({});

    // Estatísticas
    const stats = {
        totalWorkspaces: workspaces?.length || 0,
        totalTopics: workspaces?.reduce((sum, ws) => sum + (ws.topics_count || 0), 0) || 0,
        activeApiWorkspaces: workspaces?.filter(ws => ws.api_enabled)?.length || 0
    };

    // Filtros e busca
    useEffect(() => {
        applyFilters();
    }, [searchTerm, filters, currentView, workspaces]);

    const applyFilters = () => {
        if (!workspaces || workspaces.length === 0) {
            setFilteredWorkspaces([]);
            setHasResults(false);
            return;
        }

        let filtered = [...workspaces];

        // Aplicar filtro de busca
        if (searchTerm) {
            const term = searchTerm.toLowerCase();
            filtered = filtered.filter(workspace => 
                workspace.title?.toLowerCase().includes(term) ||
                workspace.description?.toLowerCase().includes(term)
            );
        }

        // Aplicar filtro de status
        if (filters.status !== 'all') {
            filtered = filtered.filter(workspace => {
                switch (filters.status) {
                    case 'active':
                        return workspace.api_enabled;
                    case 'inactive':
                        return !workspace.api_enabled;
                    case 'public':
                        return workspace.is_published;
                    case 'private':
                        return !workspace.is_published;
                    default:
                        return true;
                }
            });
        }

        // Aplicar ordenação
        filtered.sort((a, b) => {
            switch (filters.sort) {
                case 'newest':
                    return new Date(b.created_at) - new Date(a.created_at);
                case 'oldest':
                    return new Date(a.created_at) - new Date(b.created_at);
                case 'name_asc':
                    return (a.title || '').localeCompare(b.title || '');
                case 'name_desc':
                    return (b.title || '').localeCompare(a.title || '');
                default:
                    return new Date(b.created_at) - new Date(a.created_at);
            }
        });

        setFilteredWorkspaces(filtered);
        setHasResults(filtered.length > 0);
    };

    const resetFilters = () => {
        setSearchTerm('');
        setFilters({
            status: 'all',
            sort: 'newest'
        });
    };

    // Funções de utilidade
    const canCreateWorkspace = () => {
        return auth.user?.can_create_workspace || false;
    };

    // Funções para dropdown menus
    const toggleWorkspaceMenu = (workspaceId) => {
        const menuId = `menu-${workspaceId}`;
        
        // Fechar todos os outros menus
        Object.keys(menuRefs.current).forEach(key => {
            if (key !== menuId && menuRefs.current[key]) {
                menuRefs.current[key].classList.add('hidden');
            }
        });

        // Alternar o menu atual
        if (menuRefs.current[menuId]) {
            menuRefs.current[menuId].classList.toggle('hidden');
        }
    };

    // Fechar menus ao clicar fora
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (!event.target.closest('[id^="menu-"]') && 
                !event.target.closest('button[onclick*="toggleWorkspaceMenu"]')) {
                Object.keys(menuRefs.current).forEach(key => {
                    if (menuRefs.current[key]) {
                        menuRefs.current[key].classList.add('hidden');
                    }
                });
            }
        };

        document.addEventListener('click', handleClickOutside);
        return () => document.removeEventListener('click', handleClickOutside);
    }, []);

    return (
        <>
            <DashboardLayout>
                <Head 
                    title="Meus Workspaces" 
                    description={`Workspaces de ${auth.user?.name} no HandGeev`} 
                />

                <div className="min-h-screen max-w-7xl mx-auto">
                    <div className="p-0 sm:p-0 md:p-6">
                        {/* Header */}
                        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 py-6">
                            <div className="w-full sm:w-auto">
                                <h1 className="text-xl sm:text-2xl font-bold text-white">Meus Workspaces</h1>
                                <p className="text-slate-400 mt-1 text-sm sm:text-base">
                                    Gerencie seus workspaces
                                </p>
                            </div>
                            <div className="flex flex-col md:flex-row items-stretch md:items-center gap-3 w-full md:w-max">
                                {/* Botão Novo Workspace */}
                                {auth.user.plan?.can_create_workspace ? (
                                    <button 
                                        onClick={() => setShowCreateModal(true)}
                                        className="flex items-center justify-center px-4 py-2 text-white rounded-lg bg-teal-500 hover:bg-teal-700 transition-colors teal-glow-hover w-full md:w-auto"
                                    >
                                        <i className="fas fa-plus mr-2"></i>
                                        <span>Novo Workspace</span>
                                    </button>
                                ) : (
                                    <UpgradeButton 
                                        title="Workspaces? Upgrade to"
                                        iconPrincipal={false}
                                        iconLeft={<i className="fas fa-plus mx-2"></i>}
                                    />
                                )}

                                {/* Botão Importar */}
                                {['pro', 'start', 'premium', 'admin'].includes(auth.user.plan?.name) ? (
                                    <Link 
                                        href={route('workspace.import.form')}
                                        className="flex items-center justify-center px-4 py-2 text-white rounded-lg bg-slate-600 hover:bg-slate-700 transition-colors purple-glow-hover w-full md:w-auto"
                                    >
                                        <i className="fas fa-upload mr-2"></i>
                                        <span>Importar</span>
                                    </Link>
                                ) : (
                                    <UpgradeButton 
                                        title="Import" 
                                        iconPrincipal={false}
                                        iconLeft={<i className="fas fa-upload mx-2"></i>}
                                    />
                                )}
                            </div>
                        </div>

                        <Alert/>

                        {/* Stats Cards */}
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                                <div className="flex items-center">
                                    <div className="p-2 bg-blue-500/20 rounded-lg mr-4">
                                        <svg className="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p className="text-sm text-slate-400">Meus Workspaces</p>
                                        <p className="text-2xl font-bold text-white">{stats.totalWorkspaces}</p>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                                <div className="flex items-center">
                                    <div className="p-2 bg-purple-500/20 rounded-lg mr-4">
                                        <svg className="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p className="text-sm text-slate-400">Total de Tópicos</p>
                                        <p className="text-2xl font-bold text-white">{stats.totalTopics}</p>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                                <div className="flex items-center">
                                    <div className="p-2 bg-amber-500/20 rounded-lg mr-4">
                                        <svg className="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p className="text-sm text-slate-400">Workspaces API Ativos</p>
                                        <p className="text-2xl font-bold text-white">{stats.activeApiWorkspaces}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Barra de Controles */}
                        <div className="bg-slate-800/50 rounded-xl border border-slate-700 p-6 mb-6">
                            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div className="flex flex-col sm:flex-row gap-4 flex-1">
                                    {/* Barra de Pesquisa */}
                                    <div className="relative flex-1 max-w-md">
                                        <input 
                                            type="text" 
                                            value={searchTerm}
                                            onChange={(e) => setSearchTerm(e.target.value)}
                                            className="w-full bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 pl-10 pr-4 py-2" 
                                            placeholder="Buscar por título ou descrição..."
                                        />
                                        <svg className="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>

                                    {/* Filtros */}
                                    <div className="flex flex-col sm:flex-initial md:flex-row gap-2">
                                        <div className="grid sm:grid md:block space-x-2 grid-cols-2">
                                            <select 
                                                value={filters.status}
                                                onChange={(e) => setFilters(prev => ({ ...prev, status: e.target.value }))}
                                                className="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 px-3 py-2"
                                            >
                                                <option value="all">Todos</option>
                                                <option value="active">API Ativa</option>
                                                <option value="inactive">API Inativa</option>
                                                <option value="public">Públicos</option>
                                                <option value="private">Privados</option>
                                            </select>

                                            <select 
                                                value={filters.sort}
                                                onChange={(e) => setFilters(prev => ({ ...prev, sort: e.target.value }))}
                                                className="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 px-3 py-2"
                                            >
                                                <option value="newest">Mais Recentes</option>
                                                <option value="oldest">Mais Antigos</option>
                                                <option value="name_asc">A-Z</option>
                                                <option value="name_desc">Z-A</option>
                                            </select>
                                        </div>

                                        <button 
                                            onClick={resetFilters}
                                            className="px-3 py-2 text-sm text-slate-400 hover:text-white border border-slate-600 rounded-lg hover:bg-slate-700 transition-colors"
                                        >
                                            Limpar
                                        </button>
                                    </div>

                                    {/* Botões de Visualização */}
                                    <div className="flex border border-slate-600 rounded-lg overflow-hidden">
                                        <button 
                                            onClick={() => setCurrentView('list')}
                                            className={`p-2 border-r border-slate-600 view-toggle ${
                                                currentView === 'list' 
                                                    ? 'bg-cyan-600 text-white' 
                                                    : 'text-slate-400 hover:text-white hover:bg-slate-700'
                                            }`}
                                        >
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                            </svg>
                                        </button>
                                        <button 
                                            onClick={() => setCurrentView('grid')}
                                            className={`p-2 view-toggle ${
                                                currentView === 'grid' 
                                                    ? 'bg-cyan-600 text-white' 
                                                    : 'text-slate-400 hover:text-white hover:bg-slate-700'
                                            }`}
                                        >
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Conteúdo Principal */}
                        <div id="workspaces-content">
                            {/* Estado Original */}
                            <div id="original-content" className={!hasResults ? 'hidden' : ''}>
                                {/* Visualização Lista */}
                                <div id="list-view" className={currentView === 'list' ? 'view-type fade-in' : 'view-type hidden'}>
                                    <MyWorkspaceList 
                                        workspaces={filteredWorkspaces} 
                                        type="owner"
                                        onToggleMenu={toggleWorkspaceMenu}
                                        menuRefs={menuRefs}
                                    />
                                </div>

                                {/* Visualização Grade */}
                                <div id="grid-view" className={currentView === 'grid' ? 'view-type fade-in' : 'view-type hidden'}>
                                    {hasResults ? (
                                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            {filteredWorkspaces.map(workspace => (
                                                <MyWorkspaceCard 
                                                    key={workspace.id}
                                                    workspace={workspace}
                                                    type="owner"
                                                    onToggleMenu={toggleWorkspaceMenu}
                                                    menuRefs={menuRefs}
                                                />
                                            ))}
                                        </div>
                                    ) : (
                                        <EmptyState 
                                            type="my-workspaces"
                                            icon="📁"
                                            title="Nenhum workspace encontrado"
                                            description="Comece criando seu primeiro workspace para organizar seus dados."
                                            showButton={true}
                                        />
                                    )}
                                </div>
                            </div>

                            {/* Estado de Filtro Vazio */}
                            <div id="empty-filter-state" className={hasResults ? 'hidden' : 'fade-in'}>
                                <div className="text-center py-12 bg-slate-800/50 rounded-xl border border-slate-700">
                                    <div className="text-slate-400 text-6xl mb-4">🔍</div>
                                    <h3 className="text-lg font-semibold text-white mb-2">
                                        Nenhum workspace encontrado
                                    </h3>
                                    <p className="text-slate-400 mb-6">
                                        Tente ajustar seus filtros de busca.
                                    </p>
                                    <button 
                                        onClick={resetFilters}
                                        className="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg transition-colors"
                                    >
                                        Limpar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>{`
                    .view-toggle.active {
                        background-color: #0891b2;
                        color: white;
                    }

                    .workspace-item {
                        transition: all 0.2s ease-in-out;
                    }

                    .workspace-item:hover {
                        background-color: rgba(30, 41, 59, 0.5);
                        border-color: #06b6d4;
                    }

                    .fade-in {
                        animation: fadeIn 0.3s ease-in-out;
                    }

                    @keyframes fadeIn {
                        from { opacity: 0; transform: translateY(10px); }
                        to { opacity: 1; transform: translateY(0); }
                    }

                    .hidden {
                        display: none !important;
                    }

                    .teal-glow-hover:hover {
                        box-shadow: 0 0 20px rgba(8, 255, 240, 0.3);
                    }

                    .purple-glow-hover:hover {
                        box-shadow: 0 0 20px rgba(139, 92, 246, 0.3);
                    }
                `}</style>
            </DashboardLayout>

            {/* Modal Criar Workspace */}
            <ModalCreateWorkspace 
                show={showCreateModal}
                onClose={() => setShowCreateModal(false)}
            />
        </>
    );
};