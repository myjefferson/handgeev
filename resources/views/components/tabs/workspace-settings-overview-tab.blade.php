<div id="tab-overview" class="hidden tab-content">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Coluna principal -->
        <div class="lg:col-span-2 space-y-8">
            <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">{{ __('workspace_settings_overview.workspace_statistics') }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Total de Tópicos -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ __('workspace_settings_overview.total_topics') }}</p>
                                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-1">{{ $workspace->topics->count() }}</p>
                            </div>
                            <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg">
                                <i class="fas fa-folder text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-blue-600 dark:text-blue-400">
                            @if($workspace->topics->count() > 0)
                                <i class="fas fa-check-circle mr-1"></i>{{ __('workspace_settings_overview.configured') }}
                            @else
                                <i class="fas fa-exclamation-triangle mr-1"></i>{{ __('workspace_settings_overview.no_topics') }}
                            @endif
                        </div>
                    </div>

                    <!-- Total de Campos -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-4 border border-green-200 dark:border-green-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('workspace_settings_overview.total_fields') }}</p>
                                <p class="text-2xl font-bold text-green-700 dark:text-green-300 mt-1">{{ $workspace->getFieldsCountAttribute() }}</p>
                            </div>
                            <div class="p-2 bg-green-100 dark:bg-green-800 rounded-lg">
                                <i class="fas fa-key text-green-600 dark:text-green-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-green-600 dark:text-green-400">
                            @php
                                $visibleFields = $workspace->topics->sum(function($topic) {
                                    return $topic->fields->where('is_visible', true)->count();
                                });
                            @endphp
                            <i class="fas fa-eye mr-1"></i>{{ $visibleFields }} {{ __('workspace_settings_overview.visible_fields') }}
                        </div>
                    </div>

                    <!-- Status da API -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-lg p-4 border border-purple-200 dark:border-purple-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-purple-600 dark:text-purple-400">{{ __('workspace_settings_overview.api_status') }}</p>
                                <p class="text-lg font-bold @if($workspace->api_enabled) text-purple-700 dark:text-purple-300 @else text-gray-500 dark:text-gray-400 @endif mt-1">
                                    @if($workspace->api_enabled)
                                        {{ __('workspace_settings_overview.active') }}
                                    @else
                                        {{ __('workspace_settings_overview.inactive') }}
                                    @endif
                                </p>
                            </div>
                            <div class="p-2 bg-purple-100 dark:bg-purple-800 rounded-lg">
                                <i class="fas fa-code text-purple-600 dark:text-purple-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-3 text-xs @if($workspace->api_enabled) text-purple-600 dark:text-purple-400 @else text-gray-500 dark:text-gray-400 @endif">
                            @if($workspace->api_enabled)
                                <i class="fas fa-check-circle mr-1"></i>{{ __('workspace_settings_overview.ready_to_use') }}
                            @else
                                <i class="fas fa-pause-circle mr-1"></i>{{ __('workspace_settings_overview.disabled') }}
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Distribuição de Campos por Tópico -->
                <div class="mt-8">
                    <h3 class="text-md font-medium text-gray-900 dark:text-white mb-4">{{ __('workspace_settings_overview.field_distribution') }}</h3>
                    <div class="space-y-3">
                        @forelse($workspace->topics as $topic)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $topic->title }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $topic->fields->count() }} {{ __('workspace_settings_overview.fields_count') }}</span>
                                    <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-1 rounded">
                                        {{ number_format(($topic->fields->count() / max(1, $workspace->getFieldsCountAttribute())) * 100, 1) }}%
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-2xl mb-2"></i>
                                <p>{{ __('workspace_settings_overview.no_topics_created') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Card de Informações Básicas -->
            <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('workspace_settings_overview.workspace_information') }}</h2>
                    <div class="flex space-x-2">
                        <button type="button" id="cancel-edit" class="hidden px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            {{ __('workspace_settings_overview.cancel') }}
                        </button>
                        <button type="submit" id="button-save-info-form" form="workspace-info-form" class="text-teal-600 bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 dark:text-teal-400 dark:hover:bg-teal-900/50 px-4 py-2 rounded-lg text-sm">
                            <i class="fas fa-save mr-2"></i> {{ __('workspace_settings_overview.save') }}
                        </button>
                    </div>
                </div>
                
                <!-- Formulário de Edição -->
                <form id="workspace-info-form" action="{{ route('workspace.update', $workspace->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nome do Workspace -->
                        <div>
                            <label for="workspace-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('workspace_settings_overview.workspace_name') }} *
                            </label>
                            <input type="text" id="workspace-title" name="title" value="{{ $workspace->title }}" 
                                required
                                maxlength="100"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors"
                                placeholder="{{ __('workspace_settings_overview.workspace_name_placeholder') }}">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('workspace_settings_overview.max_characters', ['count' => 100]) }}
                            </p>
                        </div>
                        
                        <!-- Tipo do Workspace -->
                        <div>
                            <label for="workspace-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('workspace_settings_overview.workspace_type') }} *
                            </label>
                            <select id="workspace-type" name="type_workspace_id" 
                                    required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors">
                                <option value="1" @if($workspace->type_workspace_id == 1) selected @endif>
                                    {{ __('workspace_settings_overview.single_topic') }}
                                </option>
                                <option value="2" @if($workspace->type_workspace_id == 2) selected @endif>
                                    {{ __('workspace_settings_overview.multiple_topics') }}
                                </option>
                            </select>
                            <div class="mt-2 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-teal-500"></i>
                                    <span><strong>{{ __('workspace_settings_overview.single_topic') }}:</strong> {{ __('workspace_settings_overview.single_topic_description') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-teal-500"></i>
                                    <span><strong>{{ __('workspace_settings_overview.multiple_topics') }}:</strong> {{ __('workspace_settings_overview.multiple_topics_description') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descrição -->
                    <div>
                        <label for="workspace-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('workspace_settings_overview.description') }}
                        </label>
                        <textarea id="workspace-description" name="description" rows="3"
                                maxlength="500"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 transition-colors resize-none"
                                placeholder="{{ __('workspace_settings_overview.description_placeholder') }}">{{ $workspace->description }}</textarea>
                        <div class="mt-1 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ __('workspace_settings_overview.max_characters', ['count' => 500]) }}</span>
                            <span id="description-counter">{{ strlen($workspace->description ?? '') }}/500</span>
                        </div>
                    </div>

                    <!-- Informações de Criação (somente leitura) -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('workspace_settings_overview.creation_date') }}</label>
                                <p class="mt-1 text-gray-900 dark:text-white">
                                    {{ $workspace->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('workspace_settings_overview.last_update') }}</label>
                                <p class="mt-1 text-gray-900 dark:text-white">
                                    {{ $workspace->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Alertas de Validação -->
                    <div id="form-alerts" class="hidden space-y-2"></div>
                </form>

                <!-- Preview de Mudanças (opcional) -->
                <div id="changes-preview" class="hidden mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">Prévia das alterações:</h4>
                    <div class="text-sm text-blue-700 dark:text-blue-400 space-y-1">
                        <div id="preview-title"></div>
                        <div id="preview-type"></div>
                        <div id="preview-description"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coluna lateral -->
        <div class="space-y-8">
            <!-- Card de Ações Rápidas -->
            @include('components.cards.quick-actions-worskpace-card', $workspace)

            <!-- Card de Status do Workspace -->
            <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('workspace_settings_overview.status') }}</h2>
                
                <div class="space-y-4">
                    <!-- Status de Publicação -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 rounded-lg @if($workspace->is_published) bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 @else bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 @endif">
                                <i class="fas @if($workspace->is_published) fa-globe @else fa-lock @endif"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if($workspace->is_published)
                                        {{ __('workspace_settings_overview.public') }}
                                    @else
                                        {{ __('workspace_settings_overview.private') }}
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    @if($workspace->is_published)
                                        {{ __('workspace_settings_overview.public_description') }}
                                    @else
                                        {{ __('workspace_settings_overview.private_description') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status da API -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 rounded-lg @if($workspace->api_enabled) bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 @endif">
                                <i class="fas fa-code"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if($workspace->api_enabled)
                                        {{ __('workspace_settings_overview.api_active') }}
                                    @else
                                        {{ __('workspace_settings_overview.api_inactive') }}
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    @if($workspace->api_enabled)
                                        {{ __('workspace_settings_overview.api_accessible') }}
                                    @else
                                        {{ __('workspace_settings_overview.api_disabled') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('workspace.api.access.toggle', $workspace) }}" method="POST" class="flex items-center">
                            @csrf @method('PUT')
                            <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 
                                @if($workspace->api_enabled) bg-blue-500 @else bg-gray-300 dark:bg-gray-600 @endif transition-colors">
                                <span class="inline-block w-4 h-4 transform bg-white rounded-full transition 
                                    @if($workspace->api_enabled) translate-x-6 @else translate-x-1 @endif" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Card de Limites do Plano -->
            <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('workspace_settings_overview.plan_limits') }}</h2>
                
                @php
                    $user = auth()->user();
                    $plan = $user->getPlan();
                    $currentTopics = $workspace->topics->count();
                    $currentFields = $workspace->getFieldsCountAttribute();
                @endphp
                
                <div class="space-y-4">
                    <!-- Limite de Tópicos -->
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('workspace_settings_overview.topics') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $currentTopics }} 
                                @if($plan->max_topics > 0)
                                    / {{ $plan->max_topics }}
                                @else
                                    / ∞
                                @endif
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" 
                                style="width: {{ $plan->max_topics > 0 ? min(100, ($currentTopics / $plan->max_topics) * 100) : 100 }}%"></div>
                        </div>
                    </div>

                    <!-- Limite de Campos -->
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('workspace_settings_overview.fields') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $currentFields }} 
                                @if($plan->max_fields > 0)
                                    / {{ $plan->max_fields }}
                                @else
                                    / ∞
                                @endif
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-500" 
                                style="width: {{ $plan->max_fields > 0 ? min(100, ($currentFields / $plan->max_fields) * 100) : 100 }}%"></div>
                        </div>
                    </div>

                    <!-- Plano Atual -->
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('workspace_settings_overview.current_plan') }}</span>
                            <span class="px-2 py-1 bg-teal-100 dark:bg-teal-900/30 text-teal-800 dark:text-teal-300 rounded text-xs font-medium">
                                {{ ucfirst($user->getRoleNames()->first()) }}
                            </span>
                        </div>
                    </div>

                    @if($user->isFree() && ($currentTopics >= $plan->max_topics || $currentFields >= $plan->max_fields))
                        <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
                            <p class="text-amber-800 dark:text-amber-300 text-xs">
                                {{ __('workspace_settings_overview.plan_limits_warning') }} 
                                <a href="{{ route('subscription.pricing') }}" class="underline hover:text-amber-600 dark:hover:text-amber-200">
                                    {{ __('workspace_settings_overview.upgrade_prompt') }}
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
    @include('components.modals.modal-merge-confirmation')
@endpush

<script type="module">
import '/js/modules/alert.js';

document.addEventListener('DOMContentLoaded', () => {
    // Cache blade-inserted values once
    const workspaceId = {{ $workspace->id }};
    const topicsCount = {{ $workspace->topics->count() }};
    const fieldsCount = {{ $workspace->getFieldsCountAttribute() }};
    const updateAction = '{{ route("workspace.update", $workspace->id) }}';
    const mergeAction = " {{route('workspace.merge-topics', $workspace->id)}} ";

    // Short helpers
    const $ = sel => document.querySelector(sel);
    const create = (tag, props = {}) => Object.assign(document.createElement(tag), props);
    const csrf = () => document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const isTruthy = v => v !== undefined && v !== null && v !== '' && v !== false;

    // DOM refs
    const form = $('#workspace-info-form');
    const workspaceTypeSelect = $('#workspace-type');
    const saveBtn = $('#button-save-info-form');
    const descEl = $('#workspace-description');
    const descCounter = $('#description-counter');
    const mergeModal = $('#mergeTopicsModal');
    const mergePreview = $('#mergePreview');

    let pendingWorkspaceUpdate = null;

    // Translations
    const translations = {
        merge_warning_text: "{{ __('workspace_settings_overview.merge_warning_text') }}",
        merge_description: "{{ __('workspace_settings_overview.merge_description') }}",
        current_topics: "{{ __('workspace_settings_overview.current_topics') }}",
        final_topic: "{{ __('workspace_settings_overview.final_topic') }}",
        fields_will_be_moved: "{{ __('workspace_settings_overview.fields_will_be_moved') }}",
        merge_warning: "{{ __('workspace_settings_overview.merge_warning') }}",
        changes_cancelled: "{{ __('workspace_settings_overview.changes_cancelled') }}",
        merging: "{{ __('workspace_settings_overview.merging') }}",
        topics_merged_success: "{{ __('workspace_settings_overview.topics_merged_success') }}",
        merge_error: "{{ __('workspace_settings_overview.merge_error') }}",
        saving: "{{ __('workspace_settings_overview.saving') }}",
        saved: "{{ __('workspace_settings_overview.saved') }}",
        connection_error: "{{ __('workspace_settings_overview.connection_error') }}",
        save: "{{ __('workspace_settings_overview.save') }}"
    };

    // Init
    if (form) form.addEventListener('submit', handleWorkspaceUpdate);
    if (workspaceTypeSelect) workspaceTypeSelect.addEventListener('change', handleWorkspaceTypeChange);
    if (mergeModal) initializeMergeModal();
    if (descEl) {
        descEl.addEventListener('input', updateDescriptionCounter);
        updateDescriptionCounter();
    }

    // Handlers
    function handleWorkspaceTypeChange(e) {
        const newType = e.target.value;
        if (newType == '1' && topicsCount > 1) showMergeWarning();
        else hideMergeWarning();
    }

    async function handleWorkspaceUpdate(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const newType = workspaceTypeSelect?.value;
        if (newType == '1' && topicsCount > 1) {
            showMergeConfirmationModal({
                workspace_id: workspaceId,
                topics_count: topicsCount,
                fields_count: fieldsCount,
                new_type: newType,
                formData
            });
            return;
        }
        await submitWorkspaceUpdate(formData, form.action || updateAction);
    }

    // UI utilities
    function showMergeWarning() {
        hideMergeWarning();
        const warning = create('div', { id: 'merge-warning', className: 'mt-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg' });
        warning.innerHTML = `<div class="flex items-center"><i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 mr-2"></i>
            <span class="text-amber-800 dark:text-amber-300 text-sm">${translations.merge_warning_text}</span></div>`;
        workspaceTypeSelect?.insertAdjacentElement('afterend', warning);
    }
    function hideMergeWarning() { $('#merge-warning')?.remove(); }

    function showModal(modal) {
        if (!modal) return;
        modal.classList.remove('hidden'); modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        if (!$('#modal-backdrop')) {
            const backdrop = create('div', { id: 'modal-backdrop', className: 'fixed inset-0 bg-gray-900 bg-opacity-50 z-40' });
            document.body.appendChild(backdrop);
        }
    }
    function hideModal(modal) {
        if (!modal) return;
        modal.classList.add('hidden'); modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        $('#modal-backdrop')?.remove();
    }

    // Merge modal
    function showMergeConfirmationModal(data) {
        if (!mergeModal || !mergePreview) return;
        mergePreview.innerHTML = `
            <div class="space-y-4">
                <p class="text-gray-700 dark:text-gray-300">${translations.merge_description.replace(':count', data.topics_count)}</p>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">${data.topics_count}</div>
                        <div class="text-blue-600 dark:text-blue-400">${translations.current_topics}</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">1</div>
                        <div class="text-green-600 dark:text-green-400">${translations.final_topic}</div>
                    </div>
                </div>
                <div class="text-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div class="text-lg font-semibold text-purple-600 dark:text-purple-400">${data.fields_count} ${translations.fields_will_be_moved}</div>
                </div>
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                    <p class="text-yellow-800 dark:text-yellow-300 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i> ${translations.merge_warning}</p>
                </div>
            </div>
        `;
        pendingWorkspaceUpdate = {
            workspaceId: data.workspace_id,
            newType: data.new_type,
            formData: data.formData,
            formAction: updateAction
        };
        showModal(mergeModal);
    }

    function initializeMergeModal() {
        const confirmBtn = $('#confirmMergeBtn');
        const cancelBtn = $('#cancelMergeBtn');
        const closeBtn = mergeModal.querySelector('[data-modal-hide="mergeTopicsModal"]');

        confirmBtn?.addEventListener('click', async () => {
            if (!pendingWorkspaceUpdate) return;
            try {
                await executeMerge(confirmBtn);
                await submitWorkspaceUpdate(pendingWorkspaceUpdate.formData, pendingWorkspaceUpdate.formAction);
                hideModal(mergeModal);
                pendingWorkspaceUpdate = null;
            } catch (err) {
                console.error('Erro no processo completo:', err);
            }
        });

        cancelBtn?.addEventListener('click', () => {
            hideModal(mergeModal);
            pendingWorkspaceUpdate = null;
            alertManager.show(translations.changes_cancelled, 'info');
        });

        closeBtn?.addEventListener('click', () => {
            hideModal(mergeModal);
            pendingWorkspaceUpdate = null;
        });

        mergeModal.addEventListener('click', e => { if (e.target === mergeModal) { hideModal(mergeModal); pendingWorkspaceUpdate = null; } });
    }

    // Network helpers
    async function fetchJson(url, opts = {}) {
        const headers = opts.headers || {};
        if (opts.csrf !== false) headers['X-CSRF-TOKEN'] = csrf();
        headers['X-Requested-With'] = 'XMLHttpRequest';
        headers['Accept'] = 'application/json';
        const res = await fetch(url, { ...opts, headers });
        return res.json();
    }

    // Merge execution
    async function executeMerge(confirmBtn) {
        if (!pendingWorkspaceUpdate) return;
        const original = confirmBtn?.innerHTML;
        try {
            setButtonLoading(confirmBtn, translations.merging);
            const data = await fetchJson(mergeAction, { method: 'POST' });
            if (data.success) {
                alertManager.show(translations.topics_merged_success, 'success');
                updateAfterMerge(data);
                return true;
            }
            throw new Error(data.error || translations.merge_error);
        } catch (err) {
            alertManager.show(err.message || translations.merge_error, 'error');
            throw err;
        } finally {
            restoreButton(confirmBtn, original);
        }
    }

    // Submit workspace update
    async function submitWorkspaceUpdate(formData, action) {
        try {
            formData.append('_ajax', 'true');
            setButtonLoading(saveBtn, translations.saving);
            const data = await fetchJson(action, { method: 'POST', body: formData, csrf: true });
            if (data.success) {
                setButtonState(saveBtn, `<i class="fas fa-check mr-1"></i> ${translations.saved}`, true);
                alertManager.show(data.message, 'success');
                if (data.workspace) updateAfterWorkspaceUpdate(data.workspace);
            } else {
                setButtonState(saveBtn, `<i class="fas fa-save mr-2"></i> ${translations.save}`, false);
                alertManager.show(data.error || translations.connection_error, 'error');
            }
        } catch (err) {
            setButtonState(saveBtn, `<i class="fas fa-save mr-2"></i> ${translations.save}`, false);
            alertManager.show(translations.connection_error, 'error');
        } finally {
            // restore after short delay
            setTimeout(() => restoreButton(saveBtn, `<i class="fas fa-save mr-2"></i> ${translations.save}`), 1500);
        }
    }

    // Button helpers
    function setButtonLoading(btn, text) {
        if (!btn) return;
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> ${text}`;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    }
    function restoreButton(btn, html) {
        if (!btn) return;
        btn.disabled = false;
        btn.innerHTML = html;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
    }
    function setButtonState(btn, html, disabled = false) {
        if (!btn) return;
        btn.disabled = !!disabled;
        btn.innerHTML = html;
    }

    // UI updates
    function updateAfterMerge(data) {
        updateStatisticsDisplay(data.data);
        updateVisualElements({ type_workspace_id: data.data.type_workspace_id, title: $('#workspace-title')?.value });
        hideMergeWarning();
    }

    function updateAfterWorkspaceUpdate(workspaceData) {
        if (isTruthy(workspaceData.title)) $('#workspace-title') && ($('#workspace-title').value = workspaceData.title);
        if (isTruthy(workspaceData.description)) descEl && (descEl.value = workspaceData.description);
        updateDescriptionCounter();
    }

    function updateDescriptionCounter() {
        if (!descEl || !descCounter) return;
        descCounter.textContent = `${descEl.value.length}/500`;
    }

    function updateStatisticsDisplay(data) { console.log('Dados atualizados:', data); }
    function updateVisualElements(data) { console.log('Atualizar elementos visuais:', data); }
});
</script>