export const StudioApiSettingsTab = ({
    activeTab, 
    apiConfig, 
    toggleApiStatus, 
    saveApiConfig, 
    global_key_api 
}) => {
    return activeTab === 'api-config' && 
    <div className="space-y-6 animate-fadeIn">
        {/* Status da API */}
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div className="flex items-center justify-between mb-6">
                <div>
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">Status da API</h3>
                    <p className="text-sm text-gray-600 dark:text-gray-400">
                        Gerencie as configurações de acesso à sua API
                    </p>
                </div>
                <div className="flex items-center space-x-4">
                    <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${
                        apiConfig.enabled 
                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' 
                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                    }`}>
                        <i className={`fas fa-circle ${apiConfig.enabled ? 'text-green-500' : 'text-red-500'} mr-2`}></i>
                        {apiConfig.enabled ? 'Ativa' : 'Inativa'}
                    </span>
                    <button
                        onClick={toggleApiStatus}
                        className={`px-4 py-2 rounded-lg font-medium ${
                            apiConfig.enabled
                                ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800'
                                : 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800'
                        }`}
                    >
                        {apiConfig.enabled ? 'Desativar API' : 'Ativar API'}
                    </button>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Configurações de Segurança */}
                <div className="space-y-4">
                    <h4 className="font-medium text-gray-900 dark:text-white flex items-center">
                        <i className="fas fa-shield-alt mr-2 text-blue-500"></i>
                        Segurança
                    </h4>
                    <div className="space-y-3">
                        <label className="flex items-center">
                            <input
                                type="checkbox"
                                checked={apiConfig.requireHttps}
                                onChange={(e) => setApiConfig(prev => ({ ...prev, requireHttps: e.target.checked }))}
                                className="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                            />
                            <span className="ml-2 text-gray-700 dark:text-gray-300">Requer Conexão HTTPS</span>
                        </label>
                        <label className="flex items-center">
                            <input
                                type="checkbox"
                                checked={apiConfig.requireApiKey}
                                onChange={(e) => setApiConfig(prev => ({ ...prev, requireApiKey: e.target.checked }))}
                                className="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                            />
                            <span className="ml-2 text-gray-700 dark:text-gray-300">Requer API Key para acesso</span>
                        </label>
                        <label className="flex items-center">
                            <input
                                type="checkbox"
                                checked={apiConfig.logRequests}
                                onChange={(e) => setApiConfig(prev => ({ ...prev, logRequests: e.target.checked }))}
                                className="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                            />
                            <span className="ml-2 text-gray-700 dark:text-gray-300">Registrar todas as requisições</span>
                        </label>
                    </div>
                </div>

                {/* Configurações de Acesso */}
                <div className="space-y-4">
                    <h4 className="font-medium text-gray-900 dark:text-white flex items-center">
                        <i className="fas fa-globe mr-2 text-green-500"></i>
                        Acesso
                    </h4>
                    <div className="space-y-3">
                        <label className="flex items-center">
                            <input
                                type="checkbox"
                                checked={apiConfig.allowCors}
                                onChange={(e) => setApiConfig(prev => ({ ...prev, allowCors: e.target.checked }))}
                                className="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                            />
                            <span className="ml-2 text-gray-700 dark:text-gray-300">Permitir CORS (Cross-Origin)</span>
                        </label>
                        <label className="flex items-center">
                            <input
                                type="checkbox"
                                checked={apiConfig.enableWebhooks}
                                onChange={(e) => setApiConfig(prev => ({ ...prev, enableWebhooks: e.target.checked }))}
                                className="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                            />
                            <span className="ml-2 text-gray-700 dark:text-gray-300">Habilitar Webhooks</span>
                        </label>
                    </div>
                </div>
            </div>

            {/* Limites de Rate */}
            <div className="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h4 className="font-medium text-gray-900 dark:text-white mb-4">
                    <i className="fas fa-tachometer-alt mr-2 text-purple-500"></i>
                    Limites de Requisições
                </h4>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Limite por Minuto
                        </label>
                        <input
                            type="number"
                            value={apiConfig.rateLimitPerMinute}
                            onChange={(e) => setApiConfig(prev => ({ ...prev, rateLimitPerMinute: parseInt(e.target.value) }))}
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            min="1"
                            max="1000"
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Limite por Dia
                        </label>
                        <input
                            type="number"
                            value={apiConfig.rateLimitPerDay}
                            onChange={(e) => setApiConfig(prev => ({ ...prev, rateLimitPerDay: parseInt(e.target.value) }))}
                            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            min="1"
                            max="100000"
                        />
                    </div>
                </div>
            </div>

            {/* Botão Salvar */}
            <div className="mt-8 flex justify-end">
                <button
                    onClick={saveApiConfig}
                    className="px-6 py-3 bg-teal-600 text-white font-medium rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors"
                >
                    <i className="fas fa-save mr-2"></i>
                    Salvar Configurações
                </button>
            </div>
        </div>

        {/* Gerenciamento de API Keys */}
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            {/* <div className="flex items-center justify-between mb-6">
                <div>
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                        <i className="fas fa-key mr-2 text-yellow-500"></i>
                        Gerenciamento de API Keys
                    </h3>
                    <p className="text-sm text-gray-600 dark:text-gray-400">
                        Gere e revogue suas chaves de API
                    </p>
                </div>
                <button
                    onClick={() => setShowApiKeyModal(true)}
                    className="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700"
                >
                    <i className="fas fa-plus mr-2"></i>
                    Nova API Key
                </button>
            </div> */}

            <div className="space-y-4">
                <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="font-medium text-gray-900 dark:text-white">API Key Principal</p>
                            <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Chave de acesso global ao workspace
                            </p>
                        </div>
                        <div className="flex items-center space-x-2">
                            <code className="bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300 px-3 py-1 rounded text-sm font-mono">
                                {global_key_api}
                            </code>
                            <button
                                onClick={() => copyToClipboard(global_key_api, 'API Key copiada!')}
                                className="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                <i className="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div className="mt-6 text-sm text-gray-600 dark:text-gray-400">
                <p className="flex items-center">
                    <i className="fas fa-info-circle mr-2 text-blue-500"></i>
                    Dica: Mantenha suas API Keys seguras e nunca as compartilhe publicamente.
                </p>
            </div>
        </div>
    </div>
}