// resources/js/Components/Workspace/StructuredRecordsTable.jsx
import React, { useState, useEffect } from 'react';
import { usePage, router } from '@inertiajs/react';

export default function StructuredRecordsTable({ topic, topicLimits, auth, translations, availableStructures = [] }) {
    const [selectedStructureId, setSelectedStructureId] = useState(topic.structure?.id || '');
    const [records, setRecords] = useState([]);
    const [savingStates, setSavingStates] = useState({});
    const [isAddingRecord, setIsAddingRecord] = useState(false);
    const [fieldValues, setFieldValues] = useState({});

    // Carregar registros
    useEffect(() => {
        if (topic.records) {
            setRecords(topic.records);
            
            // Inicializar valores
            const initialValues = {};
            topic.records.forEach(record => {
                record.field_values?.forEach(fieldValue => {
                    const key = `${record.id}_${fieldValue.structure_field_id}`;
                    initialValues[key] = fieldValue.field_value || '';
                });
            });
            setFieldValues(initialValues);
        }
    }, [topic.records]);

    const handleFieldChange = async (recordId, fieldId, value) => {
        const key = `${recordId}_${fieldId}`;
        setFieldValues(prev => ({ ...prev, [key]: value }));
        
        setSavingStates(prev => ({ ...prev, [key]: 'saving' }));
        
        try {
            await router.put(route('record.field.update', { record: recordId, field: fieldId }), {
                value: value
            }, {preserveScroll: true});
            
            setSavingStates(prev => ({ ...prev, [key]: 'saved' }));
            
            setTimeout(() => {
                setSavingStates(prev => ({ ...prev, [key]: null }));
            }, 2000);
            
        } catch (error) {
            setSavingStates(prev => ({ ...prev, [key]: 'error' }));
            console.error('Erro ao salvar campo:', error);
        }
    };


    const handleAddRecord = () => {
        router.post(route('topic.store-record'), {
            topic_id: topic.id,
            record_order: topic.records?.length + 1 || 1,
            field_values: {}
        }, {
            preserveScroll: true,
            onSuccess: (page) => {
                // AQUI ficam os dados retornados pelo backend
                const { success, message, data } = page.props.ziggy.response;

                if (success) {
                    handleAddStructureFields();
                }
            },
            onError: () => {
                alert("Erro ao adicionar registro.");
            }
        });
    };

    const handleDeleteRecord = async (recordId) => {
        if (confirm("Tem certeza que deseja excluir este registro?")) {
            router.delete(route('records.destroy', recordId), {
                preserveScroll: true,
                onSuccess: () => {
                    // Remover local sem impedir o flash
                    setRecords(prev => prev.filter(r => r.id !== recordId));
                }
            });
        }
    };

    const getSavingStatusIcon = (status) => {
        switch (status) {
            case 'saving':
                return <i className="fas fa-spinner fa-spin text-yellow-400 text-xs"></i>;
            case 'saved':
                return <i className="fas fa-check text-teal-400 text-xs"></i>;
            case 'error':
                return <i className="fas fa-exclamation-triangle text-red-400 text-xs"></i>;
            default:
                return null;
        }
    };

    const structureFields = topic.structure?.fields || [];
    const canAddMore = topicLimits.canAddMoreRecords;

    if (!topic.structure) {
        return (
            <div className="text-center py-12">
                <i className="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
                <h3 className="text-lg font-medium text-white mb-2">Nenhuma estrutura vinculada</h3>

                <p className="text-gray-400 mb-4">
                    Selecione uma estrutura para gerenciar registros.
                </p>

                <select
                    value={selectedStructureId}
                    onChange={async (e) => {
                        const structureId = e.target.value;
                        setSelectedStructureId(structureId);

                        await router.put(route('topic.structure.update', { id: topic.id }), {
                            structure_id: structureId
                        }, {
                            preserveScroll: true,
                            onSuccess: (page) => {
                                const updated = availableStructures.find(s => s.id == structureId);
                                topic.structure = updated;
                            }
                        });
                    }}
                    className="px-4 py-2 rounded-lg bg-slate-700 text-white"
                >
                    <option value="">Selecione uma estrutura</option>
                {
                console.log(availableStructures)}
                    {availableStructures.map(structure => (
                        <option key={structure.id} value={structure.id}>
                            {structure.name}
                        </option>
                    ))}
                </select>
            </div>
        );
    }

    return (
        <div className="overflow-x-auto">
            {/* Cabeçalho da Tabela */}
            <div className="min-w-full bg-slate-700/50 border-b border-slate-600">
                <div className="flex">
                    {/* Coluna de Número/Ordem */}
                    <div className="w-16 py-4 px-4 text-sm font-semibold text-gray-400 border-r border-slate-600">
                        #
                    </div>
                    
                    {/* Colunas dos Campos da Estrutura */}
                    {structureFields.map((field) => (
                        <div 
                            key={field.id}
                            className="flex-1 py-4 px-4 text-sm font-semibold text-gray-400 border-r border-slate-600 min-w-40"
                        >
                            <div className="flex items-center space-x-2">
                                <span>{field.name}</span>
                                {field.is_required && (
                                    <span className="text-red-400 text-xs">*</span>
                                )}
                                <span className="text-xs text-gray-500 bg-slate-600 px-2 py-1 rounded">
                                    {field.type}
                                </span>
                            </div>
                        </div>
                    ))}
                    
                    {/* Coluna de Ações */}
                    <div className="w-20 py-4 px-4 text-sm font-semibold text-gray-400">
                        Ações
                    </div>
                </div>
            </div>

            {/* Corpo da Tabela */}
            <div className="min-w-full">
                {records.map((record, index) => (
                    <div 
                        key={record.id}
                        className="flex border-b border-slate-700/50 hover:bg-slate-700/20 transition-colors"
                    >
                        {/* Número do Registro */}
                        <div className="w-16 py-4 px-4 border-r border-slate-600 flex items-center justify-center">
                            <span className="text-sm text-gray-400 font-medium">
                                {index + 1}
                            </span>
                        </div>
                        
                        {/* Campos do Registro */}
                        {structureFields.map((field) => {
                            const key = `${record.id}_${field.id}`;
                            const value = fieldValues[key] || '';
                            const savingState = savingStates[key];
                            
                            return (
                                <div 
                                    key={field.id}
                                    className="flex-1 py-4 px-4 border-r border-slate-600 min-w-40"
                                >
                                    <div className="relative">
                                        <input
                                            type="text"
                                            value={value}
                                            onChange={(e) => handleFieldChange(record.id, field.id, e.target.value)}
                                            className="w-full px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent text-sm"
                                            placeholder={`Digite ${field.name.toLowerCase()}`}
                                        />
                                        
                                        {/* Status de Salvamento */}
                                        {savingState && (
                                            <div className="absolute right-2 top-1/2 transform -translate-y-1/2">
                                                {getSavingStatusIcon(savingState)}
                                            </div>
                                        )}
                                    </div>
                                    
                                    {/* Label do Status */}
                                    {savingState === 'saving' && (
                                        <div className="text-xs text-yellow-400 mt-1">Salvando...</div>
                                    )}
                                    {savingState === 'saved' && (
                                        <div className="text-xs text-teal-400 mt-1">Salvo</div>
                                    )}
                                    {savingState === 'error' && (
                                        <div className="text-xs text-red-400 mt-1">Erro ao salvar</div>
                                    )}
                                </div>
                            );
                        })}
                        
                        {/* Ações */}
                        <div className="w-20 py-4 px-4 flex items-center justify-center">
                            <button
                                onClick={() => handleDeleteRecord(record.id)}
                                className="p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors"
                                title="Excluir registro"
                            >
                                <i className="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                ))}
                
                {/* Botão para Adicionar Novo Registro */}
                {canAddMore && (
                    <div className="flex border-b border-slate-700/50">
                        <div className="w-16 py-4 px-4 border-r border-slate-600"></div>
                        
                        <div className="flex-1 py-4 px-4 border-r border-slate-600">
                            <button
                                onClick={handleAddRecord}
                                disabled={isAddingRecord}
                                className="w-full py-3 bg-slate-700 hover:bg-slate-600 disabled:bg-slate-800 text-gray-300 rounded-lg transition-colors flex items-center justify-center space-x-2 text-sm"
                            >
                                {isAddingRecord ? (
                                    <>
                                        <i className="fas fa-spinner fa-spin"></i>
                                        <span>Adicionando...</span>
                                    </>
                                ) : (
                                    <>
                                        <i className="fas fa-plus"></i>
                                        <span>Adicionar Novo Registro</span>
                                    </>
                                )}
                            </button>
                        </div>
                        
                        {/* Preencher colunas restantes */}
                        {structureFields.slice(1).map((field, index) => (
                            <div 
                                key={field.id}
                                className="flex-1 py-4 px-4 border-r border-slate-600 min-w-40"
                            ></div>
                        ))}
                        
                        <div className="w-20 py-4 px-4"></div>
                    </div>
                )}
            </div>
            
            {/* Estado Vazio */}
            {records.length === 0 && (
                <div className="text-center py-12">
                    <i className="fas fa-inbox text-4xl text-gray-500 mb-4"></i>
                    <h3 className="text-lg font-medium text-white mb-2">Nenhum registro criado</h3>
                    <p className="text-gray-400 mb-4">
                        {canAddMore 
                            ? 'Adicione o primeiro registro a este tópico'
                            : 'Limite de registros atingido'
                        }
                    </p>
                    {canAddMore && (
                        <button
                            onClick={handleAddRecord}
                            disabled={isAddingRecord}
                            className="px-6 py-2 bg-teal-500 hover:bg-teal-400 disabled:bg-teal-600 text-slate-900 font-medium rounded-xl transition-colors"
                        >
                            {isAddingRecord ? 'Adicionando...' : 'Adicionar Primeiro Registro'}
                        </button>
                    )}
                </div>
            )}
            
            {/* Contador de Registros */}
            <div className="bg-slate-700/30 px-6 py-3 border-t border-slate-600">
                <div className="flex justify-between items-center text-sm text-gray-400">
                    <span>
                        {records.length} registro{records.length !== 1 ? 's' : ''} encontrado{records.length !== 1 ? 's' : ''}
                    </span>
                    {!topicLimits.isUnlimited && (
                        <span>
                            Limite: {topicLimits.currentRecordsCount}/{topicLimits.recordsLimit}
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
}