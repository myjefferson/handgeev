// resources/js/Components/Workspace/StructureFieldsManager.jsx
import React, { useState, useEffect } from 'react';
import { usePage, router } from '@inertiajs/react';

export default function StructureFieldsManager({ topic, topicLimits, auth }) {
    const { translations } = usePage().props;
    const [fields, setFields] = useState([]);
    const [isAddingField, setIsAddingField] = useState(false);
    const [newField, setNewField] = useState({
        name: '',
        type: 'text',
        default_value: '',
        is_required: false,
        order: 0
    });
    const [editingField, setEditingField] = useState(null);
    const [savingStates, setSavingStates] = useState({});

    // Carregar campos da estrutura
    useEffect(() => {
        if (topic.structure?.fields) {
            setFields([...topic.structure.fields].sort((a, b) => a.order - b.order));
        }
    }, [topic.structure]);

    const handleAddField = async () => {
        if (!topicLimits.canCreateField) return;

        setIsAddingField(true);
        
        try {
            const response = await router.post(route('structure.fields.store', { structure: topic.structure.id }), {
                ...newField,
                order: fields.length + 1
            });

            if (response.data.success) {
                // Atualizar lista de campos
                setFields(prev => [...prev, response.data.field]);
                // Resetar formulário
                setNewField({
                    name: '',
                    type: 'text',
                    default_value: '',
                    is_required: false,
                    order: 0
                });
            }
        } catch (error) {
            console.error('Erro ao adicionar campo:', error);
        } finally {
            setIsAddingField(false);
        }
    };

    const handleUpdateField = async (fieldId, data) => {
        setSavingStates(prev => ({ ...prev, [fieldId]: 'saving' }));
        
        try {
            const response = await router.put(route('structure.fields.update', { 
                structure: topic.structure.id, 
                field: fieldId 
            }), data);

            if (response.data.success) {
                // Atualizar campo na lista
                setFields(prev => prev.map(field => 
                    field.id === fieldId ? { ...field, ...data } : field
                ));
                setSavingStates(prev => ({ ...prev, [fieldId]: 'saved' }));
            }
        } catch (error) {
            console.error('Erro ao atualizar campo:', error);
            setSavingStates(prev => ({ ...prev, [fieldId]: 'error' }));
        }

        setTimeout(() => {
            setSavingStates(prev => ({ ...prev, [fieldId]: null }));
        }, 2000);
    };

    const handleDeleteField = async (fieldId) => {
        if (!confirm('Tem certeza que deseja excluir este campo? Esta ação não pode ser desfeita.')) {
            return;
        }

        try {
            const response = await router.delete(route('structure.fields.destroy', { 
                structure: topic.structure.id, 
                field: fieldId 
            }));

            if (response.data.success) {
                // Remover campo da lista
                setFields(prev => prev.filter(field => field.id !== fieldId));
            }
        } catch (error) {
            console.error('Erro ao excluir campo:', error);
        }
    };

    const handleReorderFields = async (fieldId, newOrder) => {
        try {
            await router.put(route('structure.fields.reorder', { 
                structure: topic.structure.id, 
                field: fieldId 
            }), { order: newOrder });

            // Reordenar localmente
            const updatedFields = [...fields];
            const fieldIndex = updatedFields.findIndex(f => f.id === fieldId);
            if (fieldIndex > -1) {
                const [movedField] = updatedFields.splice(fieldIndex, 1);
                updatedFields.splice(newOrder - 1, 0, movedField);
                
                // Atualizar ordens
                updatedFields.forEach((field, index) => {
                    field.order = index + 1;
                });
                
                setFields(updatedFields);
            }
        } catch (error) {
            console.error('Erro ao reordenar campos:', error);
        }
    };

    const getFieldTypeIcon = (type) => {
        const icons = {
            text: 'fa-font',
            number: 'fa-hashtag',
            decimal: 'fa-divide',
            boolean: 'fa-toggle-on',
            date: 'fa-calendar',
            datetime: 'fa-clock',
            email: 'fa-envelope',
            url: 'fa-link',
            json: 'fa-code'
        };
        return icons[type] || 'fa-question';
    };

    const canAddMore = topicLimits.canCreateField;

    return (
        <div className="p-6">
            {/* Header */}
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h3 className="text-lg font-semibold text-white">
                        Campos da Estrutura
                    </h3>
                    <p className="text-gray-400 text-sm">
                        Gerencie os campos disponíveis para os registros deste tópico
                    </p>
                </div>
                
                {canAddMore && (
                    <button
                        onClick={() => setIsAddingField(true)}
                        className="px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors"
                    >
                        <i className="fas fa-plus mr-2"></i>
                        Adicionar Campo
                    </button>
                )}
            </div>

            {/* Formulário para Adicionar Campo */}
            {isAddingField && (
                <div className="bg-slate-700/50 border border-slate-600 rounded-lg p-4 mb-6">
                    <h4 className="text-white font-medium mb-4">Novo Campo</h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-300 mb-1">
                                Nome do Campo *
                            </label>
                            <input
                                type="text"
                                value={newField.name}
                                onChange={(e) => setNewField(prev => ({ ...prev, name: e.target.value }))}
                                className="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                                placeholder="Ex: Nome, Email, Idade..."
                            />
                        </div>
                        
                        <div>
                            <label className="block text-sm font-medium text-gray-300 mb-1">
                                Tipo *
                            </label>
                            <select
                                value={newField.type}
                                onChange={(e) => setNewField(prev => ({ ...prev, type: e.target.value }))}
                                className="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                            >
                                <option value="text">Texto</option>
                                <option value="number">Número</option>
                                <option value="decimal">Decimal</option>
                                <option value="boolean">Booleano</option>
                                <option value="date">Data</option>
                                <option value="datetime">Data e Hora</option>
                                <option value="email">E-mail</option>
                                <option value="url">URL</option>
                                <option value="json">JSON</option>
                            </select>
                        </div>
                        
                        <div>
                            <label className="block text-sm font-medium text-gray-300 mb-1">
                                Valor Padrão
                            </label>
                            <input
                                type="text"
                                value={newField.default_value}
                                onChange={(e) => setNewField(prev => ({ ...prev, default_value: e.target.value }))}
                                className="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent"
                                placeholder="Valor inicial..."
                            />
                        </div>
                        
                        <div className="flex items-end">
                            <label className="flex items-center space-x-2">
                                <input
                                    type="checkbox"
                                    checked={newField.is_required}
                                    onChange={(e) => setNewField(prev => ({ ...prev, is_required: e.target.checked }))}
                                    className="rounded bg-slate-600 border-slate-500 text-teal-400 focus:ring-teal-400"
                                />
                                <span className="text-sm text-gray-300">Campo Obrigatório</span>
                            </label>
                        </div>
                    </div>
                    
                    <div className="flex justify-end space-x-3">
                        <button
                            onClick={() => setIsAddingField(false)}
                            className="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            onClick={handleAddField}
                            disabled={!newField.name || isAddingField}
                            className="px-4 py-2 bg-teal-500 hover:bg-teal-400 disabled:bg-teal-600 text-slate-900 font-medium rounded-lg transition-colors"
                        >
                            {isAddingField ? 'Adicionando...' : 'Adicionar Campo'}
                        </button>
                    </div>
                </div>
            )}

            {/* Lista de Campos */}
            <div className="space-y-3">
                {fields.map((field, index) => (
                    <div
                        key={field.id}
                        className="bg-slate-700/50 border border-slate-600 rounded-lg p-4"
                    >
                        <div className="flex items-center justify-between">
                            <div className="flex items-center space-x-4 flex-1">
                                {/* Ordem */}
                                <div className="flex items-center space-x-2">
                                    <button
                                        onClick={() => handleReorderFields(field.id, Math.max(1, field.order - 1))}
                                        disabled={field.order === 1}
                                        className="p-1 text-gray-400 hover:text-white disabled:opacity-30"
                                    >
                                        <i className="fas fa-chevron-up"></i>
                                    </button>
                                    <span className="text-sm text-gray-400 w-6 text-center">
                                        {field.order}
                                    </span>
                                    <button
                                        onClick={() => handleReorderFields(field.id, field.order + 1)}
                                        disabled={field.order === fields.length}
                                        className="p-1 text-gray-400 hover:text-white disabled:opacity-30"
                                    >
                                        <i className="fas fa-chevron-down"></i>
                                    </button>
                                </div>

                                {/* Ícone e Nome */}
                                <div className="flex items-center space-x-3">
                                    <i className={`fas ${getFieldTypeIcon(field.type)} text-teal-400`}></i>
                                    <div>
                                        <h4 className="text-white font-medium">
                                            {field.name}
                                            {field.is_required && (
                                                <span className="text-red-400 text-xs ml-1">*</span>
                                            )}
                                        </h4>
                                        <p className="text-gray-400 text-sm">
                                            {field.type} {field.default_value && `• Padrão: ${field.default_value}`}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* Ações */}
                            <div className="flex items-center space-x-2">
                                {/* Status de Salvamento */}
                                {savingStates[field.id] === 'saving' && (
                                    <i className="fas fa-spinner fa-spin text-yellow-400"></i>
                                )}
                                {savingStates[field.id] === 'saved' && (
                                    <i className="fas fa-check text-teal-400"></i>
                                )}
                                {savingStates[field.id] === 'error' && (
                                    <i className="fas fa-exclamation-triangle text-red-400"></i>
                                )}
                                
                                <button
                                    onClick={() => handleDeleteField(field.id)}
                                    className="p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors"
                                    title="Excluir campo"
                                >
                                    <i className="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                ))}

                {fields.length === 0 && (
                    <div className="text-center py-12">
                        <i className="fas fa-list text-4xl text-gray-500 mb-4"></i>
                        <h3 className="text-lg font-medium text-white mb-2">Nenhum campo criado</h3>
                        <p className="text-gray-400 mb-4">
                            {canAddMore 
                                ? 'Adicione o primeiro campo à estrutura'
                                : 'Limite de campos atingido'
                            }
                        </p>
                        {canAddMore && (
                            <button
                                onClick={() => setIsAddingField(true)}
                                className="px-6 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors"
                            >
                                Adicionar Primeiro Campo
                            </button>
                        )}
                    </div>
                )}
            </div>

            {/* Contador de Campos */}
            <div className="mt-6 pt-4 border-t border-slate-600">
                <div className="flex justify-between items-center text-sm text-gray-400">
                    <span>
                        {fields.length} campo{fields.length !== 1 ? 's' : ''} na estrutura
                    </span>
                    {!topicLimits.isUnlimited && (
                        <span>
                            Limite: {topicLimits.currentFieldsCount}/{topicLimits.fieldsLimit}
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
}