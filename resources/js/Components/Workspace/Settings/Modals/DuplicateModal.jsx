// resources/js/Components/Workspace/Settings/Modals/DuplicateModal.jsx
import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function DuplicateModal({ isOpen, onClose, workspace }) {
    const { data, setData, post, processing, errors } = useForm({
        new_title: `${workspace.title} - Cópia`
    });

    const topicsCount = workspace.topics?.length || 0;
    const fieldsCount = workspace.topics?.reduce((total, topic) => total + (topic.fields?.length || 0), 0) || 0;

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        try {
            await post(route('workspace.duplicate', workspace.id), {
                preserveScroll: true,
                onSuccess: () => {
                    onClose();
                }
            });
        } catch (error) {
            // Erro será tratado pelo Inertia
        }
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="relative p-4 w-full max-w-md">
                <div className="relative bg-slate-800 rounded-lg shadow border border-slate-700">
                    <div className="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-slate-700">
                        <h3 className="text-lg font-semibold text-white">
                            📋 Duplicar Workspace
                        </h3>
                        <button
                            type="button"
                            onClick={onClose}
                            className="text-slate-400 bg-transparent hover:bg-slate-700 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        >
                            <i className="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form onSubmit={handleSubmit}>
                        <div className="p-4 md:p-5">
                            <div className="mb-4">
                                <label htmlFor="new_title" className="block mb-2 text-sm font-medium text-slate-300">
                                    Nome do novo workspace
                                </label>
                                <input
                                    type="text"
                                    id="new_title"
                                    value={data.new_title}
                                    onChange={e => setData('new_title', e.target.value)}
                                    className="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                                    placeholder="Ex: Meu Workspace - Cópia"
                                    required
                                    autoComplete="off"
                                />
                                <p className="mt-1 text-xs text-slate-400">
                                    Escolha um nome único para o workspace duplicado
                                </p>
                                {errors.new_title && (
                                    <p className="mt-1 text-sm text-red-400">{errors.new_title}</p>
                                )}
                            </div>
                            
                            <div className="bg-slate-900/50 rounded-lg p-3 mb-4">
                                <p className="text-sm text-slate-300">
                                    <span className="font-semibold">Serão duplicados:</span>
                                </p>
                                <ul className="text-xs text-slate-400 mt-1 space-y-1">
                                    <li>• {topicsCount} tópicos</li>
                                    <li>• {fieldsCount} campos</li>
                                </ul>
                            </div>

                            {errors.message && (
                                <div className="p-3 mb-3 text-sm text-red-400 bg-red-900/20 rounded-lg">
                                    {errors.message}
                                </div>
                            )}
                        </div>
                        
                        <div className="flex items-center justify-end p-4 md:p-5 border-t border-slate-700 rounded-b">
                            <button
                                type="button"
                                onClick={onClose}
                                className="py-2.5 px-5 text-sm font-medium text-slate-300 focus:outline-none bg-slate-700 rounded-lg border border-slate-600 hover:bg-slate-600"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="flex items-center py-2.5 px-5 ms-3 text-sm font-medium text-white bg-cyan-600 rounded-lg hover:bg-cyan-700 focus:outline-none focus:ring-4 focus:ring-cyan-800 disabled:opacity-50"
                            >
                                <i className="fas fa-copy mr-2"></i>
                                {processing ? 'Duplicando...' : 'Duplicar'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}