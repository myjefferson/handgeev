import React, { useState, useRef, useEffect } from 'react';
import { Link, router } from '@inertiajs/react';
import EmptyState from '@/Components/State/MyWorkspaceEmptyState';

const MyWorkspaceList = ({ workspaces, type = 'owner' }) => {
    const [openMenu, setOpenMenu] = useState(null);
    const menuRefs = useRef({});

    // Fechar menu ao clicar fora
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (openMenu && menuRefs.current[openMenu] && !menuRefs.current[openMenu].contains(event.target)) {
                setOpenMenu(null);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [openMenu]);

    const toggleMenu = (menuId) => {
        setOpenMenu(openMenu === menuId ? null : menuId);
    };

    const closeMenu = () => {
        setOpenMenu(null);
    };

    const handleDeleteWorkspace = (workspace) => {
        if (confirm(`Tem certeza que deseja excluir o workspace "${workspace.title}"? Esta ação não pode ser desfeita.`)) {
            // Implementar exclusão via Inertia
            router.delete(route('workspace.delete', { id: workspace.id }));
        }
    };

    const handleLeaveWorkspace = (workspace) => {
        if (confirm(`Tem certeza que deseja sair do workspace "${workspace.title}"?`)) {
            // Implementar saída do workspace via Inertia
            router.post(route('workspace.leave', { id: workspace.id }));
        }
    };

    if (!workspaces || workspaces.length === 0) {
        return (
            <EmptyState 
                type={type === 'collaborator' ? 'collaborations' : 'my-workspaces'}
                icon={type === 'collaborator' ? '👥' : '📁'}
                title={type === 'collaborator' ? 'Nenhuma colaboração' : 'Nenhum workspace encontrado'}
                description={type === 'collaborator' 
                    ? 'Você ainda não está colaborando em nenhum workspace.' 
                    : 'Comece criando seu primeiro workspace para organizar seus dados.'}
                showButton={type === 'owner'}
            />
        );
    }

    return (
        <div className="bg-slate-800/50 rounded-xl border border-slate-700">
            {/* Cabeçalho da Lista */}
            <div className="grid grid-cols-12 gap-4 px-6 py-3 border-b border-slate-700 text-sm font-medium text-slate-400">
                <div className="col-span-7">Workspace</div>
                <div className="col-span-2 text-center">Status</div>
                <div className="col-span-2 text-center">Atualizado</div>
                <div className="col-span-1 text-center">Ações</div>
            </div>

            {/* Itens da Lista */}
            <div className="divide-y divide-slate-700">
                {workspaces.map((workspace) => {
                    const menuId = `menu-${workspace.id}-list-${type}`;
                    const isMenuOpen = openMenu === menuId;

                    return (
                        <div 
                            key={workspace.id}
                            className="workspace-item workspace-list-item grid grid-cols-12 gap-4 px-6 py-4 border-b border-slate-700/50 last:border-b-0"
                            data-topics={workspace.topics_count}
                            data-created={workspace.created_at}
                            data-updated={workspace.updated_at}
                        >
                            
                            {/* Nome e Descrição */}
                            <div className="col-span-7">
                                <div className="flex items-start space-x-3">
                                    <div className="flex-shrink-0 w-10 h-10 bg-slate-700 rounded-lg flex items-center justify-center">
                                        <svg className="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <Link 
                                            href={route('workspace.show', workspace.id)}
                                            className="workspace-title text-sm font-semibold text-white truncate hover:text-teal-500"
                                        >
                                            {workspace.title}
                                        </Link>
                                        <p className="workspace-description text-sm text-slate-400 mt-1 truncate">
                                            {workspace.description || 'Sem descrição'}
                                        </p>
                                        {type === 'collaborator' && (
                                            <div className="flex items-center mt-1 text-xs text-slate-500">
                                                <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                {workspace.user?.name}
                                                {workspace.collaboration && (
                                                    <span className="ml-2 px-1.5 py-0.5 bg-slate-600 rounded text-slate-300 text-xs">
                                                        {workspace.collaboration.role ? workspace.collaboration.role.charAt(0).toUpperCase() + workspace.collaboration.role.slice(1) : 'Colaborador'}
                                                    </span>
                                                )}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Status */}
                            <div className="col-span-2 flex items-center justify-center">
                                <div className="flex flex-wrap gap-1 justify-center">
                                    {workspace.api_enabled ? (
                                        <span className="status-badge-active px-2 py-1 bg-green-500/20 text-green-400 text-xs rounded-full flex items-center">
                                            <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            API
                                        </span>
                                    ) : (
                                        <span className="px-2 py-1 bg-slate-500/20 text-slate-400 text-xs rounded-full">
                                            Inativo
                                        </span>
                                    )}

                                    {workspace.is_published ? (
                                        <span className="status-badge-public px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded-full">
                                            Público
                                        </span>
                                    ) : (
                                        <span className="px-2 py-1 bg-amber-500/20 text-amber-400 text-xs rounded-full">
                                            Privado
                                        </span>
                                    )}

                                    {workspace.api_jwt_required && (
                                        <span className="status-badge-jwt px-2 py-1 bg-purple-500/20 text-purple-400 text-xs rounded-full">
                                            JWT
                                        </span>
                                    )}
                                </div>
                            </div>

                            {/* Data de Atualização */}
                            <div className="col-span-2 flex items-center justify-center">
                                <div className="text-center">
                                    <div className="text-sm text-white">
                                        {new Date(workspace.updated_at).toLocaleDateString('pt-BR')}
                                    </div>
                                    <div className="text-xs text-slate-400">
                                        {new Date(workspace.updated_at).toLocaleTimeString('pt-BR', { 
                                            hour: '2-digit', 
                                            minute: '2-digit' 
                                        })}
                                    </div>
                                </div>
                            </div>

                            {/* Ações */}
                            <div className="col-span-1 flex items-center justify-center">
                                <div className="relative">
                                    <button 
                                        onClick={() => toggleMenu(menuId)}
                                        className="p-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors"
                                    >
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                                        </svg>
                                    </button>
                                    
                                    {/* Dropdown Menu */}
                                    <div 
                                        ref={el => menuRefs.current[menuId] = el}
                                        id={menuId}
                                        className={`absolute right-0 top-10 bg-slate-700 border border-slate-600 rounded-lg shadow-lg z-10 min-w-48 transition-all duration-200 ${
                                            isMenuOpen 
                                                ? 'opacity-100 visible translate-y-0' 
                                                : 'opacity-0 invisible -translate-y-2'
                                        }`}
                                    >
                                        <Link 
                                            href={route('workspace.show', workspace.id)} 
                                            className="flex items-center px-4 py-2 text-sm text-slate-300 hover:bg-slate-600 transition-colors"
                                            onClick={closeMenu}
                                        >
                                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Visualizar
                                        </Link>
                                        
                                        {type === 'owner' ? (
                                            <>
                                                <Link 
                                                    href={route('workspace.setting', workspace.id)} 
                                                    className="flex items-center px-4 py-2 text-sm text-slate-300 hover:bg-slate-600 transition-colors"
                                                    onClick={closeMenu}
                                                >
                                                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    Configurações
                                                </Link>
                                                
                                                <div className="border-t border-slate-600"></div>
                                                <button 
                                                    onClick={() => {
                                                        handleDeleteWorkspace(workspace);
                                                        closeMenu();
                                                    }}
                                                    className="w-full flex items-center justify-between p-3 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors"
                                                >
                                                    <span>Excluir Workspace</span>
                                                    <i className="fas fa-trash"></i>
                                                </button>
                                            </>
                                        ) : (
                                            /* Menu para colaborador */
                                            <button 
                                                onClick={() => {
                                                    handleLeaveWorkspace(workspace);
                                                    closeMenu();
                                                }}
                                                className="flex items-center w-full px-4 py-2 text-sm text-amber-400 hover:bg-slate-600 transition-colors"
                                            >
                                                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                </svg>
                                                Sair do Workspace
                                            </button>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
};

export default MyWorkspaceList;