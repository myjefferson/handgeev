export const StudioWorkspaceTab = ({
    activeTab, 
    searchTerm, 
    setSearchTerm, 
    viewMode, 
    filteredTopics, 
    expandedTopics,
    workspace
}) =>{
    return activeTab === 'workspace' && (
    <div className="space-y-6 animate-fadeIn">
        {/* Barra de Controles */}
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="flex-1 max-w-xl">
                    <div className="relative">
                        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i className="fas fa-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:border-gray-600 dark:text-white"
                            placeholder="Pesquisar por tópico, chave ou valor..."
                        />
                    </div>
                </div>
                <div className="flex items-center space-x-4">
                    <div className="flex items-center space-x-2">
                        <span className="text-sm text-gray-700 dark:text-gray-300">Visualização:</span>
                        <div className="inline-flex rounded-md shadow-sm">
                            <button
                                type="button"
                                onClick={() => setViewMode('normal')}
                                className={`px-4 py-2 text-sm font-medium rounded-l-lg border ${
                                    viewMode === 'normal'
                                        ? 'bg-teal-500 text-white border-teal-500'
                                        : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                                }`}
                            >
                                Normal
                            </button>
                            <button
                                type="button"
                                onClick={() => setViewMode('json')}
                                className={`px-4 py-2 text-sm font-medium rounded-r-lg border ${
                                    viewMode === 'json'
                                        ? 'bg-teal-500 text-white border-teal-500'
                                        : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
                                }`}
                            >
                                JSON
                            </button>
                        </div>
                    </div>
                    <button 
                        onClick={() => copyToClipboard(JSON.stringify(workspace, null, 2), 'Workspace copiado como JSON!')}
                        className="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        <i className="fas fa-download mr-2"></i>Exportar JSON
                    </button>
                </div>
            </div>
        </div>

        {/* Lista de Tópicos */}
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
            {filteredTopics.length > 0 ? (
                <div className="divide-y divide-gray-200 dark:divide-gray-700">
                    {filteredTopics.map((topic) => (
                        <div key={topic.id} className="transition-colors">
                            {/* Cabeçalho do Tópico */}
                            <div className="p-6">
                                <div className="flex items-start justify-between">
                                    <div className="flex-1">
                                        <div className="flex items-center space-x-3 mb-2">
                                            <i className="fas fa-folder text-teal-500 text-lg"></i>
                                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                                {topic.title}
                                            </h3>
                                            <span className="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                                {topic.fields?.length || 0} campos
                                            </span>
                                        </div>
                                        
                                        {/* URL da API */}
                                        <div className="ml-8">
                                            <div className="flex items-center space-x-2 text-sm">
                                                <span className="text-gray-600 dark:text-gray-400">Endpoint:</span>
                                                <code className="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded text-xs font-mono">
                                                    /api/v1/topics/{topic.id}
                                                </code>
                                                <button
                                                    onClick={() => copyToClipboard(generateApiUrl(topic.id), 'URL da API copiada!')}
                                                    className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                                    title="Copiar URL"
                                                >
                                                    <i className="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button
                                        onClick={() => toggleTopic(topic.id)}
                                        className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                                    >
                                        <i className={`fas fa-chevron-${expandedTopics.has(topic.id) ? 'up' : 'down'}`}></i>
                                    </button>
                                </div>
                            </div>

                            {/* Conteúdo do Tópico (expandido) */}
                            {expandedTopics.has(topic.id) && (
                                <div className="px-6 pb-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                                    {viewMode === 'normal' ? (
                                        <div className="overflow-x-auto">
                                            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead>
                                                    <tr className="bg-gray-50 dark:bg-gray-700">
                                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Chave</th>
                                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                                                    {topic.fields?.map((field, index) => (
                                                        <tr key={field.id || index} className="hover:bg-gray-50 dark:hover:bg-gray-750">
                                                            <td className="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                                {field.key_name}
                                                            </td>
                                                            <td className="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 break-all max-w-md">
                                                                {field.value}
                                                            </td>
                                                            <td className="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                                                <span className="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                    {field.type}
                                                                </span>
                                                            </td>
                                                            <td className="px-4 py-3 text-sm">
                                                                <div className="flex space-x-2">
                                                                    <button
                                                                        onClick={() => copyToClipboard(field.value, 'Valor copiado!')}
                                                                        className="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                                                        title="Copiar valor"
                                                                    >
                                                                        <i className="fas fa-copy"></i>
                                                                    </button>
                                                                    <button
                                                                        onClick={() => copyToClipboard(field.key_name, 'Chave copiada!')}
                                                                        className="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300"
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
                                    ) : (
                                        <div className="bg-gray-900 rounded-lg p-4">
                                            <pre className="text-green-400 text-sm overflow-x-auto">
                                                <code>
                                                    {JSON.stringify(topic, null, 2)}
                                                </code>
                                            </pre>
                                            <button
                                                onClick={() => copyToClipboard(JSON.stringify(topic, null, 2), 'Tópico copiado como JSON!')}
                                                className="mt-2 text-sm text-gray-400 hover:text-white"
                                            >
                                                <i className="fas fa-copy mr-1"></i> Copiar JSON
                                            </button>
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            ) : (
                <div className="p-12 text-center">
                    <i className="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
                    <p className="text-gray-500 dark:text-gray-400">
                        {searchTerm ? 'Nenhum tópico encontrado para sua pesquisa' : 'Nenhum tópico disponível'}
                    </p>
                </div>
            )}
        </div>
    </div>
)
}