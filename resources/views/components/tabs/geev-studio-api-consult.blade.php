<div class="tab-content hidden" id="api-consult-content" role="tabpanel">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Coluna Esquerda - Endpoints -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📡 Endpoints Disponíveis</h3>
            
            <div class="space-y-4">
                <!-- Workspace Endpoints -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                        <h4 class="font-medium text-gray-900 dark:text-white">Workspace</h4>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Workspace Show -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        GET
                                    </span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Obter workspace completo</span>
                                </div>
                                <button class="run-endpoint-btn inline-flex items-center px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors" 
                                        data-endpoint="/api/workspaces/{{ $workspace->id }}"
                                        data-method="GET">
                                    <i class="fas fa-play mr-1"></i>Run
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <code class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded flex-1 mr-2 truncate">
                                    /api/workspaces/{workspaceId}
                                </code>
                                <button class="copy-url-btn text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" 
                                        data-url="/api/workspaces/{{ $workspace->id }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Workspace Stats -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        GET
                                    </span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Estatísticas do workspace</span>
                                </div>
                                <button class="run-endpoint-btn inline-flex items-center px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors" 
                                        data-endpoint="/api/workspaces/{{ $workspace->id }}/stats"
                                        data-method="GET">
                                    <i class="fas fa-play mr-1"></i>Run
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <code class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded flex-1 mr-2 truncate">
                                    /api/workspaces/{workspaceId}/stats
                                </code>
                                <button class="copy-url-btn text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" 
                                        data-url="/api/workspaces/{{ $workspace->id }}/stats">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Topics Endpoints -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                        <h4 class="font-medium text-gray-900 dark:text-white">Topics</h4>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- List Topics -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        GET
                                    </span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Listar tópicos do workspace</span>
                                </div>
                                <button class="run-endpoint-btn inline-flex items-center px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors" 
                                        data-endpoint="/api/workspaces/{{ $workspace->id }}/topics"
                                        data-method="GET">
                                    <i class="fas fa-play mr-1"></i>Run
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <code class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded flex-1 mr-2 truncate">
                                    /api/workspaces/{workspaceId}/topics
                                </code>
                                <button class="copy-url-btn text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" 
                                        data-url="/api/workspaces/{{ $workspace->id }}/topics">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Show Topic -->
                        @if($workspace->topics->count() > 0)
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        GET
                                    </span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Obter tópico específico</span>
                                </div>
                                <button class="run-endpoint-btn inline-flex items-center px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors" 
                                        data-endpoint="/api/topics/{{ $workspace->topics->first()->id }}"
                                        data-method="GET">
                                    <i class="fas fa-play mr-1"></i>Run
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <code class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded flex-1 mr-2 truncate">
                                    /api/topics/{topicId}
                                </code>
                                <button class="copy-url-btn text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" 
                                        data-url="/api/topics/{{ $workspace->topics->first()->id }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Fields Endpoints -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                        <h4 class="font-medium text-gray-900 dark:text-white">Fields</h4>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @php
                            $firstTopic = $workspace->topics->first();
                            $firstField = $firstTopic ? $firstTopic->fields->first() : null;
                        @endphp
                        
                        @if($firstTopic)
                        <!-- List Fields -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        GET
                                    </span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Listar fields do tópico</span>
                                </div>
                                <button class="run-endpoint-btn inline-flex items-center px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors" 
                                        data-endpoint="/api/topics/{{ $firstTopic->id }}/fields"
                                        data-method="GET">
                                    <i class="fas fa-play mr-1"></i>Run
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <code class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded flex-1 mr-2 truncate">
                                    /api/topics/{topicId}/fields
                                </code>
                                <button class="copy-url-btn text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" 
                                        data-url="/api/topics/{{ $firstTopic->id }}/fields">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                        
                        @if($firstField)
                        <!-- Show Field -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        GET
                                    </span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Obter field específico</span>
                                </div>
                                <button class="run-endpoint-btn inline-flex items-center px-3 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors" 
                                        data-endpoint="/api/fields/{{ $firstField->id }}"
                                        data-method="GET">
                                    <i class="fas fa-play mr-1"></i>Run
                                </button>
                            </div>
                            <div class="flex items-center justify-between">
                                <code class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded flex-1 mr-2 truncate">
                                    /api/fields/{fieldId}
                                </code>
                                <button class="copy-url-btn text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" 
                                        data-url="/api/fields/{{ $firstField->id }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna Direita - Resultados da Consulta -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 Resultado da Consulta</h3>
            
            <div class="space-y-4">
                <!-- Informações da Requisição -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Informações da Requisição</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Método:</span>
                            <span id="request-method" class="font-mono text-gray-900 dark:text-white">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">URL:</span>
                            <span id="request-url" class="font-mono text-gray-900 dark:text-white truncate max-w-xs">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span id="request-status" class="font-mono text-gray-900 dark:text-white">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tempo:</span>
                            <span id="request-time" class="font-mono text-gray-900 dark:text-white">-</span>
                        </div>
                    </div>
                </div>

                <!-- Resposta da API -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Resposta da API
                        </label>
                        <button id="copy-response-btn" class="inline-flex items-center px-3 py-1 text-sm bg-gray-600 hover:bg-gray-700 text-white rounded transition-colors opacity-0">
                            <i class="fas fa-copy mr-1"></i>Copiar
                        </button>
                    </div>
                    <div class="bg-gray-900 rounded-lg p-4 max-h-96 overflow-auto">
                        <pre id="api-response" class="text-green-400 text-sm whitespace-pre-wrap">// Clique em "Run" em algum endpoint para testar a API</pre>
                    </div>
                </div>

                <!-- Dicas -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2 flex items-center">
                        <i class="fas fa-lightbulb mr-2"></i>Dicas
                    </h4>
                    <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                        <li>• Clique em "Run" para testar um endpoint</li>
                        <li>• Use o botão de cópia para copiar a URL do endpoint</li>
                        <li>• As respostas são formatadas em JSON</li>
                        <li>• Todos os endpoints requerem autenticação via token</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
    
@endpush

@push('scripts_end')    
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            // API Consult functionality
            initializeApiConsult();
        });

        // API Consult Functionality
        function initializeApiConsult() {
            // Copy URL buttons
            document.querySelectorAll('.copy-url-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const url = this.getAttribute('data-url');
                    copyToClipboard(url, 'URL copiada!');
                });
            });

            // Run endpoint buttons
            document.querySelectorAll('.run-endpoint-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const endpoint = this.getAttribute('data-endpoint');
                    const method = this.getAttribute('data-method');
                    runApiRequest(endpoint, method, this);
                });
            });

            // Copy response button
            document.getElementById('copy-response-btn').addEventListener('click', function() {
                const responseText = document.getElementById('api-response').textContent;
                copyToClipboard(responseText, 'Resposta copiada!');
            });
        }

        async function runApiRequest(endpoint, method, button) {
            const originalText = button.innerHTML;
            const responseElement = document.getElementById('api-response');
            const copyResponseBtn = document.getElementById('copy-response-btn');
            const requestMethod = document.getElementById('request-method');
            const requestUrl = document.getElementById('request-url');
            const requestStatus = document.getElementById('request-status');
            const requestTime = document.getElementById('request-time');

            // Update request info
            requestMethod.textContent = method;
            requestUrl.textContent = endpoint;
            requestStatus.textContent = 'Loading...';
            requestTime.textContent = '-';

            // Show loading state
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Running...';
            button.disabled = true;
            responseElement.textContent = 'Loading...';
            responseElement.className = 'text-yellow-400 text-sm whitespace-pre-wrap';
            copyResponseBtn.classList.add('opacity-0');

            const startTime = performance.now();

            try {
                // In a real implementation, you would use the actual API endpoint
                // For now, we'll simulate the request
                const fullUrl = window.location.origin + endpoint;
                
                const response = await fetch(fullUrl, {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer {{$workspace->workspace_key_api}}`
                        // Add authentication headers if needed
                    }
                });

                const endTime = performance.now();
                const duration = (endTime - startTime).toFixed(2);

                const data = await response.json();

                // Update request info
                requestStatus.textContent = `${response.status} ${response.statusText}`;
                requestTime.textContent = `${duration}ms`;

                // Format and display response
                responseElement.textContent = JSON.stringify(data, null, 2);
                responseElement.className = 'text-green-400 text-sm whitespace-pre-wrap';
                
                // Show copy button
                copyResponseBtn.classList.remove('opacity-0');

                alertManager.show('Requisição executada com sucesso!', 'success');

            } catch (error) {
                const endTime = performance.now();
                const duration = (endTime - startTime).toFixed(2);

                // Update request info
                requestStatus.textContent = 'Error';
                requestTime.textContent = `${duration}ms`;

                responseElement.textContent = `Erro: ${error.message}`;
                responseElement.className = 'text-red-400 text-sm whitespace-pre-wrap';
                
                // Show copy button even for errors
                copyResponseBtn.classList.remove('opacity-0');

                alertManager.show('Erro na requisição', 'error');
            } finally {
                // Restore button state
                button.innerHTML = originalText;
                button.disabled = false;
            }
        }
    </script>
@endpush