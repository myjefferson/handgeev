import React, { useState, useEffect } from 'react';
import { useForm, router } from '@inertiajs/react';

export default function StructuredFieldsGrid({ topic, topicLimits, auth }) {
    const [editingField, setEditingField] = useState(null);
    const [saving, setSaving] = useState(false);

    // Carrega os campos do tópico
    const { data, setData, put, processing, errors } = useForm({
        fields: topic.fields || []
    });

    // Atualiza automaticamente quando o tópico muda
    useEffect(() => {
        setData('fields', topic.fields || []);
    }, [topic]);

    const handleFieldUpdate = async (fieldId, newValue) => {
        if (!fieldId) return;

        setSaving(true);
        setEditingField(null);

        try {
            await router.put(route('field.update', fieldId), {
                value: newValue,
                key_name: data.fields.find(f => f.id === fieldId)?.key_name,
                type: data.fields.find(f => f.id === fieldId)?.type,
                topic_id: topic.id
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    // Atualiza o estado local
                    const updatedFields = data.fields.map(field =>
                        field.id === fieldId ? { ...field, value: newValue } : field
                    );
                    setData('fields', updatedFields);
                }
            });
        } catch (error) {
            console.error('Erro ao atualizar campo:', error);
        } finally {
            setSaving(false);
        }
    };

    const handleAddField = async () => {
        if (!topicLimits.canAddMore) return;

        try {
            await router.post(route('fields.store'), {
                topic_id: topic.id,
                key_name: `novo_campo_${Date.now()}`,
                value: '',
                type: 'text',
                is_visible: true
            }, {
                preserveScroll: true,
                onSuccess: () => {
                    // Recarrega a página para atualizar os campos
                    router.reload({ only: ['workspace'] });
                }
            });
        } catch (error) {
            console.error('Erro ao adicionar campo:', error);
        }
    };

    const getFieldIcon = (type) => {
        const icons = {
            text: 'fa-font',
            number: 'fa-hashtag',
            decimal: 'fa-dollar-sign',
            boolean: 'fa-toggle-on',
            date: 'fa-calendar',
            datetime: 'fa-clock',
            email: 'fa-envelope',
            url: 'fa-link',
            json: 'fa-code'
        };
        return icons[type] || 'fa-font';
    };

    const renderFieldInput = (field) => {
        if (editingField === field.id) {
            return (
                <input
                    type="text"
                    defaultValue={field.value}
                    onBlur={(e) => handleFieldUpdate(field.id, e.target.value)}
                    onKeyPress={(e) => {
                        if (e.key === 'Enter') {
                            handleFieldUpdate(field.id, e.target.value);
                        }
                    }}
                    className="w-full px-3 py-2 bg-slate-700 border border-teal-400 rounded-lg text-white focus:outline-none focus:ring-1 focus:ring-teal-400"
                    autoFocus
                />
            );
        }

        return (
            <div 
                onClick={() => setEditingField(field.id)}
                className="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white cursor-text hover:border-slate-500 transition-colors min-h-[42px] flex items-center"
            >
                {field.value || <span className="text-gray-400">Clique para editar</span>}
            </div>
        );
    };

    return (
        <div className="p-6">
            {/* Header */}
            <div className="flex items-center justify-between mb-6">
                <div>
                    <h3 className="text-lg font-semibold text-white">
                        {topic.title}
                        {topic.structure && (
                            <span className="ml-2 text-sm text-teal-400 bg-teal-400/10 px-2 py-1 rounded-full">
                                Estrutura: {topic.structure.name}
                            </span>
                        )}
                    </h3>
                    <p className="text-sm text-gray-400 mt-1">
                        {topic.is_free ? 'Tópico Livre' : 'Tópico Estruturado'}
                    </p>
                </div>

                {topic.is_free && topicLimits.canAddMore && (
                    <button
                        onClick={handleAddField}
                        disabled={processing}
                        className="px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-lg transition-colors disabled:opacity-50"
                    >
                        <i className="fas fa-plus mr-2"></i>
                        Novo Campo
                    </button>
                )}
            </div>

            {/* Grid de Campos Responsivo */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                {data.fields.map((field) => (
                    <div 
                        key={field.id} 
                        className="bg-slate-700/50 rounded-lg p-4 border border-slate-600 hover:border-slate-500 transition-colors"
                    >
                        {/* Header do Campo */}
                        <div className="flex items-center justify-between mb-3">
                            <div className="flex items-center space-x-2">
                                <div className="w-8 h-8 rounded-full bg-slate-600 flex items-center justify-center">
                                    <i className={`fas ${getFieldIcon(field.type)} text-teal-400 text-sm`}></i>
                                </div>
                                <div>
                                    <h4 className="font-medium text-white text-sm">
                                        {field.key_name}
                                    </h4>
                                    <p className="text-xs text-gray-400 capitalize">
                                        {field.type}
                                    </p>
                                </div>
                            </div>
                            
                            {field.is_required && (
                                <span className="text-xs bg-red-500 text-white px-2 py-1 rounded">Obrigatório</span>
                            )}
                        </div>

                        {/* Input do Campo */}
                        {renderFieldInput(field)}

                        {/* Status de Salvamento */}
                        {saving && editingField === field.id && (
                            <div className="flex items-center space-x-2 mt-2">
                                <div className="w-3 h-3 border-2 border-teal-400 border-t-transparent rounded-full animate-spin"></div>
                                <span className="text-xs text-teal-400">Salvando...</span>
                            </div>
                        )}
                    </div>
                ))}
            </div>

            {/* Mensagem quando não há campos */}
            {data.fields.length === 0 && (
                <div className="text-center py-12">
                    <i className="fas fa-inbox text-4xl text-gray-500 mb-4"></i>
                    <h3 className="text-lg font-medium text-white mb-2">Nenhum campo criado</h3>
                    <p className="text-gray-400 mb-4">
                        {topic.is_free 
                            ? 'Adicione campos personalizados ao seu tópico livre'
                            : 'Este tópico está vinculado a uma estrutura, mas ainda não possui campos'
                        }
                    </p>
                    {topic.is_free && topicLimits.canAddMore && (
                        <button
                            onClick={handleAddField}
                            className="px-6 py-3 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors"
                        >
                            <i className="fas fa-plus mr-2"></i>
                            Adicionar Primeiro Campo
                        </button>
                    )}
                </div>
            )}

            {/* Indicador de Limite */}
            {!topicLimits.isUnlimited && (
                <div className="mt-6 p-4 bg-slate-700/50 rounded-lg border border-slate-600">
                    <div className="flex items-center justify-between text-sm">
                        <span className="text-gray-300">
                            Campos utilizados: {data.fields.length}/{topicLimits.fieldsLimit}
                        </span>
                        <span className={`font-medium ${topicLimits.canAddMore ? 'text-teal-400' : 'text-yellow-400'}`}>
                            {topicLimits.remainingFields} restantes
                        </span>
                    </div>
                    <div className="w-full bg-slate-600 rounded-full h-2 mt-2">
                        <div 
                            className="bg-teal-400 h-2 rounded-full transition-all duration-300"
                            style={{ 
                                width: `${Math.min((data.fields.length / topicLimits.fieldsLimit) * 100, 100)}%` 
                            }}
                        ></div>
                    </div>
                </div>
            )}
        </div>
    );
}