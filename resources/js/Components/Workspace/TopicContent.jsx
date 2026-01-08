// resources/js/Components/Workspace/TopicContent.jsx
import React, { useState, useEffect } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import FieldsTable from './FieldsTable';
import StructuredRecordsTable from './StructuredRecordsTable';
import Alert from '../Alerts/Alert';

export default function TopicContent({ topic, topicLimits, isVisible, workspace, availableStructures }) {
    const { translations, auth, topicsWithLimits } = usePage().props;
    const [isAddingFields, setIsAddingFields] = useState(false);    
    if (!isVisible) return null;

    // Função para adicionar campos da estrutura
    const handleAddStructureFields = async () => {
        if (!topic.structure) {
            alert('Este tópico não possui uma estrutura vinculada.');
            return;
        }
        
        setIsAddingFields(true);
        
        try {
            await router.post(route('topic.add-structure-fields', topic.id), {preserveScroll: true});
        } catch (error) {            
            alert('Erro ao adicionar campos da estrutura. Verifique o console.');
        } finally {
            setIsAddingFields(false);
        }
    };

    return (
        <div className="topic-content animate-fade-in">
            {/* Header do Tópico */}
            <div className="bg-slate-800 rounded-2xl border border-slate-700 p-6 mb-6">
                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div className="flex-1">
                        <div className="flex items-center space-x-3 mb-2">
                            <i className="fas fa-cube text-teal-400 text-xl"></i>
                            <div>
                                <h2 className="text-xl font-semibold text-white">{topic.title}</h2>
                                {topic.structure?.name && (
                                    <p className="text-gray-400 text-sm">
                                        Estrutura: <span className="text-teal-300 hover:underline"><Link href={route('structures.show', topic.structure.id)}>{topic.structure.name}</Link></span>
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>
                    
                    <div className="flex items-center space-x-3">
                        {/* Status do Limite */}
                        <LimitIndicator topicLimits={topicLimits} />
                        
                        {/* Botões de Ação */}
                        <div className="flex space-x-2">
                            {/* Botão Adicionar Campos da Estrutura */}
                            {topic.structure && (
                                <button
                                    onClick={handleAddStructureFields}
                                    disabled={isAddingFields}
                                    className="px-4 py-2 bg-teal-500 hover:bg-teal-400 disabled:bg-teal-600 text-slate-900 font-medium rounded-xl transition-colors text-sm flex items-center"
                                >
                                    {isAddingFields ? (
                                        <>
                                            <i className="fas fa-spinner fa-spin mr-2"></i>
                                            Adicionando...
                                        </>
                                    ) : (
                                        <>
                                            <i className="fas fa-plus mr-2"></i>
                                            Adicionar Campos
                                        </>
                                    )}
                                </button>
                            )}
                            
                            <button
                                onClick={() => {
                                    window.open(route('topics.export', topic.id), '_blank');
                                }}
                                className="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-xl transition-colors text-sm"
                            >
                                <i className="fas fa-download mr-2"></i>
                                Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <Alert/>

            {/* Tabela de Campos */}
            <div className="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden">
                <StructuredRecordsTable 
                    topic={topic}
                    topicLimits={topicLimits}
                    availableStructures={availableStructures}
                    auth={auth}
                    translations={translations}
                />
            </div>
        </div>
    );
}

function LimitIndicator({ topicLimits }) {
    if (!topicLimits) return null;

    const { isUnlimited, fieldsLimit, currentFieldsCount, remainingFields } = topicLimits;
    
    if (isUnlimited) {
        return (
            <div className="bg-teal-400/10 border border-teal-400/20 rounded-lg px-3 py-2">
                <div className="flex items-center space-x-2 text-sm">
                    <i className="fas fa-infinity text-teal-400"></i>
                    <span className="text-teal-300">Ilimitado</span>
                    <span className="text-gray-400">({currentFieldsCount} campos)</span>
                </div>
            </div>
        );
    }

    const isNearLimit = remainingFields <= 5;
    const isAtLimit = remainingFields === 0;

    return (
        remainingFields > 9999 ?
            <div className={`border rounded-lg px-3 py-2 ${
                isAtLimit 
                    ? 'bg-red-400/10 border-red-400/20' 
                    : isNearLimit 
                        ? 'bg-yellow-400/10 border-yellow-400/20' 
                        : 'bg-teal-400/10 border-teal-400/20'
            }`}>
                <div className="flex items-center space-x-2 text-sm">
                    <i className={`fas ${
                        isAtLimit ? 'fa-exclamation-triangle text-red-400' :
                        isNearLimit ? 'fa-exclamation-circle text-yellow-400' :
                        'fa-chart-pie text-teal-400'
                    }`}></i>
                    
                    <span className={isAtLimit ? 'text-red-300' : isNearLimit ? 'text-yellow-300' : 'text-teal-300'}>
                        {currentFieldsCount}/{fieldsLimit}
                    </span>
                    
                    <span className="text-gray-400">
                        ({remainingFields} restantes)
                    </span>
                </div>
            </div>
        : ''
    );
}