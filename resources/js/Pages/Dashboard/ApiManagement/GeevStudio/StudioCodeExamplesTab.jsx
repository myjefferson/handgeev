import React from 'react';

export const StudioCodeExamplesTab = ({
    activeTab, 
    activeLanguage, 
    global_key_api, 
    workspace_key_api,
    apiConfig = {
        rateLimitPerMinute: 60,
        requireHttps: true
    },
    setActiveLanguage,
    copyToClipboard
}) => {
    
    // Gerar exemplos de código
    const codeExamples = {
        javascript: `// Using Fetch API
const API_KEY = '${global_key_api}';
const BASE_URL = '${window.location.origin}/api/v1';

async function fetchWorkspace() {
    try {
        const response = await fetch(\`\${BASE_URL}/workspace/${workspace_key_api}\`, {
            headers: {
                'Authorization': \`Bearer \${API_KEY}\`,
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(\`HTTP error! status: \${response.status}\`);
        }
        
        const data = await response.json();
        console.log('Workspace data:', data);
        return data;
    } catch (error) {
        console.error('Error fetching workspace:', error);
    }
}

// Example usage
fetchWorkspace().then(data => {
    console.log('Topics:', data.topics);
});`,

        python: `import requests
import json

API_KEY = '${global_key_api}'
BASE_URL = '${window.location.origin}/api/v1'

headers = {
    'Authorization': f'Bearer {API_KEY}',
    'Content-Type': 'application/json'
}

def fetch_workspace():
    """Fetch workspace data"""
    try:
        response = requests.get(f'{BASE_URL}/workspace/${workspace_key_api}', headers=headers)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as e:
        print(f"Error: {e}")
        return None

# Example usage
data = fetch_workspace()
if data:
    print(f"Workspace: {data['title']}")
    print(f"Topics: {len(data['topics'])}")`,

        curl: `# Get workspace data
curl -X GET "${window.location.origin}/api/v1/workspace/${workspace_key_api}" \\
  -H "Authorization: Bearer ${global_key_api}" \\
  -H "Content-Type: application/json"

# Get specific topic
curl -X GET "${window.location.origin}/api/v1/topics/{topic_id}" \\
  -H "Authorization: Bearer ${global_key_api}" \\
  -H "Content-Type: application/json"

# Pretty format with jq
curl -s "${window.location.origin}/api/v1/workspace/${workspace_key_api}" \\
  -H "Authorization: Bearer ${global_key_api}" | jq '.'`,

        php: `<?php

$apiKey = '${global_key_api}';
$baseUrl = '${window.location.origin}/api/v1';

function fetchWorkspace($apiKey, $workspaceKey) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/workspace/' . $workspaceKey);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    } else {
        error_log("API Error: HTTP " . $httpCode);
        return null;
    }
    
    curl_close($ch);
}

// Example usage
$workspaceData = fetchWorkspace($apiKey, '${workspace_key_api}');
if ($workspaceData) {
    echo "Workspace: " . $workspaceData['title'] . "\\n";
    echo "Topics: " . count($workspaceData['topics']) . "\\n";
}

?>`
    };

    // Função para copiar todos os exemplos
    const copyAllExamples = () => {
        const allCode = Object.values(codeExamples).join('\n\n// ' + '='.repeat(50) + '\n\n');
        copyToClipboard(allCode, 'Todos os exemplos copiados!');
    };

    if (activeTab !== 'code-examples') return null;

    return (
        <div className="space-y-6 animate-fadeIn">
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div className="flex items-center justify-between mb-6">
                    <div>
                        <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                            <i className="fas fa-code mr-2 text-purple-500"></i>
                            Exemplos de Código
                        </h3>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Integre sua API facilmente com essas implementações
                        </p>
                    </div>
                    <button
                        onClick={copyAllExamples}
                        className="px-4 py-2 bg-teal-600 text-white font-medium rounded-lg hover:bg-teal-700"
                    >
                        <i className="fas fa-copy mr-2"></i>
                        Copiar Todos
                    </button>
                </div>

                {/* Language Tabs */}
                <div className="border-b border-gray-200 dark:border-gray-700 mb-6">
                    <nav className="-mb-px flex space-x-4">
                        {['javascript', 'python', 'curl', 'php'].map((lang) => (
                            <button
                                key={lang}
                                className={`py-2 px-4 text-sm font-medium border-b-2 ${
                                    activeLanguage === lang
                                        ? 'border-teal-500 text-teal-600 dark:text-teal-400'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
                                }`}
                                onClick={() => setActiveLanguage(lang)}
                            >
                                {lang === 'curl' ? 'cURL' : lang.charAt(0).toUpperCase() + lang.slice(1)}
                            </button>
                        ))}
                    </nav>
                </div>

                {/* Code Example */}
                <div className="mb-6">
                    <div className="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre className="text-green-400 text-sm">
                            <code>{codeExamples[activeLanguage]}</code>
                        </pre>
                    </div>
                    <div className="flex justify-end mt-2">
                        <button
                            onClick={() => copyToClipboard(codeExamples[activeLanguage], `Código ${activeLanguage} copiado!`)}
                            className="px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white rounded text-sm"
                        >
                            <i className="fas fa-copy mr-1"></i>Copiar
                        </button>
                    </div>
                </div>

                {/* Informações Importantes */}
                <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 className="font-medium text-blue-900 dark:text-blue-100 mb-2 flex items-center">
                        <i className="fas fa-lightbulb mr-2"></i>
                        Informações Importantes
                    </h4>
                    <ul className="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                        <li className="flex items-start">
                            <i className="fas fa-key text-xs mt-1 mr-2"></i>
                            <span>Todas as requisições requerem o header <code>Authorization: Bearer {global_key_api.substring(0, 8)}...</code></span>
                        </li>
                        <li className="flex items-start">
                            <i className="fas fa-lock text-xs mt-1 mr-2"></i>
                            <span>A API {apiConfig?.requireHttps ? 'requer' : 'recomenda'} conexão HTTPS para todas as requisições</span>
                        </li>
                        <li className="flex items-start">
                            <i className="fas fa-clock text-xs mt-1 mr-2"></i>
                            <span>Limite de rate: {apiConfig?.rateLimitPerMinute || 60} requisições por minuto</span>
                        </li>
                        <li className="flex items-start">
                            <i className="fas fa-code text-xs mt-1 mr-2"></i>
                            <span>Todas as respostas são retornadas no formato JSON</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    );
};