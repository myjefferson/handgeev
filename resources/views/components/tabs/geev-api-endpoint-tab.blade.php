<div class="hidden p-6 rounded-lg bg-slate-800/50 border border-slate-700" id="endpoints-tab" role="tabpanel">
    <div class="mb-6">
        <h3 class="text-xl font-semibold text-white mb-4">Endpoints Disponíveis</h3>

        @if ($workspace->api_jwt_required)
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-medium text-cyan-400">🔐 Autenticação JWT</h4>
                </div>
                <div class="space-y-4" id="authEndpoints">
                    <!-- Endpoint de Auth -->
                    <div class="bg-slate-800 rounded-lg p-4 border border-slate-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <span class="bg-green-500 text-white text-xs px-2 py-1 rounded mr-2">POST</span>
                                    <code class="text-cyan-300 text-sm">{{ url('/api/auth/login/token') }}</code>
                                </div>
                                <p class="text-slate-300 text-sm mb-3">
                                    Obtenha um token JWT para autenticação
                                </p>
                                
                                <div class="bg-slate-900 rounded p-3 mb-3">
                                    <p class="text-slate-400 text-xs mb-1">Body:</p>
                                    <pre class="text-green-400 text-xs"><code>{
    "email": "seu@email.com",
    "password": "sua-senha"
}</code></pre>
                                </div>
                                
                                <div class="bg-slate-900 rounded p-3">
                                    <p class="text-slate-400 text-xs mb-1">Resposta:</p>
                                    <pre class="text-green-400 text-xs"><code>{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_at": "2024-01-01T00:00:00Z"
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Workspace Endpoints -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-medium text-cyan-400">📁 Workspace</h4>
                <button onclick="toggleAllExamples('workspace')" class="text-slate-400 hover:text-white text-sm">
                    <span class="example-toggle-text">Mostrar Exemplos</span>
                </button>
            </div>
            <div class="space-y-4" id="workspaceEndpoints">
                <!-- Gerado via JavaScript -->
            </div>
        </div>

        <!-- Topics Endpoints -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-medium text-cyan-400">🗂️ Tópicos</h4>
                <button onclick="toggleAllExamples('topics')" class="text-slate-400 hover:text-white text-sm">
                    <span class="example-toggle-text">Mostrar Exemplos</span>
                </button>
            </div>
            <div class="space-y-4" id="topicsEndpoints">
                <!-- Gerado via JavaScript -->
            </div>
        </div>

        <!-- Fields Endpoints -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-medium text-cyan-400">🔤 Campos</h4>
                <button onclick="toggleAllExamples('fields')" class="text-slate-400 hover:text-white text-sm">
                    <span class="example-toggle-text">Mostrar Exemplos</span>
                </button>
            </div>
            <div class="space-y-4" id="fieldsEndpoints">
                <!-- Gerado via JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        renderEndpoints();
    });

    // Gerar estrutura de endpoints atualizada
    function generateEndpoints() {
        const baseWorkspacePath = `/api/workspaces/{{ $workspace->id }}`;
        const baseUrl = '{{ url('') }}';
        
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
                            workspace_id: {{ $workspace->id }},
                            view_type: "full",
                            rate_limits: {
                                remaining_minute: 45,
                                plan: "Premium"
                            }
                        },
                        workspace: {
                            id: {{ $workspace->id }},
                            title: "{{ $workspace->title }}",
                            description: "{{ $workspace->description }}",
                            type: "{{ $workspace->typeWorkspace->description ?? 'Personal' }}",
                            type_id: {{ $workspace->type_workspace_id }},
                            is_published: {{ $workspace->is_published ? 'true' : 'false' }},
                            api_enabled: {{ $workspace->api_enabled ? 'true' : 'false' }},
                            owner: {
                                id: {{ $workspace->user->id }},
                                name: "{{ $workspace->user->name }}",
                                email: "{{ $workspace->user->email }}"
                            },
                            dates: {
                                created: "{{ $workspace->created_at->toISOString() }}",
                                updated: "{{ $workspace->updated_at->toISOString() }}"
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
                            workspace_id: {{ $workspace->id }},
                            workspace_title: "{{ $workspace->title }}",
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
                    path: `/api/topics/{topicId}`,
                    full_url: `${baseUrl}/topics/{topicId}`,
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
                                id: {{ $workspace->id }},
                                title: "{{ $workspace->title }}"
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
                    path: `/api/topics/{topicId}`,
                    full_url: `${baseUrl}/topics/{topicId}`,
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
                    path: `/api/topics/{topicId}`,
                    full_url: `${baseUrl}/topics/{topicId}`,
                    description: 'Excluir tópico',
                    parameters: [
                        { name: 'topicId', optional: false, description: 'ID do tópico' }
                    ]
                }
            ],
            fields: [
                {
                    method: 'GET',
                    path: `/api/topics/{topicId}/fields`,
                    full_url: `${baseUrl}/topics/{topicId}/fields`,
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
                            workspace_id: {{ $workspace->id }},
                            workspace_title: "{{ $workspace->title }}",
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
                    path: `/api/topics/{topicId}/fields`,
                    full_url: `${baseUrl}/topics/{topicId}/fields`,
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
                    path: `/api/fields/{fieldId}`,
                    full_url: `${baseUrl}/fields/{fieldId}`,
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
                    path: `/api/fields/{fieldId}`,
                    full_url: `${baseUrl}/fields/{fieldId}`,
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
                    path: `/api/fields/{fieldId}/visibility`,
                    full_url: `${baseUrl}/fields/{fieldId}/visibility`,
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
                    path: `/api/fields/{fieldId}`,
                    full_url: `${baseUrl}/fields/{fieldId}`,
                    description: 'Excluir campo',
                    parameters: [
                        { name: 'fieldId', optional: false, description: 'ID do campo' }
                    ]
                }
            ]
        }
    }

    function renderEndpointSection(containerId, endpoints) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = endpoints.map(endpoint => `
            <div class="bg-slate-900 rounded-lg p-4 border border-slate-700 hover:border-cyan-500/50 transition-colors">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 text-xs font-mono rounded ${getMethodColor(endpoint.method)}">
                            ${endpoint.method}
                        </span>
                        <code class="text-cyan-300 text-sm">${endpoint.path}</code>
                        ${endpoint.query_params ? `
                            <code class="text-slate-400 text-sm">${endpoint.query_params}</code>
                        ` : ''}
                    </div>
                    <div class="flex items-center space-x-2">
                        ${endpoint.response_example ? `
                            <button onclick="showJsonExample('${endpoint.response_example.replace(/'/g, "\\'")}', '${endpoint.description}')" 
                                    class="text-slate-400 hover:text-cyan-300 transition-colors p-1 rounded hover:bg-slate-800"
                                    title="Ver exemplo de resposta">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                </svg>
                            </button>
                        ` : ''}
                        <button onclick="copyToClipboard('${endpoint.full_url}')" 
                                class="text-slate-400 hover:text-cyan-300 transition-colors p-1 rounded hover:bg-slate-800"
                                title="Copiar URL">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <p class="text-slate-400 text-sm mb-3">${endpoint.description}</p>
                
                ${endpoint.parameters && endpoint.parameters.length ? `
                    <div class="mt-2">
                        <span class="text-slate-500 text-xs uppercase font-semibold">Parâmetros:</span>
                        <div class="mt-2 space-y-1">
                            ${endpoint.parameters.map(param => `
                                <div class="flex items-center text-xs">
                                    <code class="bg-slate-800 text-cyan-300 px-2 py-1 rounded mr-2 min-w-20 text-center">${param.name}</code>
                                    <span class="text-slate-400 ${param.optional ? 'italic' : ''}">
                                        ${param.description}
                                        ${param.optional ? ' (opcional)' : ' (obrigatório)'}
                                    </span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : ''}
                
                ${endpoint.request_example ? `
                    <div class="mt-3 pt-3 border-t border-slate-700">
                        <button onclick="toggleExample(this)" class="text-slate-400 hover:text-white text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                            Exemplo de Request
                        </button>
                        <div class="mt-2 hidden">
                            <div class="bg-slate-800 rounded p-3 relative">
                                <button onclick="copyToClipboard(this.nextElementSibling.textContent)" class="absolute top-2 right-2 text-slate-400 hover:text-white p-1 rounded hover:bg-slate-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                <pre class="text-slate-300 text-xs font-mono whitespace-pre-wrap overflow-x-auto">${endpoint.request_example}</pre>
                            </div>
                        </div>
                    </div>
                ` : ''}
            </div>
        `).join('');
    }

    function renderEndpoints() {
        const endpoints = generateEndpoints();
        
        renderEndpointSection('workspaceEndpoints', endpoints.workspace);
        renderEndpointSection('topicsEndpoints', endpoints.topics);
        renderEndpointSection('fieldsEndpoints', endpoints.fields);
    }

    function getMethodColor(method) {
        const colors = {
            'GET': 'bg-green-500',
            'POST': 'bg-blue-500',
            'PUT': 'bg-yellow-500',
            'PATCH': 'bg-orange-500',
            'DELETE': 'bg-red-500'
        };
        return colors[method] || 'bg-slate-500';
    }

    function toggleExample(button) {
        const content = button.nextElementSibling;
        const isHidden = content.classList.contains('hidden');
        
        if (isHidden) {
            content.classList.remove('hidden');
            button.querySelector('svg').style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            button.querySelector('svg').style.transform = 'rotate(0deg)';
        }
    }

    function toggleAllExamples(section) {
        const container = document.getElementById(section + 'Endpoints');
        const buttons = container.querySelectorAll('button[onclick="toggleExample(this)"]');
        const anyHidden = Array.from(buttons).some(btn => 
            btn.nextElementSibling.classList.contains('hidden')
        );

        buttons.forEach(btn => {
            const content = btn.nextElementSibling;
            if (anyHidden && content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                btn.querySelector('svg').style.transform = 'rotate(180deg)';
            } else if (!anyHidden && !content.classList.contains('hidden')) {
                content.classList.add('hidden');
                btn.querySelector('svg').style.transform = 'rotate(0deg)';
            }
        });

        // Atualizar texto do botão toggle geral
        const toggleBtn = document.querySelector(`[onclick="toggleAllExamples('${section}')"] .example-toggle-text`);
        if (toggleBtn) {
            toggleBtn.textContent = anyHidden ? 'Ocultar Exemplos' : 'Mostrar Exemplos';
        }
    }

    function showJsonExample(jsonString, title = 'Exemplo de Resposta') {
        // Criar modal para mostrar o JSON
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-slate-800 rounded-lg p-6 max-w-4xl max-h-[80vh] w-full mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">${title}</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="bg-slate-900 rounded p-4 relative">
                    <button onclick="copyToClipboard(this.nextElementSibling.textContent)" class="absolute top-2 right-2 text-slate-400 hover:text-white p-1 rounded hover:bg-slate-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                    <pre class="text-slate-300 text-sm font-mono whitespace-pre-wrap overflow-auto max-h-[60vh]">${jsonString}</pre>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Mostrar feedback visual
            const originalText = event.target.innerHTML;
            event.target.innerHTML = `
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            `;
            setTimeout(() => {
                event.target.innerHTML = originalText;
            }, 2000);
        });
    }
</script>