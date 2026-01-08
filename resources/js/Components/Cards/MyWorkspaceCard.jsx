import React from 'react';
import { Link } from '@inertiajs/react';

const MyWorkspaceCard = ({ workspace, type = 'owner', onToggleMenu, menuRefs }) => {
    const formatDate = (dateString) => {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 1) return 'Hoje';
        if (diffDays === 2) return 'Ontem';
        if (diffDays <= 7) return `${diffDays - 1} dias atrás`;
        if (diffDays <= 30) return `${Math.floor(diffDays / 7)} semanas atrás`;
        return date.toLocaleDateString('pt-BR');
    };

    return (
        <div className="workspace-card bg-slate-800 rounded-xl p-6 border border-slate-700 hover:border-cyan-500/50 transition-all duration-300 group">
            {/* Header do Card */}
            <div className="flex justify-between items-start mb-4">
                <div className="flex-1">
                    <Link 
                        href={route('workspace.show', workspace.id)}
                        className="text-lg font-semibold text-white group-hover:text-cyan-300 transition-colors truncate block"
                    >
                        {workspace.title}
                    </Link>
                    <p className="text-slate-400 text-sm mt-1 line-clamp-2 overflow-hidden">
                        {workspace.description || 'Sem descrição'}
                    </p>
                </div>
                
                <div className="relative">
                    <button 
                        onClick={() => onToggleMenu(`${workspace.id}-${type}`)}
                        className="p-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                        </svg>
                    </button>
                    
                    {/* Dropdown Menu */}
                    <div 
                        ref={el => menuRefs.current[`menu-${workspace.id}-${type}`] = el}
                        id={`menu-${workspace.id}-${type}`}
                        className="hidden absolute right-0 top-10 bg-slate-700 border border-slate-600 rounded-lg shadow-lg z-10 min-w-48"
                    >
                        <Link 
                            href={route('workspace.show', workspace.id)} 
                            className="flex items-center px-4 py-2 text-sm text-slate-300 hover:bg-slate-600 transition-colors"
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
                                >
                                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Configurações
                                </Link>
                                
                                <div className="border-t border-slate-600"></div>
                                <button 
                                    className="delete-btn w-full flex items-center justify-between p-3 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30"
                                    data-id={workspace.id}
                                    data-title={workspace.title}
                                    data-route={route('workspace.delete', { id: workspace.id })}
                                >
                                    <span>Excluir Workspace</span>
                                    <i className="fas fa-trash"></i>
                                </button>
                            </>
                        ) : (
                            /* Menu para colaborador */
                            <button 
                                onClick={() => leaveWorkspace(workspace.id)}
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

            {/* Status Badges */}
            <div className="flex flex-wrap gap-2 mb-4">
                {workspace.api_enabled ? (
                    <span className="px-2 py-1 bg-green-500/20 text-green-400 text-xs rounded-full flex items-center">
                        <svg className="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        API Ativa
                    </span>
                ) : (
                    <span className="px-2 py-1 bg-slate-500/20 text-slate-400 text-xs rounded-full">
                        API Inativa
                    </span>
                )}

                {workspace.is_published ? (
                    <span className="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded-full">
                        Público
                    </span>
                ) : (
                    <span className="px-2 py-1 bg-amber-500/20 text-amber-400 text-xs rounded-full">
                        Privado
                    </span>
                )}

                {workspace.api_jwt_required && (
                    <span className="px-2 py-1 bg-purple-500/20 text-purple-400 text-xs rounded-full">
                        JWT
                    </span>
                )}
            </div>

            {/* Footer do Card */}
            <div className="flex justify-between items-center pt-4 border-t border-slate-700">
                <div className="text-xs text-slate-400">
                    Atualizado {formatDate(workspace.updated_at)}
                </div>
                <Link 
                    href={route('workspace.show', workspace.id)} 
                    className="bg-slate-700 hover:bg-slate-600 text-white px-3 py-1 rounded-lg text-sm transition-colors flex items-center"
                >
                    Acessar
                    <svg className="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </Link>
            </div>
        </div>
    );
};

// Função auxiliar para sair do workspace (colaborador)
const leaveWorkspace = (workspaceId) => {
    if (confirm('Tem certeza que deseja sair deste workspace?')) {
        // Implementar saída do workspace via API
        console.log('Sair do workspace:', workspaceId);
        // router.post(route('workspace.leave', { id: workspaceId }));
    }
};

export default MyWorkspaceCard;