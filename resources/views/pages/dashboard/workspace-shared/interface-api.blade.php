@extends('template.template-site')

@section('title', $workspace->title)
@section('description', 'Workspace compartilhado por '.$user->name)

@section('content_site')
<div class="bg-slate-900 dark:bg-gray-900 min-h-screen">

    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $workspace->title }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Workspace compartilhado</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Informações do compartilhador -->
                    <div class="hidden md:flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-share-alt"></i>
                        <span>Compartilhado por: {{ $user->name }} ({{ $user->email }})</span>
                    </div>
                    
                    <!-- Modo de acesso -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        <i class="fas fa-eye mr-1"></i>
                        Visualização
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Barra de pesquisa e ações -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between space-x-4">
                <!-- Barra de pesquisa -->
                <div class="flex-1 max-w-2xl">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            id="search-input"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:border-gray-600 dark:text-white"
                            placeholder="Pesquisar por chave ou valor..."
                        >
                    </div>
                </div>
                
                <!-- Botões de ação -->
                <div class="flex space-x-3">
                    <button data-modal-target="share-modal" data-modal-toggle="share-modal" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-2 focus:ring-teal-500">
                        <i class="fas fa-share-alt mr-2"></i>
                        Compartilhar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="main-tabs" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg active" id="workspace-tab-btn" data-tab-target="workspace" type="button" role="tab" aria-selected="true">
                        <i class="fas fa-layer-group mr-2"></i>Workspace
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="api-test-tab-btn" data-tab-target="api-test" type="button" role="tab">
                        <i class="fas fa-flask mr-2"></i>API Test
                    </button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="code-examples-tab-btn" data-tab-target="code-examples" type="button" role="tab">
                        <i class="fas fa-code mr-2"></i>Code Examples
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tabs Content -->
        <div id="tab-content-container">
            <!-- Tab Workspace -->
            <div class="tab-content active" id="workspace-content" role="tabpanel">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden max-w-3xl mx-auto">
                    @if($workspace)
                        @forelse($workspace->topics as $topic)
                            <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                <!-- Header do Tópico -->
                                <button onclick="toggleTopic({{ $topic->id }})" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-folder text-teal-500"></i>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $topic->title }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded-full">
                                            {{ $topic->fields->count() }} campo(s)
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="icon-{{ $topic->id }}"></i>
                                </button>

                                <!-- Campos do Tópico -->
                                <div id="topic-{{ $topic->id }}" class="hidden">
                                    <div class="border-t border-gray-200 dark:border-gray-700">
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                    <tr>
                                                        <th class="px-6 py-3">Chave</th>
                                                        <th class="px-6 py-3">Valor</th>
                                                        <th class="px-6 py-3">Tipo</th>
                                                        <th class="px-6 py-3">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($topic->fields->where('is_visible', true) as $field)
                                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                                {{ $field->key_name }}
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                <div class="text-gray-600 dark:text-gray-300 break-all max-w-md">
                                                                    {{ $field->value }}
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                <div class="text-gray-600 dark:text-gray-300 break-all max-w-md">
                                                                    {{ $field->type }}
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4">
                                                                <div class="flex space-x-2">
                                                                    <button onclick="copyToClipboard('{{ addslashes($field->value) }}', 'Valor copiado!')" 
                                                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                                                            title="Copiar valor">
                                                                        <i class="fas fa-copy"></i>
                                                                    </button>
                                                                    
                                                                    <button onclick="copyToClipboard('{{ addslashes($field->key_name) }}', 'Chave copiada!')" 
                                                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 transition-colors"
                                                                            title="Copiar chave">
                                                                        <i class="fas fa-key"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                                                <div class="flex flex-col items-center justify-center">
                                                                    <i class="fas fa-inbox text-3xl mb-2 text-gray-400"></i>
                                                                    <p class="text-sm">Nenhum campo visível encontrado</p>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-4"></i>
                                <p class="text-lg">Nenhum tópico encontrado</p>
                            </div>
                        @endforelse
                    @else
                        <p class="p-4 text-red-500">Workspace não encontrado.</p>
                    @endif
                </div>
            </div>

            <!-- Tab API Test -->
            <div class="tab-content hidden" id="api-test-content" role="tabpanel">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🔍 Testar API</h3>
                    
                    <div class="space-y-4">
                        <!-- Endpoint Info -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Endpoint
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 py-2 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                                    GET
                                </span>
                                <input type="text" 
                                       value="{{ url("/api/shared/{$global_key_api}/{$workspace_key_api}") }}" 
                                       class="flex-1 min-w-0 block w-full px-3 py-2 rounded-r-lg border border-gray-300 bg-white text-gray-900 text-sm dark:bg-gray-600 dark:border-gray-600 dark:text-white"
                                       readonly>
                            </div>
                        </div>

                        <!-- Test Button -->
                        <button id="test-api-btn" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-play mr-2"></i>Testar Requisição
                        </button>

                        <!-- Results -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Resposta
                            </label>
                            <div class="bg-gray-800 rounded-lg p-4">
                                <pre class="text-green-400 text-sm overflow-x-auto max-h-64" id="api-response">// Clique em "Testar Requisição" para ver a resposta</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Code Examples -->
            <div class="tab-content hidden" id="code-examples-content" role="tabpanel">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">💻 Exemplos de Código</h3>
                    
                    <!-- Language Tabs -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="language-tabs" role="tablist">
                            <li class="mr-2">
                                <button class="inline-block p-2 border-b-2 rounded-t-lg active" data-language-target="javascript" type="button" role="tab">
                                    JavaScript
                                </button>
                            </li>
                            <li class="mr-2">
                                <button class="inline-block p-2 border-b-2 rounded-t-lg border-transparent hover:text-gray-600" data-language-target="python" type="button" role="tab">
                                    Python
                                </button>
                            </li>
                            <li class="mr-2">
                                <button class="inline-block p-2 border-b-2 rounded-t-lg border-transparent hover:text-gray-600" data-language-target="curl" type="button" role="tab">
                                    cURL
                                </button>
                            </li>
                            <li>
                                <button class="inline-block p-2 border-b-2 rounded-t-lg border-transparent hover:text-gray-600" data-language-target="php" type="button" role="tab">
                                    PHP
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Code Examples Content -->
                    <div id="language-content-container">
                        <!-- JavaScript -->
                        <div class="language-content active" id="javascript-content" role="tabpanel">
                            <div class="bg-gray-800 rounded-lg p-4">
                                <pre class="text-green-400 text-sm overflow-x-auto"><code>// Using Fetch API
const url = '{{ url("/api/shared/{$global_key_api}/{$workspace_key_api}") }}';

fetch(url)
  .then(response => response.json())
  .then(data => {
    console.log('Workspace data:', data);
    // Access topics: data.topics
    // Access fields: data.topics[0].fields
  })
  .catch(error => {
    console.error('Error:', error);
  });

// Using async/await
async function fetchWorkspaceData() {
  try {
    const response = await fetch(url);
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Fetch error:', error);
  }
}</code></pre>
                            </div>
                        </div>

                        <!-- Python -->
                        <div class="language-content hidden" id="python-content" role="tabpanel">
                            <div class="bg-gray-800 rounded-lg p-4">
                                <pre class="text-green-400 text-sm overflow-x-auto"><code>import requests
import json

url = "{{ url("/api/shared/{$global_key_api}/{$workspace_key_api}") }}"

try:
    response = requests.get(url)
    response.raise_for_status()
    
    data = response.json()
    print("Workspace data retrieved successfully!")
    print(f"Workspace title: {data['workspace']['title']}")
    print(f"Total topics: {data['statistics']['total_topics']}")
    
    # Access topics and fields
    for topic in data['topics']:
        print(f"Topic: {topic['title']}")
        for field in topic['fields']:
            print(f"  {field['key']}: {field['value']}")
            
except requests.exceptions.RequestException as e:
    print(f"Error: {e}")</code></pre>
                            </div>
                        </div>

                        <!-- cURL -->
                        <div class="language-content hidden" id="curl-content" role="tabpanel">
                            <div class="bg-gray-800 rounded-lg p-4">
                                <pre class="text-green-400 text-sm overflow-x-auto"><code># Basic GET request
curl -X GET "{{ url("/api/shared/{$global_key_api}/{$workspace_key_api}") }}"

# With pretty JSON output
curl -X GET "{{ url("/api/shared/{$global_key_api}/{$workspace_key_api}") }}" | jq '.'

# Save to file
curl -X GET "{{ url("/api/shared/{$global_key_api}/{$workspace_key_api}") }}" -o workspace_data.json

# With headers and verbose output
curl -X GET \
  "{{ url("/api/shared/{$global_key_api}/{$workspace_key_api}") }}" \
  -H "Accept: application/json" \
  -v</code></pre>
                            </div>
                        </div>

                        <!-- PHP -->
                        <div class="language-content hidden" id="php-content" role="tabpanel">
                            <div class="bg-gray-800 rounded-lg p-4">
                                <pre class="text-green-400 text-sm overflow-x-auto"><code>$url = "{{ url("/api/shared/{$global_key_api}/{$workspace_key_api}") }}";

// Using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "Workspace: " . $data['workspace']['title'] . "\n";
    echo "Topics: " . count($data['topics']) . "\n";
} else {
    echo "Error: HTTP " . $httpCode;
}

curl_close($ch);

// Alternative using file_get_contents (if allow_url_fopen is enabled)
/*
$context = stream_context_create([
    'http' => [
        'header' => 'Accept: application/json'
    ]
]);

$response = file_get_contents($url, false, $context);
$data = json_decode($response, true);
*/</code></pre>
                            </div>
                        </div>
                    </div>

                    <!-- Copy All Button -->
                    <div class="mt-4">
                        <button onclick="copyAllCodeExamples()" class="flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-copy mr-2"></i>Copiar Código
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Share Modal -->
    <div id="share-modal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Compartilhar Workspace
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="share-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Link de compartilhamento
                    </label>
                    <div class="flex">
                        <input type="text" 
                               value="{{ url()->current() }}" 
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                               readonly>
                        <button onclick="copyShareLink()" class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:ring-teal-300 font-medium rounded-r-lg text-sm px-4 text-center inline-flex items-center dark:bg-teal-500 dark:hover:bg-teal-600 dark:focus:ring-teal-800 transition-colors">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease-out;
    }
    .language-content {
        display: none;
    }
    .language-content.active {
        display: block;
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .rotate-180 {
        transform: rotate(180deg);
    }
</style>

<script type="module">
    import '/js/modules/alert.js'
    
    // Tab Management
    document.addEventListener('DOMContentLoaded', function() {
        // Main Tabs
        const mainTabButtons = document.querySelectorAll('#main-tabs button[data-tab-target]');
        const mainTabContents = document.querySelectorAll('#tab-content-container .tab-content');
        
        mainTabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab-target');
                
                // Update buttons
                mainTabButtons.forEach(btn => {
                    btn.classList.remove('active', 'text-teal-600', 'border-teal-600', 'dark:text-teal-500', 'dark:border-teal-500');
                    btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                });
                
                this.classList.add('active', 'text-teal-600', 'border-teal-600', 'dark:text-teal-500', 'dark:border-teal-500');
                this.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                
                // Update contents
                mainTabContents.forEach(content => {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                });
                
                const targetContent = document.getElementById(targetTab + '-content');
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                    setTimeout(() => {
                        targetContent.classList.add('active');
                    }, 10);
                }
            });
        });

        // Language Tabs
        const languageTabButtons = document.querySelectorAll('#language-tabs button[data-language-target]');
        const languageContents = document.querySelectorAll('#language-content-container .language-content');
        
        languageTabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetLanguage = this.getAttribute('data-language-target');
                
                // Update buttons
                languageTabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-teal-600', 'text-teal-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                
                this.classList.add('active', 'border-teal-600', 'text-teal-600');
                this.classList.remove('border-transparent', 'text-gray-500');
                
                // Update contents
                languageContents.forEach(content => {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                });
                
                const targetContent = document.getElementById(targetLanguage + '-content');
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                    setTimeout(() => {
                        targetContent.classList.add('active');
                    }, 10);
                }
            });
        });

        // Initialize first language tab as active
        const firstLanguageBtn = document.querySelector('#language-tabs button[data-language-target]');
        if (firstLanguageBtn) {
            firstLanguageBtn.click();
        }
    });

    // Utility Functions
    function toggleTopic(topicId) {
        const topicElement = document.getElementById('topic-' + topicId);
        const iconElement = document.getElementById('icon-' + topicId);
        
        topicElement.classList.toggle('hidden');
        iconElement.classList.toggle('rotate-180');
    }

    function copyToClipboard(text, message = 'Copiado!') {
        navigator.clipboard.writeText(text).then(() => {
            showNotification(message, 'success');
        }).catch(err => {
            console.error('Erro ao copiar:', err);
            showNotification('Erro ao copiar', 'error');
        });
    }

    function copyShareLink() {
        const shareModal = document.getElementById('share-modal');
        const shareInput = shareModal.querySelector('input');
        shareInput.select();
        document.execCommand('copy');
        showNotification('Link copiado!', 'success');
    }

    function copyAllCodeExamples() {
        const codes = [];
        document.querySelectorAll('.language-content.active pre code').forEach(codeBlock => {
            codes.push(codeBlock.textContent);
        });
        
        const allCode = codes.join('\n\n// ' + '='.repeat(50) + '\n\n');
        copyToClipboard(allCode, 'Todos os exemplos copiados!');
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-32 ${
            type === 'success' 
                ? 'bg-green-100 text-green-800 border border-green-200' 
                : 'bg-red-100 text-red-800 border border-red-200'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('translate-x-32');
            notification.classList.add('translate-x-0');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-32');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // API Test Functionality
    document.getElementById('test-api-btn')?.addEventListener('click', async function () {
        const button = this;
        const responseElement = document.getElementById('api-response');
        
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testando...';
        button.disabled = true;
        
        try {
            const response = await fetch("{{ route('workspace.shared-api', ['global_key_api' => $global_key_api, 'workspace_key_api' => $workspace_key_api]) }}");
            const data = await response.json();
            
            responseElement.textContent = JSON.stringify(data, null, 2);
            responseElement.className = 'text-green-400 text-sm overflow-x-auto max-h-64';
            
            showNotification('API testada com sucesso!', 'success');
        } catch (error) {
            responseElement.textContent = `Erro: ${error.message}`;
            responseElement.className = 'text-red-400 text-sm overflow-x-auto max-h-64';
            
            showNotification('Erro ao testar API', 'error');
        } finally {
            button.innerHTML = '<i class="fas fa-play mr-2"></i>Testar Requisição';
            button.disabled = false;
        }
    });
    

    // Enhanced Search functionality
    document.getElementById('search-input')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        const workspaceContainer = document.getElementById('workspace-container');
        const noResultsMessage = document.getElementById('no-results-message');
        const topicContainers = document.querySelectorAll('.topic-container');
        
        let hasVisibleResults = false;
        
        if (searchTerm === '') {
            // Reset para estado original
            topicContainers.forEach(topic => {
                topic.style.display = '';
                const fieldRows = topic.querySelectorAll('.field-row');
                fieldRows.forEach(row => row.style.display = '');
                
                // Remove highlights
                removeHighlights(topic);
            });
            
            workspaceContainer.style.display = '';
            noResultsMessage.classList.add('hidden');
            return;
        }
        
        // Pesquisar em todos os tópicos e campos
        topicContainers.forEach(topic => {
            const topicTitle = topic.querySelector('.topic-title').textContent.toLowerCase();
            const fieldRows = topic.querySelectorAll('.field-row');
            let topicHasVisibleFields = false;
            
            // Pesquisar nos campos
            fieldRows.forEach(row => {
                const fieldKey = row.getAttribute('data-field-key') || '';
                const fieldValue = row.getAttribute('data-field-value') || '';
                const fieldType = row.getAttribute('data-field-type') || '';
                
                const matches = fieldKey.includes(searchTerm) || 
                              fieldValue.includes(searchTerm) || 
                              fieldType.includes(searchTerm);
                
                if (matches) {
                    row.style.display = '';
                    topicHasVisibleFields = true;
                    hasVisibleResults = true;
                    
                    // Adicionar highlight
                    highlightText(row, searchTerm);
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Verificar se o título do tópico corresponde
            const titleMatches = topicTitle.includes(searchTerm);
            
            if (titleMatches || topicHasVisibleFields) {
                topic.style.display = '';
                hasVisibleResults = true;
                
                // Se o título corresponde, expandir o tópico
                if (titleMatches) {
                    const topicId = topic.getAttribute('data-topic-id');
                    const topicContent = document.getElementById('topic-' + topicId);
                    const icon = document.getElementById('icon-' + topicId);
                    
                    if (topicContent && topicContent.classList.contains('hidden')) {
                        topicContent.classList.remove('hidden');
                        icon.classList.add('rotate-180');
                    }
                    
                    // Highlight no título
                    highlightText(topic.querySelector('.topic-title'), searchTerm);
                }
            } else {
                topic.style.display = 'none';
            }
        });
        
        // Mostrar/ocultar mensagem de nenhum resultado
        if (hasVisibleResults) {
            workspaceContainer.style.display = '';
            noResultsMessage.classList.add('hidden');
        } else {
            workspaceContainer.style.display = 'none';
            noResultsMessage.classList.remove('hidden');
        }
    });
</script>

@include('components.footer.footer')
@endsection