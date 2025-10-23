<div class="hidden p-6 rounded-lg bg-slate-800/50 border border-slate-700" id="documentation-tab" role="tabpanel">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold text-white">
            <i class="fas fa-book mr-2 text-cyan-400"></i>
            Documentação da API
        </h3>
        <div class="flex items-center space-x-4">
            <!-- Indicador do Tipo de Autenticação -->
            <div class="flex items-center space-x-2 px-3 py-1 rounded-full {{ $workspace->api_jwt_required ? 'bg-amber-500/20 border border-amber-500/50' : 'bg-green-500/20 border border-green-500/50' }}">
                <div class="w-2 h-2 rounded-full {{ $workspace->api_jwt_required ? 'bg-amber-500' : 'bg-green-500' }}"></div>
                <span class="text-sm font-medium {{ $workspace->api_jwt_required ? 'text-amber-300' : 'text-green-300' }}">
                    {{ $workspace->api_jwt_required ? 'JWT Authentication' : 'Workspace Key Authentication' }}
                </span>
            </div>
            
            <div class="flex space-x-2">
                <button onclick="exportDocumentation('json')" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm transition-colors flex items-center">
                    <i class="fas fa-file-export mr-2"></i>
                    Exportar JSON
                </button>
                @if(auth()->user()->isPro() || auth()->user()->isPremium())
                <button onclick="exportDocumentation('yaml')" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm transition-colors flex items-center">
                    <i class="fas fa-file-code mr-2"></i>
                    Exportar YAML
                </button>
                <button onclick="exportOpenAPI()" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm transition-colors flex items-center">
                    <i class="fas fa-api mr-2"></i>
                    OpenAPI Spec
                </button>
                @endif
            </div>
        </div>
    </div>
    
    <div class="space-y-6">
        <!-- Autenticação Dinâmica -->
        <div class="bg-slate-800 border border-slate-700 mb-6 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-cyan-400 text-lg font-semibold">
                    <i class="fas fa-key mr-2"></i>
                    Autenticação
                </h4>
                <span class="px-2 py-1 text-xs rounded {{ $workspace->api_jwt_required ? 'bg-amber-500/20 text-amber-300' : 'bg-green-500/20 text-green-300' }}">
                    {{ $workspace->api_jwt_required ? 'JWT Required' : 'Workspace Key' }}
                </span>
            </div>

            @if($workspace->api_jwt_required)
            <!-- Documentação JWT -->
            <div class="space-y-4">
                <p class="text-slate-300">Esta workspace requer autenticação JWT. Primeiro obtenha um token JWT:</p>
                
                <div class="bg-slate-800 rounded p-4">
                    <h5 class="text-cyan-300 font-semibold mb-2">
                        <i class="fas fa-token mr-1"></i>
                        Obter Token JWT:
                    </h5>
                    <div class="bg-black rounded p-3 mb-3">
                        <code class="text-green-300 font-mono text-sm">
                            POST {{ url('/api/auth/token') }}
                        </code>
                    </div>
                    <pre class="text-slate-300 text-sm font-mono overflow-x-auto">
                        <code>
// Request Body
{
    "email": "{{ auth()->user()->email }}",
    "password": "sua-senha"
}

// Example Response
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "expires_in": 3600,
    "type": "Bearer"
}
                        </code>
                    </pre>
                    <button onclick="copyToClipboard(this.parentNode.querySelector('code').textContent)" class="mt-2 text-cyan-400 hover:text-cyan-300 text-sm flex items-center">
                        <i class="fas fa-copy mr-1"></i>
                        Copiar
                    </button>
                </div>

                <p class="text-slate-300 mt-4">Use o token JWT em todas as requisições:</p>
                <div class="bg-slate-800 rounded p-3 flex items-center justify-between">
                    <code class="text-cyan-300 font-mono text-sm">
                        Authorization: Bearer <span id="authKeyExample">SEU_TOKEN_JWT_AQUI</span>
                    </code>
                    <button onclick="copyToClipboard('Bearer SEU_TOKEN_JWT_AQUI')" class="text-cyan-400 hover:text-cyan-300">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            @else
            <!-- Documentação Workspace Key -->
            <div class="space-y-4">
                <p class="text-slate-300">Use sua Workspace Key em todas as requisições:</p>
                <div class="bg-slate-800 rounded p-3 flex items-center justify-between">
                    <code class="text-cyan-300 font-mono text-sm">
                        Authorization: Bearer <span id="authKeyExample">{{ $workspace->email_api ?: 'SUA_WORKSPACE_KEY' }}</span>
                    </code>
                    <button onclick="copyToClipboard('Bearer {{ $workspace->email_api }}')" class="text-cyan-400 hover:text-cyan-300">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <p class="text-slate-300 text-sm bg-blue-500/10 border border-blue-500/20 rounded p-3">
                    <i class="fas fa-lightbulb text-yellow-400 mr-2"></i>
                    <strong>Dica:</strong> Esta é sua chave permanente. Mantenha-a segura!
                </p>
            </div>
            @endif
        </div>

        <!-- Estrutura de Resposta -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-slate-900 rounded-lg p-6">
                <h4 class="text-cyan-400 text-lg font-semibold mb-3">
                    <i class="fas fa-code mr-2"></i>
                    Estrutura de Resposta
                </h4>
                <pre class="text-slate-300 text-sm font-mono overflow-x-auto">
                    <code id="responseStructure">
{
    "success": true,
    "data": { ... },
    "message": "Operação realizada com sucesso",
    "metadata": {
        "version": "1.0",
        "generated_at": "2024-01-01T00:00:00Z",
        "workspace_id": {{ $workspace->id }},
        "rate_limit": {
            "remaining": 59,
            "limit": 60
        }
    }
}
                    </code>
                </pre>
                <button onclick="copyToClipboard(document.getElementById('responseStructure').textContent)" class="mt-2 text-cyan-400 hover:text-cyan-300 text-sm flex items-center">
                    <i class="fas fa-copy mr-1"></i>
                    Copiar
                </button>
            </div>

            <div class="bg-slate-900 rounded-lg p-6">
                <h4 class="text-cyan-400 text-lg font-semibold mb-3">
                    <i class="fas fa-list-alt mr-2"></i>
                    Códigos de Status
                </h4>
                <ul class="text-slate-300 space-y-2 text-sm">
                    <li class="flex items-center"><span class="text-green-400 font-mono mr-2">200</span> Sucesso</li>
                    <li class="flex items-center"><span class="text-blue-400 font-mono mr-2">201</span> Criado</li>
                    <li class="flex items-center"><span class="text-yellow-400 font-mono mr-2">400</span> Erro na requisição</li>
                    <li class="flex items-center"><span class="text-amber-400 font-mono mr-2">401</span> Não autorizado</li>
                    <li class="flex items-center"><span class="text-orange-400 font-mono mr-2">403</span> Proibido/Acesso negado</li>
                    <li class="flex items-center"><span class="text-red-400 font-mono mr-2">404</span> Não encontrado</li>
                    <li class="flex items-center"><span class="text-red-400 font-mono mr-2">405</span> Método não permitido</li>
                    <li class="flex items-center"><span class="text-purple-400 font-mono mr-2">429</span> Limite de requisições excedido</li>
                </ul>
                
                <div class="mt-4 p-3 bg-slate-800 rounded">
                    <h5 class="text-cyan-300 text-sm font-semibold mb-2">
                        <i class="fas fa-tachometer-alt mr-1"></i>
                        Headers de Rate Limit:
                    </h5>
                    <ul class="text-slate-300 text-xs space-y-1">
                        <li><code class="text-cyan-300">X-RateLimit-Limit: 60</code></li>
                        <li><code class="text-cyan-300">X-RateLimit-Remaining: 59</code></li>
                        <li><code class="text-cyan-300">X-RateLimit-Reset: 1704067200</code></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Exemplo de Uso Dinâmico -->
        <div class="bg-slate-900 rounded-lg p-6">
            <h4 class="text-cyan-400 text-lg font-semibold mb-3">
                <i class="fas fa-rocket mr-2"></i>
                Exemplo de Uso
            </h4>
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            <i class="fas fa-code mr-1"></i>
                            Selecione a linguagem:
                        </label>
                        <select id="selectDocExample" class="bg-slate-800 border border-slate-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                            <option value="javascript" selected>JavaScript</option>
                            <option value="php">PHP</option>
                            <option value="python">Python</option>
                            <option value="curl">cURL</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">
                            <i class="fas fa-link mr-1"></i>
                            Endpoint:
                        </label>
                        <select id="selectEndpoint" class="bg-slate-800 border border-slate-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                            <option value="workspace">Workspace</option>
                            <option value="topics">Topics</option>
                            <option value="fields">Fields</option>
                        </select>
                    </div>
                </div>
                <div class="bg-slate-800 rounded p-4 relative">
                    <button onclick="copyToClipboard(document.getElementById('docCodeOutput').textContent)" class="absolute top-2 right-2 text-slate-400 hover:text-white">
                        <i class="fas fa-copy"></i>
                    </button>
                    <pre id="docCodeOutput" class="text-slate-300 text-sm font-mono whitespace-pre-wrap overflow-x-auto"></pre>
                </div>
            </div>
        </div>

        <!-- Dicas de Segurança -->
        <div class="bg-slate-900 rounded-lg p-6">
            <h4 class="text-cyan-400 text-lg font-semibold mb-3">
                <i class="fas fa-shield-alt mr-2"></i>
                Melhores Práticas
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-800 rounded p-4">
                    <h5 class="text-green-400 font-semibold mb-2">
                        <i class="fas fa-check-circle mr-1"></i>
                        O que fazer:
                    </h5>
                    <ul class="text-slate-300 text-sm space-y-1">
                        <li><i class="fas fa-chevron-right text-green-400 mr-2 text-xs"></i>Armazene chaves em variáveis de ambiente</li>
                        <li><i class="fas fa-chevron-right text-green-400 mr-2 text-xs"></i>Use HTTPS em produção</li>
                        <li><i class="fas fa-chevron-right text-green-400 mr-2 text-xs"></i>Valide respostas da API</li>
                        <li><i class="fas fa-chevron-right text-green-400 mr-2 text-xs"></i>Implemente retry com backoff exponencial</li>
                        <li><i class="fas fa-chevron-right text-green-400 mr-2 text-xs"></i>Monitore seus rate limits</li>
                    </ul>
                </div>
                <div class="bg-slate-800 rounded p-4">
                    <h5 class="text-red-400 font-semibold mb-2">
                        <i class="fas fa-times-circle mr-1"></i>
                        O que evitar:
                    </h5>
                    <ul class="text-slate-300 text-sm space-y-1">
                        <li><i class="fas fa-chevron-right text-red-400 mr-2 text-xs"></i>Não comite chaves no versionamento</li>
                        <li><i class="fas fa-chevron-right text-red-400 mr-2 text-xs"></i>Não exponha chaves no client-side</li>
                        <li><i class="fas fa-chevron-right text-red-400 mr-2 text-xs"></i>Não ignore erros de autenticação</li>
                        <li><i class="fas fa-chevron-right text-red-400 mr-2 text-xs"></i>Não faça requisições sem rate limiting</li>
                        <li><i class="fas fa-chevron-right text-red-400 mr-2 text-xs"></i>Não use métodos HTTP incorretos</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dados dinâmicos para exemplos de código
// Dados dinâmicos para exemplos de código
const apiBaseUrl = '{{ url("/api") }}';
const workspaceId = {{ $workspace->id }};
const apiKey = '{{ $workspace->email_api }}';
const isJWTRequired = {{ $workspace->api_jwt_required ? 'true' : 'false' }};

// Exemplos de código por linguagem
const codeExamples = {
    javascript: {
        workspace: isJWTRequired ? 
`// 1. Primeiro obtenha o token JWT
const getToken = async () => {
    const response = await fetch('${apiBaseUrl}/auth/token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email: '${apiKey}',
            password: '{{ auth()->user()->global_key_api }}'
        })
    });
    const data = await response.json();
    return data.token;
};

// 2. Use o token para acessar a API
const fetchWorkspace = async () => {
    const token = await getToken();
    
    const response = await fetch(\`\${apiBaseUrl}/workspaces/\${workspaceId}\`, {
        headers: {
            'Authorization': \`Bearer \${token}\`,
            'Content-Type': 'application/json'
        }
    });
    
    const data = await response.json();
    console.log(data);
};` :
`const fetchWorkspace = async () => {
    const response = await fetch(\`\${apiBaseUrl}/workspaces/\${workspaceId}\`, {
        headers: {
            'Authorization': 'Bearer ${apiKey}',
            'Content-Type': 'application/json'
        }
    });
    
    const data = await response.json();
    console.log(data);
};`
    },
    php: {
        workspace: isJWTRequired ?
`<?php
// USANDO cURL (Recomendado)
function getJWToken() {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => '${apiBaseUrl}/auth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'email' => '${apiKey}',
            'password' => '{{ auth()->user()->global_key_api }}'
        ])
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

function fetchWorkspace($token) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => '${apiBaseUrl}/workspaces/${workspaceId}',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    } else {
        throw new Exception('HTTP Error: ' . $httpCode);
    }
}

// Uso
try {
    $tokenData = getJWToken();
    $workspace = fetchWorkspace($tokenData['token']);
    print_r($workspace);
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
?>` :
`<?php
// Workspace Key Authentication
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => '${apiBaseUrl}/workspaces/${workspaceId}',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ${apiKey}',
        'Content-Type: application/json',
        'Accept: application/json'
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    print_r($data);
} else {
    echo 'Erro HTTP: ' . $httpCode;
}
?>`
    },
    python: {
        workspace: isJWTRequired ?
`import requests
import json

# 1. Primeiro obtenha o token JWT
def get_jwt_token():
    response = requests.post(
        '${apiBaseUrl}/auth/token',
        json={
            'email': '${apiKey}',
            'password': '{{ auth()->user()->global_key_api }}'
        },
        headers={'Content-Type': 'application/json'}
    )
    response.raise_for_status()
    return response.json()['token']

# 2. Use o token para acessar a API
def fetch_workspace():
    token = get_jwt_token()
    
    response = requests.get(
        f'${apiBaseUrl}/workspaces/{workspaceId}',
        headers={
            'Authorization': f'Bearer {token}',
            'Content-Type': 'application/json'
        }
    )
    response.raise_for_status()
    return response.json()

# Uso
try:
    workspace_data = fetch_workspace()
    print(workspace_data)
except requests.exceptions.RequestException as e:
    print(f'Erro: {e}')` :
`import requests

def fetch_workspace():
    response = requests.get(
        f'${apiBaseUrl}/workspaces/{workspaceId}',
        headers={
            'Authorization': 'Bearer ${apiKey}',
            'Content-Type': 'application/json'
        }
    )
    response.raise_for_status()
    return response.json()

# Uso
try:
    workspace_data = fetch_workspace()
    print(workspace_data)
except requests.exceptions.RequestException as e:
    print(f'Erro: {e}')`
    },
    curl: {
        workspace: isJWTRequired ?
`# 1. Primeiro obtenha o token JWT
TOKEN=$(curl -X POST \\
  '${apiBaseUrl}/auth/token' \\
  -H 'Content-Type: application/json' \\
  -d '{
    "email": "${apiKey}",
    "password": "{{ auth()->user()->global_key_api }}"
  }' | jq -r '.token')

# 2. Use o token para acessar a API
curl -X GET \\
  '${apiBaseUrl}/workspaces/${workspaceId}' \\
  -H 'Authorization: Bearer $TOKEN' \\
  -H 'Content-Type: application/json'` :
`# Workspace Key Authentication
curl -X GET \\
  '${apiBaseUrl}/workspaces/${workspaceId}' \\
  -H 'Authorization: Bearer ${apiKey}' \\
  -H 'Content-Type: application/json'`
    }
};

// Atualizar exemplo de código quando selecionar linguagem/endpoint
function updateCodeExample() {
    const language = document.getElementById('selectDocExample').value;
    const endpoint = document.getElementById('selectEndpoint').value;
    const codeOutput = document.getElementById('docCodeOutput');
    
    if (codeExamples[language] && codeExamples[language][endpoint]) {
        codeOutput.textContent = codeExamples[language][endpoint];
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    updateCodeExample();
    document.getElementById('selectDocExample').addEventListener('change', updateCodeExample);
    document.getElementById('selectEndpoint').addEventListener('change', updateCodeExample);
});
</script>