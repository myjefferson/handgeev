// resources/js/Components/Workspace/StructuredTopicSidebar.jsx
import React, { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';
import TopicItem from './TopicItem';

export default function TopicSidebar({ 
    workspace, 
    topics, 
    topicsWithLimits,
    workspaceLimits,
    availableStructures,
    selectedTopicId, 
    onSelectTopic,
    onOpenImport,
    onOpenExport,
    onOpenRename,
    auth
}) {
    const { translations } = usePage().props;
    const { user } = auth;
    const [creatingTopic, setCreatingTopic] = useState(false);

    const topicsLimit = user.plan.topics_limit;
    const currentTopics = user.current_topics_count;
    const remainingTopics = user.remaining_topics_count;
    const canCreateTopic = workspaceLimits.canCreateTopics;

    const handleCreateTopic = async (structureId) => {
        if (!canCreateTopic) return;

        setCreatingTopic(true);
        
        try {
            const topicName = prompt(
                'Digite o nome do novo tópico:',
                `Novo Tópico ${topics.length + 1}`
            );
            
            if (topicName && topicName.trim()) {
                await router.post(route('topic.store'), {
                    workspace_id: workspace.id,
                    structure_id: structureId,
                    title: topicName.trim(),
                    order: topics.length + 1
                });
            }
        } catch (error) {
            console.error('Erro ao criar tópico:', error);
        } finally {
            setCreatingTopic(false);
        }
    };

    return (
        <div className="w-full lg:w-80 bg-slate-800 border border-slate-700 rounded-2xl h-fit">
            <div className="p-6">
                {/* Header */}
                <div className="mb-6">
                    <h1 className="text-xl font-bold text-white truncate">{workspace.title}</h1>
                    <div className="flex items-center justify-between text-sm text-gray-400 mt-2">
                        <span>
                            {currentTopics}/{topicsLimit < 9999 ? topicsLimit : '∞'} tópicos
                        </span>
                        {!canCreateTopic && (
                            <span className="text-yellow-400 text-xs">
                                <i className="fas fa-exclamation-triangle mr-1"></i>
                                Limite
                            </span>
                        )}
                    </div>
                </div>

                {/* Botão de Criar Tópico com Estrutura */}
                <div className="mb-6">
                    {canCreateTopic ? (
                        <StructureSelector 
                            availableStructures={availableStructures}
                            onCreateTopic={handleCreateTopic}
                            disabled={creatingTopic}
                        />
                    ) : (
                        <UpsellButton />
                    )}
                </div>

                {/* Lista de Tópicos */}
                <div className="space-y-2">
                    <h3 className="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">
                        Tópicos ({topics.length})
                    </h3>
                    
                    {topics.map((topic) => (
                        <TopicItem
                            key={topic.id}
                            topic={topic}
                            topicLimits={topicsWithLimits[topic.id]}
                            isSelected={topic.id === selectedTopicId}
                            onSelect={onSelectTopic}
                            onRename={onOpenRename}
                            translations={translations}
                        />
                    ))}
                    
                    {topics.length === 0 && (
                        <div className="text-center py-8 text-gray-500">
                            <i className="fas fa-folder-open text-2xl mb-2"></i>
                            <p className="text-sm">Nenhum tópico criado</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

function StructureSelector({ availableStructures, onCreateTopic, disabled }) {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <div className="relative">
            <button
                onClick={() => setIsOpen(!isOpen)}
                disabled={disabled}
                className="w-full px-4 py-3 bg-teal-500 hover:bg-teal-400 disabled:bg-teal-600 text-slate-900 font-medium rounded-xl transition-colors duration-300 flex items-center justify-center"
            >
                {disabled ? (
                    <><i className="fas fa-spinner fa-spin mr-2"></i> Criando...</>
                ) : (
                    <><i className="fas fa-plus mr-2"></i> Novo Tópico <i className="fas fa-chevron-down ml-2 text-sm"></i></>
                )}
            </button>
            
            {isOpen && !disabled && (
                <div className="absolute top-full left-0 right-0 mt-2 bg-slate-700 border border-slate-600 rounded-xl shadow-lg z-10">
                    <div className="p-2 max-h-60 overflow-y-auto">
                        <div className="text-xs text-gray-400 px-3 py-2 uppercase font-semibold">
                            Escolha uma estrutura
                        </div>
                        
                        {availableStructures.map((structure) => (
                            <button
                                key={structure.id}
                                onClick={() => {
                                    onCreateTopic(structure.id);
                                    setIsOpen(false);
                                }}
                                className="w-full text-left px-3 py-2 hover:bg-slate-600 rounded-lg transition-colors"
                            >
                                <div className="flex items-center justify-between">
                                    <span className="text-white text-sm">{structure.name}</span>
                                    <span className="text-xs text-gray-400 bg-slate-500 px-2 py-1 rounded">
                                        {structure.fields_count} campos
                                    </span>
                                </div>
                                {structure.description && (
                                    <p className="text-xs text-gray-400 mt-1 truncate">
                                        {structure.description}
                                    </p>
                                )}
                            </button>
                        ))}
                        
                        {availableStructures.length === 0 && (
                            <div className="px-3 py-4 text-center text-gray-400">
                                <i className="fas fa-cubes text-lg mb-2"></i>
                                <p className="text-sm">Nenhuma estrutura disponível</p>
                                <Link 
                                    href={route('structures.create')}
                                    className="text-teal-400 hover:text-teal-300 text-xs mt-2 inline-block"
                                >
                                    Criar estrutura
                                </Link>
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}

function UpsellButton() {
    return (
        <Link 
            href={route('subscription.pricing')}
            className="w-full px-4 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-medium rounded-xl transition-all duration-300 text-center flex items-center justify-center"
        >
            <i className="fas fa-crown mr-2"></i>
            Fazer Upgrade
        </Link>
    );
}