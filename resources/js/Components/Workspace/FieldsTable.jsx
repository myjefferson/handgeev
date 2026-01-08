// resources/js/Components/Workspace/FieldsTable.jsx
import React, { useState, useEffect } from 'react';
import { usePage, router } from '@inertiajs/react';

export default function FieldsTable({ topic, topicLimits, auth, translations }) {
    const { fields } = topic;
    const [savingStates, setSavingStates] = useState({});
    const [fieldValues, setFieldValues] = useState({});
    const [isAddingField, setIsAddingField] = useState(false);

    // Inicializar valores dos campos
    useEffect(() => {
        const initialValues = {};
        fields.forEach(field => {
            initialValues[field.id] = field.value || '';
        });
        setFieldValues(initialValues);
    }, [fields]);

    const handleFieldChange = async (fieldId, value) => {
        setFieldValues(prev => ({ ...prev, [fieldId]: value }));
        
        // Mostrar estado de salvamento
        setSavingStates(prev => ({ ...prev, [fieldId]: 'saving' }));
        
        try {
            await router.put(route('field.update', fieldId), {
                value: value,
                key_name: fields.find(f => f.id === fieldId)?.key_name,
                type: fields.find(f => f.id === fieldId)?.type
            });
            
            setSavingStates(prev => ({ ...prev, [fieldId]: 'saved' }));
            
            // Limpar status após 2 segundos
            setTimeout(() => {
                setSavingStates(prev => ({ ...prev, [fieldId]: null }));
            }, 2000);
            
        } catch (error) {
            setSavingStates(prev => ({ ...prev, [fieldId]: 'error' }));
            console.error('Erro ao salvar campo:', error);
        }
    };

    const handleAddField = async () => {
        if (!topicLimits.canAddMore) return;
        
        setIsAddingField(true);
        
        try {
            const newFieldName = prompt('Digite o nome do novo campo:');
            if (newFieldName && newFieldName.trim()) {
                await router.post(route('field.store'), {
                    topic_id: topic.id,
                    workspace_id: topic.workspace_id,
                    key_name: newFieldName.trim(),
                    value: '',
                    type: 'text',
                    order: fields.length + 1
                });
            }
        } catch (error) {
            console.error('Erro ao adicionar campo:', error);
        } finally {
            setIsAddingField(false);
        }
    };

    const handleDeleteField = async (fieldId) => {
        if (confirm('Tem certeza que deseja excluir este campo?')) {
            try {
                await router.delete(route('field.destroy', fieldId));
            } catch (error) {
                console.error('Erro ao excluir campo:', error);
            }
        }
    };

    const getSavingStatusIcon = (status) => {
        switch (status) {
            case 'saving':
                return <i className="fas fa-spinner fa-spin text-yellow-400"></i>;
            case 'saved':
                return <i className="fas fa-check text-teal-400"></i>;
            case 'error':
                return <i className="fas fa-exclamation-triangle text-red-400"></i>;
            default:
                return null;
        }
    };

    const canAddMore = topicLimits.canAddMore;

    return (
        <div className="overflow-x-auto">
            <table className="w-full">
                <thead>
                    <tr className="border-b border-slate-700">
                        <th className="text-left py-4 px-6 text-sm font-semibold text-gray-400 uppercase">
                            Campo
                        </th>
                        <th className="text-left py-4 px-6 text-sm font-semibold text-gray-400 uppercase">
                            Tipo
                        </th>
                        <th className="text-left py-4 px-6 text-sm font-semibold text-gray-400 uppercase">
                            Valor
                        </th>
                        <th className="text-left py-4 px-6 text-sm font-semibold text-gray-400 uppercase">
                            Status
                        </th>
                        <th className="text-left py-4 px-6 text-sm font-semibold text-gray-400 uppercase">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {fields.map((field) => (
                        <tr key={field.id} className="border-b border-slate-700/50 hover:bg-slate-700/20 transition-colors">
                            <td className="py-4 px-6">
                                <div className="flex items-center space-x-3">
                                    <div className="w-8 h-8 rounded-lg bg-slate-600 flex items-center justify-center">
                                        <i className="fas fa-tag text-sm text-gray-300"></i>
                                    </div>
                                    <div>
                                        <div className="font-medium text-white">{field.key_name}</div>
                                        {field.is_required && (
                                            <span className="text-xs bg-red-500 text-white px-2 py-1 rounded">Obrigatório</span>
                                        )}
                                    </div>
                                </div>
                            </td>
                            
                            <td className="py-4 px-6">
                                <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-600 text-gray-300">
                                    {field.type}
                                </span>
                            </td>
                            
                            <td className="py-4 px-6">
                                <div className="relative">
                                    <input
                                        type="text"
                                        value={fieldValues[field.id] || ''}
                                        onChange={(e) => handleFieldChange(field.id, e.target.value)}
                                        className="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                                        placeholder={`Digite o valor para ${field.key_name}`}
                                    />
                                </div>
                            </td>
                            
                            <td className="py-4 px-6">
                                <div className="flex items-center space-x-2">
                                    {getSavingStatusIcon(savingStates[field.id])}
                                    {savingStates[field.id] === 'saving' && (
                                        <span className="text-xs text-yellow-400">Salvando...</span>
                                    )}
                                    {savingStates[field.id] === 'saved' && (
                                        <span className="text-xs text-teal-400">Salvo</span>
                                    )}
                                    {savingStates[field.id] === 'error' && (
                                        <span className="text-xs text-red-400">Erro</span>
                                    )}
                                </div>
                            </td>
                            
                            <td className="py-4 px-6">
                                <button
                                    onClick={() => handleDeleteField(field.id)}
                                    className="p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors"
                                    title="Excluir campo"
                                >
                                    <i className="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    ))}
                    
                    {/* Linha para adicionar novo campo */}
                    {canAddMore && (
                        <tr className="border-b border-slate-700/50">
                            <td colSpan="5" className="py-4 px-6">
                                <button
                                    onClick={handleAddField}
                                    disabled={isAddingField}
                                    className="w-full py-3 bg-slate-700 hover:bg-slate-600 disabled:bg-slate-800 text-gray-300 rounded-lg transition-colors flex items-center justify-center space-x-2"
                                >
                                    {isAddingField ? (
                                        <>
                                            <i className="fas fa-spinner fa-spin"></i>
                                            <span>Adicionando...</span>
                                        </>
                                    ) : (
                                        <>
                                            <i className="fas fa-plus"></i>
                                            <span>Adicionar Novo Campo</span>
                                        </>
                                    )}
                                </button>
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
            
            {fields.length === 0 && (
                <div className="text-center py-12">
                    <i className="fas fa-inbox text-4xl text-gray-500 mb-4"></i>
                    <h3 className="text-lg font-medium text-white mb-2">Nenhum campo criado</h3>
                    <p className="text-gray-400 mb-4">
                        {canAddMore 
                            ? 'Adicione o primeiro campo a este tópico'
                            : 'Limite de campos atingido'
                        }
                    </p>
                    {canAddMore && (
                        <button
                            onClick={handleAddField}
                            className="px-6 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors"
                        >
                            Adicionar Primeiro Campo
                        </button>
                    )}
                </div>
            )}
        </div>
    );
}