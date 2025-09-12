@extends('template.dashboard')

@section('content_dashboard')
    <div class="w-full p-4">
        <!-- Indicador de limite de campos -->
        @if(!$canAddMoreFields && $fieldsLimit > 0)
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg dark:bg-yellow-900/20 dark:border-yellow-700">
                <p class="text-sm text-yellow-800 dark:text-yellow-300">
                    ⚠️ Limite de campos atingido ({{ $currentFieldsCount }}/{{ $fieldsLimit }}). 
                    <a href="{{ route('landing.plans') }}" class="underline font-medium">Faça upgrade</a> 
                    para adicionar mais campos.
                </p>
            </div>
            @elseif($fieldsLimit > 0)
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-700">
                <p class="text-sm text-blue-800 dark:text-blue-300">
                    📊 Campos utilizados: {{ $currentFieldsCount }}/{{ $fieldsLimit }} 
                    ({{ $remainingFields }} restantes)
                </p>
            </div>
        @endif

        <div class="block mb-8">
            @include('components.header' ,[
                'title' => $workspace->title,
                'options' => null,
                'buttonViewJson' => [
                    'active' => true,
                    'api_route' => route('api.workspace', ['id' => $workspace->id])
                ]
            ])
            
            <!-- Indicador de AutoSave -->
            <div id="autoSaveIndicator" class="flex items-center mt-4 text-sm text-gray-500 dark:text-gray-400">
                <svg id="savingIcon" class="w-4 h-4 mr-2 hidden animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 极速赛车开奖直播12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg id="savedIcon" class="w-4 h-4 mr-2 text-green-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span id="saveStatusText">Todas as alterações salvas</span>
            </div>
        </div>

        <!-- Botão para adicionar tópico (apenas se for workspace de múltiplos tópicos) -->
        @if($workspace->type_workspace_id == 2)
        <div class="mb-4">
            <button id="addTopicBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Adicionar Tópico
            </button>
        </div>
        @endif

        <!-- Tabs de navegação entre tópicos (apenas se houver mais de um tópico) -->
        @if (count($workspace->topics) > 1)
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="topicTabs">
                    @foreach($workspace->topics as $index => $topic)
                    <li class="me-2">
                        <button class="topic-tab inline-block p-4 border-b-2 rounded-t-lg {{ $index === 0 ? 'border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}"
                                data-topic-id="{{ $topic->id }}">
                            {{ $topic->title }}
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Conteúdo dos tópicos -->
        @foreach($workspace->topics as $index => $topic)
        <div class="topic-content mb-6 {{ $index === 0 ? '' : 'hidden' }}" data-topic-id="{{ $topic->id }}">
            <!-- Cabeçalho do tópico -->
            @if (count($workspace->topics) > 1)
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $topic->title }}</h3>
                    @if(count($workspace->topics) > 1)
                    <button class="delete-topic-btn text-red-600 hover:text-red-800" data-topic-id="{{ $topic->id }}">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                    @endif
                </div>
            @endif

            <!-- Tabela de fields do tópico -->
            <div class="relative overflow-x-auto">
                <table class="key-value-table w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Visibility</th>
                            <th scope="col" class="px-6 py-3">Key</th>
                            <th scope="col" class="px-6 py-3">Value</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topic->fields as $field)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200" 
                                data-id="{{ $field->id }}" data-topic-id="{{ $topic->id }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="visibility-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" 
                                            {{ $field->is_visible ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="key_name" value="{{ $field->key_name }}" class="key-input w-full px-2 py-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="key_value" value="{{ $field->value }}" class="value-input w-full px-2 py-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-6 py-4 flex space-x-2">
                                    <button type="button" class="save-row text-green-600 hover:text-green-800 dark:hover:text-green-400" title="Salvar">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    <button type="button" class="remove-row text-red-600 hover:text-red-800 dark:hover:text-red-400" title="Remover">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Nenhum campo cadastrado neste tópico
                                </td>
                            </tr>
                        @endforelse

                        <!-- Linha para adicionar novo campo -->
                        @if($canAddMoreFields)
                            <tr class="add-field-trigger bg-gray-100 dark:bg-gray-900 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-800" data-topic-id="{{ $topic->id }}">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Clique para adicionar novo campo
                                    </div>
                                </td>
                            </tr>
                            @else
                            <tr class="limit-reached-row bg-gray-100 dark:bg-gray-900">
                                <td colspan="4" class="px-6 py-4 text-center text-yellow-600 dark:text-yellow-400">
                                    <div class="flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        Limite de campos atingido. 
                                        <a href="{{ route('landing.plans') }}" class="underline ml-1">Faça upgrade</a>
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
@endsection

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
        $('.delete-topic-btn').on('click', function() {
            const topic_id = $(this).data('topic-id');
            const topicTitle = $(this).closest('.topic-content').find('h3').text();
            
            if (confirm(`Tem certeza que deseja excluir o tópico "${topicTitle}"? Todos os campos serão removidos.`)) {
                deleteTopic(routes.delete_topic.replace(':id', topic_id));
            }
        });

    </script>
@endpush