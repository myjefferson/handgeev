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
                        {{ $workspace->topics->count() }} tópicos • {{ $workspace->totalFields() }} campos
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
                <!-- Botão Renomear -->
                <button data-modal-target="modal-rename-workspace-{{ $workspace->id }}" 
                        data-modal-toggle="modal-rename-workspace-{{ $workspace->id }}"
                        class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md">
                    <i class="fas fa-edit"></i>
                </button>
                
                <!-- Botão Configurar -->
                <a href="{{ route('workspace.setting', ['id' => $workspace->id]) }}"
                   class="p-1.5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md">
                    <i class="fas fa-cog"></i>
                </a>
                
                <!-- Botão Deletar (apenas para dono) -->
                @if($isOwner)
                    <button data-modal-target="modal-delete-workspace-{{ $workspace->id }}"
                            data-modal-toggle="modal-delete-workspace-{{ $workspace->id }}"
                            class="p-1.5 text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-md">
                        <i class="fas fa-trash"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modais para cada workspace -->
@if($isOwner)
    {{-- @include('components.modals.modal-rename-workspace', ['workspace' => $workspace]) --}}
    {{-- @include('components.modals.modal-delete-workspace', ['workspace' => $workspace]) --}}
@endif