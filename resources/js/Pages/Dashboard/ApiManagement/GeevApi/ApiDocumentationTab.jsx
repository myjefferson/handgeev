// resources/js/Pages/ApiManagement/Partials/ApiDocumentationTab.jsx
import React, { useState, useEffect } from 'react';
import { usePage, router } from '@inertiajs/react';

export default function ApiDocumentationTab({ workspace }) {
    const { auth } = usePage().props;
    const [selectedLanguage, setSelectedLanguage] = useState('javascript');
    const [selectedEndpoint, setSelectedEndpoint] = useState('workspace');
    
    const apiBaseUrl = window.location.origin + '/api';
    const workspaceId = workspace.id;
    const apiKey = workspace.email_api;
    const isJWTRequired = workspace.api_jwt_required;

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
            password: 'SUA_SENHA'
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
            'password' => 'SUA_SENHA'
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
            'Content-Type: 'application/json',
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
            'password': 'SUA_SENHA'
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
    "password": "SUA_SENHA"
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

    const currentCodeExample = codeExamples[selectedLanguage]?.[selectedEndpoint] || '';

    const copyToClipboard = async (text) => {
        try {
            await navigator.clipboard.writeText(text);
            // Você pode adicionar um toast de sucesso aqui
        } catch (err) {
            console.error('Erro ao copiar:', err);
        }
    };

    const exportDocumentation = (format) => {
        if (format === 'openapi' && !['pro', 'premium', 'admin'].includes(auth.user.plan?.name.toLowerCase())) {
            alert('Exportação OpenAPI disponível apenas para planos Pro e Premium');
            return;
        }
        
        // Implementar lógica de exportação
        // console.log(`Exportando documentação em ${format}`);
    };

    const responseStructure = `{
    "success": true,
    "data": { ... },
    "message": "Operação realizada com sucesso",
    "metadata": {
        "version": "1.0",
        "generated_at": "2024-01-01T00:00:00Z",
        "workspace_id": ${workspace.id},
        "rate_limit": {
            "remaining": 59,
            "limit": 60
        }
    }
}`;

    return (
        <div className="p-6 rounded-lg bg-slate-800/50 border border-slate-700">
            <div className="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-5 sm:space-y-0">
                <h3 className="text-xl font-semibold text-white">
                    <i className="fas fa-book mr-2 text-cyan-400"></i>
                    Documentação da API
                </h3>
                <div className="flex flex-col sm:flex-col md:flex-row items-center space-x-4 space-y-5 sm:space-y-0">
                    {/* Indicador do Tipo de Autenticação */}
                    <div className={`flex text-nowrap items-center space-x-2 px-3 py-1 rounded-full border ${
                        workspace.api_jwt_required 
                            ? 'bg-amber-500/20 border-amber-500/50 text-amber-300' 
                            : 'bg-green-500/20 border-green-500/50 text-green-300'
                    }`}>
                        <div className={`w-2 h-2 rounded-full ${
                            workspace.api_jwt_required ? 'bg-amber-500' : 'bg-green-500'
                        }`}></div>
                        <span className="text-sm font-medium">
                            {workspace.api_jwt_required ? 'JWT Authentication' : 'Workspace Key Authentication'}
                        </span>
                    </div>
                    
                    <div className="flex items-center flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 w-full">
                        <button 
                            onClick={() => exportDocumentation('json')}
                            className="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm transition-colors flex items-center w-full md:w-max"
                        >
                            <i className="fas fa-file-export mr-2"></i>
                            Exportar JSON
                        </button>
                        {(auth.user.isPro || auth.user.isPremium) && (
                            <button 
                                onClick={() => exportDocumentation('openapi')}
                                className="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm transition-colors flex items-center w-full md:w-max"
                            >
                                <i className="fas fa-api mr-2"></i>
                                OpenAPI Spec
                            </button>
                        )}
                    </div>
                </div>
            </div>
            
            <div className="space-y-6">
                {/* Autenticação Dinâmica */}
                <div className="bg-slate-800 border border-slate-700 mb-6 rounded-lg p-6">
                    <div className="flex items-center justify-between mb-4">
                        <h4 className="text-cyan-400 text-lg font-semibold">
                            <i className="fas fa-key mr-2"></i>
                            Autenticação
                        </h4>
                        <span className={`px-2 py-1 text-xs rounded ${
                            workspace.api_jwt_required 
                                ? 'bg-amber-500/20 text-amber-300' 
                                : 'bg-green-500/20 text-green-300'
                        }`}>
                            {workspace.api_jwt_required ? 'JWT Required' : 'Workspace Key'}
                        </span>
                    </div>

                    {workspace.api_jwt_required ? (
                        // Documentação JWT
                        <div className="space-y-4">
                            <p className="text-slate-300">Esta workspace requer autenticação JWT. Primeiro obtenha um token JWT:</p>
                            
                            <div className="bg-slate-800 rounded p-4">
                                <h5 className="text-cyan-300 font-semibold mb-2">
                                    <i className="fas fa-token mr-1"></i>
                                    Obter Token JWT:
                                </h5>
                                <div className="bg-black rounded p-3 mb-3">
                                    <code className="text-green-300 font-mono text-sm">
                                        POST {apiBaseUrl}/auth/token
                                    </code>
                                </div>
                                <pre className="text-slate-300 text-sm font-mono overflow-x-auto">
                                    <code>
{`// Request Body
{
    "email": "${auth.user.email}",
    "password": "sua-senha"
}

// Example Response
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "expires_in": 3600,
    "type": "Bearer"
}`}
                                    </code>
                                </pre>
                                <button 
                                    onClick={() => copyToClipboard(`POST ${apiBaseUrl}/auth/token\n\n// Request Body\n{\n    "email": "${auth.user.email}",\n    "password": "sua-senha"\n}`)}
                                    className="mt-2 text-cyan-400 hover:text-cyan-300 text-sm flex items-center"
                                >
                                    <i className="fas fa-copy mr-1"></i>
                                    Copiar
                                </button>
                            </div>

                            <p className="text-slate-300 mt-4">Use o token JWT em todas as requisições:</p>
                            <div className="bg-slate-800 rounded p-3 flex items-center justify-between">
                                <code className="text-cyan-300 font-mono text-sm">
                                    Authorization: Bearer <span>SEU_TOKEN_JWT_AQUI</span>
                                </code>
                                <button 
                                    onClick={() => copyToClipboard('Bearer SEU_TOKEN_JWT_AQUI')}
                                    className="text-cyan-400 hover:text-cyan-300"
                                >
                                    <i className="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    ) : (
                        // Documentação Workspace Key
                        <div className="space-y-4">
                            <p className="text-slate-300">Use sua Workspace Key em todas as requisições:</p>
                            <div className="bg-slate-800 rounded p-3 flex items-center justify-between">
                                <code className="text-cyan-300 font-mono text-sm">
                                    Authorization: Bearer <span>{workspace.email_api || 'SUA_WORKSPACE_KEY'}</span>
                                </code>
                                <button 
                                    onClick={() => copyToClipboard(`Bearer ${workspace.email_api}`)}
                                    className="text-cyan-400 hover:text-cyan-300"
                                >
                                    <i className="fas fa-copy"></i>
                                </button>
                            </div>
                            <p className="text-slate-300 text-sm bg-blue-500/10 border border-blue-500/20 rounded p-3">
                                <i className="fas fa-lightbulb text-yellow-400 mr-2"></i>
                                <strong>Dica:</strong> Esta é sua chave permanente. Mantenha-a segura!
                            </p>
                        </div>
                    )}
                </div>

                {/* Estrutura de Resposta */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="bg-slate-900 rounded-lg p-6">
                        <h4 className="text-cyan-400 text-lg font-semibold mb-3">
                            <i className="fas fa-code mr-2"></i>
                            Estrutura de Resposta
                        </h4>
                        <pre className="text-slate-300 text-sm font-mono overflow-x-auto">
                            <code>{responseStructure}</code>
                        </pre>
                        <button 
                            onClick={() => copyToClipboard(responseStructure)}
                            className="mt-2 text-cyan-400 hover:text-cyan-300 text-sm flex items-center"
                        >
                            <i className="fas fa-copy mr-1"></i>
                            Copiar
                        </button>
                    </div>

                    <div className="bg-slate-900 rounded-lg p-6">
                        <h4 className="text-cyan-400 text-lg font-semibold mb-3">
                            <i className="fas fa-list-alt mr-2"></i>
                            Códigos de Status
                        </h4>
                        <ul className="text-slate-300 space-y-2 text-sm">
                            <li className="flex items-center"><span className="text-green-400 font-mono mr-2">200</span> Sucesso</li>
                            <li className="flex items-center"><span className="text-blue-400 font-mono mr-2">201</span> Criado</li>
                            <li className="flex items-center"><span className="text-yellow-400 font-mono mr-2">400</span> Erro na requisição</li>
                            <li className="flex items-center"><span className="text-amber-400 font-mono mr-2">401</span> Não autorizado</li>
                            <li className="flex items-center"><span className="text-orange-400 font-mono mr-2">403</span> Proibido/Acesso negado</li>
                            <li className="flex items-center"><span className="text-red-400 font-mono mr-2">404</span> Não encontrado</li>
                            <li className="flex items-center"><span className="text-red-400 font-mono mr-2">405</span> Método não permitido</li>
                            <li className="flex items-center"><span className="text-purple-400 font-mono mr-2">429</span> Limite de requisições excedido</li>
                        </ul>
                        
                        <div className="mt-4 p-3 bg-slate-800 rounded">
                            <h5 className="text-cyan-300 text-sm font-semibold mb-2">
                                <i className="fas fa-tachometer-alt mr-1"></i>
                                Headers de Rate Limit:
                            </h5>
                            <ul className="text-slate-300 text-xs space-y-1">
                                <li><code className="text-cyan-300">X-RateLimit-Limit: 60</code></li>
                                <li><code className="text-cyan-300">X-RateLimit-Remaining: 59</code></li>
                                <li><code className="text-cyan-300">X-RateLimit-Reset: 1704067200</code></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {/* Exemplo de Uso Dinâmico */}
                <div className="bg-slate-900 rounded-lg p-6">
                    <h4 className="text-cyan-400 text-lg font-semibold mb-3">
                        <i className="fas fa-rocket mr-2"></i>
                        Exemplo de Uso
                    </h4>
                    <div className="space-y-4">
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <label className="block text-sm font-medium text-slate-300 mb-2">
                                    <i className="fas fa-code mr-1"></i>
                                    Selecione a linguagem:
                                </label>
                                <select 
                                    value={selectedLanguage}
                                    onChange={(e) => setSelectedLanguage(e.target.value)}
                                    className="bg-slate-800 border border-slate-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                                >
                                    <option value="javascript">JavaScript</option>
                                    <option value="php">PHP</option>
                                    <option value="python">Python</option>
                                    <option value="curl">cURL</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-slate-300 mb-2">
                                    <i className="fas fa-link mr-1"></i>
                                    Endpoint:
                                </label>
                                <select 
                                    value={selectedEndpoint}
                                    onChange={(e) => setSelectedEndpoint(e.target.value)}
                                    className="bg-slate-800 border border-slate-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5"
                                >
                                    <option value="workspace">Workspace</option>
                                    <option value="topics">Topics</option>
                                    <option value="fields">Fields</option>
                                </select>
                            </div>
                        </div>
                        <div className="bg-slate-800 rounded p-4 relative">
                            <button 
                                onClick={() => copyToClipboard(currentCodeExample)}
                                className="absolute top-2 right-2 text-slate-400 hover:text-white"
                            >
                                <i className="fas fa-copy"></i>
                            </button>
                            <pre className="text-slate-300 text-sm font-mono whitespace-pre-wrap overflow-x-auto">
                                {currentCodeExample}
                            </pre>
                        </div>
                    </div>
                </div>

                {/* Dicas de Segurança */}
                <div className="bg-slate-900 rounded-lg p-6">
                    <h4 className="text-cyan-400 text-lg font-semibold mb-3">
                        <i className="fas fa-shield-alt mr-2"></i>
                        Melhores Práticas
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="bg-slate-800 rounded p-4">
                            <h5 className="text-green-400 font-semibold mb-2">
                                <i className="fas fa-check-circle mr-1"></i>
                                O que fazer:
                            </h5>
                            <ul className="text-slate-300 text-sm space-y-1">
                                <li><i className="fas fa-chevron-right text-green-400 mr-2 text-xs"></i>Armazene chaves em variáveis de ambiente</li>
                                <li><i className="fas fa-chevron-right text-green-400 mr-2 text-xs"></i>Use HTTPS em produção</li>
                                <li><i className="fas fa-chevron-right text-green-400 mr-2 text-xs"></i>Valide respostas da API</li>
                                <li><i className="fas fa-chevron-right text-green-400 mr-2 text-xs"></i>Implemente retry com backoff exponencial</li>
                                <li><i className="fas fa-chevron-right text-green-400 mr-2 text-xs"></i>Monitore seus rate limits</li>
                            </ul>
                        </div>
                        <div className="bg-slate-800 rounded p-4">
                            <h5 className="text-red-400 font-semibold mb-2">
                                <i className="fas fa-times-circle mr-1"></i>
                                O que evitar:
                            </h5>
                            <ul className="text-slate-300 text-sm space-y-1">
                                <li><i className="fas fa-chevron-right text-red-400 mr-2 text-xs"></i>Não comite chaves no versionamento</li>
                                <li><i className="fas fa-chevron-right text-red-400 mr-2 text-xs"></i>Não exponha chaves no client-side</li>
                                <li><i className="fas fa-chevron-right text-red-400 mr-2 text-xs"></i>Não ignore erros de autenticação</li>
                                <li><i className="fas fa-chevron-right text-red-400 mr-2 text-xs"></i>Não faça requisições sem rate limiting</li>
                                <li><i className="fas fa-chevron-right text-red-400 mr-2 text-xs"></i>Não use métodos HTTP incorretos</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}