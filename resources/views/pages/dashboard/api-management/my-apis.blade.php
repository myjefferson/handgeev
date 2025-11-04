@extends('template.template-dashboard')

@section('title', 'Minhas APIs - HandGeev')
@section('description', 'Gerencie e visualize suas APIs no HandGeev')

@push('styles')
    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .api-card {
        transition: all 0.3s ease;
    }

    .api-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }

    /* Garantir que os ícones sejam exibidos corretamente */
    .fas, .fa {
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
    }
    </style>
@endpush

@push('scripts_start')
    <script type="module" src="{{ asset('js/modules/api-management/api-management-ajax.js') }}"></script>
@endpush

@section('content_dashboard')
    <div class="min-h-screen max-w-7xl mx-auto">
        <!-- Header -->
        <div class="p-0 sm:p-0 md:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 py-6">
                <div class="w-full sm:w-auto">
                    <h1 class="text-xl sm:text-2xl font-bold text-white">Minhas APIs</h1>
                    <p class="text-slate-400 mt-1 text-sm sm:text-base">Gerencie e visualize suas APIs ativas</p>
                </div>
                
                <!-- Filtros Rápidos -->
                <div class="flex items-center gap-3">
                    <div class="flex bg-slate-800 rounded-full p-1">
                        <button type="button" class="filter-btn px-3 py-1 text-sm rounded-full bg-teal-600 text-white" data-filter="all">
                            Todas
                        </button>
                        <button type="button" class="filter-btn px-3 py-1 text-sm rounded-full text-slate-400 hover:text-white" data-filter="active">
                            Ativas
                        </button>
                        <button type="button" class="filter-btn px-3 py-1 text-sm rounded-full text-slate-400 hover:text-white" data-filter="inactive">
                            Inativas
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-500/20 rounded-lg mr-4">
                            <i class="fas fa-plug text-blue-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Total de APIs</p>
                            <p class="text-2xl font-bold text-white">{{ $workspaces->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-500/20 rounded-lg mr-4">
                            <i class="fas fa-check-circle text-green-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">APIs Ativas</p>
                            <p class="text-2xl font-bold text-white">{{ $workspaces->where('is_published', true)->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                    <div class="flex items-center">
                        <div class="p-2 bg-amber-500/20 rounded-lg mr-4">
                            <i class="fas fa-code text-amber-400 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Campos Visíveis</p>
                            <p class="text-2xl font-bold text-white">{{ $workspaces->sum('visible_fields_count') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de Pesquisa e Filtros -->
            <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6 mb-6">
                <div class="flex flex-col justify-center lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row gap-4 flex-1 justify-center">
                        <!-- Barra de Pesquisa -->
                        <div class="relative flex-1 max-w-md">
                            <input type="text" id="search-apis" 
                                class="w-full bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 pl-10 pr-4 py-2" 
                                placeholder="Buscar APIs por nome...">
                            <i class="fas fa-search absolute left-3 top-3 text-slate-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid de APIs -->
            <div id="apis-content">
                @if($workspaces->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($workspaces as $workspace)
                            <div class="api-card bg-slate-800 rounded-xl border border-slate-700 p-6 hover:border-teal-500/50 transition-all duration-300" 
                                data-status="{{ $workspace->api_enabled ? 'active' : 'inactive' }}"
                                data-type="{{ strtolower(str_replace(' ', '-', $workspace->api_type)) }}">
                                
                                <!-- Header com Nome e Status -->
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-lg font-semibold text-white truncate flex-1 pr-4">{{ $workspace->title }}</h3>
                                    
                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                        {{ $workspace->api_enabled ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                        <span class="w-2 h-2 rounded-full {{ $workspace->api_enabled ? 'bg-green-400' : 'bg-red-400' }} mr-2"></span>
                                        {{ $workspace->api_enabled ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </div>

                                <!-- Estatísticas Principais -->
                                <div class="space-y-4 mb-6">
                                    <!-- Total de Consultas -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-slate-400">
                                            <i class="fas fa-chart-bar mr-3 text-sm"></i>
                                            <span class="text-sm">Total de Consultas</span>
                                        </div>
                                        <span class="text-white font-semibold text-sm">
                                            {{ $workspace->api_requests_count ?? '0' }}
                                        </span>
                                    </div>

                                    <!-- Endpoint -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-slate-400">
                                            <i class="fas fa-link mr-3 text-sm"></i>
                                            <span class="text-sm">Endpoint</span>
                                        </div>
                                        <button onclick="copyApiUrl('{{ route('workspace.shared.api', [
                                            'global_key_api' => auth()->user()->global_key_api,
                                            'workspace_key_api' => $workspace->workspace_key_api ]) }}')"
                                                class="text-cyan-400 hover:text-cyan-300 flex items-center text-sm"
                                                title="Copiar URL da API">
                                            <i class="fas fa-copy mr-1"></i>
                                            Copiar
                                        </button>
                                    </div>

                                    <!-- Tipo de Visualização -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-slate-400">
                                            <i class="fas {{ $workspace->type_view_workspace_id == 1 ? 'fa-eye' : 'fa-code' }} mr-3 text-sm"></i>
                                            <span class="text-sm">Tipo</span>
                                        </div>
                                        <span class="text-white font-semibold text-sm">
                                            {{ $workspace->type_view_workspace_id == 1 ? 'Geev Studio' : 'Geev API' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Ações -->
                                <div class="flex justify-between items-center pt-4 border-t border-slate-700">
                                    <!-- Botão Gerenciar -->
                                    <a href="{{ 
                                            $workspace->type_view_workspace_id == 1 
                                            ? route('workspace.shared-geev-studio.show', ['global_key_api' => auth()->user()->global_key_api, 'workspace_key_api' => $workspace->workspace_key_api]) 
                                            : route('workspace.api-rest.show', ['global_key_api' => auth()->user()->global_key_api, 'workspace_key_api' => $workspace->workspace_key_api])
                                        }}" 
                                        class="text-slate-300 hover:text-white text-sm flex items-center bg-slate-700 hover:bg-slate-600 px-3 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-cog mr-2"></i>
                                        Gerenciar {{ $workspace->type_view_workspace_id == 1 ? 'Geev Studio' : 'Geev API' }}
                                    </a>
                                    
                                    <!-- Botão Ativar/Inativar -->
                                    <button onclick="toggleApiStatus(`{{ route('management.api.access.toggle', $workspace->id) }}`, {{ !$workspace->api_enabled }})" class="relative inline-flex items-center h-6 rounded-full w-11 
                                        @if($workspace->api_enabled) bg-blue-500 @else bg-gray-300 dark:bg-gray-600 @endif transition-colors">
                                        <span class="inline-block w-4 h-4 transform bg-white rounded-full transition 
                                            @if($workspace->api_enabled) translate-x-6 @else translate-x-1 @endif" />
                                    </button>
                                </div>
                            </div>
                            @endforeach
                    </div>
                @else
                    <!-- Estado Vazio -->
                    <div class="text-center py-16 bg-slate-800/50 rounded-xl border border-slate-700">
                        <div class="text-slate-400 text-6xl mb-4">
                            <i class="fas fa-plug"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Nenhuma API encontrada</h3>
                        <p class="text-slate-400 mb-6 max-w-md mx-auto">
                            Você ainda não possui APIs configuradas. Crie um workspace e ative a API para começar.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('workspaces.show') }}" 
                            class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-plus mr-2"></i>
                                Criar Workspace
                            </a>
                            <a href="{{ route('dashboard.home') }}" 
                            class="bg-slate-700 hover:bg-slate-600 text-white px-6 py-2 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-home mr-2"></i>
                                Voltar ao Dashboard
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>    
@endsection

@push('modals')
    @include('components.modals.modal-confirm')
@endpush

@push('scripts_end')
    <script>
        // Filtros e Busca
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-apis');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const apiCards = document.querySelectorAll('.api-card');

            // Função para filtrar cards
            function filterCards() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusFilter = document.querySelector('.filter-btn.bg-teal-600')?.dataset.filter || 'all';

                apiCards.forEach(card => {
                    const title = card.querySelector('h3').textContent.toLowerCase();
                    const status = card.dataset.status;
                    const type = card.dataset.type;
                    
                    const matchesSearch = title.includes(searchTerm);
                    const matchesStatus = statusFilter === 'all' || status === statusFilter;

                    if (matchesSearch && matchesStatus) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Event Listeners
            searchInput.addEventListener('input', filterCards);
            
            filterButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterButtons.forEach(b => b.classList.remove('bg-teal-600', 'text-white'));
                    filterButtons.forEach(b => b.classList.add('text-slate-400'));
                    
                    this.classList.remove('text-slate-400');
                    this.classList.add('bg-teal-600', 'text-white');
                    filterCards();
                });
            });
        });

        // Copiar URL da API
        function copyApiUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                // Mostrar notificação de sucesso (pode usar Toast do Flowbite)
                showNotification('URL copiada para a área de transferência!', 'success');
            }).catch(() => {
                showNotification('Erro ao copiar URL', 'error');
            });
        }

        // Função auxiliar para notificações
        function showNotification(message, type) {
            // Implementar notificação (pode usar Toast do Flowbite)
            alert(message); // Placeholder - substituir por sistema de notificação real
        }
        </script>
        @endpush

        @push('styles')
        <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .api-card {
            transition: all 0.3s ease;
        }

        .api-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
    </style>
@endpush