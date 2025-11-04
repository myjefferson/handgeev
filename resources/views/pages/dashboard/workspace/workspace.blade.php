@extends('template.template-dashboard')

@section('title', $workspace->title)
@section('description', __('workspace.description', ['title' => $workspace->title]))

@php
    $user = auth()->user();
    $topicsLimit = $user->getTopicsLimit();
    $currentTopics = $user->getCurrentTopicsCount($workspace->id);
    $remainingTopics = $user->getRemainingTopicsCount($workspace->id);
    $canAddMoreTopics = $user->canAddMoreTopics($workspace->id);
@endphp

@section('content_dashboard')
    <div class="max-w-full sm:max-w-6xl md:max-w-7xl xl:max-w-7xl mx-auto">
        <header class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('workspaces.show', ['id' => $workspace->id]) }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-1"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Workspace {{ $workspace->title }}</h1>
                    </div>
                </div>
                <div class="block">
                    @include('components.headers.header-workspace', [
                        'workspace' => $workspace,
                    ])                     
                </div>
            </div>
        </header>

        <div class="flex bg-slate-900 rounded-xl min-h-dvh">
            <!-- Sidebar de Tópicos -->
            @if ($workspace->type_workspace_id !== 1)
                <div class="w-64 bg-slate-800 border-r border-slate-700 rounded-xl">
                    <div class="p-4">
                        <!-- Cabeçalho do Workspace -->
                        <div class="mb-6">
                            <h1 class="text-xl font-bold text-white truncate">{{ $workspace->title }}</h1>
                            <p class="text-sm text-gray-400 mt-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium {{ $canAddMoreTopics ? 'text-teal-400' : 'text-yellow-400' }}">
                                        {{ $currentTopics }}/{{ $topicsLimit < 9999 ? '∞' : $topicsLimit }} {{__('workspace.sidebar.topics_count') }}
                                    </span>
                                    @if(!$canAddMoreTopics)
                                        <p class="text-xs text-yellow-400 mt-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Limite atingido
                                        </p>
                                    @endif
                                </div>
                            </p>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex flex-col space-y-3 mb-6">
                            @if($workspace->type_workspace_id == 2)
                                @if ($canAddMoreTopics)
                                    <button id="addTopicBtn" class="w-full px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors duration-300 teal-glow-hover">
                                        <i class="fas fa-plus mr-2"></i> {{ __('workspace.sidebar.new_topic') }}
                                    </button>
                                @else
                                    @include('components.upsell.button-upgrade-pro')
                                @endif
                            @endif
                            <button id="dropdownImportExportButton" 
                                    data-dropdown-toggle="dropdownImportExport"
                                    class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium rounded-lg transition-colors duration-300 flex items-center justify-center border border-slate-600 hover:border-slate-500"
                                    type="button">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                <span class="hidden sm:inline">{{ __('workspace.import_export.actions') }}</span>
                                <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                </svg>
                            </button>
                            <!-- Botões de Importação/Exportação -->
                            @include('components.dropdown.options-topics-dropdown', $workspace)
                        </div>

                        <!-- Lista de Tópicos -->
                        <div class="space-y-1">
                            @foreach($workspace->topics as $index => $topic)
                                @php
                                    $topicLimits = $topicsWithLimits[$topic->id] ?? [
                                        'canAddMoreFields' => $globalCanAddMoreFields,
                                        'fieldsLimit' => $globalFieldsLimit,
                                        'currentFieldsCount' => $globalCurrentFieldsCount,
                                        'remainingFields' => $globalRemainingFields
                                    ];
                                @endphp
                                <div class="topic-item group relative" data-topic-id="{{ $topic->id }}">
                                    <button class="w-full text-left p-3 rounded-xl transition-colors duration-200 flex items-center justify-between
                                        {{ $index === 0 ? 'bg-teal-400/20 text-teal-400 border border-teal-400/30' : 'text-gray-400 hover:text-teal-300 hover:bg-slate-750' }} topic-select-btn">
                                        <div class="flex items-center truncate">
                                            <i class="fas fa-folder mr-3 text-sm"></i>
                                            <span class="truncate topic-title">{{ $topic->title }}</span>
                                        </div>
                                        <span class="text-xs bg-slate-700 px-2 py-1 rounded-full group-hover:bg-slate-600 text-teal-300">
                                            {{ trans_choice('workspace.sidebar.fields_count', count($topic->fields), ['count' => count($topic->fields)]) }}
                                        </span>
                                    </button>
                                    
                                    <!-- Menu de Ações do Tópico -->
                                    <div class="absolute right-2 top-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <button id="dropdownTopicButton-{{ $topic->id }}" 
                                                data-dropdown-toggle="dropdownTopic-{{ $topic->id }}" 
                                                class="flex items-center justify-center h-8 w-8 bg-gray-800 text-gray-400 hover:text-teal-300 rounded-full transition-colors"
                                                type="button">
                                            <i class="fas fa-ellipsis-v text-sm"></i>
                                        </button>
                                        
                                        <!-- Dropdown menu -->
                                        <div id="dropdownTopic-{{ $topic->id }}" 
                                            class="absolute z-10 hidden bg-slate-800 divide-y divide-gray-600 rounded-lg shadow w-44 border border-slate-700 right-0 top-full mt-1">
                                            <ul class="py-2 text-sm text-gray-200" aria-labelledby="dropdownTopicButton-{{ $topic->id }}">
                                                <li>
                                                    <button type="button" 
                                                            class="rename-topic-btn w-full px-4 py-2 text-left hover:bg-slate-700 flex items-center"
                                                            data-topic-id="{{ $topic->id }}"
                                                            data-topic-title="{{ $topic->title }}">
                                                        <i class="fas fa-edit mr-2 text-blue-400"></i>
                                                        {{ __('workspace.sidebar.rename_topic') }}
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" 
                                                            class="download-topic-btn w-full px-4 py-2 text-left hover:bg-slate-700 flex items-center"
                                                            data-topic-id="{{ $topic->id }}"
                                                            data-topic-title="{{ $topic->title }}">
                                                        <i class="fas fa-download mr-2 text-green-400"></i>
                                                        {{ __('workspace.sidebar.download_topic') }}
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Área Principal de Conteúdo -->
            <div class="flex-1 overflow-y-auto p-6 bg-slate-800/40 rounded-r-lg">
                <!-- Conteúdo dos tópicos -->
                @foreach($workspace->topics as $index => $topic)
                    @php
                        $topicLimits = $topicsWithLimits[$topic->id] ?? [
                            'canAddMoreFields' => $globalCanAddMoreFields,
                            'fieldsLimit' => $globalFieldsLimit,
                            'currentFieldsCount' => $globalCurrentFieldsCount,
                            'remainingFields' => $globalRemainingFields,
                            'isUnlimited' => $globalIsUnlimited
                        ];
                        
                        $canAddMore = $topicLimits['isUnlimited'] || $topicLimits['currentFieldsCount'] < $topicLimits['fieldsLimit'];
                    @endphp
                    
                    <div class="topic-content mb-6 {{ $index === 0 ? '' : 'hidden' }}" data-topic-id="{{ $topic->id }}">
                        <!-- Indicadores de Limite POR TÓPICO -->
                        @if(!$topicLimits['isUnlimited'])
                            @if(!$canAddMore && $topicLimits['fieldsLimit'] > 0)
                                <div class="mb-6 p-4 bg-purple-500/10 border border-purple-500/20 rounded-xl">
                                    <div class="flex items-center">
                                        <i class="fas fa-exclamation-triangle text-purple-400 mr-3"></i>
                                        <div>
                                            <p class="text-purple-300 text-sm">
                                                Limite de {{ $topicLimits['fieldsLimit'] }} campos por tópico atingido. 
                                                Este tópico já tem {{ $topicLimits['currentFieldsCount'] }} campos.
                                                <a href="{{ route('subscription.pricing') }}" class="underline font-medium text-white ml-1">
                                                    Faça upgrade para mais campos.
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @elseif($topicLimits['fieldsLimit'] > 0)
                                <div class="mb-6 p-4 bg-teal-400/10 border border-teal-400/20 rounded-xl">
                                    <div class="flex items-center">
                                        <i class="fas fa-chart-pie text-teal-400 mr-3"></i>
                                        <div>
                                            <p class="text-teal-300 text-sm">
                                                Campos neste tópico: {{ $topicLimits['currentFieldsCount'] }}/{{ $topicLimits['fieldsLimit'] < 9999 ? '∞' : $topicLimits['fieldsLimit'] }} 
                                                ({{ $topicLimits['remainingFields'] }} restantes)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Cabeçalho do tópico -->
                        @if (count($workspace->topics) > 1)
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="topic-title text-xl font-semibold text-white">{{ $topic->title }}</h3>
                                @if(count($workspace->topics) > 1)
                                    <button class="delete-topic-btn mr-4 p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors duration-200" data-topic-id="{{ $topic->id }}" title="{{ __('workspace.actions.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        @endif

                        <!-- Tabela de fields do tópico -->
                        <div class="bg-slate-800 rounded-2xl border border-slate-700 overflow-hidden">
                            @include('components.tables.workspace-fields-table', [
                                $topic,
                                $topicLimits,
                                $canAddMore
                            ])
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <style>
        .teal-glow-hover:hover {
            box-shadow: 0 0 20px rgba(0, 230, 216, 0.3);
        }
    </style>
@endsection

@push('modals')
    @include('components.modals.modal-share-geev-studio', ['workspace' => $workspace])
    @include('components.modals.modal-import-topic')
    @include('components.modals.modal-export-topic')
    @include('components.modals.modal-rename-topic')
@endpush

@push('scripts_end')
    <script>
        window.topicsWithLimits = @json($topicsWithLimits ?? []);
        window.globalCanAddMoreFields = @json($globalCanAddMoreFields ?? true);
        window.globalFieldsLimit = @json($globalFieldsLimit ?? 0);
        window.globalCurrentFieldsCount = @json($globalCurrentFieldsCount ?? 0);
        window.globalRemainingFields = @json($globalRemainingFields ?? 0);
        window.globalIsUnlimited = @json($globalIsUnlimited ?? false);
        window.isAdmin = @json(auth()->user()->isAdmin() ?? false);
                
        // Função auxiliar robusta
        window.getTopicLimits = function(topicId) {
            if (window.topicsWithLimits && window.topicsWithLimits[topicId]) {
                return window.topicsWithLimits[topicId];
            }
            
            // Se não encontrou limites específicos, criar um objeto padrão
            return {
                canAddMoreFields: window.globalCanAddMoreFields,
                fieldsLimit: window.globalFieldsLimit,
                currentFieldsCount: 0,
                remainingFields: window.globalRemainingFields,
                isUnlimited: window.globalIsUnlimited
            };
        };

        window.updateTopicLimitsFromResponse = function(topicId, limits) {
            if (!window.topicsWithLimits) {
                window.topicsWithLimits = {};
            }
            
            window.topicsWithLimits[topicId] = {
                canAddMoreFields: limits.is_unlimited || limits.current < limits.max,
                fieldsLimit: limits.max,
                currentFieldsCount: limits.current,
                remainingFields: limits.remaining,
                isUnlimited: limits.is_unlimited
            };
            
        };

        // Traduções para JavaScript
        window.translations = {
            workspace: {
                sidebar: {
                    rename_topic: "{{ __('workspace.modals.rename_topic.title') }}",
                    download_topic: "{{ __('workspace.sidebar.download_topic') }}",
                    fields_count: "{{ trans_choice('workspace.sidebar.fields_count', 2, ['count' => 2]) }}".replace('2 campos', ':count')
                },
                modals: {
                    new_topic: {
                        prompt: "{{ __('workspace.modals.new_topic.prompt') }}",
                        placeholder: "{{ __('workspace.modals.new_topic.placeholder') }}"
                    },
                    delete_topic: {
                        message: "{{ __('workspace.modals.delete_topic.message') }}"
                    },
                    rename_topic: {
                        title: "{{ __('workspace.modals.rename_topic.title') }}",
                        topic_title: "{{ __('workspace.modals.rename_topic.topic_title') }}",
                        characters: "{{ __('workspace.modals.rename_topic.characters') }}",
                        cancel: "{{ __('workspace.modals.rename_topic.cancel') }}",
                        save: "{{ __('workspace.modals.rename_topic.save') }}",
                        close: "{{ __('workspace.modals.rename_topic.close') }}"
                    }
                },
                notifications: {
                    saving: "{{ __('workspace.notifications.saving') }}",
                    saved: "{{ __('workspace.notifications.saved') }}",
                    deleting: "{{ __('workspace.notifications.deleting') }}",
                    topic_renamed: "{{ __('workspace.notifications.topic_renamed') }}",
                    topic_rename_error: "{{ __('workspace.notifications.topic_rename_error') }}",
                    download_started: "{{ __('workspace.notifications.download_started') }}",
                    download_error: "{{ __('workspace.notifications.download_error') }}"
                }
            }
        };

        window.topicsWithLimits = @json($topicsWithLimits ?? []);
        window.globalCanAddMoreFields = @json($globalCanAddMoreFields ?? true);
    </script>
    
    <script type="module">
        import { createTopic, deleteTopic } from '/js/modules/topic/topic-ajax.js'
        import { createField, updateField, deleteField } from '/js/modules/field/field-ajax.js'
        import '/js/modules/topic/topic-interations.js'
        import { 
            updateSaveIndicator, 
            initializeTypeChangeListeners, 
            addNewField, 
            updateAddFieldButtons
        } from '/js/modules/field/field-interations.js'

        const workspace_id = {{ $workspace->id }}

        const routes = {
            create_field: "{{ route('field.store') }}",
            update_field: "{{ route('field.update', ['id' => ':id']) }}",
            delete_field: "{{ route('field.destroy', ['id' => ':id']) }}",

            create_topic: "{{ route('topic.store') }}",
            update_topic: "{{ route('topic.update', ['id' => ':id']) }}",
            delete_topic: "{{ route('topic.destroy', ['id' => ':id']) }}"
        };

        // Inicializar UI quando documento estiver pronto
        $(document).ready(function() {          
            setTimeout(() => {
                initializeTypeChangeListeners();
                
                
                $('.topic-content').each(function() {
                    const topicId = $(this).data('topic-id');
                    const limits = window.getTopicLimits(topicId);
                });
            }, 200);
        });

        // Adicionar novo tópico
        $(document).on('click', '#addTopicBtn', function() {
            const topicName = prompt(
                window.translations.workspace.modals.new_topic.prompt,
                window.translations.workspace.modals.new_topic.placeholder
            );
            
            if (topicName && topicName.trim() !== '') {
                createTopic(
                    workspace_id, 
                    topicName.trim(),
                    routes.create_topic
                );
            }
        });

        // Evento para salvar linha individual
        $(document).on('click', '.save-row', function() {
            const row = $(this).closest('tr');
            const field_id = row.attr('data-id');
            const topicContent = row.closest('.topic-content');
            const topic_id = topicContent.data('topic-id');
            
            updateSaveIndicator(true, false);
            
            if (field_id && field_id !== '') {
                updateField(
                    row,
                    topic_id,
                    routes.update_field.replace(':id', field_id)
                );
            } else {
                createField(
                    row,
                    topic_id,
                    workspace_id,
                    routes.create_field
                );
            }
        });  
        
        // Evento para remover linha
        $(document).on('click', '.remove-row', function() {
            const row = $(this).closest('tr');
            const field_id = row.attr('data-id');
            const topicContent = row.closest('.topic-content');
            const topic_id = topicContent.data('topic-id'); 
            
            deleteField(
                row,
                topic_id,
                routes.delete_field.replace(':id', field_id)
            );
        });

        // Deletar tópico
        $(document).on('click', '.delete-topic-btn', function() {
            const topic_id = $(this).data('topic-id');
            const topicTitle = $(this).closest('.topic-item').find('span').text() || $(this).closest('.topic-content').find('h3').text();
            
            const message = window.translations.workspace.modals.delete_topic.message.replace(':title', topicTitle);
            
            if (confirm(message)) {
                deleteTopic(routes.delete_topic.replace(':id', topic_id));
            }
        });
    </script>

    <script>
        // Variáveis globais
        let currentWorkspaceId = {{ $workspace->id }};
        let selectedTopicId = null;
        let selectedTopicTitle = null;
        let currentImportMethod = 'file';

        // Modal de Importação
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
            loadImportableTopics();
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        // Modal de Exportação
        function openExportModal(topicId, topicTitle) {
            selectedTopicId = topicId;
            selectedTopicTitle = topicTitle;
            document.getElementById('exportModal').classList.remove('hidden');
        }

        function closeExportModal() {
            document.getElementById('exportModal').classList.add('hidden');
        }

        // Sistema de Abas
        document.querySelectorAll('.import-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Atualizar abas
                document.querySelectorAll('.import-tab').forEach(t => {
                    t.classList.remove('border-blue-500', 'text-white');
                    t.classList.add('border-transparent', 'text-slate-400');
                });
                this.classList.add('border-blue-500', 'text-white');
                this.classList.remove('border-transparent', 'text-slate-400');
                
                // Atualizar conteúdo
                const tabName = this.id.replace('tab', '').toLowerCase();
                document.querySelectorAll('.import-tab-content').forEach(content => {
                    content.classList.add('hidden');
                });
                document.getElementById(`tab${tabName.charAt(0).toUpperCase() + tabName.slice(1)}Content`).classList.remove('hidden');
                
                currentImportMethod = tabName;
            });
        });

        // Carregar tópicos importáveis
        function loadImportableTopics() {
            const select = document.getElementById('existingTopicSelect').innerHTML = 
            '<option value="">{{ __("workspace.import_export.loading_topics") }}</option>';
            
            fetch(`/importable-topics?current_workspace_id=${currentWorkspaceId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    select.innerHTML = '<option value="">Selecione um tópico</option>';
                    data.data.topics.forEach(topic => {
                        const option = document.createElement('option');
                        option.value = topic.id;
                        option.textContent = `${topic.title} (${topic.fields_count} campos) - ${topic.workspace.title}`;
                        option.dataset.fieldsCount = topic.fields_count;
                        select.appendChild(option);
                    });
                } else {
                    select.innerHTML = '<option value="">Erro ao carregar tópicos</option>';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                select.innerHTML = '<option value="">Erro ao carregar tópicos</option>';
            });
        }

        // Confirmar Importação
        function confirmImport() {
            const confirmBtn = document.getElementById('confirmImportBtn');
            const originalText = confirmBtn.innerHTML;
            
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Importando...';
            confirmBtn.disabled = true;

            if (currentImportMethod === 'file') {
                importFromFile();
            } else {
                importFromExisting();
            }
        }

        // Importar de Arquivo
        function importFromFile() {
            const form = document.getElementById('importFileForm');
            const formData = new FormData(form);
            
            fetch(`/workspaces/${currentWorkspaceId}/import-topic`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Tópico importado com sucesso!', 'success');
                    closeImportModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Erro ao importar tópico', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao importar tópico', 'error');
            })
            .finally(() => {
                document.getElementById('confirmImportBtn').innerHTML = '<i class="fas fa-download mr-2"></i>Importar';
                document.getElementById('confirmImportBtn').disabled = false;
            });
        }

        // Importar de Tópico Existente
        function importFromExisting() {
            const topicId = document.getElementById('existingTopicSelect').value;
            const topicTitle = document.getElementById('existingTopicTitle').value;
            
            if (!topicId || !topicTitle) {
                showNotification('Selecione um tópico e digite um nome', 'error');
                return;
            }

            // Primeiro exporta o tópico existente
            fetch(`/topics/${topicId}/export`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cria um arquivo JSON com os dados exportados
                    const exportData = data.data;
                    const blob = new Blob([JSON.stringify(exportData)], { type: 'application/json' });
                    const file = new File([blob], 'import.json', { type: 'application/json' });
                    
                    // Prepara form data para importação
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('topic_title', topicTitle);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    
                    // Importa o arquivo
                    return fetch(`/workspaces/${currentWorkspaceId}/import-topic`, {
                        method: 'POST',
                        body: formData
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Tópico importado com sucesso!', 'success');
                    closeImportModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Erro ao importar tópico', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showNotification('Erro ao importar tópico: ' + error.message, 'error');
            })
            .finally(() => {
                document.getElementById('confirmImportBtn').innerHTML = '<i class="fas fa-download mr-2"></i>Importar';
                document.getElementById('confirmImportBtn').disabled = false;
            });
        }

        // Exportar Tópico
        function exportTopic(method) {
            if (!selectedTopicId) return;

            if (method === 'json') {
                // Exportar como JSON (visualização)
                fetch(`/topics/${selectedTopicId}/export`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar JSON em um modal ou console
                        console.log('Dados exportados:', data.data);
                        showNotification('Tópico exportado! Verifique o console do navegador.', 'success');
                        closeExportModal();
                    } else {
                        showNotification(data.message || 'Erro ao exportar tópico', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao exportar tópico', 'error');
                });
            } else if (method === 'download') {
                // Download do arquivo
                window.location.href = `/topics/${selectedTopicId}/download`;
                closeExportModal();
                showNotification('Download iniciado!', 'success');
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Botão Importar
            const importBtn = document.getElementById('importTopicBtn');
            if (importBtn) {
                importBtn.addEventListener('click', openImportModal);
            }

            // Botão Exportar Todos
            const exportAllBtn = document.getElementById('exportAllBtn');
            if (exportAllBtn) {
                exportAllBtn.addEventListener('click', function() {
                    // Implementar exportação de todos os tópicos
                    showNotification('Exportar todos os tópicos - em desenvolvimento', 'info');
                });
            }

            // Botões de Exportação individual
            document.querySelectorAll('.export-topic-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const topicId = this.getAttribute('data-topic-id');
                    const topicTitle = this.getAttribute('data-topic-title');
                    openExportModal(topicId, topicTitle);
                });
            });

            // Botões de Download individual
            document.querySelectorAll('.download-topic-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const topicId = this.getAttribute('data-topic-id');
                    window.location.href = `/topics/${topicId}/download`;
                });
            });
        });

        // Função auxiliar para notificações
        function showNotification(message, type = 'info') {
            // Implementar sistema de notificação existente
            alert(message); // Substituir pelo sistema de notificação do HandGeev
        }
    </script>
@endpush