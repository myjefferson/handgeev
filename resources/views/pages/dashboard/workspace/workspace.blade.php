@extends('template.template-dashboard')

@section('title', $workspace->title)
@section('description', __('workspace.description', ['title' => $workspace->title]))

@section('content_dashboard')
    <div class="max-w-full sm:max-w-6xl md:max-w-7xl xl:max-w-7xl mx-auto">
        <a href="{{ route('workspaces.index') }}" class="block w-max text-sm text-gray-300 hover:text-teal-400 transition-colors mb-8">
            <i class="fas fa-arrow-left mr-1"></i> {{ __('workspace.navigation.back_to_workspaces') }}
        </a>
        <div class="flex bg-slate-900 rounded-xl min-h-dvh">
            <!-- Sidebar de Tópicos -->
            @if ($workspace->type_workspace_id !== 1)
                <div class="w-64 bg-slate-800 border-r border-slate-700 overflow-y-auto rounded-xl">
                    <div class="p-4">
                        <!-- Cabeçalho do Workspace -->
                        <div class="mb-6">
                            <h1 class="text-xl font-bold text-white truncate">{{ $workspace->title }}</h1>
                            @if(count($workspace->topics) > 1)
                                <p class="text-sm text-gray-400 mt-1">
                                    {{ trans_choice('workspace.sidebar.topics_count', count($workspace->topics), ['count' => count($workspace->topics)]) }}
                                </p>
                            @endif
                        </div>

                        <!-- Botão Adicionar Tópico -->
                        @if($workspace->type_workspace_id == 2)
                            <button id="addTopicBtn" class="w-full mb-6 px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-xl transition-colors duration-300 teal-glow-hover">
                                <i class="fas fa-plus mr-2"></i> {{ __('workspace.sidebar.new_topic') }}
                            </button>
                        @endif

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
                                        {{ $index === 0 ? 'bg-teal-400/20 text-teal-400 border border-teal-400/30' : 'text-gray-400 hover:text-teal-300 hover:bg-slate-750' }}">
                                        <div class="flex items-center truncate">
                                            <i class="fas fa-folder mr-3 text-sm"></i>
                                            <span class="truncate topic-title">{{ $topic->title }}</span>
                                        </div>
                                        <span class="text-xs bg-slate-700 px-2 py-1 rounded-full group-hover:bg-slate-600 text-teal-300">
                                            {{ __('workspace.sidebar.fields_count', ['count' => count($topic->fields)]) }}
                                        </span>
                                    </button>
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
                                                Campos neste tópico: {{ $topicLimits['currentFieldsCount'] }}/{{ $topicLimits['fieldsLimit'] }} 
                                                ({{ $topicLimits['remainingFields'] }} restantes)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="block mb-8">
                            @include('components.headers.header-workspace', [
                                'workspace' => $workspace,
                                'title' => __('workspace.title')
                            ])                     
                        </div>

                        <!-- Cabeçalho do tópico -->
                        @if (count($workspace->topics) > 1)
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-semibold text-white">{{ $topic->title }}</h3>
                                @if(count($workspace->topics) > 1)
                                    <button class="delete-topic-btn mr-4 p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors duration-200" data-topic-id="{{ $topic->id }}" title="{{ __('workspace.actions.delete') }}">
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
                                        <th scope="col" class="px-6 py-4 font-medium">{{ __('workspace.table.headers.visibility') }}</th>
                                        <th scope="col" class="px-6 py-4 font-medium">{{ __('workspace.table.headers.key') }}</th>
                                        <th scope="col" class="px-6 py-4 font-medium">{{ __('workspace.table.headers.value') }}</th>
                                        <th scope="col" class="px-6 py-4 font-medium">{{ __('workspace.table.headers.type') }}</th>
                                        <th scope="col" class="px-6 py-4 font-medium">{{ __('workspace.table.headers.actions') }}</th>
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
                                                    class="key-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors" 
                                                    placeholder="{{ __('workspace.fields.placeholders.key') }}">
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($field->type === 'boolean')
                                                    <select name="key_value" class="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                                                        <option value="true" {{ $field->value === 'true' ? 'selected' : '' }}>{{ __('workspace.fields.boolean_options.true') }}</option>
                                                        <option value="false" {{ $field->value === 'false' ? 'selected' : '' }}>{{ __('workspace.fields.boolean_options.false') }}</option>
                                                    </select>
                                                @else
                                                    <input type="{{ $field->type === 'number' ? 'number' : 'text' }}" 
                                                        name="key_value" 
                                                        value="{{ $field->value }}" 
                                                        class="value-input w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors" 
                                                        placeholder="{{ $field->type === 'number' ? __('workspace.fields.placeholders.number_value') : __('workspace.fields.placeholders.text_value') }}"
                                                        step="{{ $field->type === 'number' ? 'any' : '' }}">
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <select name="field_type" class="type-select w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                                                    <option value="text" {{ $field->type === 'text' ? 'selected' : '' }}>{{ __('workspace.fields.types.text') }}</option>
                                                    @if(!auth()->user()->isFree())
                                                        <option value="number" {{ $field->type === 'number' ? 'selected' : '' }}>{{ __('workspace.fields.types.number') }}</option>
                                                        <option value="boolean" {{ $field->type === 'boolean' ? 'selected' : '' }}>{{ __('workspace.fields.types.boolean') }}</option>
                                                    @else
                                                        <option value="number" disabled class="text-gray-500 bg-slate-600">{{ __('workspace.fields.types.locked.number') }}</option>
                                                        <option value="boolean" disabled class="text-gray-500 bg-slate-600">{{ __('workspace.fields.types.locked.boolean') }}</option>
                                                    @endif
                                                </select>
                                                @if(auth()->user()->isFree())
                                                    <p class="text-xs text-purple-400 mt-1">
                                                        <i class="{{ __('workspace.fields.upgrade_message.icon') }} mr-1"></i>
                                                        {!! __('workspace.fields.upgrade_message.text', [
                                                            'upgrade_link' => '<a href="'.route('subscription.pricing').'" class="underline hover:text-purple-300">'.__('workspace.fields.upgrade_message.link').'</a>'
                                                        ]) !!}
                                                    </p>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex space-x-2">
                                                    <button type="button" class="save-row p-2 text-teal-400 hover:text-teal-300 rounded-lg transition-colors duration-200" title="{{ __('workspace.actions.save') }}">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                    <button type="button" class="remove-row p-2 text-red-400 hover:text-red-300 rounded-lg transition-colors duration-200" title="{{ __('workspace.actions.remove') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                                <i class="{{ __('workspace.table.empty.icon') }} text-2xl mb-2"></i>
                                                <p>{{ __('workspace.table.empty.message') }}</p>
                                            </td>
                                        </tr>
                                    @endforelse

                                    <!-- Linha para adicionar novo campo -->
                                    @if($canAddMore || $topicLimits['isUnlimited'])
                                        <tr class="add-field-trigger bg-slate-750 cursor-pointer hover:bg-slate-700 transition-colors duration-200" data-topic-id="{{ $topic->id }}">
                                            <td colspan="5" class="px-6 py-4 text-center text-teal-400">
                                                <div class="flex items-center justify-center">
                                                    <i class="fas fa-plus-circle mr-2"></i>
                                                    {{ __('workspace.table.add_field.trigger') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        <tr class="limit-reached-row bg-slate-750">
                                            <td colspan="5" class="px-6 py-4 text-center text-purple-400">
                                                <div class="flex items-center justify-center">
                                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                                    Limite de {{ $topicLimits['fieldsLimit'] }} campos por tópico atingido.
                                                    <a href="{{ route('subscription.pricing') }}" class="underline ml-1 text-white">
                                                        Faça upgrade
                                                    </a>
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
    </div>
    <style>
        .teal-glow-hover:hover {
            box-shadow: 0 0 20px rgba(0, 230, 216, 0.3);
        }
    </style>
@endsection

@push('modals')
    @include('components.modals.modal-share-api-interface', ['workspace' => $workspace])
@endpush

@push('scripts')
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
                table: {
                    add_field: {
                        trigger: "{{ __('workspace.table.add_field.trigger') }}"
                    }
                },
                modals: {
                    new_topic: {
                        prompt: "{{ __('workspace.modals.new_topic.prompt') }}",
                        placeholder: "{{ __('workspace.modals.new_topic.placeholder') }}"
                    },
                    delete_topic: {
                        message: "{{ __('workspace.modals.delete_topic.message') }}"
                    }
                },
                notifications: {
                    saving: "{{ __('workspace.notifications.saving') }}",
                    saved: "{{ __('workspace.notifications.saved') }}",
                    deleting: "{{ __('workspace.notifications.deleting') }}"
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
@endpush