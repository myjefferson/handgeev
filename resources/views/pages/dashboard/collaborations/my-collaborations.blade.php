@extends('template.template-dashboard')

@section('title', 'Colaborações')
@section('description', 'Colaborações')

@section('content_dashboard')
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header com Título e Botão Add -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Colaborações</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Gerencie todos as suas colaborações
                    </p>
                </div>
                
                {{-- <!-- Simulando a verificação de permissão -->
                @if (auth()->user()->canCreateWorkspace())
                    <button id="modal-add-workspace-btn" data-modal-target="modal-add-workspace" data-modal-toggle="modal-add-workspace"
                        class="flex items-center px-4 py-2 text-white rounded-lg bg-teal-600 hover:bg-teal-700 transition-colors teal-glow-hover">
                        <i class="fas fa-plus mr-2"></i>
                        Novo Workspace
                    </button>
                @else
                    <div>
                        @include('components.upsell.button-upgrade-pro', ['subtitle' => 'Unlock unlimited workspaces'])
                    </div>
                @endif --}}
            </div>

            <!-- Barra de Pesquisa e Filtros -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <!-- Barra de Pesquisa -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                id="search-collaborations"
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 dark:border-gray-600 dark:text-white"
                                placeholder="Pesquisar colaborações..."
                            />
                        </div>
                    </div>
                    
                    <!-- Filtros e Ordenação -->
                    <div class="flex space-x-3">
                        <select id="sort-workspaces" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="newest">Mais recentes</option>
                            <option value="oldest">Mais antigos</option>
                            <option value="name_asc">A-Z</option>
                            <option value="name_desc">Z-A</option>
                        </select>
                        
                        <button id="filter-btn" class="px-3 py-2 border border-gray-300 rounded-lg text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </div>

            
            <!-- Minhas colaborações -->
            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800" 
                id="styled-collaborations">
                @if($collaborations->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="collaborations-container">
                        @foreach($collaborations as $collaboration)
                            <div class="workspace-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <!-- Header do Card -->
                                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 rounded-full bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                                                <i class="fas fa-users text-teal-600 dark:text-teal-400"></i>
                                            </div>
                                            <div>
                                                <h3 class="workspace-title text-lg font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $collaboration->workspace->title }}
                                                </h3>
                                                <p class="workspace-description text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $collaboration->workspace->topics_count }} tópicos • {{ $collaboration->workspace->fields_count }} campos
                                                </p>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Compartilhado por: {{ $collaboration->workspace->user->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $collaboration->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                                ($collaboration->role === 'editor' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200') }}">
                                        {{ ucfirst($collaboration->role) }}
                                    </span>
                                    
                                    <!-- Status do convite -->
                                    @if($collaboration->status === 'pending')
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            <i class="fas fa-clock mr-1"></i> Pendente
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Footer do Card com Ações -->
                                <div class="p-4 bg-gray-50 dark:bg-gray-700/50">
                                    <div class="flex items-center justify-center">
                                        @if($collaboration->status === 'accepted')
                                            <a href="{{ route('collaboration.show', ['workspaceId' => $collaboration->workspace->id]) }}" 
                                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-teal-700 dark:text-teal-300 bg-teal-50 dark:bg-teal-900/30 rounded-md hover:bg-teal-100 dark:hover:bg-teal-900/50">
                                                <i class="fas fa-eye mr-1.5"></i>Abrir
                                            </a>
                                        @elseif($collaboration->status === 'pending')
                                            <div class="flex space-x-2">
                                                <form action="{{ route('collaboration.invite.reject', ['token' => $collaboration->invitation_token]) }}" method="POST" class="w-full">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex w-full items-center px-3 py-1.5 text-sm font-medium text-white bg-slate-600 rounded-md hover:bg-slate-700">
                                                        <i class="fas fa-times mr-1.5"></i>Rejeitar
                                                    </button>
                                                </form>
                                                <form action="{{ route('collaboration.invite.accept', ['token' => $collaboration->invitation_token]) }}" method="POST" class="w-full">
                                                    @csrf
                                                    <button type="submit" class="inline-flex w-full items-center px-3 py-1.5 text-sm font-medium text-white bg-teal-600 rounded-md hover:bg-teal-700">
                                                        <i class="fas fa-check mr-1.5"></i>Confirmar
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                        
                                    </div>
                                    <div class="w-full text-xs text-center text-gray-500 dark:text-gray-400 mt-3">
                                        Convite em: {{ \Carbon\Carbon::parse($collaboration->invited_at)->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 mx-auto mb-4 text-gray-400">
                            <i class="fas fa-users text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            Nenhuma colaboração
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            Você ainda não foi convidado para colaborar em nenhun workspace.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts_end')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Funcionalidade de pesquisa
            const searchInput = document.getElementById('search-collaborations');

            searchInput.addEventListener('input', function() {
               const searchTerm = this.value.toLowerCase();
                // Descobre qual aba está ativa
                const activeTab = document.querySelector('[aria-selected="true"]').dataset.tabsTarget;
                const cards = document.querySelectorAll(`#collaborations-container .workspace-card`);
                
                // Filtra os cards
                cards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.classList.toggle('hidden', !text.includes(searchTerm));
                });
            });
        });
    </script>
@endpush