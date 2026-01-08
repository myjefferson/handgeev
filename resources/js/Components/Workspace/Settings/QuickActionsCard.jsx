// resources/js/Components/Workspace/Settings/QuickActionsCard.jsx
import React from 'react';

export default function QuickActionsCard({ workspace, onDuplicate, onDelete }) {
    const topicsCount = workspace.topics?.length || 0;
    const fieldsCount = workspace.topics?.reduce((total, topic) => total + (topic.fields?.length || 0), 0) || 0;

    return (
        <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Ações Rápidas
            </h2>
            
            <div className="space-y-3">
                <button
                    onClick={onDuplicate}
                    className="w-full flex items-center justify-between p-3 text-left bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg transition-colors"
                >
                    <div>
                        <div className="font-medium text-blue-700 dark:text-blue-300">
                            Duplicar Workspace
                        </div>
                        <div className="text-sm text-blue-600 dark:text-blue-400">
                            Criar uma cópia completa
                        </div>
                    </div>
                    <i className="fas fa-copy text-blue-500"></i>
                </button>

                <button
                    onClick={onDelete}
                    className="w-full flex items-center justify-between p-3 text-left bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg transition-colors"
                >
                    <div>
                        <div className="font-medium text-red-700 dark:text-red-300">
                            Excluir Workspace
                        </div>
                        <div className="text-sm text-red-600 dark:text-red-400">
                            Remover permanentemente
                        </div>
                    </div>
                    <i className="fas fa-trash text-red-500"></i>
                </button>

                <div className="pt-3 border-t border-gray-200 dark:border-gray-600">
                    <div className="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                        <div className="flex justify-between">
                            <span>Tópicos:</span>
                            <span className="font-medium text-gray-700 dark:text-gray-300">{topicsCount}</span>
                        </div>
                        <div className="flex justify-between">
                            <span>Campos:</span>
                            <span className="font-medium text-gray-700 dark:text-gray-300">{fieldsCount}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}