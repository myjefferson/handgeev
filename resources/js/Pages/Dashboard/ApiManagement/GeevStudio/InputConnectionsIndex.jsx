import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';

export default function InputConnectionsIndex({ workspace, connections }) {
    return (
        <DashboardLayout>
            <Head title={`Conexões de Entrada - ${workspace.title}`} />
            
            <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div className="px-4 py-6 sm:px-0">
                    <div className="flex justify-between items-center mb-6">
                        <div>
                            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                                Conexões de Entrada
                            </h1>
                            <p className="text-gray-600 dark:text-gray-400">
                                Configure conexões para enriquecer tópicos com dados externos
                            </p>
                        </div>
                        <Link
                            href={route('workspaces.input-connections.create', workspace.id)}
                            className="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700"
                        >
                            Nova Conexão
                        </Link>
                    </div>

                    <div className="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        {connections.length > 0 ? (
                            <ul className="divide-y divide-gray-200 dark:divide-gray-700">
                                {connections.map(connection => (
                                    <li key={connection.id} className="p-6 hover:bg-gray-50 dark:hover:bg-gray-750">
                                        <div className="flex items-center justify-between">
                                            <div className="flex-1">
                                                <div className="flex items-center space-x-3">
                                                    <i className={`fas ${
                                                        connection.source?.source_type === 'rest_api' ? 'fa-cloud' :
                                                        connection.source?.source_type === 'webhook' ? 'fa-broadcast-tower' :
                                                        connection.source?.source_type === 'csv' ? 'fa-file-csv' :
                                                        connection.source?.source_type === 'excel' ? 'fa-file-excel' : 'fa-window-restore'
                                                    } text-teal-500 text-lg`}></i>
                                                    <div>
                                                        <h3 className="text-lg font-medium text-gray-900 dark:text-white">
                                                            {connection.name}
                                                        </h3>
                                                        <p className="text-gray-600 dark:text-gray-400">
                                                            {connection.description}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div className="mt-4 flex items-center space-x-4">
                                                    <span className={`inline-flex items-center px-2 py-1 rounded text-xs ${
                                                        connection.is_active 
                                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                    }`}>
                                                        {connection.is_active ? 'Ativa' : 'Inativa'}
                                                    </span>
                                                    <span className="text-xs text-gray-500">
                                                        Estrutura: {connection.structure?.name}
                                                    </span>
                                                    <span className="text-xs text-gray-500">
                                                        {connection.mappings?.length || 0} mapeamentos
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex space-x-2">
                                                <Link
                                                    href={route('workspaces.input-connections.edit', [workspace.id, connection.id])}
                                                    className="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
                                                >
                                                    Editar
                                                </Link>
                                                <Link
                                                    href={route('workspaces.input-connections.logs', [workspace.id, connection.id])}
                                                    className="px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                                                >
                                                    Logs
                                                </Link>
                                            </div>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <div className="text-center py-12">
                                <i className="fas fa-plug text-4xl text-gray-400 mb-4"></i>
                                <p className="text-gray-500">Nenhuma conexão configurada</p>
                                <Link
                                    href={route('workspaces.input-connections.create', workspace.id)}
                                    className="mt-4 inline-block px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700"
                                >
                                    Criar Primeira Conexão
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}