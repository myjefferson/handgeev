import { Link } from "@inertiajs/react"

export const StudioInputConnectionsTab = ({
    activeTab,
    workspace
}) => {
    return activeTab === 'input-connections' && (
    <div className="space-y-6 animate-fadeIn">
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div className="flex items-center justify-between mb-6">
                <div>
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                        <i className="fas fa-plug mr-2 text-green-500"></i>
                        Conexões de Entrada
                    </h3>
                    <p className="text-sm text-gray-600 dark:text-gray-400">
                        Configure conexões para enriquecer tópicos com dados externos
                    </p>
                </div>
                <Link
                    href={route('workspaces.input-connections.create', workspace.id)}
                    className="px-4 py-2 bg-teal-600 text-white font-medium rounded-lg hover:bg-teal-700"
                >
                    <i className="fas fa-plus mr-2"></i>
                    Nova Conexão
                </Link>
            </div>

            {/* Lista de Conexões */}
            <div className="space-y-4">
                {workspace.inputConnections?.map(connection => (
                    <div key={connection.id} className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-650 transition-colors">
                        <div className="flex items-center justify-between">
                            <div className="flex-1">
                                <div className="flex items-center space-x-3">
                                    <i className={`fas ${
                                        connection.source?.source_type === 'rest_api' ? 'fa-cloud' :
                                        connection.source?.source_type === 'webhook' ? 'fa-broadcast-tower' :
                                        connection.source?.source_type === 'csv' ? 'fa-file-csv' :
                                        connection.source?.source_type === 'excel' ? 'fa-file-excel' : 'fa-window-restore'
                                    } text-teal-500`}></i>
                                    <div>
                                        <h4 className="font-medium text-gray-900 dark:text-white">{connection.name}</h4>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">{connection.description}</p>
                                    </div>
                                </div>
                                <div className="flex items-center mt-3 space-x-4">
                                    <span className={`inline-flex items-center px-2 py-1 rounded text-xs font-medium ${
                                        connection.is_active 
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                    }`}>
                                        <i className={`fas fa-circle text-xs mr-1 ${connection.is_active ? 'text-green-500' : 'text-red-500'}`}></i>
                                        {connection.is_active ? 'Ativa' : 'Inativa'}
                                    </span>
                                    <span className="text-xs text-gray-500 dark:text-gray-400">
                                        <i className="fas fa-layer-group mr-1"></i>
                                        {connection.structure?.name}
                                    </span>
                                    <span className="text-xs text-gray-500 dark:text-gray-400">
                                        <i className="fas fa-exchange-alt mr-1"></i>
                                        {connection.mappings?.length || 0} mapeamentos
                                    </span>
                                </div>
                            </div>
                            <div className="flex space-x-2">
                                <Link 
                                    href={route('workspaces.input-connections.edit', [workspace.id, connection.id])}
                                    className="px-3 py-1 text-sm bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded hover:bg-blue-200 dark:hover:bg-blue-800"
                                >
                                    <i className="fas fa-edit mr-1"></i>Editar
                                </Link>
                                <Link 
                                    href={route('workspaces.input-connections.logs', [workspace.id, connection.id])}
                                    className="px-3 py-1 text-sm bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-800"
                                >
                                    <i className="fas fa-history mr-1"></i>Logs
                                </Link>
                                <button 
                                    onClick={() => executeConnection(connection.id)}
                                    className="px-3 py-1 text-sm bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded hover:bg-green-200 dark:hover:bg-green-800"
                                >
                                    <i className="fas fa-play mr-1"></i>Executar
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
                
                {(!workspace.inputConnections || workspace.inputConnections.length === 0) && (
                    <div className="text-center py-8">
                        <i className="fas fa-plug text-4xl text-gray-400 mb-4"></i>
                        <p className="text-gray-500 dark:text-gray-400 mb-4">
                            Nenhuma conexão de entrada configurada
                        </p>
                        <Link 
                            href={route('workspaces.input-connections.create', workspace.id)}
                            className="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700"
                        >
                            <i className="fas fa-plus mr-2"></i>
                            Criar Primeira Conexão
                        </Link>
                    </div>
                )}
            </div>
        </div>
    </div>
)
}