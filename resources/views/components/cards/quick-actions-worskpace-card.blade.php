<div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Ações Rápidas</h2>
    
    <div class="space-y-3">
        <a href="{{ route('workspace.show', $workspace->id) }}" 
        class="w-full flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
            <span>Ver Workspace</span>
            <i class="fas fa-external-link-alt"></i>
        </a>
        <a href="
            {{ $workspace->type_view_workspace_id === 1 ? 
            route('workspace.shared-geev-studio.show', ['global_key_api' => auth()->user()->global_key_api, 'workspace_key_api' => $workspace->workspace_key_api]) : 
            route('workspace.api-rest.show', ['global_key_api' => auth()->user()->global_key_api, 'workspace_key_api' => $workspace->workspace_key_api]) }}" 
            class="w-full flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
            <span>Gerenciar API</span>
            <i class="fas fa-code"></i>
        </a>

        @if (auth()->user()->isStart() || auth()->user()->isPro() || auth()->user()->isPremium() || auth()->user()->isAdmin()) 
            <button type="button" 
                    data-modal-target="duplicateModal" 
                    data-modal-toggle="duplicateModal"
                    data-workspace-id="{{ $workspace->id }}"
                    data-workspace-title="{{ $workspace->title }}"
                    data-topics-count="{{ $workspace->topics_count }}"
                    data-fields-count="{{ $workspace->totalFields() }}" 
                    class="w-full flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                <span>Duplicar Workspace</span>
                <i class="fas fa-copy"></i>
            </button>
        @else
            <a href="{{ route('subscription.pricing') }}"  
                    class="w-full flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                <span>Duplicar Workspace @include("components.badges.upgrade-badge")</span>
                <i class="fas fa-copy"></i>
            </a>
        @endif
        <button class="delete-btn w-full flex items-center justify-between p-3 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30"
            data-id="{{ $workspace->id }}"
            data-title="{{ $workspace->title }}"
            data-route="{{ route('workspace.delete', ['id' => $workspace->id]) }}"
        >
            <span>Excluir Workspace</span>
            <i class="fas fa-trash"></i>
        </button>
    </div>
</div>