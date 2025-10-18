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

    // Gerar estrutura de endpoints
    function generateEndpoints() {
        const baseWorkspacePath = `/api/workspaces/{{ $workspace->id }}`;
        
        return {
            workspace: [
                {
                    method: 'GET',
                    path: `${baseWorkspacePath}`,
                    full_url: `${BASE_URL}${baseWorkspacePath}`,
                    description: 'Obter informações completas do workspace',
                    parameters: [],
                    response_example: JSON.stringify({
                        metadata: { 
                            version: "1.0", 
                            generated_at: "2024-01-01T00:00:00Z",
                            workspace_id: {{ $workspace->id }},
                            rate_limits: {
                                remaining_minute: 45,
                                plan: USER_PLAN
                            }
                        },
                        workspace: {
                            id: "{{ $workspace->id }}",
                            title: "{{ $workspace->title }}",
                            description: "{{ $workspace->description }}",
                            type: "{{ $workspace->typeWorkspace->description }}",
                            type_id: "{{ $workspace->type_workspace_id }}",
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
                                fields: [
                                    {
                                        id: 1,
                                        key: "nome",
                                        value: "João Silva",
                                        type: "text",
                                        visibility: true,
                                        order: 1,
                                        metadata: {
                                            created: "2024-01-01T00:00:00Z",
                                            updated: "2024-01-01T00:00:00Z"
                                        }
                                    },
                                    {
                                        id: 2,
                                        key: "idade",
                                        value: "28",
                                        type: "number",
                                        visibility: true,
                                        order: 2,
                                        metadata: {
                                            created: "2024-01-01T00:00:00Z",
                                            updated: "2024-01-01T00:00:00Z"
                                        }
                                    }
                                ]
                            }
                        ],
                        statistics: {
                            total_topics: 1,
                            total_fields: 3,
                            visible_fields: 3
                        }
                    }, null, 2)
                },
                {
                    method: 'GET',
                    path: `${baseWorkspacePath}/stats`,
                    full_url: `${BASE_URL}${baseWorkspacePath}/stats`,
                    description: 'Obter estatísticas detalhadas do workspace',
                    parameters: []
                },
                {
                    method: 'PUT',
                    path: `${baseWorkspacePath}`,
                    full_url: `${BASE_URL}${baseWorkspacePath}`,
                    description: 'Atualizar informações do workspace',
                    parameters: ['title', 'description', 'is_published'],
                    request_example: JSON.stringify({
                        title: "Novo Título do Workspace",
                        description: "Nova descrição atualizada",
                        is_published: true
                    }, null, 2)
                },
                {
                    method: 'PATCH',
                    path: `${baseWorkspacePath}/settings`,
                    full_url: `${BASE_URL}${baseWorkspacePath}/settings`,
                    description: 'Atualizar configurações da API',
                    parameters: ['api_enabled', 'allowed_domains'],
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
                    full_url: `${BASE_URL}${baseWorkspacePath}/topics`,
                    description: 'Listar todos os tópicos do workspace',
                    parameters: []
                },
                {
                    method: 'POST',
                    path: `${baseWorkspacePath}/topics`,
                    full_url: `${BASE_URL}${baseWorkspacePath}/topics`,
                    description: 'Criar novo tópico',
                    parameters: ['title', 'order'],
                    request_example: JSON.stringify({
                        title: "Novo Tópico",
                        order: 1
                    }, null, 2)
                },
                {
                    method: 'GET',
                    path: `/topics/{id}`,
                    full_url: `${BASE_URL}/topics/{id}`,
                    description: 'Obter detalhes de um tópico específico',
                    parameters: ['id']
                },
                {
                    method: 'PUT',
                    path: `/topics/{id}`,
                    full_url: `${BASE_URL}/topics/{id}`,
                    description: 'Atualizar tópico',
                    parameters: ['id', 'title', 'order'],
                    request_example: JSON.stringify({
                        title: "Tópico Atualizado",
                        order: 2
                    }, null, 2)
                },
                {
                    method: 'DELETE',
                    path: `/topics/{id}`,
                    full_url: `${BASE_URL}/topics/{id}`,
                    description: 'Excluir tópico',
                    parameters: ['id']
                }
            ],
            fields: [
                {
                    method: 'GET',
                    path: `/topics/{id}/fields`,
                    full_url: `${BASE_URL}/topics/{id}/fields`,
                    description: 'Listar campos de um tópico',
                    parameters: ['topic_id']
                },
                {
                    method: 'POST',
                    path: `/topics/{id}/fields`,
                    full_url: `${BASE_URL}/topics/{id}/fields`,
                    description: 'Criar novo campo',
                    parameters: ['topic_id', 'key_name', 'value', 'type', 'is_visible', 'order'],
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
                    path: `/fields/{id}`,
                    full_url: `${BASE_URL}/fields/{id}`,
                    description: 'Obter detalhes de um campo',
                    parameters: ['field_id']
                },
                {
                    method: 'PUT',
                    path: `/fields/{id}`,
                    full_url: `${BASE_URL}/fields/{id}`,
                    description: 'Atualizar campo',
                    parameters: ['field_id', 'key_name', 'value', 'type', 'is_visible', 'order'],
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
                    path: `/fields/{id}/visibility`,
                    full_url: `${BASE_URL}/fields/{id}/visibility`,
                    description: 'Atualizar visibilidade do campo',
                    parameters: ['field_id', 'is_visible'],
                    request_example: JSON.stringify({
                        is_visible: false
                    }, null, 2)
                },
                {
                    method: 'DELETE',
                    path: `/fields/{id}`,
                    full_url: `${BASE_URL}/fields/{id}`,
                    description: 'Excluir campo',
                    parameters: ['field_id']
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
                    </div>
                    <div class="flex items-center space-x-2">
                        ${endpoint.response_example ? `
                            <button onclick="showJsonExample('${endpoint.response_example.replace(/'/g, "\\'")}')" 
                                    class="text-slate-400 hover:text-cyan-300 transition-colors"
                                    title="Ver exemplo de resposta">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                </svg>
                            </button>
                        ` : ''}
                        <button onclick="copyToClipboard('${endpoint.full_url}')" 
                                class="text-slate-400 hover:text-cyan-300 transition-colors"
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
                        <div class="flex flex-wrap gap-1 mt-1">
                            ${endpoint.parameters.map(param => `
                                <span class="px-2 py-1 bg-slate-800 text-slate-300 text-xs rounded">${param}</span>
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
                                <button onclick="copyToClipboard(this.nextElementSibling.textContent)" class="absolute top-2 right-2 text-slate-400 hover:text-white">
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
</script>