export const StudioStatisticsTab = ({
    activeTab, 
    statistics, 
    rateLimitInfo, 
    usageData
}) =>{
    return activeTab === 'statistics' && (
    <div className="space-y-6 animate-fadeIn">
        {/* Cards de Métricas */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div className="flex items-center">
                    <div className="flex-shrink-0">
                        <div className="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <i className="fas fa-bolt text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                    </div>
                    <div className="ml-4">
                        <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Requisições Hoje</p>
                        <p className="text-2xl font-semibold text-gray-900 dark:text-white">
                            {statistics.requestsToday.toLocaleString()}
                        </p>
                    </div>
                </div>
            </div>

            <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div className="flex items-center">
                    <div className="flex-shrink-0">
                        <div className="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <i className="fas fa-chart-line text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                    </div>
                    <div className="ml-4">
                        <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Total de Requisições</p>
                        <p className="text-2xl font-semibold text-gray-900 dark:text-white">
                            {statistics.totalRequests.toLocaleString()}
                        </p>
                    </div>
                </div>
            </div>

            <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div className="flex items-center">
                    <div className="flex-shrink-0">
                        <div className="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <i className="fas fa-rocket text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                    </div>
                    <div className="ml-4">
                        <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Tempo Médio</p>
                        <p className="text-2xl font-semibold text-gray-900 dark:text-white">
                            {statistics.averageResponseTime}
                        </p>
                    </div>
                </div>
            </div>

            <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div className="flex items-center">
                    <div className="flex-shrink-0">
                        <div className="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                            <i className="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                    </div>
                    <div className="ml-4">
                        <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Taxa de Erro</p>
                        <p className="text-2xl font-semibold text-gray-900 dark:text-white">
                            {statistics.errorRate}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {/* Gráfico e Detalhes */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i className="fas fa-chart-bar mr-2"></i>
                    Uso da API (últimos 6 meses)
                </h3>
                <div className="h-64 flex items-end space-x-2">
                    {usageData.data.map((value, index) => (
                        <div key={index} className="flex-1 flex flex-col items-center">
                            <div
                                className="w-10 bg-teal-500 rounded-t-lg transition-all hover:bg-teal-600"
                                style={{ height: `${(value / Math.max(...usageData.data)) * 80}%` }}
                                title={`${value} requisições`}
                            ></div>
                            <span className="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {usageData.labels[index]}
                            </span>
                        </div>
                    ))}
                </div>
            </div>

            <div className="space-y-6">
                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i className="fas fa-fire mr-2"></i>
                        Endpoint Mais Popular
                    </h3>
                    <div className="space-y-3">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center">
                                <i className="fas fa-link text-gray-400 mr-3"></i>
                                <span className="text-gray-700 dark:text-gray-300">{statistics.popularEndpoint}</span>
                            </div>
                            <span className="text-sm font-medium text-teal-600 dark:text-teal-400">42% do tráfego</span>
                        </div>
                        <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div className="bg-teal-500 h-2 rounded-full" style={{ width: '42%' }}></div>
                        </div>
                    </div>
                </div>

                <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i className="fas fa-users mr-2"></i>
                        Visitas Únicas
                    </h3>
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-3xl font-bold text-gray-900 dark:text-white">{statistics.uniqueVisitors}</p>
                            <p className="text-sm text-gray-600 dark:text-gray-400">Visitantes este mês</p>
                        </div>
                        <div className="text-right">
                            <p className="text-sm text-gray-600 dark:text-gray-400">Horário de Pico</p>
                            <p className="text-lg font-semibold text-gray-900 dark:text-white">{statistics.peakHour}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {/* Limites da API */}
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i className="fas fa-tachometer-alt mr-2"></i>
                Limites da API
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div className="flex items-center justify-between mb-2">
                        <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Por Minuto</span>
                        <span className="text-sm font-bold text-blue-600 dark:text-blue-400">
                            {rateLimitInfo?.remaining_minute || 60}/{rateLimitInfo?.limit_per_minute || 60}
                        </span>
                    </div>
                    <div className="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                        <div 
                            className="bg-blue-500 h-2 rounded-full" 
                            style={{ 
                                width: `${((rateLimitInfo?.remaining_minute || 60) / (rateLimitInfo?.limit_per_minute || 60)) * 100}%` 
                            }}
                        ></div>
                    </div>
                </div>
                <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div className="flex items-center justify-between mb-2">
                        <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Por Dia</span>
                        <span className="text-sm font-bold text-green-600 dark:text-green-400">
                            {rateLimitInfo?.remaining_daily || 1000}/{rateLimitInfo?.limit_per_day || 1000}
                        </span>
                    </div>
                    <div className="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                        <div 
                            className="bg-green-500 h-2 rounded-full" 
                            style={{ 
                                width: `${((rateLimitInfo?.remaining_daily || 1000) / (rateLimitInfo?.limit_per_day || 1000)) * 100}%` 
                            }}
                        ></div>
                    </div>
                </div>
                <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div className="flex items-center justify-between mb-2">
                        <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Por Mês</span>
                        <span className="text-sm font-bold text-purple-600 dark:text-purple-400">
                            24,500/50,000
                        </span>
                    </div>
                    <div className="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                        <div className="bg-purple-500 h-2 rounded-full" style={{ width: '49%' }}></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
)
}