import React, { useState, useEffect } from 'react';

export default function ApiEndpointsTab({ workspace }) {
    const [expandedExamples, setExpandedExamples] = useState({
        workspace: false,
        topics: false,
        fields: false
    });
    const [modalContent, setModalContent] = useState(null);
    const [modalTitle, setModalTitle] = useState('');

    // Gerar estrutura de endpoints
    const generateEndpoints = () => {
        const baseWorkspacePath = `/api/v1/workspaces/${workspace.id}`;
        const baseUrl = window.location.origin;

        return {
            workspace: [
                {
                    method: 'GET',
                    path: `${baseWorkspacePath}`,
                    full_url: `${baseUrl}${baseWorkspacePath}`,
                    description: 'Obter informações completas do workspace (tópicos e campos incluídos)',
                    parameters: [
                        { name: 'view', optional: true, description: 'Tipo de visualização: utilize "full" para detalhes completos.' }
                    ],
                    query_params: '?view=full',
                    response_example: JSON.stringify({
                        metadata: { 
                            version: "1.0", 
                            generated_at: "2024-01-01T00:00:00Z",
                            workspace_id: workspace.id,
                            view_type: "full",
                            rate_limits: {
                                remaining_minute: 45,
                                plan: "Premium"
                            }
                        },
                        workspace: {
                            id: workspace.id,
                            title: workspace.title,
                            description: workspace.description || '',
                            type: workspace.typeWorkspace?.description || 'Personal',
                            type_id: workspace.type_workspace_id,
                            is_published: workspace.is_published || false,
                            api_enabled: workspace.api_enabled || false,
                            owner: {
                                id: workspace.user?.id,
                                name: workspace.user?.name,
                                email: workspace.user?.email
                            },
                            dates: {
                                created: new Date(workspace.created_at).toISOString(),
                                updated: new Date(workspace.updated_at).toISOString()
                            }
                        },
                        topics: [
                            {
                                id: 1,
                                title: "Informações Pessoais",
                                order: 1,
                                fields_count: 3,
                                fields: {
                                    "nome": "João Silva",
                                    "email": "joao@email.com",
                                    "telefone": "11999999999"
                                },
                                created_at: "2024-01-01T00:00:00Z",
                                updated_at: "2024-01-01T00:00:00Z"
                            },
                            {
                                id: 2,
                                title: "Informações Profissionais",
                                order: 2,
                                fields_count: 2,
                                fields: {
                                    "empresa": "Tech Corp",
                                    "cargo": "Desenvolvedor"
                                },
                                created_at: "2024-01-01T00:00:00Z",
                                updated_at: "2024-01-01T00:00:00Z"
                            }
                        ],
                        statistics: {
                            total_topics: 2,
                            total_fields: 5,
                            visible_fields: 5
                        }
                    }, null, 2)
                },
                {
                    method: 'GET',
                    path: `${baseWorkspacePath}/stats`,
                    full_url: `${baseUrl}${baseWorkspacePath}/stats`,
                    description: 'Obter estatísticas detalhadas do workspace',
                    parameters: []
                },
                {
                    method: 'PUT',
                    path: `${baseWorkspacePath}`,
                    full_url: `${baseUrl}${baseWorkspacePath}`,
                    description: 'Atualizar informações do workspace',
                    parameters: [
                        { name: 'title', optional: false, description: 'Título do workspace' },
                        { name: 'description', optional: true, description: 'Descrição do workspace' },
                        { name: 'is_published', optional: true, description: 'Status de publicação' }
                    ],
                    request_example: JSON.stringify({
                        title: "Novo Título do Workspace",
                        description: "Nova descrição atualizada",
                        is_published: true
                    }, null, 2)
                },
                {
                    method: 'PATCH',
                    path: `${baseWorkspacePath}/settings`,
                    full_url: `${baseUrl}${baseWorkspacePath}/settings`,
                    description: 'Atualizar configurações da API',
                    parameters: [
                        { name: 'api_enabled', optional: true, description: 'Habilitar/desabilitar API' },
                        { name: 'allowed_domains', optional: true, description: 'Domínios permitidos' }
                    ],
                    request_example: JSON.stringify({
                        api_enabled: true,
                        allowed_domains: ["meusite.com", "app.meusite.com"]
                    }, null, 2)
                }
            ],
            topics: [
                {
                    method: 'GET',
                    path: `${baseWorkspacePath}/topics`,
                    full_url: `${baseUrl}${baseWorkspacePath}/topics`,
                    description: 'Listar todos os tópicos do workspace',
                    parameters: [
                        { name: 'view', optional: true, description: 'Tipo de visualização: utilize "full" para detalhes completos.' }
                    ],
                    query_params: '?view=full',
                    response_example: JSON.stringify({
                        metadata: {
                            workspace_id: workspace.id,
                            workspace_title: workspace.title,
                            total_topics: 2,
                            view_type: "simple",
                            generated_at: "2024-01-01T00:00:00Z"
                        },
                        topics: [
                            {
                                id: 1,
                                title: "Informações Pessoais",
                                order: 1,
                                fields_count: 3,
                                created_at: "2024-01-01T00:00:00Z",
                                updated_at: "2024-01-01T00:00:00Z"
                            },
                            {
                                id: 2,
                                title: "Informações Profissionais",
                                order: 2,
                                fields_count: 2,
                                created_at: "2024-01-01T00:00:00Z",
                                updated_at: "2024-01-01T00:00:00Z"
                            }
                        ]
                    }, null, 2)
                },
                {
                    method: 'POST',
                    path: `${baseWorkspacePath}/topics`,
                    full_url: `${baseUrl}${baseWorkspacePath}/topics`,
                    description: 'Criar novo tópico no workspace',
                    parameters: [
                        { name: 'title', optional: false, description: 'Título do tópico' },
                        { name: 'order', optional: false, description: 'Ordem de exibição' }
                    ],
                    request_example: JSON.stringify({
                        title: "Novo Tópico",
                        order: 1
                    }, null, 2)
                },
                {
                    method: 'GET',
                    path: `/api/v1/topics/{topicId}`,
                    full_url: `${baseUrl}/api/v1/topics/{topicId}`,
                    description: 'Obter detalhes de um tópico específico com todos os campos',
                    parameters: [
                        { name: 'topicId', optional: false, description: 'ID do tópico' }
                    ],
                    response_example: JSON.stringify({
                        topic: {
                            id: 1,
                            title: "Informações Pessoais",
                            order: 1,
                            workspace: {
                                id: workspace.id,
                                title: workspace.title
                            },
                            fields: {
                                "nome": "João Silva",
                                "email": "joao@email.com",
                                "telefone": "11999999999"
                            },
                            created_at: "2024-01-01T00:00:00Z",
                            updated_at: "2024-01-01T00:00:00Z"
                        }
                    }, null, 2)
                },
                {
                    method: 'PUT',
                    path: `/api/v1/topics/{topicId}`,
                    full_url: `${baseUrl}/api/v1/topics/{topicId}`,
                    description: 'Atualizar tópico',
                    parameters: [
                        { name: 'topicId', optional: false, description: 'ID do tópico' },
                        { name: 'title', optional: true, description: 'Novo título' },
                        { name: 'order', optional: true, description: 'Nova ordem' }
                    ],
                    request_example: JSON.stringify({
                        title: "Tópico Atualizado",
                        order: 2
                    }, null, 2)
                },
                {
                    method: 'DELETE',
                    path: `/api/v1/topics/{topicId}`,
                    full_url: `${baseUrl}/api/v1/topics/{topicId}`,
                    description: 'Excluir tópico',
                    parameters: [
                        { name: 'topicId', optional: false, description: 'ID do tópico' }
                    ]
                }
            ],
            fields: [
                {
                    method: 'GET',
                    path: `/api/v1/topics/{topicId}/fields`,
                    full_url: `${baseUrl}/api/v1/topics/{topicId}/fields`,
                    description: 'Listar todos os campos de um tópico',
                    parameters: [
                        { name: 'topicId', optional: false, description: 'ID do tópico' },
                        { name: 'view', optional: true, description: 'Tipo de visualização: utilize "full" para detalhes completos.' }
                    ],
                    query_params: '?view=full',
                    response_example: JSON.stringify({
                        metadata: {
                            topic_id: 1,
                            topic_title: "Informações Pessoais",
                            workspace_id: workspace.id,
                            workspace_title: workspace.title,
                            total_fields: 3,
                            view_type: "simple",
                            generated_at: "2024-01-01T00:00:00Z"
                        },
                        fields: [
                            {
                                key: "nome",
                                value: "João Silva",
                                type: "text"
                            },
                            {
                                key: "email",
                                value: "joao@email.com",
                                type: "email"
                            },
                            {
                                key: "telefone",
                                value: "11999999999",
                                type: "text"
                            }
                        ]
                    }, null, 2)
                },
                {
                    method: 'POST',
                    path: `/api/v1/topics/{topicId}/fields`,
                    full_url: `${baseUrl}/api/v1/topics/{topicId}/fields`,
                    description: 'Criar novo campo em um tópico',
                    parameters: [
                        { name: 'topicId', optional: false, description: 'ID do tópico' },
                        { name: 'key_name', optional: false, description: 'Chave do campo' },
                        { name: 'value', optional: false, description: 'Valor do campo' },
                        { name: 'type', optional: true, description: 'Tipo do campo (text, number, email, etc.)' },
                        { name: 'is_visible', optional: true, description: 'Visibilidade do campo' },
                        { name: 'order', optional: true, description: 'Ordem de exibição' }
                    ],
                    request_example: JSON.stringify({
                        key_name: "email",
                        value: "usuario@exemplo.com",
                        type: "email",
                        is_visible: true,
                        order: 1
                    }, null, 2)
                },
                {
                    method: 'GET',
                    path: `/api/v1/fields/{fieldId}`,
                    full_url: `${baseUrl}/api/v1/fields/{fieldId}`,
                    description: 'Obter detalhes de um campo específico',
                    parameters: [
                        { name: 'fieldId', optional: false, description: 'ID do campo' }
                    ],
                    response_example: JSON.stringify({
                        field: {
                            id: 1,
                            key_name: "email",
                            value: "usuario@exemplo.com",
                            type: "email",
                            order: 1,
                            is_visible: true,
                            topic_id: 1,
                            created_at: "2024-01-01T00:00:00Z",
                            updated_at: "2024-01-01T00:00:00Z"
                        }
                    }, null, 2)
                },
                {
                    method: 'PUT',
                    path: `/api/v1/fields/{fieldId}`,
                    full_url: `${baseUrl}/api/v1/fields/{fieldId}`,
                    description: 'Atualizar campo',
                    parameters: [
                        { name: 'fieldId', optional: false, description: 'ID do campo' },
                        { name: 'key_name', optional: true, description: 'Nova chave' },
                        { name: 'value', optional: true, description: 'Novo valor' },
                        { name: 'type', optional: true, description: 'Novo tipo' },
                        { name: 'is_visible', optional: true, description: 'Nova visibilidade' },
                        { name: 'order', optional: true, description: 'Nova ordem' }
                    ],
                    request_example: JSON.stringify({
                        key_name: "telefone",
                        value: "+5511999999999",
                        type: "text",
                        is_visible: true,
                        order: 2
                    }, null, 2)
                },
                {
                    method: 'PATCH',
                    path: `/api/v1/fields/{fieldId}/visibility`,
                    full_url: `${baseUrl}/api/v1/fields/{fieldId}/visibility`,
                    description: 'Atualizar apenas a visibilidade do campo',
                    parameters: [
                        { name: 'fieldId', optional: false, description: 'ID do campo' },
                        { name: 'is_visible', optional: false, description: 'Status de visibilidade' }
                    ],
                    request_example: JSON.stringify({
                        is_visible: false
                    }, null, 2)
                },
                {
                    method: 'DELETE',
                    path: `/api/v1/fields/{fieldId}`,
                    full_url: `${baseUrl}/api/v1/fields/{fieldId}`,
                    description: 'Excluir campo',
                    parameters: [
                        { name: 'fieldId', optional: false, description: 'ID do campo' }
                    ]
                }
            ]
        };
    };

    const endpoints = generateEndpoints();

    const getMethodColor = (method) => {
        const colors = {
            'GET': 'bg-green-500',
            'POST': 'bg-blue-500',
            'PUT': 'bg-yellow-500',
            'PATCH': 'bg-orange-500',
            'DELETE': 'bg-red-500'
        };
        return colors[method] || 'bg-slate-500';
    };

    const toggleAllExamples = (section) => {
        setExpandedExamples(prev => ({
            ...prev,
            [section]: !prev[section]
        }));
    };

    const showJsonExample = (jsonString, title = 'Exemplo de Resposta') => {
        setModalTitle(title);
        setModalContent(jsonString);
    };

    const copyToClipboard = async (text, event) => {
        try {
            await navigator.clipboard.writeText(text);
            
            // Mostrar feedback visual
            const button = event.currentTarget;
            const originalContent = button.innerHTML;
            
            button.innerHTML = `
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            `;
            
            setTimeout(() => {
                button.innerHTML = originalContent;
            }, 2000);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    };

    const EndpointSection = ({ section, title, endpointsData }) => (
        <div className="mb-8">
            <div className="flex items-center justify-between mb-4">
                <h4 className="text-lg font-medium text-cyan-400">{title}</h4>
                <button 
                    onClick={() => toggleAllExamples(section)}
                    className="text-slate-400 hover:text-white text-sm"
                >
                    <span className="example-toggle-text">
                        {expandedExamples[section] ? 'Ocultar Exemplos' : 'Mostrar Exemplos'}
                    </span>
                </button>
            </div>
            <div className="space-y-4">
                {endpointsData.map((endpoint, index) => (
                    <EndpointCard 
                        key={index} 
                        endpoint={endpoint}
                        showExample={expandedExamples[section]}
                        onShowJson={showJsonExample}
                        onCopy={copyToClipboard}
                    />
                ))}
            </div>
        </div>
    );

    const EndpointCard = ({ endpoint, showExample, onShowJson, onCopy }) => {
        const [showRequest, setShowRequest] = useState(false);

        return (
            <div className="bg-slate-900 rounded-lg p-4 border border-slate-700 hover:border-cyan-500/50 transition-colors">
                <div className="flex items-center justify-between mb-3">
                    <div className="flex items-center space-x-3">
                        <span className={`px-2 py-1 text-xs font-mono rounded ${getMethodColor(endpoint.method)}`}>
                            {endpoint.method}
                        </span>
                        <code className="text-cyan-300 text-sm">{endpoint.path}</code>
                        {endpoint.query_params && (
                            <code className="text-slate-400 text-sm">{endpoint.query_params}</code>
                        )}
                    </div>
                    <div className="flex items-center space-x-2">
                        {endpoint.response_example && (
                            <button
                                onClick={() => onShowJson(endpoint.response_example, endpoint.description)}
                                className="text-slate-400 hover:text-cyan-300 transition-colors p-1 rounded hover:bg-slate-800"
                                title="Ver exemplo de resposta"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                </svg>
                            </button>
                        )}
                        <button
                            onClick={(e) => onCopy(endpoint.full_url, e)}
                            className="text-slate-400 hover:text-cyan-300 transition-colors p-1 rounded hover:bg-slate-800"
                            title="Copiar URL"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <p className="text-slate-400 text-sm mb-3">{endpoint.description}</p>
                
                {endpoint.parameters && endpoint.parameters.length > 0 && (
                    <div className="mt-2">
                        <span className="text-slate-500 text-xs uppercase font-semibold">Parâmetros:</span>
                        <div className="mt-2 space-y-1">
                            {endpoint.parameters.map((param, idx) => (
                                <div key={idx} className="flex items-center text-xs">
                                    <code className="bg-slate-800 text-cyan-300 px-2 py-1 rounded mr-2 min-w-20 text-center">
                                        {param.name}
                                    </code>
                                    <span className={`text-slate-400 ${param.optional ? 'italic' : ''}`}>
                                        {param.description}
                                        {param.optional ? ' (opcional)' : ' (obrigatório)'}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
                
                {endpoint.request_example && showExample && (
                    <div className="mt-3 pt-3 border-t border-slate-700">
                        <button
                            onClick={() => setShowRequest(!showRequest)}
                            className="text-slate-400 hover:text-white text-sm flex items-center"
                        >
                            <svg 
                                className={`w-4 h-4 mr-1 transition-transform ${showRequest ? 'rotate-180' : ''}`}
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            Exemplo de Request
                        </button>
                        {showRequest && (
                            <div className="mt-2">
                                <div className="bg-slate-800 rounded p-3 relative">
                                    <button
                                        onClick={(e) => onCopy(endpoint.request_example, e)}
                                        className="absolute top-2 right-2 text-slate-400 hover:text-white p-1 rounded hover:bg-slate-700"
                                    >
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                    <pre className="text-slate-300 text-xs font-mono whitespace-pre-wrap overflow-x-auto">
                                        {endpoint.request_example}
                                    </pre>
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </div>
        );
    };

    return (
        <>
            <div className="p-6 rounded-lg bg-slate-800/50 border border-slate-700">
                <div className="mb-6">
                    <h3 className="text-xl font-semibold text-white mb-4">Endpoints Disponíveis</h3>

                    {workspace.api_jwt_required && (
                        <div className="mb-8">
                            <div className="flex items-center justify-between mb-4">
                                <h4 className="text-lg font-medium text-cyan-400">🔐 Autenticação JWT</h4>
                            </div>
                            <div className="space-y-4">
                                <div className="bg-slate-800 rounded-lg p-4 border border-slate-700">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <div className="flex items-center mb-2">
                                                <span className="bg-green-500 text-white text-xs px-2 py-1 rounded mr-2">
                                                    POST
                                                </span>
                                                <code className="text-cyan-300 text-sm">
                                                    {window.location.origin}/api/v1/auth/login/token
                                                </code>
                                            </div>
                                            <p className="text-slate-300 text-sm mb-3">
                                                Obtenha um token JWT para autenticação
                                            </p>
                                            
                                            <div className="bg-slate-900 rounded p-3 mb-3">
                                                <p className="text-slate-400 text-xs mb-1">Body:</p>
                                                <pre className="text-green-400 text-xs">
                                                    <code>{`{
    "email": "seu@email.com",
    "password": "sua-senha"
}`}</code>
                                                </pre>
                                            </div>
                                            
                                            <div className="bg-slate-900 rounded p-3">
                                                <p className="text-slate-400 text-xs mb-1">Resposta:</p>
                                                <pre className="text-green-400 text-xs">
                                                    <code>{`{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_at": "2024-01-01T00:00:00Z"
}`}</code>
                                                </pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                    
                    {/* Workspace Endpoints */}
                    <EndpointSection 
                        section="workspace"
                        title="📁 Workspace"
                        endpointsData={endpoints.workspace}
                    />

                    {/* Topics Endpoints */}
                    <EndpointSection 
                        section="topics"
                        title="🗂️ Tópicos"
                        endpointsData={endpoints.topics}
                    />

                    {/* Fields Endpoints */}
                    <EndpointSection 
                        section="fields"
                        title="🔤 Campos"
                        endpointsData={endpoints.fields}
                    />
                </div>
            </div>

            {/* Modal para JSON */}
            {modalContent && (
                <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
                    <div className="bg-slate-800 rounded-lg p-6 max-w-4xl max-h-[80vh] w-full mx-4">
                        <div className="flex justify-between items-center mb-4">
                            <h3 className="text-lg font-semibold text-white">{modalTitle}</h3>
                            <button
                                onClick={() => setModalContent(null)}
                                className="text-slate-400 hover:text-white"
                            >
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div className="bg-slate-900 rounded p-4 relative">
                            <button
                                onClick={(e) => copyToClipboard(modalContent, e)}
                                className="absolute top-2 right-2 text-slate-400 hover:text-white p-1 rounded hover:bg-slate-700"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                            <pre className="text-slate-300 text-sm font-mono whitespace-pre-wrap overflow-auto max-h-[60vh]">
                                {modalContent}
                            </pre>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}