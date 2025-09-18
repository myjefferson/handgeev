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
    </style>
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    {{-- <img src="{{ asset('assets/images/logo.png') }}" alt="Handgeev" class="h-8"> --}}
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Projetos</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Workspace compartilhado</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Informações do compartilhador -->
                    <div class="hidden md:flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-share-alt"></i>
                        <span>Compartilhado por: Jefferson jcs@gmail.com</span>
                    </div>
                    
                    <!-- Modo de acesso -->
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                {{ 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                        <i class="fas 'fa-eye' }} mr-1"></i>
                        {{ 'Visualização' }}
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
                    {{-- @if($accessLevel === 'viewer') --}}
                    @if(1)
                        <button id="request-edit-btn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            <i class="fas fa-edit mr-2"></i>
                            Solicitar Edição
                        </button>
                    @else
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            <i class="fas fa-save mr-2"></i>
                            Salvar Alterações
                        </button>
                    @endif
                    
                    <button id="share-btn" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        <i class="fas fa-share-alt mr-2"></i>
                        Compartilhar
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
            <div class="flex space-x-8">
                <button class="tab-button py-4 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 active" data-tab="tab-workspace">
                    <i class="fas fa-layer-group mr-2"></i>Workspace
                </button>
                <button class="tab-button py-4 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300" data-tab="tab-json">
                    <i class="fas fa-code mr-2"></i>JSON
                </button>
            </div>
        </div>

        <!-- Conteúdo das Tabs -->
        <div id="tab-workspace" class="tab-content active">
            <!-- Estrutura do Workspace -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                {{-- @foreach($workspace->topics as $topic) --}}
                    <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                        <!-- Header do Tópico -->
                        <button class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700" >
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-folder text-teal-500"></i>
                                <span class="font-medium text-gray-900 dark:text-white">teste</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded-full">
                                    {{-- {{ $topic->fields->count() }} campos --}}
                                    23 campos
                                </span>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 transform transition-transform" id="icon-45"></i>
                        </button>

                        <!-- Campos do Tópico -->
                        <div id="topic-45" class="hidden">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    {{-- @foreach($topic->fields as $field) --}}
                                        <div class="bg-white dark:bg-gray-600 rounded-lg p-3 shadow-xs">
                                            <div class="flex items-center justify-between mb-2">
                                                {{-- <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $field->key_name }}</span> --}}
                                                {{-- @if($field->is_visible) --}}
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        <i class="fas fa-eye mr-1"></i> Visível
                                                    </span>
                                                {{-- @endif --}}
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">teste</p>
                                        </div>
                                    {{-- @endforeach --}}
                                </div>
                            </div>
                        </div>
                    </div>
                {{-- @endforeach --}}
            </div>
        </div>

        <div id="tab-json" class="tab-content">
            <!-- Visualização JSON -->
            <div class="bg-gray-800 rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-4 py-2 bg-gray-900">
                    <span class="text-sm font-medium text-gray-200">JSON Response</span>
                    <button id="copy-json-btn" class="text-gray-400 hover:text-white">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <pre class="p-4 text-green-400 overflow-auto max-h-96" id="json-output">json</pre>
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
                            id="share-link" 
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

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nível de acesso
                    </label>
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <option value="viewer">Visualização</option>
                        <option value="editor">Edição</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeShareModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                        Gerar Link
                    </button>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tabs functionality
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.getAttribute('data-tab');
                    
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    button.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });

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
            new ClipboardJS('#copy-json-btn', {
                text: function() {
                    return document.getElementById('json-output').textContent;
                }
            });

            // Copy link
            new ClipboardJS('#copy-link-btn', {
                text: function() {
                    return document.getElementById('share-link').value;
                }
            });

            // Event listeners
            document.getElementById('share-btn').addEventListener('click', openShareModal);
            
            document.getElementById('request-edit-btn')?.addEventListener('click', function() {
                alert('Solicitação de edição enviada para o proprietário do workspace.');
            });

            // Search functionality
            document.getElementById('search-input').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const fields = document.querySelectorAll('[class*="bg-white dark:bg-gray-600"]');
                
                fields.forEach(field => {
                    const text = field.textContent.toLowerCase();
                    field.style.display = text.includes(searchTerm) ? 'block' : 'none';
                });
            });
        });
    </script>
</div>
    @include('components.footer.footer_login')
@endsection