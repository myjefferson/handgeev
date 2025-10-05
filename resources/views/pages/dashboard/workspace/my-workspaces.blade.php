@extends('template.template-dashboard')

@section('title', 'Meus Workspaces')
@section('description', 'Meus workspaces do HandGeev')

@section('content_dashboard')
    <style>
        /* Animações para os botões de exportação */
        .teal-glow-hover:hover {
            box-shadow: 0 0 15px rgba(45, 212, 191, 0.5);
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }

        /* Estilo para o JSON preview */
        #json-preview-content {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            line-height: 1.4;
        }

        /* Loading states */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-right-color: transparent;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header com Título e Botão Add -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Meus Workspaces
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Gerencie todos os seus workspaces.
                    </p>
                </div>
                <!-- Simulando a verificação de permissão -->
                <div class="flex items-center space-x-3">
                    @if (auth()->user()->canCreateWorkspace())
                        <button id="modal-add-workspace-btn" data-modal-target="modal-add-workspace" data-modal-toggle="modal-add-workspace"
                            class="flex items-center px-4 py-2 text-white rounded-lg bg-teal-600 hover:bg-teal-700 transition-colors teal-glow-hover">
                            <i class="fas fa-plus mr-2"></i>
                            New Workspace
                        </button>
                    @else
                        <div>
                            @include('components.upsell.button-upgrade-pro', [
                                'title' => 'Workspaces? Upgrade to',
                                'iconPrincipal' => false,
                                'iconLeft' => '<i class="fas fa-plus mx-2"></i>'
                            ])
                        </div>
                    @endif
                    {{-- Botão de Importação apenas para Start, Pro, Premium e Admin --}}
                    @if(auth()->user()->isStart() || auth()->user()->isPro() || auth()->user()->isPremium() || auth()->user()->isAdmin())
                        <a href="{{ route('workspace.import.form') }}" 
                            class="flex items-center px-4 py-2 text-white rounded-lg bg-purple-600 hover:bg-purple-700 transition-colors purple-glow-hover">
                            <i class="fas fa-upload mr-2"></i>
                            Importar Workspace
                        </a>
                    @else
                        @include('components.upsell.button-upgrade-pro', [
                            'title' => 'Import', 
                            'iconPrincipal' => false,
                            'iconLeft' => '<i class="fas fa-upload mx-2"></i>'
                        ])
                    @endif
                </div>
            </div>

            <!-- Barra de Pesquisa e Filtros -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <!-- Barra de Pesquisa -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="search-workspaces"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:border-gray-600 dark:text-white"
                                placeholder="Pesquisar workspaces..."
                            />
                        </div>
                    </div>
                    
                    <!-- Filtros e Ordenação -->
                    <div class="flex space-x-3">
                        <select id="sort-workspaces" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="newest">Mais recentes</option>
                            <option value="oldest">Mais antigos</option>
                            <option value="name_asc">A-Z</option>
                            <option value="name_desc">Z-A</option>
                        </select>
                        
                        <button id="filter-btn" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </div>  
            
            {{-- <div class="flex mb-5 space-x-4">
                <h3 class="text-sm font-semibold text-white mb-2 bg-slate-800 py-2 px-2 rounded-full">{{ auth()->user()->workspaces()->count() }} Workspaces</h3>
                <h3 class="text-sm font-semibold text-white mb-2">{{ auth()->user()->topics()->count() }} Tópicos</h3>
                <h3 class="text-sm font-semibold text-white mb-2">{{ auth()->user()->fields()->count() }} Campos</h3>
            </div> --}}

            <!-- Meus Workspaces -->
            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                <!-- Workspaces do Usuário -->
                @if($workspaces->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="my-workspaces-container">
                        @foreach($workspaces as $workspace)
                            <div class="workspace-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <!-- Header do Card -->
                                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                                                <i class="fas fa-layer-group text-teal-600 dark:text-teal-400"></i>
                                            </div>
                                            <div>
                                                <h3 class="workspace-title text-lg font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $workspace->title }}
                                                </h3>
                                                <p class="workspace-description text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $workspace->topics_count }} tópicos • {{ $workspace->fields_count }} campos
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Footer do Card com Ações -->
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50">
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('workspace.show', ['id' => $workspace->id]) }}" 
                                        class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-900/30 rounded-md hover:bg-teal-100 dark:hover:bg-teal-900/50">
                                            <i class="fas fa-eye mr-1.5"></i>Abrir
                                        </a>
                                        
                                        <div class="flex space-x-2">
                                            <!-- Botão Exportar -->
                                            <a href="{{ route('workspace.export', ['id' => $workspace->id]) }}"
                                            class="p-1.5 text-blue-500 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-md"
                                            title="Exportar Workspace">
                                                <i class="fas fa-file-export"></i>
                                            </a>
                                            
                                            <!-- Botão Configurar -->
                                            <a href="{{ route('workspace.setting', ['id' => $workspace->id]) }}"
                                            class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                            
                                            <!-- Botão Excluir -->
                                            <button type="button" 
                                                    class="delete-btn p-1.5 text-gray-500 dark:text-gray-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md"
                                                    data-id="{{ $workspace->id }}"
                                                    data-title="{{ $workspace->title }}"
                                                    data-route="{{ route('workspace.delete', ['id' => $workspace->id]) }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 mx-auto mb-4 text-gray-400">
                            <i class="fas fa-layer-group text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            Nenhum workspace criado
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            Comece criando seu primeiro workspace para organizar seus dados.
                        </p>
                        <button data-modal-target="modal-add-workspace" data-modal-toggle="modal-add-workspace" 
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                            Criar Primeiro Workspace
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('modals')    
    @include('components.modals.modal-add-workspace')
    @include('components.modals.modal-delete-workspace')
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Funcionalidade de pesquisa
            const searchInput = document.getElementById('search-workspaces');

            searchInput.addEventListener('input', function() {
               const searchTerm = this.value.toLowerCase();
                // Descobre qual aba está ativa
                const activeTab = document.querySelector('[aria-selected="true"]').dataset.tabsTarget;
                const cards = document.querySelectorAll(`#my-workspaces-container .workspace-card`);
                
                // Filtra os cards
                cards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.classList.toggle('hidden', !text.includes(searchTerm));
                });
            });
        });

        // Função para visualizar JSON
        async function previewWorkspaceJson(workspaceId) {
            try {
                const response = await fetch(`/workspace/${workspaceId}/export`);
                const data = await response.json();
                
                const jsonPreview = document.getElementById('json-preview-content');
                jsonPreview.textContent = JSON.stringify(data, null, 2);
                
                const modal = document.getElementById('json-preview-modal');
                modal.classList.remove('hidden');
            } catch (error) {
                console.error('Erro ao carregar JSON:', error);
                showNotification('Erro ao carregar JSON para visualização', 'error');
            }
        }

        // Visualizar JSON
        const previewJsonButtons = document.querySelectorAll('.export-preview-json');
        previewJsonButtons.forEach(button => {
            button.addEventListener('click', function() {
                const workspaceId = this.getAttribute('data-workspace-id');
                previewWorkspaceJson(workspaceId);
            });
        });

        // Função para copiar JSON para clipboard
        async function copyWorkspaceJsonToClipboard(workspaceId) {
            try {
                const response = await fetch(`/workspace/${workspaceId}/export`);
                const data = await response.json();
                
                await navigator.clipboard.writeText(JSON.stringify(data, null, 2));
                
                // Feedback visual
                showNotification('JSON copiado para a área de transferência!', 'success');
            } catch (error) {
                console.error('Erro ao copiar JSON:', error);
                showNotification('Erro ao copiar JSON', 'error');
            }
        }

        // Fechar modal de preview
        function closeJsonPreview() {
            const modal = document.getElementById('json-preview-modal');
            modal.classList.add('hidden');
        }

        // Copiar JSON do modal
        function copyJsonToClipboard() {
            const jsonContent = document.getElementById('json-preview-content').textContent;
            navigator.clipboard.writeText(jsonContent).then(() => {
                showNotification('JSON copiado para a área de transferência!', 'success');
            });
        }
    </script>
@endpush