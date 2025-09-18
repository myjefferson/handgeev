@extends('template.template-dashboard')

@section('content_dashboard')
    <div class="max-w-7xl mx-auto min-h-screen p-6">
        <!-- Header com Saudação Personalizada -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">
                Olá, Jefferson! 👋
            </h1>
            <p class="text-gray-400">
                Como é bom ter você aqui! Aqui está o resumo do seu dia.
            </p>
            <div class="w-20 h-1 bg-teal-400 rounded-full mt-3"></div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Card Workspaces -->
            <div class="bg-slate-800 rounded-2xl shadow-lg p-6 border-l border-teal-400 hover:border">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-teal-400/10 rounded-full">
                        <i class="fas fa-layer-group text-teal-400 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-400">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">5 Workspaces</h3>
                <p class="text-sm text-gray-400">Seus ambientes de trabalho</p>
            </div>

            <!-- Card Tópicos -->
            <div class="bg-slate-800 rounded-2xl shadow-lg p-6 border-l border-teal-400 hover:border">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-teal-400/10 rounded-full">
                        <i class="fas fa-folder text-teal-400 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-400">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">23 Tópicos</h3>
                <p class="text-sm text-gray-400">Organizados por workspace</p>
    </div>

            <!-- Card Campos -->
            <div class="bg-slate-800 rounded-2xl shadow-lg p-6 border-l border-teal-400 hover:border">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-teal-400/10 rounded-full">
                        <i class="fas fa-table text-teal-400 text-xl"></i>
                    </div>
                    <span class="text-sm text-gray-400">Total</span>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">156 Campos</h3>
                <p class="text-sm text-gray-400">Dados cadastrados</p>
            </div>
        </div>

        <!-- Grid Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Workspaces Recentes -->
            <div class="bg-slate-800 rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white">Workspaces Recentes</h2>
                    <a href="{{route('workspaces.myworkspaces')}}" class="text-teal-400 hover:text-teal-300 text-sm font-medium">
                        Ver todos →
                    </a>
                </div>
                
                <div class="space-y-4">
                    @for($i = 0; $i < 3; $i++)
                    <div class="flex items-center justify-between p-4 bg-slate-700/50 rounded-xl hover:bg-slate-700 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-teal-400 to-teal-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-layer-group text-slate-900"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-white">Workspace {{ $i + 1 }}</h4>
                                <p class="text-sm text-gray-400">5 tópicos • 32 campos</p>
                            </div>
                        </div>
                        <span class="text-xs px-3 py-1 bg-teal-400/20 text-teal-400 rounded-full">Ativo</span>
                    </div>
                    @endfor
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="bg-slate-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-white mb-6">Ações Rápidas</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <a href="#" class="p-4 bg-gradient-to-r from-teal-400 to-teal-500 text-slate-900 rounded-xl text-center hover:shadow-lg transition-shadow duration-300 teal-glow">
                        <div class="mb-2">
                            <i class="fas fa-plus-circle text-2xl"></i>
                        </div>
                        <span class="font-medium">Novo Workspace</span>
                    </a>

                    <a href="#" class="p-4 bg-slate-700 border border-teal-400/30 text-teal-400 rounded-xl text-center hover:bg-slate-600 transition-colors duration-300">
                        <div class="mb-2">
                            <i class="fas fa-folder-plus text-2xl"></i>
                        </div>
                        <span class="font-medium">Novo Tópico</span>
                    </a>

                    <a href="#" class="p-4 bg-slate-700 border border-teal-400/30 text-teal-400 rounded-xl text-center hover:bg-slate-600 transition-colors duration-300">
                        <div class="mb-2">
                            <i class="fas fa-table text-2xl"></i>
                        </div>
                        <span class="font-medium">Novo Campo</span>
                    </a>

                    <a href="#" class="p-4 bg-slate-700 border border-teal-400/30 text-teal-400 rounded-xl text-center hover:bg-slate-600 transition-colors duration-300">
                        <div class="mb-2">
                            <i class="fas fa-file-export text-2xl"></i>
                        </div>
                        <span class="font-medium">Exportar Dados</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Seção de Atividade Recente -->
        {{-- <div class="mt-8 bg-slate-800 rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-white">Atividade Recente</h2>
                <a href="#" class="text-teal-400 hover:text-teal-300 text-sm font-medium">
                    Ver histórico completo →
                </a>
            </div>

            <div class="space-y-4">
                @for($i = 0; $i < 4; $i++)
                <div class="flex items-center space-x-4 p-4 bg-slate-700/50 rounded-xl">
                    <div class="w-10 h-10 bg-teal-400 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-slate-900 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-white font-medium">Novo campo adicionado</p>
                        <p class="text-sm text-gray-400">em "Workspace Marketing" • há 2 horas</p>
                    </div>
                    <span class="text-xs text-teal-400 font-medium">Concluído</span>
                </div>
                @endfor
            </div>
        </div> --}}

        <!-- Footer com Status do Sistema -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                Sistema atualizado • Última sincronização: {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>

    <style>
        .teal-glow {
            box-shadow: 0 0 15px rgba(0, 230, 216, 0.3);
        }
        
        .teal-glow-hover:hover {
            box-shadow: 0 0 20px rgba(0, 230, 216, 0.4);
        }
        
        .nav-item.active {
            background-color: rgba(0, 230, 216, 0.1);
            color: #00e6d8;
        }
        
        .nav-item.active .bg-slate-700 {
            background-color: rgba(0, 230, 216, 0.2);
        }
    </style>
@endsection