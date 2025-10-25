@extends('template.template-dashboard')

@section('title', 'API '.$workspace->title)
@section('description', 'API REST do HandGeev - '.$workspace->title)

@section('content_dashboard')
<div class="min-h-screen">
    <div class="max-w-7xl mx-auto p-0 sm:p-0 md:p-6">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">API REST - {{ $workspace->title }}</h1>
                <p class="text-slate-400 mt-2">Gerencie e integre seus dados através de API</p>
            </div>
            <a href="{{ url()->previous() }}" 
               class="flex items-center text-cyan-400 hover:text-cyan-300 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar
            </a>
        </div>

        <!-- API Status Cards -->
        @include('components.alerts.alert')
        
        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Status Card -->
            <div class="bg-slate-800 rounded-xl p-6 border border-cyan-500/20">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Status da API</h3>
                        @if ($workspace->api_enabled)    
                            <p class="text-green-400 flex items-center mt-1">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                API Ativa
                            </p>
                        @else
                            <p class="text-red-400 flex items-center mt-1">
                                <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                API Desativada
                            </p>
                        @endif
                    </div>
                    <div class="p-3 bg-cyan-500/10 rounded-lg">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Base URL Card -->
            <div class="bg-slate-800 rounded-xl p-6 border border-cyan-500/20">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Base URL</h3>
                        <p class="text-slate-300 font-mono text-sm mt-1">{{ url('/api') }}</p>
                    </div>
                    <button onclick="copyToClipboard('{{ url('/api') }}')" class="p-2 text-cyan-400 hover:text-cyan-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Workspace Key Card -->
            <div class="bg-slate-800 rounded-xl p-6 border {{ $workspace->api_jwt_required ? 'border-amber-500/20' : 'border-cyan-500/20' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-2 mb-1">
                            <h3 class="text-lg font-semibold text-white">
                                {{ $workspace->api_jwt_required ? 'JWT Authentication' : 'Workspace Key' }}
                            </h3>
                        </div>
                        
                        @if($workspace->api_jwt_required)
                            <p class="text-slate-300 text-sm">Use seu login para gerar tokens JWT</p>
                        @else
                            <p class="text-slate-300 font-mono text-sm mt-1" id="apiKeyDisplay">
                                @if($workspace->workspace_key_api)
                                    ••••••••{{ substr($workspace->workspace_key_api, -8) }}
                                @else
                                    Não gerada
                                @endif
                            </p>
                        @endif
                    </div>
                    
                    <div class="flex space-x-2">
                        @if(!$workspace->api_jwt_required)
                            @if($workspace->workspace_key_api)
                                <button onclick="regenerateApiKey()" class="p-2 text-yellow-400 hover:text-yellow-300" title="Regenerar Key">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                            @else
                                <button onclick="generateApiKey()" class="p-2 text-green-400 hover:text-green-300" title="Gerar Key">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                </button>
                            @endif
                            <button onclick="copyToClipboard('{{ $workspace->workspace_hash_api }}')" class="p-2 {{ $workspace->api_jwt_required ? 'text-amber-400 hover:text-amber-300' : 'text-cyan-400 hover:text-cyan-300' }}" title="{{ $workspace->api_jwt_required ? 'Copiar Workspace Hash' : 'Copiar Workspace Key' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        @endif
                        
                    </div>
                </div>
            </div>

            {{-- <!-- Usage Stats Card -->
            <div class="bg-slate-800 rounded-xl p-6 border border-cyan-500/20">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Uso do Plano</h3>
                        <p class="text-slate-300 text-sm mt-1" id="usageText">Carregando...</p>
                    </div>
                    <div class="p-3 bg-purple-500/10 rounded-lg">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div id="usageBar" class="bg-purple-400 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-8 border-b border-slate-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" 
                id="apiTabs"
                role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-slate-400 border-transparent hover:text-slate-300 hover:border-slate-300 flex items-center" 
                            type="button" 
                            role="tab" 
                            aria-controls="statistics-tab" 
                            aria-selected="true" 
                            data-tab-target="statistics-tab">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Estatísticas
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-cyan-400 border-cyan-400 flex items-center" 
                            type="button" 
                            role="tab" 
                            aria-controls="endpoints-tab" 
                            aria-selected="false"
                            data-tab-target="endpoints-tab">
                        <i class="fas fa-satellite-dish mr-2"></i>
                        Endpoints
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-slate-400 border-transparent hover:text-slate-300 hover:border-slate-300 flex items-center" 
                            type="button" 
                            role="tab" 
                            aria-controls="documentation-tab" 
                            aria-selected="false" 
                            data-tab-target="documentation-tab">
                        <i class="fas fa-book mr-2"></i>
                        Documentação
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-slate-400 border-transparent hover:text-slate-300 hover:border-slate-300 flex items-center" 
                            type="button" 
                            role="tab" 
                            aria-controls="permissions-tab" 
                            aria-selected="false" 
                            data-tab-target="permissions-tab">
                        <i class="fas fa-lock mr-2"></i>
                        Permissões
                    </button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-slate-400 border-transparent hover:text-slate-300 hover:border-slate-300 flex items-center" 
                            type="button" 
                            role="tab" 
                            aria-controls="settings-tab" 
                            aria-selected="false" 
                            data-tab-target="settings-tab">
                        <i class="fas fa-cog mr-2"></i>
                        Configurações
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div>
            <!-- Statistics Tab -->
            @include('components.tabs.restapi-statistics-tab', $workspace)

            <!-- Endpoints Tab -->
            @include('components.tabs.restapi-endpoint-tab', $workspace)

            <!-- Documentation Tab -->
            @include('components.tabs.restapi-documentation-tab', $workspace)

            <!-- Permissions Tab -->
            @include('components.tabs.restapi-permission-tab', $workspace)

            <!-- Settings Tab -->
            @include('components.tabs.restapi-setting-tab', $workspace)
        </div>
    </div>
</div>

<!-- Modal para Exemplo de JSON -->
<div id="jsonExampleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-slate-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-hidden">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-white">Exemplo de Resposta</h3>
            <button onclick="closeModal('jsonExampleModal')" class="text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="bg-slate-900 rounded p-4 max-h-[60vh] overflow-y-auto">
            <pre id="modalJsonContent" class="text-slate-300 text-sm font-mono whitespace-pre-wrap"></pre>
        </div>
        <div class="mt-4 flex justify-end space-x-2">
            <button onclick="copyToClipboard(document.getElementById('modalJsonContent').textContent)" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded text-sm transition-colors">
                Copiar JSON
            </button>
            <button onclick="closeModal('jsonExampleModal')" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded text-sm transition-colors">
                Fechar
            </button>
        </div>
    </div>
</div>

<script type="module">
    import '/js/modules/tab/RestApiTabManager.js'
</script>

<script>
// Configurações
const WORKSPACE_ID = {{ $workspace->id }};
const BASE_URL = '{{ url('/api') }}';
const workspace_key = '{{ $workspace->workspace_key_api }}';
const USER_PLAN = '{{ auth()->user()->getPlan()->name }}';
const USER_ID = {{ auth()->id() }};
const CSRF_TOKEN = '{{ csrf_token() }}';

// Estado global
let currentPermissions = {
    workspace: [],
    topics: [],
    fields: []
};

// Inicializar quando o DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    updateDocCodeExample();
});

// Sistema de Tabs
function initializeTabs() {
    const tabButtons = document.querySelectorAll('[data-tab-target]');
    const tabContents = document.querySelectorAll('[role="tabpanel"]');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-tab-target');
            
            // Esconder todos os conteúdos
            tabContents.forEach(content => content.classList.add('hidden'));
            
            // Remover estilos ativos de todos os botões
            tabButtons.forEach(btn => {
                btn.classList.remove('text-cyan-400', 'border-cyan-400');
                btn.classList.add('text-slate-400', 'border-transparent');
            });
            
            // Mostrar conteúdo alvo e estilizar botão ativo
            document.getElementById(targetId).classList.remove('hidden');
            button.classList.remove('text-slate-400', 'border-transparent');
            button.classList.add('text-cyan-400', 'border-cyan-400');
        });
    });
}

async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showNotification('Copiado para a área de transferência!', 'success');
    } catch (err) {
        console.error('Erro ao copiar:', err);
        showNotification('Erro ao copiar texto', 'error');
    }
}

// Notificações
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Modal System
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function updateUsageDisplay(data) {
    const usageText = document.getElementById('usageText');
    const usageBar = document.getElementById('usageBar');
    
    const remaining = data.current_usage?.minute?.remaining || 0;
    const limit = data.limits?.per_minute || 60;
    const percentage = (remaining / limit) * 100;
    
    usageText.textContent = `${remaining} / ${limit} req/min`;
    usageBar.style.width = `${percentage}%`;
    
    // Cor baseada no uso
    if (percentage < 20) {
        usageBar.className = 'bg-red-400 h-2 rounded-full transition-all duration-500';
    } else if (percentage < 50) {
        usageBar.className = 'bg-yellow-400 h-2 rounded-full transition-all duration-500';
    } else {
        usageBar.className = 'bg-purple-400 h-2 rounded-full transition-all duration-500';
    }
}

function toggleExample(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('svg');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}

function toggleAllExamples(section) {
    const buttons = document.querySelectorAll(`#${section}Endpoints [onclick="toggleExample(this)"]`);
    const firstButton = buttons[0];
    const isHidden = firstButton.nextElementSibling.classList.contains('hidden');
    
    buttons.forEach(button => {
        const content = button.nextElementSibling;
        const icon = button.querySelector('svg');
        
        if (isHidden) {
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    });
    
    // Atualizar texto do botão toggle
    const toggleButton = document.querySelector(`#${section}Endpoints`).previousElementSibling.querySelector('button');
    const toggleText = toggleButton.querySelector('.example-toggle-text');
    toggleText.textContent = isHidden ? 'Ocultar Exemplos' : 'Mostrar Exemplos';
}

function showJsonExample(jsonString) {
    try {
        const jsonObj = JSON.parse(jsonString);
        const formattedJson = JSON.stringify(jsonObj, null, 2);
        document.getElementById('modalJsonContent').textContent = formattedJson;
        openModal('jsonExampleModal');
    } catch (e) {
        document.getElementById('modalJsonContent').textContent = jsonString;
        openModal('jsonExampleModal');
    }
}

// Documentação
function updateDocCodeExample() {
    const select = document.getElementById('selectDocExample');
    const output = document.getElementById('docCodeOutput');
    
    const examples = {
        javascript: `// JavaScript (Fetch API)
const response = await fetch('${BASE_URL}/workspaces/${WORKSPACE_ID}', {
    method: 'GET',
    headers: {
        'Authorization': 'Bearer ${workspace_key}',
        'Content-Type': 'application/json'
    }
});

if (response.ok) {
    const data = await response.json();
    console.log(data);
} else {
    console.error('Erro:', response.status);
}`,

        php: `<?php
// PHP (cURL)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, '${BASE_URL}/workspaces/${WORKSPACE_ID}');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ${workspace_key}',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    print_r($data);
} else {
    echo "Erro: " . $httpCode;
}
?>`,

        python: `# Python (requests)
import requests

url = '${BASE_URL}/workspaces/${WORKSPACE_ID}'
headers = {
    'Authorization': 'Bearer ${workspace_key}',
    'Content-Type': 'application/json'
}

response = requests.get(url, headers=headers)

if response.status_code == 200:
    data = response.json()
    print(data)
else:
    print(f"Erro: {response.status_code}")`,

        curl: `# cURL
curl -X GET \\
  '${BASE_URL}/workspaces/${WORKSPACE_ID}' \\
  -H 'Authorization: Bearer ${workspace_key}' \\
  -H 'Content-Type: application/json'`
    };

    output.textContent = examples[select.value] || examples.javascript;
}

// Adicionar event listener para o select
document.getElementById('selectDocExample').addEventListener('change', updateDocCodeExample);

// Exportação de Documentação
async function exportDocumentation(format) {
    try {
        const endpoints = generateEndpoints();
        let content, mimeType, filename;

        if (format === 'json') {
            content = JSON.stringify({
                openapi: "3.0.0",
                info: {
                    title: "{{ $workspace->title }} - API Documentation",
                    description: "API documentation for {{ $workspace->title }} workspace",
                    version: "1.0.0",
                    contact: {
                        name: "{{ $workspace->user->name }}",
                        email: "{{ $workspace->user->email }}"
                    }
                },
                servers: [
                    {
                        url: BASE_URL,
                        description: "Production server"
                    }
                ],
                paths: generateOpenAPIPaths(endpoints),
                components: {
                    securitySchemes: {
                        bearerAuth: {
                            type: "http",
                            scheme: "bearer",
                            bearerFormat: "JWT"
                        }
                    }
                },
                security: [{
                    bearerAuth: []
                }]
            }, null, 2);
            mimeType = 'application/json';
            filename = '{{ $workspace->title }}-api-docs.json';
        } else if (format === 'yaml') {
            // Implementação básica de YAML - em produção use uma lib
            content = generateYamlContent(endpoints);
            mimeType = 'application/yaml';
            filename = '{{ $workspace->title }}-api-docs.yaml';
        }

        downloadFile(content, filename, mimeType);
        showNotification('Documentação exportada com sucesso!', 'success');
    } catch (error) {
        console.error('Erro ao exportar documentação:', error);
        showNotification('Erro ao exportar documentação', 'error');
    }
}

function exportOpenAPI() {
    if (!['pro', 'premium', 'admin'].includes(USER_PLAN.toLowerCase())) {
        showNotification('Exportação OpenAPI disponível apenas para planos Pro e Premium', 'error');
        return;
    }
    exportDocumentation('json');
}

function generateOpenAPIPaths(endpoints) {
    const paths = {};
    
    // Converter endpoints para formato OpenAPI
    Object.values(endpoints).flat().forEach(endpoint => {
        const path = endpoint.path.replace(`{WORKSPACE_ID}`, `${WORKSPACE_ID}`);
        if (!paths[path]) paths[path] = {};
        
        paths[path][endpoint.method.toLowerCase()] = {
            summary: endpoint.description,
            parameters: endpoint.parameters.map(param => ({
                name: param,
                in: 'path',
                required: true,
                schema: { type: 'string' }
            })),
            responses: {
                '200': {
                    description: 'Success',
                    content: {
                        'application/json': {
                            example: endpoint.response_example ? JSON.parse(endpoint.response_example) : {}
                        }
                    }
                }
            }
        };
    });
    
    return paths;
}

function generateYamlContent(endpoints) {
    // Implementação simplificada - em produção use js-yaml ou similar
    return `openapi: 3.0.0
info:
  title: "{{ $workspace->title }} - API Documentation"
  description: "API documentation for {{ $workspace->title }} workspace"
  version: "1.0.0"
servers:
  - url: ${BASE_URL}
    description: Production server
paths:
  # ... paths would be generated here
`;
}

function downloadFile(content, filename, mimeType) {
    const blob = new Blob([content], { type: mimeType });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Gerenciamento de Workspace Key
async function generateApiKey() {
    if (!confirm('Deseja gerar uma nova Workspace Key?')) return;
    
    try {
        const response = await fetch(`/workspace/${WORKSPACE_ID}/generate-api-key`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Workspace Key gerada com sucesso!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Erro ao gerar Workspace Key', 'error');
        }
    } catch (error) {
        showNotification('Erro de conexão', 'error');
    }
}

async function regenerateApiKey() {
    if (!confirm('Tem certeza? Isso invalidará a chave atual.')) return;
    await generateApiKey();
}

// Funções auxiliares
function getMethodColor(method) {
    const colors = {
        'GET': 'bg-green-500/20 text-green-400',
        'POST': 'bg-blue-500/20 text-blue-400',
        'PUT': 'bg-yellow-500/20 text-yellow-400',
        'PATCH': 'bg-orange-500/20 text-orange-400',
        'DELETE': 'bg-red-500/20 text-red-400'
    };
    return colors[method] || 'bg-slate-500/20 text-slate-400';
}

// Inicializar exemplos de documentação
updateDocCodeExample();
</script>

@endsection