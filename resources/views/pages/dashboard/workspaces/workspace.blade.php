@extends('template.template-dashboard')

@section('content_dashboard')
    <div class="flex max-w-full sm:max-w-6xl md:max-w-7xl xl:max-w-7xl mx-auto min-h-dvh bg-slate-900 rounded-xl">
        <!-- Sidebar de Tópicos -->
        <div class="w-64 bg-slate-800 border-r border-slate-700 overflow-y-auto rounded-xl">
            <div class="p-4">
                <!-- Cabeçalho do Workspace -->
                <div class="mb-6">
                    <h1 class="text-xl font-bold text-white truncate">{{ $workspace->title }}</h1>
                    @if(count($workspace->topics) > 1)
                        <p class="text-sm text-gray-400 mt-1">{{ count($workspace->topics) }} tópicos</p>
                    @endif
                </div>

                <!-- Botão Adicionar Tópico -->
                @if($workspace->type_workspace_id == 2)
                    <button id="addTopicBtn" class="w-full mb-6 px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors duration-300 teal-glow-hover">
                        <i class="fas fa-plus mr-2"></i> Novo Tópico
                    </button>
                @endif

                <!-- Lista de Tópicos -->
                <div class="space-y-1">
                    @foreach($workspace->topics as $index => $topic)
                        <div class="topic-item group relative" data-topic-id="{{ $topic->id }}">
                            <button class="w-full text-left p-3 rounded-xl transition-colors duration-200 flex items-center justify-between
                                {{ $index === 0 ? 'bg-teal-400/20 text-teal-400 border border-teal-400/30' : 'text-gray-400 hover:text-teal-300 hover:bg-slate-750' }}">
                                <div class="flex items-center truncate">
                                    <i class="fas fa-folder mr-3 text-sm"></i>
                                    <span class="truncate topic-title">{{ $topic->title }}</span>
                                </div>
                                <span class="text-xs bg-slate-700 px-2 py-1 rounded-full group-hover:bg-slate-600 text-teal-300">
                                    {{ count($topic->fields) }}
                                </span>
                            </button>
                            
                            <!-- Botão Excluir Tópico -->
                            @if(count($workspace->topics) > 1)
                            <button class="delete-topic-btn absolute right-3 top-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-red-400 hover:text-red-300"
                                    data-topic-id="{{ $topic->id }}" title="Excluir tópico">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Área Principal de Conteúdo -->
        <div class="flex-1 overflow-y-auto p-6 bg-slate-800/40 rounded-r-lg">
            <!-- Indicadores de Limite -->
            @if(!$canAddMoreFields && $fieldsLimit > 0)
                <div class="mb-6 p-4 bg-purple-500/10 border border-purple-500/20 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-purple-400 mr-3"></i>
                        <div>
                            <p class="text-purple-300 text-sm">
                                Limite de campos atingido ({{ $currentFieldsCount }}/{{ $fieldsLimit }}). 
                                <a href="{{ route('landing.offers') }}" class="underline font-medium text-white">Faça upgrade</a> 
                                para adicionar mais campos.
                            </p>
                        </div>
                    </div>
                </div>
                @elseif($fieldsLimit > 0)
                <div class="mb-6 p-4 bg-teal-400/10 border border-teal-400/20 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-chart-pie text-teal-400 mr-3"></i>
                        <div>
                            <p class="text-teal-300 text-sm">
                                📊 Campos utilizados: {{ $currentFieldsCount }}/{{ $fieldsLimit }} 
                                ({{ $remainingFields }} restantes)
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="block mb-8">
                <div class="flex justify-between">
                    <div class="text-2xl font-semibold">Workspace</div>
                    <div class="flex space-x-2">
                        @if($workspace->type_view_workspace_id == 1)
                            <button data-modal-target="modalShareApiInterface" data-modal-toggle="modalShareApiInterface" class="flex items-center font-medium bg-cyan-400 text-slate-950 rounded-xl py-2 px-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2" width="1.4em" height="1.4em" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/>
                                </svg>
                                <span>Interface API</span>
                            </button>
                        @endif
                        @if($workspace->type_view_workspace_id == 2)
                            <a href="{{ route('workspace.api-rest', $workspace->id) }}" 
                                class="flex items-center font-medium bg-cyan-400 text-slate-950 rounded-xl py-2 px-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2" width="1.4em" height="1.4em" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/>
                                </svg>
                                <span>REST API</span>
                            </a>
                        @endif
                        <a href="{{ route('workspace.setting', ['id' => $workspace->id]) }}" class="flex items-center font-medium bg-cyan-400 text-slate-950 rounded-xl py-2 px-4">
                            <svg class="w-6 h-6 text-gray-800 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10.83 5a3.001 3.001 0 0 0-5.66 0H4a1 1 0 1 0 0 2h1.17a3.001 3.001 0 0 0 5.66 0H20a1 1 0 1 0 0-2h-9.17ZM4 11h9.17a3.001 3.001 0 0 1 5.66 0H20a1 1 0 1 1 0 2h-1.17a3.001 3.001 0 0 1-5.66 0H4a1 1 0 1 1 0-2Zm1.17 6H4a1 1 0 1 0 0 2h1.17a3.001 3.001 0 0 0 5.66 0H20a1 1 0 1 0 0-2h-9.17a3.001 3.001 0 0 0-5.66 0Z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Conteúdo dos tópicos -->
            @foreach($workspace->topics as $index => $topic)
            <div class="topic-content mb-6 {{ $index === 0 ? '' : 'hidden' }}" data-topic-id="{{ $topic->id }}">
                <!-- Cabeçalho do tópico -->
                @if (count($workspace->topics) > 1)
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-semibold text-white">{{ $topic->title }}</h3>
                        @if(count($workspace->topics) > 1)
                        <button class="delete-topic-btn p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors duration-200" data-topic-id="{{ $topic->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </div>
                @endif

                <!-- Tabela de fields do tópico -->
                <div class="relative overflow-x-auto bg-slate-800 rounded-2xl border border-slate-700">
                    <table class="key-value-table w-full text-sm text-left text-gray-400">
                        <thead class="text-xs uppercase bg-slate-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-medium">Visibilidade</th>
                                <th scope="col" class="px-6 py-4 font-medium">Chave</th>
                                <th scope="col" class="px-6 py-4 font-medium">Valor</th>
                                <th scope="col" class="px-6 py-4 font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topic->fields as $field)
                                <tr class="border-b border-slate-700 hover:bg-slate-750 transition-colors duration-200" 
                                    data-id="{{ $field->id }}" data-topic-id="{{ $topic->id }}">
                                    <td class="px-6 py-4">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="visibility-checkbox sr-only peer" 
                                                {{ $field->is_visible ? 'checked' : '' }}>
                                            <div class="relative w-11 h-6 bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-500"></div>
                                        </label>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" name="key_name" value="{{ $field->key_name }}" 
                                            class="key-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors" placeholder="Nome da chave">
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="text" name="key_value" value="{{ $field->value }}" 
                                            class="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors" placeholder="Valor">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <button type="button" class="save-row p-2 text-teal-400 hover:text-teal-300 rounded-lg transition-colors duration-200" title="Salvar">
                                                <i class="fas fa-save"></i>
                                            </button>
                                            <button type="button" class="remove-row p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors duration-200" title="Remover">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-2xl mb-2"></i>
                                        <p>Nenhum campo cadastrado neste tópico</p>
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Linha para adicionar novo campo -->
                            @if($canAddMoreFields)
                                <tr class="add-field-trigger bg-slate-750 cursor-pointer hover:bg-slate-700 transition-colors duration-200" data-topic-id="{{ $topic->id }}">
                                    <td colspan="4" class="px-6 py-4 text-center text-teal-400">
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-plus-circle mr-2"></i>
                                            Clique para adicionar novo campo
                                        </div>
                                    </td>
                                </tr>
                                @else
                                <tr class="limit-reached-row bg-slate-750">
                                    <td colspan="4" class="px-6 py-4 text-center text-purple-400">
                                        <div class="flex items-center justify-center">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            Limite de campos atingido. 
                                            <a href="{{ route('landing.offers') }}" class="underline ml-1 text-white">Faça upgrade</a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <style>
        .teal-glow-hover:hover {
            box-shadow: 0 0 20px rgba(0, 230, 216, 0.3);
        }
    </style>
@endsection


 <!-- Modal de Compartilhamento -->
<div id="modalShareApiInterface" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <!-- Modal header -->
    <div class="bg-slate-700 rounded-lg">
        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Compartilhar Workspace
            </h3>
            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="modalShareApiInterface">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Fechar modal</span>
            </button>
        </div>
        <!-- Modal body -->
        <div class="p-4 md:p-5 space-y-4">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Link de Compartilhamento</label>
                <div class="flex">
                    <input type="text" id="share-link" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" value="{{ route('workspace.shared.interface.api', ['global_hash_api' => auth()->user()->global_hash_api, 'workspace_hash_api' => $workspace?->workspace_hash_api]) }}" readonly>
                    <button id="copy-link" data-tooltip-target="tooltip-copy-link" class="flex-shrink-0 inline-flex items-center py-2.5 px-4 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <i class="fas fa-copy mr-1"></i>
                        <span class="hidden md:inline">Copiar</span>
                    </button>
                    <div id="tooltip-copy-link" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                        Copiar link
                        <div class="tooltip-arrow" data-popper-arrow></div>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Este link contém suas chaves de acesso de forma segura.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Global Hash (User)</label>
                    <div class="flex">
                        <input type="text" id="global-hash" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" value="{{ auth()->user()->global_hash_api }}" readonly>
                        <button id="copy-global-hash" data-tooltip-target="tooltip-copy-global" class="flex-shrink-0 inline-flex items-center py-2.5 px-3 text-sm font-medium text-white bg-gray-700 rounded-r-lg border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            <i class="fas fa-copy"></i>
                        </button>
                        <div id="tooltip-copy-global" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            Copiar Global Hash
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Workspace Hash</label>
                    <div class="flex">
                        <input type="text" id="workspace-hash" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" value="{{ $workspace->workspace_hash_api }}" readonly>
                        <button id="copy-workspace-hash" data-tooltip-target="tooltip-copy-workspace" class="flex-shrink-0 inline-flex items-center py-2.5 px-3 text-sm font-medium text-white bg-gray-700 rounded-r-lg border border-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            <i class="fas fa-copy"></i>
                        </button>
                        <div id="tooltip-copy-workspace" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700">
                            Copiar Workspace Hash
                            <div class="tooltip-arrow" data-popper-arrow></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-300" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-400 mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <span class="font-medium">Aviso de Segurança!</span>
                        <ul class="mt-1.5 list-disc list-inside">
                            <li>Compartilhe este link apenas com pessoas de confiança</li>
                            <li>Qualquer pessoa com o link poderá visualizar este workspace</li>
                            <li>O Global Hash é compartilhado entre todos os seus workspaces</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button id="regenerate-global-hash" type="button" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <i class="fas fa-sync-alt mr-1"></i> Regenerar Global Hash
                </button>
                <button id="regenerate-workspace-hash" type="button" class="py-2.5 px-5 me-2 mb-2 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    <i class="fas fa-sync-alt mr-1"></i> Regenerar Workspace Hash
                </button>
                <a href="{{ route('workspace.shared.interface.api', ['global_hash_api' => auth()->user()->global_hash_api, 'workspace_hash_api' => $workspace->workspace_hash_api]) }}" target="_blank" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    <i class="fas fa-external-link-alt mr-1"></i> Abrir Link
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Variáveis globais para controle de limite
        window.canAddMoreFields = @json($canAddMoreFields);
        window.fieldsLimit = @json($fieldsLimit);
        window.currentFieldsCount = @json($currentFieldsCount);
        window.remainingFields = @json($remainingFields);
    </script>
    
    <script type="module">
        import { createTopic, deleteTopic } from '/js/modules/topic/topic-ajax.js'
        import { createField, updateField, deleteField } from '/js/modules/field/field-ajax.js'
        import '/js/modules/topic/topic-interations.js'
        import { updateSaveIndicator } from '/js/modules/field/field-interations.js'

        const workspace_id = {{ $workspace->id }}

        // URLs para as operações CRUD
        const routes = {
            create_field: "{{ route('field.store') }}",
            update_field: "{{ route('field.update', ['id' => ':id']) }}",
            delete_field: "{{ route('field.destroy', ['id' => ':id']) }}",

            create_topic: "{{ route('topic.store') }}",
            update_topic: "{{ route('topic.update', ['id' => ':id']) }}",
            delete_topic: "{{ route('topic.destroy', ['id' => ':id']) }}"
        };

        // Adicionar novo tópico
        $(document).on('click', '#addTopicBtn', function() {
            const topicName = prompt('Digite o nome do novo tópico:');
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

            console.log('Salvando linha com ID:', field_id);
            
            updateSaveIndicator(true, false);
            
            if (field_id && field_id !== '') {
                // Campo existente - atualizar
                updateField(
                    row,
                    topic_id,
                    routes.update_field.replace(':id', field_id)
                );
            } else {
                // Novo campo - criar
                createField(
                    row,
                    topic_id,
                    workspace_id,
                    routes.create_field
                );
            }
        });  
        
        // Evento para remover linha (usando delegation para linhas dinâmicas)
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
            
            if (confirm(`Tem certeza que deseja excluir o tópico "${topicTitle}"? Todos os campos serão removidos.`)) {
                deleteTopic(routes.delete_topic.replace(':id', topic_id));
            }
        });
    </script>
@endpush