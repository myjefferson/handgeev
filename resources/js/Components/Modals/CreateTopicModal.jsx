// resources/js/Components/Modals/CreateTopicModal.jsx
import React, { useState } from 'react';
import { useForm, router } from '@inertiajs/react';

export default function CreateTopicModal({ isOpen, onClose, workspace, availableStructures }) {
    const { data, setData, post, processing, errors } = useForm({
        workspace_id: workspace.id,
        title: '',
        structure_id: '',
        order: workspace.topics.length + 1
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('topic.store'), {
            onSuccess: () => {
                onClose();
                setData({
                    workspace_id: workspace.id,
                    title: '',
                    structure_id: '',
                    order: workspace.topics.length + 1
                });
            }
        });
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div className="bg-slate-800 rounded-2xl border border-slate-700 w-full max-w-md">
                <div className="p-6 border-b border-slate-700">
                    <h2 className="text-xl font-semibold text-white">Criar Novo Tópico</h2>
                    <p className="text-gray-400 mt-1">Escolha um nome e uma estrutura para o tópico</p>
                </div>

                <form onSubmit={handleSubmit} className="p-6 space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-300 mb-2">
                            Nome do Tópico *
                        </label>
                        <input
                            type="text"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            className="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400"
                            placeholder="Ex: Produtos, Clientes, Pedidos"
                            required
                        />
                        {errors.title && <p className="text-red-400 text-sm mt-1">{errors.title}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-300 mb-2">
                            Estrutura (Opcional)
                        </label>
                        <select
                            value={data.structure_id}
                            onChange={(e) => setData('structure_id', e.target.value)}
                            className="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-teal-400"
                        >
                            <option value="">Tópico Livre (sem estrutura)</option>
                            {availableStructures.map(structure => (
                                <option key={structure.id} value={structure.id}>
                                    {structure.name} ({structure.fields_count} campos)
                                </option>
                            ))}
                        </select>
                        <p className="text-gray-400 text-xs mt-2">
                            {data.structure_id ? 
                                'Tópico estruturado com campos pré-definidos' : 
                                'Tópico livre com campos personalizados'
                            }
                        </p>
                    </div>

                    <div className="flex justify-end space-x-3 pt-4">
                        <button
                            type="button"
                            onClick={onClose}
                            className="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-medium rounded-xl transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors disabled:opacity-50"
                        >
                            {processing ? 'Criando...' : 'Criar Tópico'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}