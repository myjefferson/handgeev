<div class="workspace-card bg-slate-800 rounded-xl p-6 border border-slate-700 hover:border-cyan-500/50 transition-all duration-300 group">
    <!-- Header do Card -->
    <div class="flex justify-between items-start mb-4">
        <div class="flex-1">
            <a  href="{{ route('workspace.show', $workspace->id) }}"
                class="text-lg font-semibold text-white group-hover:text-cyan-300 transition-colors truncate">
                {{ $workspace->title }}
            </a>
            <p class="text-slate-400 text-sm mt-1 line-clamp-2">
                {{ $workspace->description ?: 'Sem descrição' }}
            </p>
            
            {{-- @if($type === 'collaborator')
                <div class="flex items-center mt-2 text-xs text-slate-500">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Proprietário: {{ $workspace->user->name }}
                    @if(isset($collaboration))
                        <span class="ml-2 px-2 py-1 bg-slate-600 rounded text-slate-300">
                            {{ ucfirst($collaboration->role) }}
                        </span>
                    @endif
                </div>
            @endif --}}
        </div>
        
        <div class="relative">
            <button onclick="toggleWorkspaceMenu('{{ $workspace->id }}-{{ $type }}')" 
                    class="p-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="menu-{{ $workspace->id }}-{{ $type }}" 
                 class="hidden absolute right-0 top-10 bg-slate-700 border border-slate-600 rounded-lg shadow-lg z-10 min-w-48">
                <a href="{{ route('workspace.show', $workspace->id) }}" 
                   class="flex items-center px-4 py-2 text-sm text-slate-300 hover:bg-slate-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Visualizar
                </a>
                
                @if($type === 'owner')
                    <a href="{{ route('workspace.setting', $workspace->id) }}" 
                       class="flex items-center px-4 py-2 text-sm text-slate-300 hover:bg-slate-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Configurações
                    </a>
                    
                    <div class="border-t border-slate-600"></div>
                            <button class="delete-btn w-full flex items-center justify-between p-3 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30"
                                data-id="{{ $workspace->id }}"
                                data-title="{{ $workspace->title }}"
                                data-route="{{ route('workspace.delete', ['id' => $workspace->id]) }}"
                            >
                                <span>Excluir Workspace</span>
                                <i class="fas fa-trash"></i>
                            </button>
                @else
                    <!-- Menu para colaborador -->
                    <button onclick="leaveWorkspace('{{ $workspace->id }}')" 
                            class="flex items-center w-full px-4 py-2 text-sm text-amber-400 hover:bg-slate-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Sair do Workspace
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats do Workspace -->
    <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="text-center">
            <div class="text-lg font-bold text-white">{{ $workspace->topics_count ?? 0 }}</div>
            <div class="text-xs text-slate-400">Tópicos</div>
        </div>
        <div class="text-center">
            <div class="text-lg font-bold text-white">{{ $workspace->fields_count ?? 0 }}</div>
            <div class="text-xs text-slate-400">Campos</div>
        </div>
        <div class="text-center">
            <div class="text-lg font-bold text-white">{{ $workspace->collaborators_count ?? 0 }}</div>
            <div class="text-xs text-slate-400">Colabs</div>
        </div>
    </div>

    <!-- Status Badges -->
    <div class="flex flex-wrap gap-2 mb-4">
        @if($workspace->api_enabled)
            <span class="px-2 py-1 bg-green-500/20 text-green-400 text-xs rounded-full flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                API Ativa
            </span>
        @else
            <span class="px-2 py-1 bg-slate-500/20 text-slate-400 text-xs rounded-full">
                API Inativa
            </span>
        @endif

        @if($workspace->is_published)
            <span class="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded-full">
                Público
            </span>
        @else
            <span class="px-2 py-1 bg-amber-500/20 text-amber-400 text-xs rounded-full">
                Privado
            </span>
        @endif

        @if($workspace->api_jwt_required)
            <span class="px-2 py-1 bg-purple-500/20 text-purple-400 text-xs rounded-full">
                JWT
            </span>
        @endif
    </div>

    <!-- Footer do Card -->
    <div class="flex justify-between items-center pt-4 border-t border-slate-700">
        <div class="text-xs text-slate-400">
            Atualizado {{ $workspace->updated_at->diffForHumans() }}
        </div>
        <a href="{{ route('workspace.show', $workspace->id) }}" 
           class="bg-slate-700 hover:bg-slate-600 text-white px-3 py-1 rounded-lg text-sm transition-colors flex items-center">
            Acessar
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>

@push('modals')
    @include('components.modals.modal-delete-workspace')
@endpush

<script>
    // Toggle dropdown menus para workspace cards
    function toggleWorkspaceMenu(menuId) {
        const menu = document.getElementById(`menu-${menuId}`);
        const allMenus = document.querySelectorAll('[id^="menu-"]');
        
        // Fechar todos os outros menus
        allMenus.forEach(m => {
            if (m.id !== `menu-${menuId}`) {
                m.classList.add('hidden');
            }
        });
        
        // Toggle menu atual
        menu.classList.toggle('hidden');
    }

    // Fechar menus ao clicar fora
    document.addEventListener('click', function(event) {
        if (!event.target.closest('[id^="menu-"]') && !event.target.closest('button[onclick*="toggleWorkspaceMenu"]')) {
            document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });

    // Funções específicas para colaborações
    function leaveWorkspace(workspaceId) {
        if (confirm('Tem certeza que deseja sair deste workspace?')) {
            // Implementar saída do workspace
            console.log('Sair do workspace:', workspaceId);
        }
    }
</script>