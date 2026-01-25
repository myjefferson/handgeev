import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';

const StudioInputConnectionsForm = ({ 
    workspace, 
    topicsByStructure,  // Agora recebe tópicos agrupados por estrutura
    connection = null, 
    templates = [],
    hasStructures = true  // Nova prop para verificar se há estruturas disponíveis
}) => {
    const { sourceTypes, transformations, defaultConfig } = usePage().props;
    
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: connection?.name || '',
        description: connection?.description || '',
        topic_id: connection?.topic_id || '',  // Novo campo: ID do tópico
        structure_id: connection?.structure_id || '', // Preenchido automaticamente
        trigger_field_id: connection?.trigger_field_id || '',
        is_active: connection?.is_active ?? true,
        timeout_seconds: connection?.timeout_seconds || 30,
        prevent_loops: connection?.prevent_loops ?? true,
        
        source: {
            source_type: connection?.source?.source_type || 'rest_api',
            config: connection?.source?.config || defaultConfig?.rest_api || {},
        },
        
        mappings: connection?.mappings?.map(m => ({
            source_field: m.source_field,
            target_field_id: m.target_field_id,
            transformation_type: m.transformation_type || 'none',
            is_required: m.is_required || false,
        })) || [],
    });

    const [selectedStructure, setSelectedStructure] = useState(null);
    const [selectedTopic, setSelectedTopic] = useState(null);
    const [showMappingForm, setShowMappingForm] = useState(false);
    const [editingMappingIndex, setEditingMappingIndex] = useState(null);
    const [currentMapping, setCurrentMapping] = useState({
        source_field: '',
        target_field_id: '',
        transformation_type: 'none',
        is_required: false,
    });
    const [testing, setTesting] = useState(false);
    const [testResult, setTestResult] = useState(null);

    // Se não houver estruturas disponíveis, mostrar mensagem
    if (!hasStructures || !topicsByStructure || topicsByStructure.length === 0) {
        return (
            <DashboardLayout>
                <Head title="Criar Conexão" />
                <div className="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
                    <div className="max-w-4xl mx-auto px-4">
                        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center">
                            <div className="mb-6">
                                <i className="fas fa-layer-group text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                    Nenhuma Estrutura Disponível
                                </h2>
                                <p className="text-gray-600 dark:text-gray-400 mb-6">
                                    Para criar uma conexão de entrada, você precisa primeiro criar um tópico com uma estrutura.
                                </p>
                            </div>
                            
                            <div className="space-y-4 max-w-md mx-auto">
                                <div className="text-left bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <h3 className="font-medium text-blue-800 dark:text-blue-300 mb-2">
                                        <i className="fas fa-lightbulb mr-2"></i>
                                        Passo a passo:
                                    </h3>
                                    <ol className="list-decimal pl-5 space-y-2 text-sm text-blue-700 dark:text-blue-400">
                                        <li>Crie uma estrutura de dados (se ainda não tiver)</li>
                                        <li>Crie um tópico neste workspace usando essa estrutura</li>
                                        <li>Volte aqui para criar a conexão de entrada</li>
                                    </ol>
                                </div>
                            </div>

                            <div className="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                                <Link
                                    href={route('structures.create')}
                                    className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                >
                                    <i className="fas fa-plus mr-2"></i>
                                    Criar Nova Estrutura
                                </Link>
                                
                                <Link
                                    href={route('workspace.show', workspace.id)}
                                    className="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <i className="fas fa-arrow-left mr-2"></i>
                                    Voltar ao Workspace
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </DashboardLayout>
        );
    }

    // Achatamos todos os tópicos em uma lista para o select
    const allTopics = topicsByStructure.flatMap(group => group.topics);

    // Efeito para carregar estrutura quando selecionar tópico
    useEffect(() => {
        if (data.topic_id) {
            const topic = allTopics.find(t => t.id == data.topic_id);
            if (topic && topic.structure) {
                setSelectedTopic(topic);
                setSelectedStructure(topic.structure);
                // Atualizar structure_id automaticamente
                setData('structure_id', topic.structure.id);
            }
        } else if (connection && connection.structure_id) {
            // Se estiver editando, carregar a estrutura atual
            const structure = topicsByStructure
                .find(group => group.structure.id === connection.structure_id)?.structure;
            if (structure) {
                setSelectedStructure(structure);
            }
        }
    }, [data.topic_id, connection]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (connection) {
            put(route('workspaces.input-connections.update', [workspace.id, connection.id]));
        } else {
            post(route('workspaces.input-connections.store', workspace.id));
        }
    };

    const handleTestConnection = async () => {
        setTesting(true);
        setTestResult(null);
        
        try {
            const response = await axios.post(
                route('workspaces.input-connections.test', [workspace.id, connection?.id]),
                data
            );
            setTestResult(response.data);
        } catch (error) {
            setTestResult({
                success: false,
                message: error.response?.data?.message || error.message,
            });
        } finally {
            setTesting(false);
        }
    };

    const handleAddMapping = () => {
        if (editingMappingIndex !== null) {
            const newMappings = [...data.mappings];
            newMappings[editingMappingIndex] = currentMapping;
            setData('mappings', newMappings);
            setEditingMappingIndex(null);
        } else {
            setData('mappings', [...data.mappings, currentMapping]);
        }
        setShowMappingForm(false);
        setCurrentMapping({
            source_field: '',
            target_field_id: '',
            transformation_type: 'none',
            is_required: false,
        });
    };

    const handleEditMapping = (index) => {
        setCurrentMapping(data.mappings[index]);
        setEditingMappingIndex(index);
        setShowMappingForm(true);
    };

    const handleRemoveMapping = (index) => {
        const newMappings = [...data.mappings];
        newMappings.splice(index, 1);
        setData('mappings', newMappings);
    };

    const applyTemplate = (template) => {
        setData('source', {
            source_type: template.source_type,
            config: template.config,
        });
        // Sugerir mapeamentos baseados nos campos da estrutura
        if (selectedStructure) {
            const suggestedMappings = template.mappings.map(mapping => {
                // Tentar encontrar campo correspondente
                const field = selectedStructure.fields.find(f => 
                    f.key_name.toLowerCase().includes(mapping.source_field.split('.').pop().toLowerCase())
                );
                return {
                    ...mapping,
                    target_field_id: field?.id || '',
                };
            });
            setData('mappings', suggestedMappings);
        }
    };

    return (
        <DashboardLayout>
            <Head title={`${connection ? 'Editar' : 'Nova'} Conexão - ${workspace.title}`} />

            <div className="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
                <div className="max-w-6xl mx-auto px-4">
                    {/* Cabeçalho */}
                    <div className="mb-8">
                        <button
                            onClick={() => window.history.back() }
                            className="inline-flex items-center text-teal-600 dark:text-teal-400 hover:underline mb-4"
                        >
                            <i className="fas fa-arrow-left mr-2"></i>
                            Voltar para Conexões
                        </button>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                            <i className="fas fa-plug mr-2 text-green-500"></i>
                            {connection ? 'Editar Conexão' : 'Nova Conexão de Entrada'}
                        </h1>
                        <p className="text-gray-600 dark:text-gray-400">
                            Configure uma conexão para buscar dados externos e atualizar tópicos automaticamente
                        </p>
                    </div>

                    {/* Templates rápidos */}
                    {templates.length > 0 && !connection && (
                        <div className="mb-8 bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                <i className="fas fa-magic mr-2 text-blue-500"></i>
                                Templates Rápidos
                            </h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {templates.map(template => (
                                    <button
                                        key={template.id}
                                        onClick={() => applyTemplate(template)}
                                        className="text-left p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-teal-500 dark:hover:border-teal-500 hover:shadow transition-all"
                                    >
                                        <div className="flex items-center mb-2">
                                            <i className={`fas ${
                                                template.id === 'viacep' ? 'fa-map-marker-alt' :
                                                template.id === 'google_geocoding' ? 'fa-map' :
                                                'fa-plug'
                                            } text-teal-500 mr-2`}></i>
                                            <span className="font-medium text-gray-900 dark:text-white">
                                                {template.name}
                                            </span>
                                        </div>
                                        <p className="text-sm text-gray-600 dark:text-gray-400">
                                            {template.description}
                                        </p>
                                    </button>
                                ))}
                            </div>
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-8">
                        {/* Informações Básicas */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                                <i className="fas fa-info-circle mr-2 text-blue-500"></i>
                                Informações Básicas
                            </h3>
                            
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Nome */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nome da Conexão *
                                    </label>
                                    <input
                                        type="text"
                                        value={data.name}
                                        onChange={e => setData('name', e.target.value)}
                                        className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                        placeholder="Ex: Consulta ViaCEP para Endereços"
                                    />
                                    {errors.name && (
                                        <p className="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {errors.name}
                                        </p>
                                    )}
                                </div>

                                {/* Tópico de Destino */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Tópico de Destino *
                                    </label>
                                    <select
                                        value={data.topic_id}
                                        onChange={e => setData('topic_id', e.target.value)}
                                        className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    >
                                        <option value="">Selecione um tópico</option>
                                        {allTopics.map(topic => (
                                            <option key={topic.id} value={topic.id}>
                                                {topic.title} (Estrutura: {topic.structure?.name})
                                            </option>
                                        ))}
                                    </select>
                                    {errors.topic_id && (
                                        <p className="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {errors.topic_id}
                                        </p>
                                    )}
                                    {errors.structure_id && (
                                        <p className="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {errors.structure_id}
                                        </p>
                                    )}
                                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        A conexão atualizará registros neste tópico
                                    </p>
                                </div>

                                {/* Descrição */}
                                <div className="md:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Descrição
                                    </label>
                                    <textarea
                                        value={data.description}
                                        onChange={e => setData('description', e.target.value)}
                                        rows={3}
                                        className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                        placeholder="Descreva o propósito desta conexão. Ex: 'Consulta CEP para preenchimento automático de endereços'"
                                    />
                                </div>

                                {/* Informações da Estrutura Selecionada */}
                                {selectedStructure && (
                                    <div className="md:col-span-2 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                        <div className="flex items-center mb-3">
                                            <i className="fas fa-layer-group text-blue-500 mr-2"></i>
                                            <h4 className="font-medium text-blue-800 dark:text-blue-300">
                                                Estrutura: {selectedStructure.name}
                                            </h4>
                                            <span className="ml-auto text-sm text-blue-600 dark:text-blue-400">
                                                {selectedStructure.fields?.length || 0} campos disponíveis
                                            </span>
                                        </div>
                                        
                                        {/* Campo Trigger */}
                                        <div>
                                            <label className="block text-sm font-medium text-blue-700 dark:text-blue-300 mb-2">
                                                Campo Trigger (Opcional)
                                            </label>
                                            <select
                                                value={data.trigger_field_id}
                                                onChange={e => setData('trigger_field_id', e.target.value)}
                                                className="w-full px-4 py-2 border border-blue-300 dark:border-blue-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            >
                                                <option value="">Nenhum (execução manual)</option>
                                                {selectedStructure.fields.map(field => (
                                                    <option key={field.id} value={field.id}>
                                                        {field.key_name} ({field.type})
                                                    </option>
                                                ))}
                                            </select>
                                            <p className="mt-1 text-sm text-blue-600 dark:text-blue-400">
                                                A conexão será executada automaticamente quando este campo for alterado em qualquer registro do tópico
                                            </p>
                                        </div>
                                    </div>
                                )}

                                {/* Configurações Avançadas */}
                                <div className="space-y-4">
                                    <div className="flex items-center">
                                        <input
                                            type="checkbox"
                                            id="is_active"
                                            checked={data.is_active}
                                            onChange={e => setData('is_active', e.target.checked)}
                                            className="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                                        />
                                        <label htmlFor="is_active" className="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            Conexão ativa
                                        </label>
                                    </div>
                                    
                                    <div className="flex items-center">
                                        <input
                                            type="checkbox"
                                            id="prevent_loops"
                                            checked={data.prevent_loops}
                                            onChange={e => setData('prevent_loops', e.target.checked)}
                                            className="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                                        />
                                        <label htmlFor="prevent_loops" className="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            Prevenir loops de execução
                                        </label>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Timeout (segundos)
                                        </label>
                                        <input
                                            type="number"
                                            min="1"
                                            max="300"
                                            value={data.timeout_seconds}
                                            onChange={e => setData('timeout_seconds', e.target.value)}
                                            className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                        />
                                        <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Tempo máximo de espera por resposta da fonte externa
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Configuração da Fonte */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                                <i className="fas fa-cloud mr-2 text-purple-500"></i>
                                Fonte de Dados Externa
                            </h3>
                            
                            <div className="space-y-6">
                                {/* Tipo de Fonte */}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Tipo de Fonte *
                                    </label>
                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                                        {Object.entries(sourceTypes).map(([value, label]) => (
                                            <button
                                                key={value}
                                                type="button"
                                                onClick={() => setData('source', {
                                                    ...data.source,
                                                    source_type: value,
                                                    config: defaultConfig?.[value] || {}
                                                })}
                                                className={`p-4 border rounded-lg text-center transition-all ${
                                                    data.source.source_type === value
                                                        ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300'
                                                        : 'border-gray-300 dark:border-gray-600 hover:border-teal-500'
                                                }`}
                                            >
                                                <i className={`fas ${
                                                    value === 'rest_api' ? 'fa-cloud' :
                                                    value === 'webhook' ? 'fa-broadcast-tower' :
                                                    value === 'csv' ? 'fa-file-csv' :
                                                    value === 'excel' ? 'fa-file-excel' : 'fa-window-restore'
                                                } text-xl mb-2`}></i>
                                                <div className="font-medium">{label}</div>
                                            </button>
                                        ))}
                                    </div>
                                    {errors['source.source_type'] && (
                                        <p className="mt-1 text-sm text-red-600 dark:text-red-400">
                                            {errors['source.source_type']}
                                        </p>
                                    )}
                                </div>

                                {/* Configurações específicas por tipo */}
                                {data.source.source_type === 'rest_api' && (
                                    <div className="space-y-4">
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    URL da API *
                                                </label>
                                                <input
                                                    type="url"
                                                    value={data.source.config.url || ''}
                                                    onChange={e => setData('source.config', {
                                                        ...data.source.config,
                                                        url: e.target.value
                                                    })}
                                                    className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                                    placeholder="https://viacep.com.br/ws/{cep}/json/"
                                                />
                                            </div>
                                            
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Método HTTP
                                                </label>
                                                <select
                                                    value={data.source.config.method || 'GET'}
                                                    onChange={e => setData('source.config', {
                                                        ...data.source.config,
                                                        method: e.target.value
                                                    })}
                                                    className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                                >
                                                    <option value="GET">GET</option>
                                                    <option value="POST">POST</option>
                                                    <option value="PUT">PUT</option>
                                                    <option value="PATCH">PATCH</option>
                                                    <option value="DELETE">DELETE</option>
                                                </select>
                                            </div>
                                        </div>

                                        {/* Placeholders disponíveis */}
                                        {selectedStructure && (
                                            <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                                <div className="flex items-center justify-between mb-2">
                                                    <h4 className="text-sm font-medium text-blue-800 dark:text-blue-300">
                                                        <i className="fas fa-lightbulb mr-1"></i>
                                                        Placeholders disponíveis
                                                    </h4>
                                                    <span className="text-xs text-blue-600 dark:text-blue-400">
                                                        Use {'{'}nome_campo{'}'} na URL ou parâmetros
                                                    </span>
                                                </div>
                                                <div className="flex flex-wrap gap-2">
                                                    {selectedStructure.fields.map(field => (
                                                        <span
                                                            key={field.id}
                                                            className="px-2 py-1 bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-blue-300 text-xs rounded cursor-help"
                                                            title={`Campo: ${field.key_name}, Tipo: ${field.type}`}
                                                        >
                                                            {field.key_name}
                                                        </span>
                                                    ))}
                                                </div>
                                                <div className="mt-3 text-xs text-blue-600 dark:text-blue-400">
                                                    <p><strong>Exemplo:</strong> https://api.exemplo.com/{'{cep}'}/consultar</p>
                                                    <p>Quando o campo "cep" for alterado, o valor será automaticamente inserido na URL</p>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Mapeamentos */}
                        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                            <div className="flex items-center justify-between mb-6">
                                <div>
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                                        <i className="fas fa-exchange-alt mr-2 text-green-500"></i>
                                        Mapeamento de Campos
                                    </h3>
                                    {selectedStructure && (
                                        <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Relacione campos da fonte externa com campos da estrutura "{selectedStructure.name}"
                                        </p>
                                    )}
                                </div>
                                <button
                                    type="button"
                                    onClick={() => setShowMappingForm(true)}
                                    disabled={!selectedStructure}
                                    className={`px-4 py-2 rounded-lg transition-colors ${
                                        selectedStructure
                                            ? 'bg-teal-600 text-white hover:bg-teal-700'
                                            : 'bg-gray-300 text-gray-500 dark:bg-gray-700 dark:text-gray-400 cursor-not-allowed'
                                    }`}
                                >
                                    <i className="fas fa-plus mr-2"></i>
                                    Adicionar Campo
                                </button>
                            </div>

                            {/* Mensagem se não tiver estrutura selecionada */}
                            {!selectedStructure && (
                                <div className="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                    <i className="fas fa-layer-group text-4xl text-gray-400 mb-4"></i>
                                    <p className="text-gray-600 dark:text-gray-400 mb-2">
                                        Selecione um tópico primeiro para ver os campos disponíveis
                                    </p>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        A estrutura do tópico determina quais campos podem ser mapeados
                                    </p>
                                </div>
                            )}

                            {/* Lista de mapeamentos */}
                            {selectedStructure && data.mappings.length > 0 ? (
                                <div className="overflow-x-auto">
                                    <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead>
                                            <tr>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                    Campo da Fonte Externa
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                    Campo na Estrutura
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                    Transformação
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                    Obrigatório
                                                </th>
                                                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                    Ações
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                                            {data.mappings.map((mapping, index) => {
                                                const targetField = selectedStructure.fields?.find(
                                                    f => f.id == mapping.target_field_id
                                                );
                                                return (
                                                    <tr key={index} className="hover:bg-gray-50 dark:hover:bg-gray-750">
                                                        <td className="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                            <code className="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                                {mapping.source_field}
                                                            </code>
                                                        </td>
                                                        <td className="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                            {targetField ? (
                                                                <div className="flex items-center">
                                                                    <span className="font-medium">{targetField.key_name}</span>
                                                                    <span className="ml-2 text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                                                        {targetField.type}
                                                                    </span>
                                                                </div>
                                                            ) : (
                                                                <span className="text-red-500 dark:text-red-400">Campo não encontrado</span>
                                                            )}
                                                        </td>
                                                        <td className="px-4 py-3 text-sm">
                                                            <span className={`px-2 py-1 text-xs rounded ${
                                                                mapping.transformation_type === 'none'
                                                                    ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                                                                    : 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300'
                                                            }`}>
                                                                {transformations[mapping.transformation_type] || 'Nenhuma'}
                                                            </span>
                                                        </td>
                                                        <td className="px-4 py-3 text-sm">
                                                            {mapping.is_required ? (
                                                                <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                    <i className="fas fa-exclamation-circle mr-1"></i>
                                                                    Sim
                                                                </span>
                                                            ) : (
                                                                <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                                                    <i className="fas fa-check mr-1"></i>
                                                                    Não
                                                                </span>
                                                            )}
                                                        </td>
                                                        <td className="px-4 py-3 text-sm">
                                                            <div className="flex space-x-2">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleEditMapping(index)}
                                                                    className="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                                                    title="Editar mapeamento"
                                                                >
                                                                    <i className="fas fa-edit"></i>
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleRemoveMapping(index)}
                                                                    className="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                                                    title="Remover mapeamento"
                                                                >
                                                                    <i className="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                );
                                            })}
                                        </tbody>
                                    </table>
                                </div>
                            ) : selectedStructure && data.mappings.length === 0 ? (
                                <div className="text-center py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                    <i className="fas fa-exchange-alt text-4xl text-gray-400 mb-4"></i>
                                    <p className="text-gray-600 dark:text-gray-400 mb-4">
                                        Nenhum campo mapeado ainda
                                    </p>
                                    <button
                                        type="button"
                                        onClick={() => setShowMappingForm(true)}
                                        className="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700"
                                    >
                                        <i className="fas fa-plus mr-2"></i>
                                        Adicionar Primeiro Campo
                                    </button>
                                </div>
                            ) : null}
                        </div>

                        {/* Ações */}
                        <div className="flex items-center justify-between">
                            <div>
                                {connection && (
                                    <button
                                        type="button"
                                        onClick={handleTestConnection}
                                        disabled={testing || !data.topic_id || data.mappings.length === 0}
                                        className="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {testing ? (
                                            <>
                                                <i className="fas fa-spinner fa-spin mr-2"></i>
                                                Testando...
                                            </>
                                        ) : (
                                            <>
                                                <i className="fas fa-vial mr-2"></i>
                                                Testar Conexão
                                            </>
                                        )}
                                    </button>
                                )}
                            </div>
                            <div className="flex space-x-3">
                                <button
                                    onClick={() => window.history.back()}
                                    className="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing || !data.topic_id || data.mappings.length === 0}
                                    className="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {processing ? (
                                        <>
                                            <i className="fas fa-spinner fa-spin mr-2"></i>
                                            Salvando...
                                        </>
                                    ) : (
                                        <>
                                            <i className="fas fa-save mr-2"></i>
                                            {connection ? 'Atualizar' : 'Criar'} Conexão
                                        </>
                                    )}
                                </button>
                            </div>
                        </div>
                    </form>

                    {/* Modal de Adicionar/Editar Mapeamento */}
                    {showMappingForm && selectedStructure && (
                        <div className="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
                            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full">
                                <div className="p-6">
                                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                        <i className="fas fa-exchange-alt mr-2 text-green-500"></i>
                                        {editingMappingIndex !== null ? 'Editar' : 'Novo'} Mapeamento
                                    </h3>
                                    
                                    <div className="space-y-4">
                                        {/* Campo Fonte */}
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Campo na Fonte Externa *
                                            </label>
                                            <input
                                                type="text"
                                                value={currentMapping.source_field}
                                                onChange={e => setCurrentMapping({
                                                    ...currentMapping,
                                                    source_field: e.target.value
                                                })}
                                                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                                placeholder="Ex: cep, endereco.logradouro, results.0.nome"
                                            />
                                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Use ponto (.) para acessar objetos aninhados. Ex: "endereco.logradouro" para {"{endereco: {logradouro: '...'}}"}
                                            </p>
                                        </div>

                                        {/* Campo Destino */}
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Campo na Estrutura *
                                            </label>
                                            <select
                                                value={currentMapping.target_field_id}
                                                onChange={e => setCurrentMapping({
                                                    ...currentMapping,
                                                    target_field_id: e.target.value
                                                })}
                                                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            >
                                                <option value="">Selecione um campo</option>
                                                {selectedStructure.fields?.map(field => (
                                                    <option key={field.id} value={field.id}>
                                                        {field.key_name} ({field.type})
                                                    </option>
                                                ))}
                                            </select>
                                        </div>

                                        {/* Transformação */}
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                Transformação
                                            </label>
                                            <select
                                                value={currentMapping.transformation_type}
                                                onChange={e => setCurrentMapping({
                                                    ...currentMapping,
                                                    transformation_type: e.target.value
                                                })}
                                                className="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            >
                                                {Object.entries(transformations).map(([value, label]) => (
                                                    <option key={value} value={value}>
                                                        {label}
                                                    </option>
                                                ))}
                                            </select>
                                            <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                Aplique transformações no valor antes de salvar
                                            </p>
                                        </div>

                                        {/* Obrigatório */}
                                        <div className="flex items-center">
                                            <input
                                                type="checkbox"
                                                id="is_required"
                                                checked={currentMapping.is_required}
                                                onChange={e => setCurrentMapping({
                                                    ...currentMapping,
                                                    is_required: e.target.checked
                                                })}
                                                className="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded"
                                            />
                                            <label htmlFor="is_required" className="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                                Campo obrigatório
                                            </label>
                                            <span className="ml-2 text-xs text-gray-500 dark:text-gray-400">
                                                A conexão falhará se este campo estiver vazio na fonte externa
                                            </span>
                                        </div>
                                    </div>

                                    {/* Ações do Modal */}
                                    <div className="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setShowMappingForm(false);
                                                setEditingMappingIndex(null);
                                                setCurrentMapping({
                                                    source_field: '',
                                                    target_field_id: '',
                                                    transformation_type: 'none',
                                                    is_required: false,
                                                });
                                            }}
                                            className="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                                        >
                                            Cancelar
                                        </button>
                                        <button
                                            type="button"
                                            onClick={handleAddMapping}
                                            disabled={!currentMapping.source_field || !currentMapping.target_field_id}
                                            className="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            {editingMappingIndex !== null ? 'Atualizar' : 'Adicionar'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
};

export default StudioInputConnectionsForm;