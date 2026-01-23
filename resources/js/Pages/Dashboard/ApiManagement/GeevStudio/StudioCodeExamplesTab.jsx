export const StudioCodeExamplesTab = ({
    activeTab,
    activeLanguage,
    global_key_api,
    apiConfig,
    setActiveLanguage
})=> {
    return activeTab === 'code-examples' && (
    <div className="space-y-6 animate-fadeIn">
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div className="flex items-center justify-between mb-6">
                <div>
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                        <i className="fas fa-code mr-2 text-purple-500"></i>
                        Exemplos de Código
                    </h3>
                    <p className="text-sm text-gray-600 dark:text-gray-400">
                        Integre sua API facilmente com essas implementações
                    </p>
                </div>
                <button
                    onClick={() => {
                        const allCode = document.querySelectorAll('.language-content.active pre code');
                        let fullCode = '';
                        allCode.forEach(code => {
                            fullCode += code.textContent + '\n\n//' + '='.repeat(50) + '\n\n';
                        });
                        copyToClipboard(fullCode, 'Todos os exemplos copiados!');
                    }}
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

            {/* Code Examples */}
            <div className="space-y-6">
                {activeLanguage === 'javascript' && (
                    <div className="space-y-4">
                        <div>
                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fetch API (ES6+)</h4>
                            <div className="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                <pre className="text-green-400 text-sm">
                                    <code>{`// Obter todos os tópicos do workspace
const API_KEY = '${global_key_api}';
const BASE_URL = '${window.location.origin}/api/v1';

async function fetchTopics() {
try {
const response = await fetch(\`\${BASE_URL}/topics\`, {
headers: {
'Authorization': \`Bearer \${API_KEY}\`,
'Content-Type': 'application/json'
}
});

if (!response.ok) {
throw new Error(\`HTTP error! status: \${response.status}\`);
}

const data = await response.json();
console.log('Topics:', data);
return data;
} catch (error) {
console.error('Error fetching topics:', error);
}
}

// Obter um tópico específico
async function fetchTopicById(topicId) {
const response = await fetch(\`\${BASE_URL}/topics/\${topicId}\`, {
headers: {
'Authorization': \`Bearer \${API_KEY}\`,
'Content-Type': 'application/json'
}
});

return await response.json();
}`}</code>
                                </pre>
                            </div>
                        </div>
                    </div>
                )}

                {activeLanguage === 'python' && (
                    <div className="space-y-4">
                        <div>
                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Requests Library</h4>
                            <div className="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                <pre className="text-green-400 text-sm">
                                    <code>{`import requests
import json

API_KEY = '${global_key_api}'
BASE_URL = '${window.location.origin}/api/v1'

headers = {
'Authorization': f'Bearer {API_KEY}',
'Content-Type': 'application/json'
}

def get_topics():
\"\"\"Obter todos os tópicos do workspace\"\"\"
try:
response = requests.get(f'{BASE_URL}/topics', headers=headers)
response.raise_for_status()
return response.json()
except requests.exceptions.RequestException as e:
print(f"Error: {e}")
return None

def get_topic(topic_id):
\"\"\"Obter um tópico específico\"\"\"
url = f'{BASE_URL}/topics/{topic_id}'
response = requests.get(url, headers=headers)
return response.json()

# Exemplo de uso
if __name__ == "__main__":
topics = get_topics()
if topics:
print(f"Total topics: {len(topics)}")
for topic in topics:
print(f"Topic: {topic['title']}")`}</code>
                                </pre>
                            </div>
                        </div>
                    </div>
                )}

                {activeLanguage === 'curl' && (
                    <div className="space-y-4">
                        <div>
                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comandos cURL</h4>
                            <div className="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                <pre className="text-green-400 text-sm">
                                    <code>{`# Obter todos os tópicos
curl -X GET "${window.location.origin}/api/v1/topics" \\
-H "Authorization: Bearer ${global_key_api}" \\
-H "Content-Type: application/json"

# Obter um tópico específico
curl -X GET "${window.location.origin}/api/v1/topics/{topic_id}" \\
-H "Authorization: Bearer ${global_key_api}" \\
-H "Content-Type: application/json"

# Com saída formatada usando jq
curl -s "${window.location.origin}/api/v1/topics" \\
-H "Authorization: Bearer ${global_key_api}" | jq '.'

# Salvar resposta em arquivo
curl -X GET "${window.location.origin}/api/v1/topics" \\
-H "Authorization: Bearer ${global_key_api}" \\
-o topics.json

# Verificar headers da resposta
curl -I "${window.location.origin}/api/v1/topics" \\
-H "Authorization: Bearer ${global_key_api}"`}</code>
                                </pre>
                            </div>
                        </div>
                    </div>
                )}

                {activeLanguage === 'php' && (
                    <div className="space-y-4">
                        <div>
                            <h4 className="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PHP cURL</h4>
                            <div className="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                <pre className="text-green-400 text-sm">
                                    <code>{`<?php

$apiKey = '${global_key_api}';
$baseUrl = '${window.location.origin}/api/v1';

function getTopics($apiKey, $baseUrl) {
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $baseUrl . '/topics');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
'Authorization: Bearer ' . $apiKey,
'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
$data = json_decode($response, true);
return $data;
} else {
error_log("API Error: HTTP " . $httpCode);
return null;
}

curl_close($ch);
}

function getTopic($apiKey, $baseUrl, $topicId) {
$url = $baseUrl . '/topics/' . $topicId;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
'Authorization: Bearer ' . $apiKey,
'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

return json_decode($response, true);
}

// Exemplo de uso
$topics = getTopics($apiKey, $baseUrl);
if ($topics) {
echo "Total topics: " . count($topics) . "\\n";
foreach ($topics as $topic) {
echo "Topic: " . $topic['title'] . "\\n";
}
}

?>`}</code>
                                </pre>
                            </div>
                        </div>
                    </div>
                )}

                {/* Informações Importantes */}
                <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 className="font-medium text-blue-900 dark:text-blue-100 mb-2 flex items-center">
                        <i className="fas fa-lightbulb mr-2"></i>
                        Informações Importantes
                    </h4>
                    <ul className="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                        <li className="flex items-start">
                            <i className="fas fa-key text-xs mt-1 mr-2"></i>
                            <span>Todas as requisições requerem o header <code>Authorization: Bearer YOUR_API_KEY</code></span>
                        </li>
                        <li className="flex items-start">
                            <i className="fas fa-lock text-xs mt-1 mr-2"></i>
                            <span>A API requer conexão HTTPS para todas as requisições</span>
                        </li>
                        <li className="flex items-start">
                            <i className="fas fa-clock text-xs mt-1 mr-2"></i>
                            <span>Limite de rate: {apiConfig.rateLimitPerMinute} requisições por minuto</span>
                        </li>
                        <li className="flex items-start">
                            <i className="fas fa-code text-xs mt-1 mr-2"></i>
                            <span>Todas as respostas são retornadas no formato JSON</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
)
}