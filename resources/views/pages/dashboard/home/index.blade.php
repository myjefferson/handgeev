@extends('template.template-dashboard')

@section('title', 'Início')
@section('description', 'Início do HandGeev')

@section('content_dashboard')
    <div class="max-w-7xl mx-auto min-h-screen p-6">

        @include('components.alerts.alert')
        
        <!-- Header com Saudação Personalizada -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">
                Olá, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="text-gray-400">
                {{ $greetingMessage }}
            </p>
            <div class="w-20 h-1 bg-teal-400 rounded-full mt-3"></div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Card Workspaces -->
            <div class="bg-slate-800 rounded-2xl shadow-lg p-6 border-l-4 border-teal-400 hover:border-teal-300 transition-all duration-300 hover:transform hover:scale-105">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 flex items-center justify-center bg-teal-400/10 rounded-full">
                        <i class="fas fa-layer-group text-teal-400 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-400">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">{{ $workspacesCount }} Workspaces</h3>
                <p class="text-sm text-gray-400">{{ $publishedWorkspaces }} públicos • {{ $privateWorkspaces }} privados</p>
            </div>

            <!-- Card Tópicos -->
            <div class="bg-slate-800 rounded-2xl shadow-lg p-6 border-l-4 border-blue-400 hover:border-blue-300 transition-all duration-300 hover:transform hover:scale-105">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 flex items-center justify-center bg-blue-400/10 rounded-full">
                        <i class="fas fa-folder text-blue-400 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-400">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">{{ $topicsCount }} Tópicos</h3>
                <p class="text-sm text-gray-400">{{ $topicsWithFields }} com campos</p>
            </div>

            <!-- Card Campos -->
            <div class="bg-slate-800 rounded-2xl shadow-lg p-6 border-l-4 border-green-400 hover:border-green-300 transition-all duration-300 hover:transform hover:scale-105">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 flex items-center justify-center bg-green-400/10 rounded-full">
                        <i class="fas fa-table text-green-400 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-400">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">{{ $fieldsCount }} Campos</h3>
                <p class="text-sm text-gray-400">{{ $visibleFields }} visíveis • {{ $hiddenFields }} ocultos</p>
            </div>

            <!-- Card Colaborações -->
            {{-- <div class="bg-slate-800 rounded-2xl shadow-lg p-6 border-l-4 border-purple-400 hover:border-purple-300 transition-all duration-300 hover:transform hover:scale-105">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 flex items-center justify-center bg-purple-400/10 rounded-full">
                        <i class="fas fa-users text-purple-400 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-400">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">{{ $collaborationsCount }} Colaborações</h3>
                <p class="text-sm text-gray-400">{{ $activeCollaborations }} ativas</p>
            </div> --}}
        </div>

        <!-- Grid Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Workspaces Recentes -->
            <div class="bg-slate-800 rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white">Workspaces Recentes</h2>
                    <a href="{{ route('workspaces.index') }}" class="text-teal-400 hover:text-teal-300 text-sm font-medium flex items-center">
                        Ver todos <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-4">
                    @forelse($recentWorkspaces as $workspace)
                    <a href="{{ route('workspace.show', $workspace->id) }}" class="block p-4 bg-slate-700/50 rounded-xl hover:bg-slate-700 transition-all duration-200 group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-teal-400 to-teal-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                    <i class="fas fa-layer-group text-slate-900"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white group-hover:text-teal-300 transition-colors">{{ $workspace->title }}</h4>
                                    <p class="text-sm text-gray-400">
                                        {{ $workspace->topics_count }} tópicos • {{ $workspace->fields_count }} campos
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end space-y-1">
                                <span class="text-xs px-3 py-1 rounded-full {{ $workspace->is_published ? 'bg-green-400/20 text-green-400' : 'bg-gray-400/20 text-gray-400' }}">
                                    {{ $workspace->is_published ? 'Público' : 'Privado' }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $workspace->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-layer-group text-4xl mb-3 opacity-50"></i>
                        <p>Nenhum workspace criado ainda</p>
                        <a href="{{ route('workspaces.index') }}" class="text-teal-400 hover:text-teal-300 text-sm mt-2 inline-block">
                            Criar primeiro workspace
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Ferramentas Rápidas -->
            {{-- <div class="bg-slate-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-white mb-6">Ferramentas Rápidas</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <!-- Novo Workspace -->


                    <!-- API Explorer -->
                    <a href="#" class="p-4 bg-gradient-to-r from-blue-400 to-blue-500 text-white rounded-xl text-center hover:shadow-lg transition-all duration-300 blue-glow group">
                        <div class="mb-2 transform group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-code text-2xl"></i>
                        </div>
                        <span class="font-medium">API Explorer</span>
                    </a>



                    <!-- Exportar Dados -->
                    <a href="#" class="p-4 bg-gradient-to-r from-green-400 to-green-500 text-white rounded-xl text-center hover:shadow-lg transition-all duration-300 green-glow group">
                        <div class="mb-2 transform group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-file-export text-2xl"></i>
                        </div>
                        <span class="font-medium">Exportar Dados</span>
                    </a>
                </div>

                <!-- Links Úteis -->
                <div class="mt-6 pt-6 border-t border-slate-700">
                    <h3 class="text-sm font-medium text-gray-400 mb-3">Links Úteis</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="#" class="text-xs text-gray-400 hover:text-teal-400 transition-colors flex items-center">
                            <i class="fas fa-book mr-2"></i> Documentação
                        </a>
                        <a href="#" class="text-xs text-gray-400 hover:text-teal-400 transition-colors flex items-center">
                            <i class="fas fa-life-ring mr-2"></i> Suporte
                        </a>
                        <a href="#" class="text-xs text-gray-400 hover:text-teal-400 transition-colors flex items-center">
                            <i class="fas fa-crown mr-2"></i> Planos
                        </a>
                        <a href="#" class="text-xs text-gray-400 hover:text-teal-400 transition-colors flex items-center">
                            <i class="fas fa-cog mr-2"></i> Configurações
                        </a>
                    </div>
                </div>
            </div> --}}
            <div class="bg-slate-800 rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white">Workspaces Mais Ativos</h2>
                    <span class="text-xs text-gray-400">Por campos</span>
                </div>

                <div class="space-y-3">
                    @forelse($mostActiveWorkspaces as $workspace)
                    <div class="flex items-center justify-between p-3 bg-slate-700/50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-teal-400/20 rounded flex items-center justify-center">
                                <i class="fas fa-layer-group text-teal-400 text-sm"></i>
                            </div>
                            <span class="text-white text-sm font-medium">{{ Str::limit($workspace->title, 25) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-teal-400 text-sm font-bold">{{ $workspace->fields_count }}</span>
                            <span class="text-gray-400 text-xs block">campos</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-400">
                        <p class="text-sm">Nenhum workspace ativo</p>
                    </div>
                    @endforelse
                </div>
            </div>            
        </div>

        {{-- <!-- Seção de Atividade Rápida -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Workspaces com Mais Atividade -->
        </div> --}}
    </div>

    <style>
        .teal-glow {
            box-shadow: 0 0 15px rgba(0, 230, 216, 0.3);
        }
        
        .blue-glow {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
        }
        
        .purple-glow {
            box-shadow: 0 0 15px rgba(168, 85, 247, 0.3);
        }
        
        .green-glow {
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.3);
        }
        
        .teal-glow-hover:hover {
            box-shadow: 0 0 20px rgba(0, 230, 216, 0.4);
        }
    </style>
@endsection

{{-- @push('modals')
    @include('components.modals.modal-add-workspace')
@endpush --}}