@extends('template.template-dashboard')

@section('title', 'Meus Workspaces')
@section('description', 'Workspaces de '.auth()->user()->name.' no HandGeev')

@push('style')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .view-toggle.active {
            background-color: #0891b2;
            color: white;
        }

        .workspace-item {
            transition: all 0.2s ease-in-out;
        }

        .workspace-item:hover {
            background-color: rgba(30, 41, 59, 0.5);
            border-color: #06b6d4;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Garantir que os elementos originais fiquem ocultos quando necessário */
        .hidden {
            display: none !important;
        }
    </style>
@endpush

@section('content_dashboard')
    <div class="min-h-screen">
        <!-- Header -->
        <div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-white">Meus Workspaces</h1>
                        <p class="text-slate-400 mt-1">Gerencie seus workspaces e colaborações</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if (auth()->user()->canCreateWorkspace())
                            <button id="modal-add-workspace-btn" data-modal-target="modal-add-workspace" data-modal-toggle="modal-add-workspace"
                                class="flex items-center px-4 py-2 text-white rounded-lg bg-teal-500 hover:bg-teal-700 transition-colors teal-glow-hover">
                                <i class="fas fa-plus mr-2"></i>
                                Novo Workspace
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
                                class="flex items-center px-4 py-2 text-white rounded-lg bg-slate-600 hover:bg-slate-700 transition-colors purple-glow-hover">
                                <i class="fas fa-upload mr-2"></i>
                                Importar
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
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-500/20 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">
                                Meus Workspaces 
                            </p>
                            <p class="text-2xl font-bold text-white">{{ $workspaces->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-500/20 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Total de Tópicos</p>
                            <p class="text-2xl font-bold text-white">{{ $workspaces->sum('topics_count') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-amber-500/20 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Workspaces Ativos</p>
                            <p class="text-2xl font-bold text-white">{{ $workspaces->where('api_enabled', true)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de Controles -->
            <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row gap-4 flex-1">
                        <!-- Barra de Pesquisa -->
                        <div class="relative flex-1 max-w-md">
                            <input type="text" id="search-input" 
                                class="w-full bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 pl-10 pr-4 py-2" 
                                placeholder="Buscar por título ou descrição...">
                            <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <!-- Filtros -->
                        <div class="flex gap-2">
                            <select id="filter-status" class="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 px-3 py-2">
                                <option value="all">Todos</option>
                                <option value="active">API Ativa</option>
                                <option value="inactive">API Inativa</option>
                                <option value="public">Públicos</option>
                                <option value="private">Privados</option>
                            </select>

                            <select id="filter-sort" class="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 px-3 py-2">
                                <option value="newest">Mais Recentes</option>
                                <option value="oldest">Mais Antigos</option>
                                <option value="name_asc">A-Z</option>
                                <option value="name_desc">Z-A</option>
                            </select>

                            <button onclick="window.workspaceManager.resetFilters()" 
                                    class="px-3 py-2 text-sm text-slate-400 hover:text-white border border-slate-600 rounded-lg hover:bg-slate-700 transition-colors">
                                Limpar
                            </button>
                        </div>

                        <!-- Botões de Visualização -->
                        <div class="flex border border-slate-600 rounded-lg overflow-hidden">
                            <button id="view-list" class="p-2 bg-cyan-600 text-white border-r border-slate-600 view-toggle">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                            </button>
                            <button id="view-grid" class="p-2 text-slate-400 hover:text-white hover:bg-slate-700 view-toggle">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conteúdo Principal -->
            <div id="workspaces-content">
                <!-- Estado Original (sempre presente, mas hidden quando vazio) -->
                <div id="original-content">
                    <!-- Visualização Lista -->
                    <div id="list-view" class="view-type">
                        @include('components.list.my-workspace-list', [
                            'workspaces' => $workspaces,
                            'type' => 'owner'
                        ])
                    </div>

                    <!-- Visualização Grade -->
                    <div id="grid-view" class="view-type hidden">
                        @if($workspaces->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($workspaces as $workspace)
                                    @include('components.cards.my-workspace-card', [
                                        'workspace' => $workspace,
                                        'type' => 'owner'
                                    ])
                                @endforeach
                            </div>
                        @else
                            <!-- Estado vazio original -->
                            <div class="empty-state-original">
                                @include('components.state.my-workspace-empty-state', [
                                    'type' => 'my-workspaces',
                                    'icon' => '📁',
                                    'title' => 'Nenhum workspace encontrado',
                                    'description' => 'Comece criando seu primeiro workspace para organizar seus dados.',
                                    'showButton' => true
                                ])
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Estado de Filtro Vazio (inicialmente hidden) -->
                <div id="empty-filter-state" class="hidden">
                    <div class="text-center py-12 bg-slate-800/50 rounded-xl border border-slate-700 fade-in">
                        <div class="text-slate-400 text-6xl mb-4">🔍</div>
                        <h3 class="text-lg font-semibold text-white mb-2">Nenhum workspace encontrado</h3>
                        <p class="text-slate-400 mb-6">Tente ajustar seus filtros de busca.</p>
                        <button onclick="window.workspaceManager.resetFilters()" 
                                class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('modals')
    @include('components.modals.modal-create-workspace')
@endpush

@push('scripts')
    <script>
        // Sistema corrigido de gerenciamento
        class SimpleWorkspaceManager {
            constructor() {
                this.currentView = 'list';
                this.searchTerm = '';
                this.filters = {
                    status: 'all',
                    sort: 'newest'
                };
                this.originalContent = null;
                this.init();
            }

            init() {
                // Salvar referência ao conteúdo original
                this.originalContent = document.getElementById('original-content').innerHTML;
                
                this.setupViewToggle();
                this.setupSearch();
                this.setupFilters();
                this.applyFilters();
            }

            setupViewToggle() {
                const listViewBtn = document.getElementById('view-list');
                const gridViewBtn = document.getElementById('view-grid');

                const switchView = (viewType) => {
                    // Atualizar botões
                    [listViewBtn, gridViewBtn].forEach(btn => {
                        btn.classList.toggle('active', btn === (viewType === 'list' ? listViewBtn : gridViewBtn));
                        btn.classList.toggle('bg-cyan-600', btn === (viewType === 'list' ? listViewBtn : gridViewBtn));
                        btn.classList.toggle('text-white', btn === (viewType === 'list' ? listViewBtn : gridViewBtn));
                        btn.classList.toggle('text-slate-400', btn !== (viewType === 'list' ? listViewBtn : gridViewBtn));
                    });

                    // Atualizar visualizações
                    document.getElementById('list-view').classList.toggle('hidden', viewType !== 'list');
                    document.getElementById('grid-view').classList.toggle('hidden', viewType !== 'grid');

                    this.currentView = viewType;
                    this.applyFilters(); // Reaplicar filtros ao mudar visualização
                };

                listViewBtn.addEventListener('click', () => switchView('list'));
                gridViewBtn.addEventListener('click', () => switchView('grid'));

                // Inicializar com lista ativa
                switchView('list');
            }

            setupSearch() {
                const searchInput = document.getElementById('search-input');
                let searchTimeout;

                searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    this.searchTerm = e.target.value.toLowerCase().trim();
                    
                    searchTimeout = setTimeout(() => {
                        this.applyFilters();
                    }, 300);
                });
            }

            setupFilters() {
                document.getElementById('filter-status').addEventListener('change', (e) => {
                    this.filters.status = e.target.value;
                    this.applyFilters();
                });

                document.getElementById('filter-sort').addEventListener('change', (e) => {
                    this.filters.sort = e.target.value;
                    this.applyFilters();
                });
            }

            applyFilters() {
                const items = this.getCurrentViewItems();
                const emptyFilterState = document.getElementById('empty-filter-state');
                const originalContent = document.getElementById('original-content');
                
                if (items.length === 0) {
                    // Se não há itens para filtrar, mostrar estado vazio original
                    emptyFilterState.classList.add('hidden');
                    originalContent.classList.remove('hidden');
                    return;
                }

                let visibleItems = [];
                let hasVisibleItems = false;

                items.forEach(item => {
                    const title = this.getItemTitle(item).toLowerCase();
                    const description = this.getItemDescription(item).toLowerCase();
                    const isActive = this.hasStatus(item, 'active');
                    const isPublic = this.hasStatus(item, 'public');

                    // Aplicar filtro de busca
                    const searchMatch = !this.searchTerm || 
                                        title.includes(this.searchTerm) || 
                                        description.includes(this.searchTerm);

                    // Aplicar filtro de status
                    let statusMatch = true;
                    switch (this.filters.status) {
                        case 'active': statusMatch = isActive; break;
                        case 'inactive': statusMatch = !isActive; break;
                        case 'public': statusMatch = isPublic; break;
                        case 'private': statusMatch = !isPublic; break;
                    }

                    // Mostrar/ocultar item
                    if (searchMatch && statusMatch) {
                        item.style.display = '';
                        visibleItems.push({
                            element: item,
                            title: title,
                            date: this.getItemDate(item)
                        });
                        hasVisibleItems = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Mostrar estado apropriado
                if (!hasVisibleItems) {
                    originalContent.classList.add('hidden');
                    emptyFilterState.classList.remove('hidden');
                } else {
                    originalContent.classList.remove('hidden');
                    emptyFilterState.classList.add('hidden');
                    this.sortItems(visibleItems);
                }
            }

            getCurrentViewItems() {
                const container = document.getElementById(`${this.currentView}-view`);
                if (!container) return [];

                if (this.currentView === 'list') {
                    return Array.from(container.querySelectorAll('.workspace-item')) || [];
                } else {
                    return Array.from(container.querySelectorAll('.workspace-card')) || [];
                }
            }

            getItemTitle(item) {
                if (!item) return '';
                
                if (this.currentView === 'list') {
                    return item.querySelector('.workspace-title')?.textContent || '';
                } else {
                    return item.querySelector('h3')?.textContent || '';
                }
            }

            getItemDescription(item) {
                if (!item) return '';
                
                if (this.currentView === 'list') {
                    return item.querySelector('.workspace-description')?.textContent || '';
                } else {
                    return item.querySelector('.text-slate-400.text-sm')?.textContent || '';
                }
            }

            hasStatus(item, status) {
                if (!item) return false;
                
                const statusMap = {
                    'active': '.bg-green-500\\/20',
                    'public': '.bg-blue-500\\/20'
                };
                return item.querySelector(statusMap[status]) !== null;
            }

            getItemDate(item) {
                if (!item) return new Date();
                
                // Usar data de atualização ou criação como fallback
                return new Date(item.dataset.updated || item.dataset.created || new Date());
            }

            sortItems(visibleItems) {
                if (visibleItems.length === 0) return;

                visibleItems.sort((a, b) => {
                    switch (this.filters.sort) {
                        case 'newest': return b.date - a.date;
                        case 'oldest': return a.date - b.date;
                        case 'name_asc': return a.title.localeCompare(b.title);
                        case 'name_desc': return b.title.localeCompare(a.title);
                        default: return b.date - a.date;
                    }
                });

                // Reordenar no DOM
                const container = document.getElementById(`${this.currentView}-view`);
                if (!container) return;

                if (this.currentView === 'grid') {
                    const gridContainer = container.querySelector('.grid');
                    if (gridContainer) {
                        visibleItems.forEach(({ element }) => {
                            gridContainer.appendChild(element);
                        });
                    }
                } else {
                    visibleItems.forEach(({ element }) => {
                        container.appendChild(element);
                    });
                }
            }

            resetFilters() {
                this.searchTerm = '';
                this.filters.status = 'all';
                this.filters.sort = 'newest';
                
                document.getElementById('search-input').value = '';
                document.getElementById('filter-status').value = 'all';
                document.getElementById('filter-sort').value = 'newest';
                
                // Garantir que o conteúdo original seja mostrado
                document.getElementById('empty-filter-state').classList.add('hidden');
                document.getElementById('original-content').classList.remove('hidden');
                
                // Mostrar todos os itens
                const items = this.getCurrentViewItems();
                items.forEach(item => {
                    if (item) item.style.display = '';
                });
                
                // Reaplicar ordenação padrão
                this.applyFilters();
            }
        }

        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            window.workspaceManager = new SimpleWorkspaceManager();
        });

        // Funções globais para dropdown menus
        function toggleWorkspaceMenu(menuId) {
            const menu = document.getElementById(`menu-${menuId}`);
            if (!menu) return;
            
            const isHidden = menu.classList.toggle('hidden');
            
            // Fechar outros menus
            if (!isHidden) {
                document.querySelectorAll('[id^="menu-"]').forEach(otherMenu => {
                    if (otherMenu.id !== `menu-${menuId}` && otherMenu.classList) {
                        otherMenu.classList.add('hidden');
                    }
                });
            }
        }

        // Fechar menus ao clicar fora
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[id^="menu-"]') && !event.target.closest('button[onclick*="toggleWorkspaceMenu"]')) {
                document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                    if (menu.classList) {
                        menu.classList.add('hidden');
                    }
                });
            }
        });
        </script>
@endpush