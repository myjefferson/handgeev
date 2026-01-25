import React from 'react';
import { Link } from '@inertiajs/react';

export const InputConnectionCard = ({ connection, workspace, onExecute, onTest }) => {
    const getSourceIcon = (type) => {
        switch (type) {
            case 'rest_api': return 'fa-cloud';
            case 'webhook': return 'fa-broadcast-tower';
            case 'csv': return 'fa-file-csv';
            case 'excel': return 'fa-file-excel';
            case 'form': return 'fa-window-restore';
            default: return 'fa-plug';
        }
    };

    const getSourceColor = (type) => {
        switch (type) {
            case 'rest_api': return 'text-blue-500';
            case 'webhook': return 'text-purple-500';
            case 'csv': return 'text-green-500';
            case 'excel': return 'text-green-600';
            case 'form': return 'text-orange-500';
            default: return 'text-gray-500';
        }
    };

    return (
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-shadow">
            <div className="p-6">
                {/* Cabeçalho */}
                <div className="flex items-start justify-between mb-4">
                    <div className="flex items-center space-x-3">
                        <i className={`fas ${getSourceIcon(connection.source?.source_type)} text-xl ${getSourceColor(connection.source?.source_type)}`}></i>
                        <div>
                            <h3 className="font-semibold text-gray-900 dark:text-white">
                                {connection.name}
                            </h3>
                            <p className="text-sm text-gray-600 dark:text-gray-400">
                                {connection.description || 'Sem descrição'}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center space-x-2">
                        <span className={`inline-flex items-center px-2 py-1 rounded text-xs font-medium ${
                            connection.is_active 
                                ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                                : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                        }`}>
                            <i className={`fas fa-circle text-xs mr-1 ${connection.is_active ? 'text-green-500' : 'text-red-500'}`}></i>
                            {connection.is_active ? 'Ativa' : 'Inativa'}
                        </span>
                    </div>
                </div>

                {/* Informações */}
                <div className="space-y-3">
                    <div className="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i className="fas fa-layer-group mr-2"></i>
                        <span>Estrutura: {connection.structure?.name}</span>
                    </div>
                    
                    {connection.trigger_field_id && (
                        <div className="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i className="fas fa-bolt mr-2"></i>
                            <span>Trigger: {connection.trigger_field?.key_name}</span>
                        </div>
                    )}

                    <div className="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i className="fas fa-exchange-alt mr-2"></i>
                        <span>{connection.mappings?.length || 0} campos mapeados</span>
                    </div>

                    {/* Última execução */}
                    {connection.logs?.length > 0 && (
                        <div className="flex items-center text-sm">
                            <i className="fas fa-history mr-2 text-gray-400"></i>
                            <span className={`font-medium ${
                                connection.logs[0].status === 'success' 
                                    ? 'text-green-600 dark:text-green-400'
                                    : connection.logs[0].status === 'error'
                                    ? 'text-red-600 dark:text-red-400'
                                    : 'text-yellow-600 dark:text-yellow-400'
                            }`}>
                                {connection.logs[0].status === 'success' ? 'Sucesso' : 
                                 connection.logs[0].status === 'error' ? 'Erro' : 'Pendente'}
                            </span>
                            <span className="text-gray-500 dark:text-gray-400 ml-2">
                                • {new Date(connection.logs[0].executed_at).toLocaleDateString()}
                            </span>
                        </div>
                    )}
                </div>

                {/* Ações */}
                <div className="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div className="flex space-x-2">
                        <Link 
                            href={route('workspaces.input-connections.edit', [workspace.id, connection.id])}
                            className="px-3 py-1 text-sm bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors"
                        >
                            <i className="fas fa-edit mr-1"></i>Editar
                        </Link>
                        <Link 
                            href={route('workspaces.input-connections.logs', [workspace.id, connection.id])}
                            className="px-3 py-1 text-sm bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors"
                        >
                            <i className="fas fa-history mr-1"></i>Logs
                        </Link>
                        <button 
                            onClick={onTest}
                            className="px-3 py-1 text-sm bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-800 transition-colors"
                        >
                            <i className="fas fa-vial mr-1"></i>Testar
                        </button>
                    </div>
                    
                    <button 
                        onClick={onExecute}
                        disabled={!connection.is_active}
                        className={`px-3 py-1 text-sm rounded-lg transition-colors ${
                            connection.is_active
                                ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800'
                                : 'bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500 cursor-not-allowed'
                        }`}
                    >
                        <i className="fas fa-play mr-1"></i>Executar
                    </button>
                </div>
            </div>
        </div>
    );
};