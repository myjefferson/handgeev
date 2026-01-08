// resources/js/Components/Workspace/Settings/WorkspaceInfoForm.jsx
import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';

export default function WorkspaceInfoForm({ workspace, showMergeWarning, onMergeWarningChange }) {
    const { data, setData, put, processing, errors, reset } = useForm({
        title: workspace.title || '',
        type_workspace_id: workspace.type_workspace_id || 1,
        description: workspace.description || ''
    });

    const [charCount, setCharCount] = useState(data.description.length);

    useEffect(() => {
        setCharCount(data.description.length);
    }, [data.description]);

    useEffect(() => {
        // Mostrar aviso de merge quando mudar para tópico único com múltiplos tópicos
        if (data.type_workspace_id == 1 && workspace.topics?.length > 1) {
            onMergeWarningChange(true);
        } else {
            onMergeWarningChange(false);
        }
    }, [data.type_workspace_id, workspace.topics, onMergeWarningChange]);

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('workspace.update', workspace.id), {
            preserveScroll: true,
            onSuccess: () => {
                // Sucesso pode ser tratado aqui
            }
        });
    };

    return (
        <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div className="flex justify-between items-center mb-6">
                <h2 className="text-lg font-medium text-gray-900 dark:text-white">
                    Informações do Workspace
                </h2>
                <div className="flex space-x-2">
                    <button
                        type="button"
                        onClick={() => reset()}
                        className="hidden px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        form="workspace-info-form"
                        disabled={processing}
                        className="text-teal-600 bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 dark:text-teal-400 dark:hover:bg-teal-900/50 px-4 py-2 rounded-lg text-sm disabled:opacity-50"
                    >
                        <i className="fas fa-save mr-2"></i>
                        {processing ? 'Salvando...' : 'Salvar'}
                    </button>
                </div>
            </div>

            <form id="workspace-info-form" onSubmit={handleSubmit} className="space-y-6">
                {showMergeWarning && (
                    <div className="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
                        <div className="flex items-center">
                            <i className="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 mr-2"></i>
                            <span className="text-amber-800 dark:text-amber-300 text-sm">
                                Ao mudar para Tópico Único, todos os tópicos serão mesclados em um único.
                            </span>
                        </div>
                    </div>
                )}

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label htmlFor="workspace-title" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nome do Workspace *
                        </label>
                        <input
                            type="text"
                            id="workspace-title"
                            value={data.title}
                            onChange={e => setData('title', e.target.value)}
                            required
                            maxLength={100}
                            className="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors"
                            placeholder="Nome do workspace"
                        />
                        <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Máximo de 100 caracteres
                        </p>
                        {errors.title && (
                            <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.title}</p>
                        )}
                    </div>

                    <div>
                        <label htmlFor="workspace-type" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tipo do Workspace *
                        </label>
                        <select
                            id="workspace-type"
                            value={data.type_workspace_id}
                            onChange={e => setData('type_workspace_id', parseInt(e.target.value))}
                            required
                            className="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors"
                        >
                            <option value={1}>Tópico Único</option>
                            <option value={2}>Múltiplos Tópicos</option>
                        </select>
                        <div className="mt-2 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                            <div className="flex items-center">
                                <i className="fas fa-info-circle mr-2 text-teal-500"></i>
                                <span><strong>Tópico Único:</strong> Ideal para dados simples e organizados</span>
                            </div>
                            <div className="flex items-center">
                                <i className="fas fa-info-circle mr-2 text-teal-500"></i>
                                <span><strong>Múltiplos Tópicos:</strong> Ideal para categorizar diferentes tipos de dados</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label htmlFor="workspace-description" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Descrição
                    </label>
                    <textarea
                        id="workspace-description"
                        value={data.description}
                        onChange={e => setData('description', e.target.value)}
                        rows={3}
                        maxLength={500}
                        className="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors resize-none"
                        placeholder="Descreva o propósito deste workspace..."
                    />
                    <div className="mt-1 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>Máximo de 500 caracteres</span>
                        <span className={charCount > 450 ? 'text-red-500' : ''}>
                            {charCount}/500
                        </span>
                    </div>
                    {errors.description && (
                        <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.description}</p>
                    )}
                </div>

                <div className="pt-4 border-t border-gray-200 dark:border-gray-600">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <label className="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                Data de Criação
                            </label>
                            <p className="mt-1 text-gray-900 dark:text-white">
                                {new Date(workspace.created_at).toLocaleDateString('pt-BR', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </p>
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                Última Atualização
                            </label>
                            <p className="mt-1 text-gray-900 dark:text-white">
                                {new Date(workspace.updated_at).toLocaleDateString('pt-BR', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    );
}