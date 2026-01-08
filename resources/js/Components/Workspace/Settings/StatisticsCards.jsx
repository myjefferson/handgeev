// resources/js/Components/Workspace/Settings/StatisticsCards.jsx
import React from 'react';

export default function StatisticsCards({ workspace }) {
    const visibleFields = workspace.topics.reduce((total, topic) => {
        return total + (topic.fields?.filter(field => field.is_visible)?.length || 0);
    }, 0);

    const totalFields = workspace.topics.reduce((total, topic) => {
        return total + (topic.fields?.length || 0);
    }, 0);

    return (
        <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-6">
                Estatísticas do Workspace
            </h2>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <StatCard
                    title="Total de Tópicos"
                    value={workspace.topics?.length || 0}
                    icon="folder"
                    color="blue"
                    status={workspace.topics?.length > 0 ? 'configured' : 'no_topics'}
                />
                
                <StatCard
                    title="Total de Campos"
                    value={totalFields}
                    icon="key"
                    color="green"
                    subtitle={`${visibleFields} campos visíveis`}
                />
                
                <StatCard
                    title="Status da API"
                    value={workspace.api_enabled ? 'Ativa' : 'Inativa'}
                    icon="code"
                    color="purple"
                    status={workspace.api_enabled ? 'active' : 'inactive'}
                />
            </div>

            <FieldDistribution topics={workspace.topics} totalFields={totalFields} />
        </div>
    );
}

function StatCard({ title, value, icon, color, status, subtitle }) {
    const colorClasses = {
        blue: {
            bg: 'from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20',
            border: 'border-blue-200 dark:border-blue-700',
            text: 'text-blue-600 dark:text-blue-400',
            value: 'text-blue-700 dark:text-blue-300',
            iconBg: 'bg-blue-100 dark:bg-blue-800',
            icon: 'text-blue-600 dark:text-blue-400'
        },
        green: {
            bg: 'from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20',
            border: 'border-green-200 dark:border-green-700',
            text: 'text-green-600 dark:text-green-400',
            value: 'text-green-700 dark:text-green-300',
            iconBg: 'bg-green-100 dark:bg-green-800',
            icon: 'text-green-600 dark:text-green-400'
        },
        purple: {
            bg: 'from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20',
            border: 'border-purple-200 dark:border-purple-700',
            text: 'text-purple-600 dark:text-purple-400',
            value: 'text-purple-700 dark:text-purple-300',
            iconBg: 'bg-purple-100 dark:bg-purple-800',
            icon: 'text-purple-600 dark:text-purple-400'
        }
    };

    const statusConfig = {
        configured: { icon: 'check-circle', text: 'Configurado' },
        no_topics: { icon: 'exclamation-triangle', text: 'Sem tópicos' },
        active: { icon: 'check-circle', text: 'Pronto para uso' },
        inactive: { icon: 'pause-circle', text: 'Desativada' }
    };

    const config = colorClasses[color];
    const statusInfo = statusConfig[status];

    return (
        <div className={`bg-gradient-to-br ${config.bg} rounded-lg p-4 border ${config.border}`}>
            <div className="flex items-center justify-between">
                <div>
                    <p className={`text-sm font-medium ${config.text}`}>{title}</p>
                    <p className={`text-2xl font-bold ${config.value} mt-1`}>{value}</p>
                </div>
                <div className={`p-2 ${config.iconBg} rounded-lg`}>
                    <i className={`fas fa-${icon} ${config.icon} text-xl`}></i>
                </div>
            </div>
            {statusInfo && (
                <div className={`mt-3 text-xs ${config.text}`}>
                    <i className={`fas fa-${statusInfo.icon} mr-1`}></i>
                    {statusInfo.text}
                </div>
            )}
            {subtitle && (
                <div className={`mt-3 text-xs ${config.text}`}>
                    <i className="fas fa-eye mr-1"></i>
                    {subtitle}
                </div>
            )}
        </div>
    );
}

function FieldDistribution({ topics, totalFields }) {
    if (!topics || topics.length === 0) {
        return (
            <div className="mt-8">
                <h3 className="text-md font-medium text-gray-900 dark:text-white mb-4">
                    Distribuição de Campos por Tópico
                </h3>
                <div className="text-center py-4 text-gray-500 dark:text-gray-400">
                    <i className="fas fa-inbox text-2xl mb-2"></i>
                    <p>Nenhum tópico criado</p>
                </div>
            </div>
        );
    }

    return (
        <div className="mt-8">
            <h3 className="text-md font-medium text-gray-900 dark:text-white mb-4">
                Distribuição de Campos por Tópico
            </h3>
            <div className="space-y-3">
                {topics.map((topic, index) => {
                    const fieldCount = topic.fields?.length || 0;
                    const percentage = totalFields > 0 ? (fieldCount / totalFields) * 100 : 0;
                    const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-yellow-500', 'bg-red-500'];
                    const color = colors[index % colors.length];

                    return (
                        <div key={topic.id} className="flex items-center justify-between">
                            <div className="flex items-center space-x-3">
                                <div className={`w-3 h-3 rounded-full ${color}`}></div>
                                <span className="text-sm text-gray-700 dark:text-gray-300">
                                    {topic.title}
                                </span>
                            </div>
                            <div className="flex items-center space-x-2">
                                <span className="text-xs text-gray-500 dark:text-gray-400">
                                    {fieldCount} campos
                                </span>
                                <span className="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-1 rounded">
                                    {percentage.toFixed(1)}%
                                </span>
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}