import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import Alert from '@/Components/Alerts/Alert';

export default function DashboardHome({ 
    greetingMessage,
    workspacesCount,
    publishedWorkspaces,
    privateWorkspaces,
    topicsCount,
    topicsWithFields,
    fieldsCount,
    visibleFields,
    hiddenFields,
    recentWorkspaces,
    mostActiveWorkspaces,
    collaborationsCount,
    activeCollaborations,
    workspaceLimit,
    fieldsLimit,
    planLimits
}) {
    const { auth } = usePage().props;

    return (
        <DashboardLayout 
            title="Início" 
            description="Início do HandGeev"
        >
            <Head>
                <title>Início - HandGeev</title>
            </Head>

            <div className="max-w-7xl mx-auto min-h-screen p-0 sm:p-0 md:p-6">
                <Alert />
                
                {/* Header com Saudação Personalizada */}
                <div className="mb-8">
                    <h1 className="text-2xl sm:text-3xl md:text-3xl font-bold text-white mb-2">
                        Olá, {auth.user.name}! 👋
                    </h1>
                    <p className="text-sm sm:text-sm md:text-lg text-gray-400">
                        {greetingMessage}
                    </p>
                    <div className="w-20 h-1 bg-teal-400 rounded-full mt-3"></div>
                </div>

                {/* Cards de Estatísticas */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    {/* Card Workspaces */}
                    <div className="bg-slate-800 rounded-2xl shadow-lg p-6 border-l-4 border-teal-400 hover:border-teal-300 transition-all duration-300 hover:transform hover:scale-105">
                        <div className="flex items-center justify-between mb-4">
                            <div className="h-12 w-12 flex items-center justify-center bg-teal-400/10 rounded-full">
                                <i className="fas fa-layer-group text-teal-400 text-xl"></i>
                            </div>
                            <span className="text-sm text-gray-400">Total</span>
                        </div>
                        <h3 className="text-2xl font-bold text-white mb-2">{workspacesCount} Workspaces</h3>
                        <p className="text-sm text-gray-400">{publishedWorkspaces} públicos • {privateWorkspaces} privados</p>
                    </div>

                    {/* Card Tópicos */}
                    <div className="bg-slate-800 rounded-2xl shadow-lg p-6 border-l-4 border-blue-400 hover:border-blue-300 transition-all duration-300 hover:transform hover:scale-105">
                        <div className="flex items-center justify-between mb-4">
                            <div className="h-12 w-12 flex items-center justify-center bg-blue-400/10 rounded-full">
                                <i className="fas fa-folder text-blue-400 text-xl"></i>
                            </div>
                            <span className="text-sm text-gray-400">Total</span>
                        </div>
                        <h3 className="text-2xl font-bold text-white mb-2">{topicsCount} Tópicos</h3>
                        <p className="text-sm text-gray-400">{topicsWithFields} com campos</p>
                    </div>

                    {/* Card Campos */}
                    <div className="bg-slate-800 rounded-2xl shadow-lg p-6 border-l-4 border-green-400 hover:border-green-300 transition-all duration-300 hover:transform hover:scale-105">
                        <div className="flex items-center justify-between mb-4">
                            <div className="h-12 w-12 flex items-center justify-center bg-green-400/10 rounded-full">
                                <i className="fas fa-table text-green-400 text-xl"></i>
                            </div>
                            <span className="text-sm text-gray-400">Total</span>
                        </div>
                        <h3 className="text-2xl font-bold text-white mb-2">{fieldsCount} Campos</h3>
                        <p className="text-sm text-gray-400">{visibleFields} visíveis • {hiddenFields} ocultos</p>
                    </div>
                </div>

                {/* Grid Principal */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    {/* Workspaces Recentes */}
                    <div className="bg-slate-800 rounded-2xl shadow-lg p-6">
                        <div className="flex items-center justify-between mb-6">
                            <h2 className="text-xl font-semibold text-white">Workspaces Recentes</h2>
                            <Link 
                                href={route('workspaces.show')} 
                                className="text-teal-400 hover:text-teal-300 text-sm font-medium flex items-center"
                            >
                                Ver todos <i className="fas fa-arrow-right ml-1"></i>
                            </Link>
                        </div>
                        
                        <div className="space-y-4">
                            {recentWorkspaces.length > 0 ? (
                                recentWorkspaces.map(workspace => (
                                    <WorkspaceCard 
                                        key={workspace.id}
                                        workspace={workspace}
                                    />
                                ))
                            ) : (
                                <div className="text-center py-8 text-gray-400">
                                    <i className="fas fa-layer-group text-4xl mb-3 opacity-50"></i>
                                    <p>Nenhum workspace criado ainda</p>
                                    <Link 
                                        href={route('workspaces.show')} 
                                        className="text-teal-400 hover:text-teal-300 text-sm mt-2 inline-block"
                                    >
                                        Criar primeiro workspace
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Workspaces Mais Ativos */}
                    <div className="bg-slate-800 rounded-2xl shadow-lg p-6">
                        <div className="flex items-center justify-between mb-6">
                            <h2 className="text-xl font-semibold text-white">Workspaces Mais Ativos</h2>
                            <span className="text-xs text-gray-400">Por campos</span>
                        </div>

                        <div className="space-y-3">
                            {mostActiveWorkspaces.length > 0 ? (
                                mostActiveWorkspaces.map(workspace => (
                                    <ActiveWorkspaceCard 
                                        key={workspace.id}
                                        workspace={workspace}
                                    />
                                ))
                            ) : (
                                <div className="text-center py-4 text-gray-400">
                                    <p className="text-sm">Nenhum workspace ativo</p>
                                </div>
                            )}
                        </div>
                    </div>            
                </div>

                {/* Ferramentas Rápidas (se necessário) */}
                {/* <QuickToolsSection /> */}
            </div>

            <style jsx>{`
                .teal-glow {
                    box-shadow: 0 0 15px rgba(0, 230, 216, 0.3);
                }
                
                .blue-glow {
                    box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
                }
                
                .purple-glow {
                    box-shadow: 0 0 15px rgba(168, 85, 247, 0.3);
                }
                
                .green-glow {
                    box-shadow: 0 0 15px rgba(34, 197, 94, 0.3);
                }
                
                .teal-glow-hover:hover {
                    box-shadow: 0 0 20px rgba(0, 230, 216, 0.4);
                }
            `}</style>
        </DashboardLayout>
    );
}

// Componente para Card de Workspace
const WorkspaceCard = ({ workspace }) => {
    const formatDate = (dateString) => {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 0) {
            return 'Hoje';
        } else if (diffDays === 1) {
            return 'Ontem';
        } else if (diffDays < 7) {
            return `Há ${diffDays} dias`;
        } else {
            return date.toLocaleDateString('pt-BR');
        }
    };

    return (
        <Link 
            href={route('workspace.show', workspace.id)} 
            className="block p-4 bg-slate-700/50 rounded-xl hover:bg-slate-700 transition-all duration-200 group"
        >
            <div className="flex items-center justify-between">
                <div className="flex items-center space-x-4">
                    <div className="w-12 h-12 bg-gradient-to-r from-teal-400 to-teal-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                        <i className="fas fa-layer-group text-slate-900"></i>
                    </div>
                    <div>
                        <h4 className="font-semibold text-white group-hover:text-teal-300 transition-colors">
                            {workspace.title}
                        </h4>
                        <p className="text-sm text-gray-400">
                            {workspace.topics_count} tópicos • {workspace.fields_count} campos
                        </p>
                    </div>
                </div>
                <div className="flex flex-col items-end space-y-1">
                    <span className={`text-xs px-3 py-1 rounded-full ${
                        workspace.is_published 
                            ? 'bg-green-400/20 text-green-400' 
                            : 'bg-gray-400/20 text-gray-400'
                    }`}>
                        {workspace.is_published ? 'Público' : 'Privado'}
                    </span>
                    <span className="text-xs text-gray-500">
                        {formatDate(workspace.updated_at)}
                    </span>
                </div>
            </div>
        </Link>
    );
};

// Componente para Card de Workspace Ativo
const ActiveWorkspaceCard = ({ workspace }) => (
    <div className="flex items-center justify-between p-3 bg-slate-700/50 rounded-lg">
        <div className="flex items-center space-x-3">
            <div className="w-8 h-8 bg-teal-400/20 rounded flex items-center justify-center">
                <i className="fas fa-layer-group text-teal-400 text-sm"></i>
            </div>
            <span className="text-white text-sm font-medium">
                {workspace.title.length > 25 
                    ? `${workspace.title.substring(0, 25)}...` 
                    : workspace.title
                }
            </span>
        </div>
        <div className="text-right">
            <span className="text-teal-400 text-sm font-bold">{workspace.fields_count}</span>
            <span className="text-gray-400 text-xs block">campos</span>
        </div>
    </div>
);

// Componente para Ferramentas Rápidas (opcional)
const QuickToolsSection = () => (
    <div className="bg-slate-800 rounded-2xl shadow-lg p-6">
        <h2 className="text-xl font-semibold text-white mb-6">Ferramentas Rápidas</h2>
        
        <div className="grid grid-cols-2 gap-4">
            {/* API Explorer */}
            <Link 
                href="#" 
                className="p-4 bg-gradient-to-r from-blue-400 to-blue-500 text-white rounded-xl text-center hover:shadow-lg transition-all duration-300 blue-glow group"
            >
                <div className="mb-2 transform group-hover:scale-110 transition-transform duration-200">
                    <i className="fas fa-code text-2xl"></i>
                </div>
                <span className="font-medium">API Explorer</span>
            </Link>

            {/* Exportar Dados */}
            <Link 
                href="#" 
                className="p-4 bg-gradient-to-r from-green-400 to-green-500 text-white rounded-xl text-center hover:shadow-lg transition-all duration-300 green-glow group"
            >
                <div className="mb-2 transform group-hover:scale-110 transition-transform duration-200">
                    <i className="fas fa-file-export text-2xl"></i>
                </div>
                <span className="font-medium">Exportar Dados</span>
            </Link>
        </div>

        {/* Links Úteis */}
        <div className="mt-6 pt-6 border-t border-slate-700">
            <h3 className="text-sm font-medium text-gray-400 mb-3">Links Úteis</h3>
            <div className="grid grid-cols-2 gap-2">
                <Link href="#" className="text-xs text-gray-400 hover:text-teal-400 transition-colors flex items-center">
                    <i className="fas fa-book mr-2"></i> Documentação
                </Link>
                <Link href="#" className="text-xs text-gray-400 hover:text-teal-400 transition-colors flex items-center">
                    <i className="fas fa-life-ring mr-2"></i> Suporte
                </Link>
                <Link href="#" className="text-xs text-gray-400 hover:text-teal-400 transition-colors flex items-center">
                    <i className="fas fa-crown mr-2"></i> Planos
                </Link>
                <Link href="#" className="text-xs text-gray-400 hover:text-teal-400 transition-colors flex items-center">
                    <i className="fas fa-cog mr-2"></i> Configurações
                </Link>
            </div>
        </div>
    </div>
);