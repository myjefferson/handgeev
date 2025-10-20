@if($workspaces->count() > 0)
    <div class="bg-slate-800/50 rounded-xl border border-slate-700 overflow-hidden">
        <!-- Cabeçalho da Lista -->
        <div class="grid grid-cols-12 gap-4 px-6 py-3 border-b border-slate-700 text-sm font-medium text-slate-400">
            <div class="col-span-7">Workspace</div>
            {{-- <div class="col-span-2 text-center">Tópicos</div> --}}
            <div class="col-span-2 text-center">Status</div>
            <div class="col-span-2 text-center">Atualizado</div>
            <div class="col-span-1 text-center">Ações</div>
        </div>

        <!-- Itens da Lista -->
        <div class="divide-y divide-slate-700">
            @foreach($workspaces as $workspace)
                @php
                    $collaboration = $collaborations[$loop->index] ?? null;
                @endphp
                <div class="workspace-item workspace-list-item grid grid-cols-12 gap-4 px-6 py-4 border-b border-slate-700/50 last:border-b-0"
                     data-topics="{{ $workspace->topics_count }}"
                     data-created="{{ $workspace->created_at->toISOString() }}"
                     data-updated="{{ $workspace->updated_at->toISOString() }}">
                    
                    <!-- Nome e Descrição -->
                    <div class="col-span-7">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-slate-700 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('workspace.show', $workspace->id) }}" class="workspace-title text-sm font-semibold text-white truncate hover:text-teal-500">
                                    {{ $workspace->title }}
                                </a>
                                <p class="workspace-description text-sm text-slate-400 mt-1 truncate">
                                    {{ $workspace->description ?: 'Sem descrição' }}
                                </p>
                                @if(isset($type) && $type === 'collaborator')
                                    <div class="flex items-center mt-1 text-xs text-slate-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ $workspace->user->name }}
                                        @if($collaboration)
                                            <span class="ml-2 px-1.5 py-0.5 bg-slate-600 rounded text-slate-300 text-xs">
                                                {{ ucfirst($collaboration->role) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tópicos -->
                    {{-- <div class="col-span-2 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-lg font-bold text-white">{{ $workspace->topics_count ?? 0 }}</div>
                            <div class="text-xs text-slate-400">tópicos</div>
                        </div>
                    </div> --}}

                    <!-- Status -->
                    <div class="col-span-2 flex items-center justify-center">
                        <div class="flex flex-wrap gap-1 justify-center">
                            @if($workspace->api_enabled)
                                <span class="status-badge-active px-2 py-1 bg-green-500/20 text-green-400 text-xs rounded-full flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    API
                                </span>
                            @else
                                <span class="px-2 py-1 bg-slate-500/20 text-slate-400 text-xs rounded-full">
                                    Inativo
                                </span>
                            @endif

                            @if($workspace->is_published)
                                <span class="status-badge-public px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded-full">
                                    Público
                                </span>
                            @else
                                <span class="px-2 py-1 bg-amber-500/20 text-amber-400 text-xs rounded-full">
                                    Privado
                                </span>
                            @endif

                            @if($workspace->api_jwt_required)
                                <span class="status-badge-jwt px-2 py-1 bg-purple-500/20 text-purple-400 text-xs rounded-full">
                                    JWT
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Data de Atualização -->
                    <div class="col-span-2 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-sm text-white">{{ $workspace->updated_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-slate-400">{{ $workspace->updated_at->format('H:i') }}</div>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="col-span-1 flex items-center justify-center">
                        <div class="relative">
                            <button onclick="toggleWorkspaceMenu('{{ $workspace->id }}-list-{{ $type ?? 'owner' }}')" 
                                    class="p-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="menu-{{ $workspace->id }}-list-{{ $type ?? 'owner' }}" 
                                 class="hidden absolute right-0 top-10 bg-slate-700 border border-slate-600 rounded-lg shadow-lg z-10 min-w-48">
                                <a href="{{ route('workspace.show', $workspace->id) }}" 
                                   class="flex items-center px-4 py-2 text-sm text-slate-300 hover:bg-slate-600 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Visualizar
                                </a>
                                
                                @if(!isset($type) || $type === 'owner')
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
                </div>
            @endforeach
        </div>
    </div>
@else
    @include('components.state.my-workspace-empty-state', [
        'type' => isset($type) && $type === 'collaborator' ? 'collaborations' : 'my-workspaces',
        'icon' => isset($type) && $type === 'collaborator' ? '👥' : '📁',
        'title' => isset($type) && $type === 'collaborator' ? 'Nenhuma colaboração' : 'Nenhum workspace encontrado',
        'description' => isset($type) && $type === 'collaborator' 
            ? 'Você ainda não está colaborando em nenhum workspace.' 
            : 'Comece criando seu primeiro workspace para organizar seus dados.',
        'showButton' => !isset($type) || $type === 'owner'
    ])
@endif