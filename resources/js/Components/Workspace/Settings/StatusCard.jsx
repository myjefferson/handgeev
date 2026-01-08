// resources/js/Components/Workspace/Settings/StatusCard.jsx
import React from 'react';
import { router } from '@inertiajs/react';

export default function StatusCard({ workspace }) {
    const toggleApiStatus = async () => {
        try {
            await router.put(route('management.api.access.toggle', workspace.id), {
                api_enabled: !workspace.api_enabled
            }, {
                preserveScroll: true,
            });
            router.reload();
        } catch (error) {
            console.error('Erro ao alternar status da API:', error);
        }
    };

    return (
        <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Status
            </h2>
            
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-3">
                        <div className={`p-2 rounded-lg ${
                            workspace.is_published 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' 
                                : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400'
                        }`}>
                            <i className={`fas ${workspace.is_published ? 'fa-globe' : 'fa-lock'}`}></i>
                        </div>
                        <div>
                            <p className="text-sm font-medium text-gray-900 dark:text-white">
                                {workspace.is_published ? 'Público' : 'Privado'}
                            </p>
                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                {workspace.is_published 
                                    ? 'Acessível publicamente' 
                                    : 'Somente você pode acessar'
                                }
                            </p>
                        </div>
                    </div>
                </div>

                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-3">
                        <div className={`p-2 rounded-lg ${
                            workspace.api_enabled 
                                ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' 
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
                        }`}>
                            <i className="fas fa-code"></i>
                        </div>
                        <div>
                            <p className="text-sm font-medium text-gray-900 dark:text-white">
                                {workspace.api_enabled ? 'API Ativa' : 'API Inativa'}
                            </p>
                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                {workspace.api_enabled 
                                    ? 'API acessível' 
                                    : 'API desativada'
                                }
                            </p>
                        </div>
                    </div>
                    <button
                        onClick={toggleApiStatus}
                        className={`relative inline-flex items-center h-6 rounded-full w-11 transition-colors ${
                            workspace.api_enabled ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600'
                        }`}
                    >
                        <span className={`inline-block w-4 h-4 transform bg-white rounded-full transition ${
                            workspace.api_enabled ? 'translate-x-6' : 'translate-x-1'
                        }`} />
                    </button>
                </div>
            </div>
        </div>
    );
}