@extends('template.template-site')

@section('content_site')
<div class="bg-slate-900 dark:bg-gray-900 min-h-screen">

<style>
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .tab-button.active { 
        border-bottom: 2px solid #0d9488;
        color: #0d9488;
    }
    .animate-fade-in {
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

<!-- Header -->
<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div>
                    {{-- <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $workspaces->title }}</h1> --}}
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
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
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
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:border-gray-600 dark:text-white"
                        placeholder="Pesquisar por chave ou valor..."
                    >
                </div>
            </div>
            
            <!-- Botões de ação -->
            <div class="flex space-x-3">
                <button id="request-edit-btn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                    <i class="fas fa-edit mr-2"></i>
                    Solicitar Edição
                </button>
                
                <button id="share-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                    <i class="fas fa-share-alt mr-2"></i>
                    Compartilhar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="sticky top-0 z-40 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shadow-sm mb-6">
        <div 
            id="default-tab" 
            role="tablist" 
            class="flex space-x-8" 
            data-tabs-toggle="#default-tab-content" 
            data-tabs-active-classes="text-teal-600 hover:text-teal-600 dark:text-teal-500 dark:hover:text-teal-500 border-teal-600 dark:border-teal-500" 
            data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300">
            <button type="button" role="tab" class="inline-block p-4 border-b-2 rounded-t-lg text-teal-600 border-teal-600 dark:text-teal-500 dark:border-teal-500" data-tabs-target="#tab-workspace" aria-controls="tab-workspace" aria-selected="true">
                <i class="fas fa-layer-group mr-2"></i>Workspace
            </button>
            <button type="button" role="tab" class="inline-block p-4 border-b-2 rounded-t-lg" data-tabs-target="#tab-json" aria-controls="tab-json" aria-selected="false">
                <i class="fas fa-code mr-2"></i>JSON
            </button>
        </div>
    </div>

    <!-- Conteúdo das Tabs -->
    <div id="default-tab-content">
        <div class="" id="tab-workspace" role="tabpanel" aria-labelledby="tab-workspace-btn">
            <!-- Estrutura do Workspace -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                @if($workspace)
                    @forelse($workspace->topics as $topic)
                        <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                            <!-- Header do Tópico -->
                            <button onclick="toggleTopic({{ $topic->id }})" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700">
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
                                        <table class="w-full">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Chave
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Valor
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Status
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Ações
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                @forelse($topic->fields->where('is_visible', true) as $field)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $field->key_name }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="text-sm text-gray-600 dark:text-gray-300 break-all max-w-md">
                                                                {{ $field->value }}
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @if($field->is_visible)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                    <i class="fas fa-eye mr-1"></i> Visível
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                                                    <i class="fas fa-eye-slash mr-1"></i> Oculto
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                            <button onclick="copyToClipboard('{{ addslashes($field->value) }}')" 
                                                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3"
                                                                    title="Copiar valor">
                                                                <i class="fas fa-copy"></i>
                                                                <span class="sr-only">Copiar valor</span>
                                                            </button>
                                                            
                                                            <button onclick="copyToClipboard('{{ addslashes($field->key_name) }}')" 
                                                                    class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300"
                                                                    title="Copiar chave">
                                                                <i class="fas fa-key"></i>
                                                                <span class="sr-only">Copiar chave</span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                                            <div class="flex flex-col items-center justify-center">
                                                                <i class="fas fa-inbox text-3xl mb-2 text-gray-400"></i>
                                                                <p class="text-sm">Nenhum campo visível encontrado neste tópico</p>
                                                                <p class="text-xs mt-1">Os campos ocultos não são exibidos nesta visualização</p>
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
                            <p class="text-lg">Nenhum tópico encontrado neste workspace</p>
                        </div>
                    @endforelse
                @else
                    <p>Workspace não encontrado.</p>
                @endif
            </div>
        </div>

        <div class="hidden" id="tab-json" role="tabpanel" aria-labelledby="tab-json-btn">
            <!-- Visualização JSON -->
            <div class="bg-gray-800 rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-4 py-2 bg-gray-900">
                    <span class="text-sm font-medium text-gray-200">JSON Response</span>
                    <button id="copy-json-btn" class="text-gray-400 hover:text-white">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <pre class="p-4 text-green-400 overflow-auto max-h-max" id="json-output">
                    {{ json_encode([
                        'workspace' => [
                            'id' => $workspace->id,
                            'title' => $workspace->title,
                            'topics' => $workspace->topics->map(function($topic) {
                                return [
                                    'id' => $topic->id,
                                    'title' => $topic->title,
                                    'fields' => $topic->fields->map(function($field) {
                                        return [
                                            'id' => $field->id,
                                            'key_name' => $field->key_name,
                                            'value' => $field->value,
                                            'is_visible' => (bool) $field->is_visible,
                                            'order' => $field->order
                                        ];
                                    })
                                ];
                            })
                        ]
                    ], JSON_PRETTY_PRINT) }}
                </pre>
            </div>
        </div>
    </div>
</main>

<!-- Modal de Compartilhamento -->
<div id="share-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Compartilhar Workspace</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Link de compartilhamento
                </label>
                <div class="flex">
                    <input 
                        type="text" 
                        id="share-link-input" 
                        value="{{ url()->current() }}" 
                        class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                        readonly
                    >
                    <button 
                        id="copy-link-btn" 
                        class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-r-lg text-sm px-4 text-center inline-flex items-center dark:bg-teal-500 dark:hover:bg-teal-600 dark:focus:ring-teal-800"
                    >
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeShareModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleTopic(topicId) {
        const topicElement = document.getElementById('topic-' + topicId);
        const iconElement = document.getElementById('icon-' + topicId);
        
        if (topicElement.classList.contains('hidden')) {
            topicElement.classList.remove('hidden');
            iconElement.classList.add('rotate-180');
        } else {
            topicElement.classList.add('hidden');
            iconElement.classList.remove('rotate-180');
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Texto copiado para a área de transferência!', 'success');
        }).catch(err => {
            console.error('Erro ao copiar texto: ', err);
            showNotification('Erro ao copiar texto', 'error');
        });
    }

    function showNotification(message, type = 'success') {
        // Criar elemento de notificação
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
        
        // Mostrar notificação
        setTimeout(() => {
            notification.classList.remove('translate-x-32');
            notification.classList.add('translate-x-0');
        }, 100);
        
        // Esconder e remover notificação após 3 segundos
        setTimeout(() => {
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-32');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tabs functionality


        // Toggle topics
        window.toggleTopic = function(topicId) {
            const topicContent = document.getElementById('topic-' + topicId);
            const icon = document.getElementById('icon-' + topicId);
            
            topicContent.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        };

        // Share modal
        window.openShareModal = function() {
            document.getElementById('share-modal').classList.remove('hidden');
        };

        window.closeShareModal = function() {
            document.getElementById('share-modal').classList.add('hidden');
        };

        // Copy JSON
        document.getElementById('copy-json-btn').addEventListener('click', function() {
            const jsonContent = document.getElementById('json-output').textContent;
            navigator.clipboard.writeText(jsonContent).then(() => {
                alert('JSON copiado para a área de transferência!');
            });
        });

        // Copy link
        document.getElementById('copy-link-btn').addEventListener('click', function() {
            const shareLink = document.getElementById('share-link-input');
            shareLink.select();
            document.execCommand('copy');
            alert('Link copiado para a área de transferência!');
        });

        // Event listeners
        document.getElementById('share-btn').addEventListener('click', openShareModal);
        
        document.getElementById('request-edit-btn').addEventListener('click', function() {
            alert('Solicitação de edição enviada para o proprietário do workspace.');
        });

        // Search functionality
        document.getElementById('search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const fieldItems = document.querySelectorAll('.field-item');
            
            fieldItems.forEach(field => {
                const text = field.textContent.toLowerCase();
                field.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    });
</script>
</div>

    @include('components.footer.footer')
@endsection