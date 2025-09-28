@extends('template.template-dashboard')

@section('content_dashboard')
    <div class="min-h-screen bg-slate-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Meu Perfil</h1>
                <p class="text-gray-400">Gerencie suas informações pessoais e preferências</p>
                <div class="w-20 h-1 bg-teal-400 rounded-full mt-3"></div>
            </div>

            <!-- Alertas -->
            @if(session('success'))
                <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-xl mb-6">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-500/20 border border-red-500/50 text-red-400 px-4 py-3 rounded-xl mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Coluna Lateral - Informações do Usuário -->
                <div class="lg:col-span-1">
                    <div class="bg-slate-800 rounded-2xl shadow-lg p-6 border border-slate-700">
                        <!-- Avatar -->
                        <div class="text-center mb-6">
                            <div class="w-24 h-24 bg-teal-400/10 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-teal-400/30">
                                <i class="fas fa-user text-teal-400 text-3xl"></i>
                            </div>
                            <h2 class="text-lg font-bold text-white">{{ Auth::user()->name }} {{ Auth::user()->surname }}</h2>
                            <p class="text-gray-400 text-sm">{{ Auth::user()->email }}</p>
                            
                            <!-- Badge do Tipo de Perfil -->
                            <div class="mt-3">
                                @if(Auth::user()->isAdmin())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-400 border border-blue-400/30">
                                        <i class="fas fa-shield-alt mr-1"></i> Administrador
                                    </span>
                                @elseif(Auth::user()->isPro())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-400 border border-purple-400/30">
                                        <i class="fas fa-crown mr-1"></i> Plano Pro
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-teal-500/20 text-teal-400 border border-teal-400/30">
                                        <i class="fas fa-user mr-1"></i> Plano Free
                                    </span>
                                @endif
                            </div>

                            <!-- Botão de Upgrade -->
                            @if(Auth::user()->isFree())
                                <div class="mt-4 border-slate-700">
                                    <a href="{{ route('landing.offers') }}" 
                                    class="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-sm font-medium py-2 px-4 rounded-xl transition-all duration-300 flex items-center justify-center">
                                        <i class="fas fa-rocket mr-2"></i> Upgrade para Pro
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Informações da Conta -->
                        <div class="space-y-4">
                            <!-- Status -->
                            {{-- <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-400">Status</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    @if(Auth::user()->status === 'active') bg-green-500/20 text-green-400
                                    @elseif(Auth::user()->status === 'inactive') bg-orange-500/20 text-orange-400
                                    @else bg-red-500/20 text-red-400 @endif">
                                    {{ ucfirst(Auth::user()->status) }}
                                </span>
                            </div> --}}

                            {{-- <!-- Membro desde -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-400">Membro desde</span>
                                <span class="text-xs text-white">{{ Auth::user()->created_at->format('d/m/Y') }}</span>
                            </div>

                            <!-- Última atualização -->
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-400">Última atualização</span>
                                <span class="text-xs text-white">{{ Auth::user()->updated_at->format('d/m/Y') }}</span>
                            </div> --}}
                        </div>

                        <!-- Estatísticas Rápidas -->
                        <div class="mt-6 pt-6 border-t border-slate-700">
                            <h3 class="text-sm font-medium text-gray-400 mb-3">Estatísticas</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">Workspaces</span>
                                    <span class="text-white font-medium">{{ auth()->user()->workspaces()->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">Tópicos</span>
                                    <span class="text-white font-medium">{{ auth()->user()->topics()->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">Campos</span>
                                    <span class="text-white font-medium">{{ auth()->user()->fields()->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coluna Principal - Conteúdo com Abas -->
                <div class="lg:col-span-3">
                    <div class="bg-slate-800 rounded-2xl shadow-lg border border-slate-700">
                        <!-- Abas Flowbite -->
                        <div class="border-b border-slate-700">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">
                                        <i class="fas fa-user-edit mr-2"></i>Informações Pessoais
                                    </button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-300 hover:border-gray-300" id="password-tab" data-tabs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                                        <i class="fas fa-lock mr-2"></i>Alterar Senha
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Conteúdo das Abas -->
                        <div id="default-tab-content">
                            <!-- Aba Informações Pessoais -->
                            <div class="p-6 rounded-b-2xl flex justify-center" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="max-w-3xl">
                                    <h2 class="text-xl font-semibold text-white mb-6">Editar Informações Pessoais</h2>

                                    <form action="{{ route('user.profile.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                            <!-- Nome -->
                                            <div>
                                                <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Nome *</label>
                                                <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                                                    class="w-full bg-slate-700 border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                                                @error('name')
                                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Sobrenome -->
                                            <div>
                                                <label for="surname" class="block text-sm font-medium text-gray-400 mb-2">Sobrenome *</label>
                                                <input type="text" id="surname" name="surname" value="{{ old('surname', Auth::user()->surname) }}"
                                                    class="w-full bg-slate-700 border {{ $errors->has('surname') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                                                @error('surname')
                                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="mb-6">
                                            <label for="email" class="block text-sm font-medium text-gray-400 mb-2">Email *</label>
                                            <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                                class="w-full bg-slate-700 border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                                            @error('email')
                                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Telefone -->
                                        <div class="mb-6">
                                            <label for="phone" class="block text-sm font-medium text-gray-400 mb-2">Telefone</label>
                                            <input type="tel" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                                class="w-full bg-slate-700 border {{ $errors->has('phone') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                                                placeholder="(00) 00000-0000">
                                            @error('phone')
                                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Botões -->
                                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                                            <button type="submit"
                                                class="flex-1 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium py-3 px-6 rounded-xl transition-colors duration-300 teal-glow-hover flex items-center justify-center">
                                                <i class="fas fa-save mr-2"></i> Salvar Alterações
                                            </button>

                                            <button type="button" onclick="resetProfileForm()"
                                                class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-6 rounded-xl transition-colors duration-300 border border-slate-600 flex items-center justify-center">
                                                <i class="fas fa-undo mr-2"></i> Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Aba Alterar Senha -->
                            <div class="hidden p-6 rounded-b-2xl flex justify-center" id="password" role="tabpanel" aria-labelledby="password-tab">
                                <div class="max-w-2xl">
                                    <h2 class="text-xl font-semibold text-white mb-6">Alterar Senha</h2>

                                    <form action="{{ route('user.profile.password.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <!-- Senha Atual -->
                                        <div class="mb-6">
                                            <label for="current_password" class="block text-sm font-medium text-gray-400 mb-2">Senha Atual *</label>
                                            <input type="password" id="current_password" name="current_password"
                                                class="w-full bg-slate-700 border {{ $errors->has('current_password') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                                            @error('current_password')
                                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                            <!-- Nova Senha -->
                                            <div>
                                                <label for="new_password" class="block text-sm font-medium text-gray-400 mb-2">Nova Senha *</label>
                                                <input type="password" id="new_password" name="new_password"
                                                    class="w-full bg-slate-700 border {{ $errors->has('new_password') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                                                @error('new_password')
                                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Confirmar Nova Senha -->
                                            <div>
                                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-400 mb-2">Confirmar Nova Senha *</label>
                                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                                    class="w-full bg-slate-700 border border-slate-600 rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                                            </div>
                                        </div>

                                        <!-- Dicas de Segurança -->
                                        <div class="bg-slate-700/50 rounded-xl p-4 mb-6">
                                            <h4 class="text-sm font-medium text-teal-400 mb-2">
                                                <i class="fas fa-lightbulb mr-2"></i>Dicas para uma senha segura:
                                            </h4>
                                            <ul class="text-xs text-gray-400 space-y-1">
                                                <li>• Use pelo menos 8 caracteres</li>
                                                <li>• Combine letras maiúsculas e minúsculas</li>
                                                <li>• Inclua números e caracteres especiais</li>
                                                <li>• Evite informações pessoais</li>
                                            </ul>
                                        </div>

                                        <!-- Botões -->
                                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                                            <button type="submit"
                                                class="flex-1 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium py-3 px-6 rounded-xl transition-colors duration-300 teal-glow-hover flex items-center justify-center">
                                                <i class="fas fa-key mr-2"></i> Atualizar Senha
                                            </button>

                                            <button type="button" onclick="showTab('profile')"
                                                class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-6 rounded-xl transition-colors duration-300 border border-slate-600 flex items-center justify-center">
                                                <i class="fas fa-arrow-left mr-2"></i> Voltar para Perfil
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    {{-- Card Limites do Plano 
                    <div class="mt-6 bg-slate-800 rounded-2xl shadow-lg p-6 border border-slate-700">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i> Limites do Plano
                        </h3>
                        
                        @php
                            $planInfo = Auth::user()->planInfo();
                            $limits = $planInfo['limits'];
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Workspaces -->
                            <div class="text-center">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-400">Workspaces</span>
                                    <span class="text-sm text-white font-medium">
                                        {{ auth()->user()->workspaces()->count() }} 
                                        @if($limits['max_workspaces'] > 0)
                                            / {{ $limits['max_workspaces'] }}
                                        @else
                                            / ∞
                                        @endif
                                    </span>
                                </div>
                                <div class="w-full bg-slate-700 rounded-full h-2">
                                    <div class="bg-teal-400 h-2 rounded-full" 
                                        style="width: {{ $limits['max_workspaces'] > 0 ? min(100, (auth()->user()->workspaces()->count() / $limits['max_workspaces']) * 100) : 100 }}%"></div>
                                </div>
                            </div>

                            <!-- Tópicos -->
                            <div class="text-center">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-400">Tópicos</span>
                                    <span class="text-sm text-white font-medium">
                                        {{ auth()->user()->topics()->count() }} 
                                        @if($limits['max_topics'] > 0)
                                            / {{ $limits['max_topics'] }}
                                        @else
                                            / ∞
                                        @endif
                                    </span>
                                </div>
                                <div class="w-full bg-slate-700 rounded-full h-2">
                                    <div class="bg-teal-400 h-2 rounded-full" 
                                        style="width: {{ $limits['max_topics'] > 0 ? min(100, (auth()->user()->topics()->count() / $limits['max_topics']) * 100) : 100 }}%"></div>
                                </div>
                            </div>

                            <!-- Campos -->
                            <div class="text-center">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-400">Campos</span>
                                    <span class="text-sm text-white font-medium">
                                        {{ auth()->user()->fields()->count() }} 
                                        @if($limits['max_fields'] > 0)
                                            / {{ $limits['max_fields'] }}
                                        @else
                                            / ∞
                                        @endif
                                    </span>
                                </div>
                                <div class="w-full bg-slate-700 rounded-full h-2">
                                    <div class="bg-teal-400 h-2 rounded-full" 
                                        style="width: {{ $limits['max_fields'] > 0 ? min(100, (auth()->user()->fields()->count() / $limits['max_fields']) * 100) : 100 }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Recursos -->
                        <div class="mt-4 flex flex-wrap gap-4">
                            @if($limits['can_export'])
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-green-500/20 text-green-400">
                                    <i class="fas fa-file-export mr-1"></i> Exportação
                                </span>
                            @endif
                            
                            @if($limits['can_use_api'])
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-500/20 text-blue-400">
                                    <i class="fas fa-code mr-1"></i> API
                                </span>
                            @endif
                            
                            @if(Auth::user()->isPro() || Auth::user()->isAdmin())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-purple-500/20 text-purple-400">
                                    <i class="fas fa-infinity mr-1"></i> Ilimitado
                                </span>
                            @endif
                        </div>

                        
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

<!-- Flowbite Tabs Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>

    <script>
        // Inicializar tabs do Flowbite com a primeira aba ativa
        document.addEventListener('DOMContentLoaded', function() {
            // Ativar a primeira aba por padrão
            const profileTab = document.getElementById('profile-tab');
            const profileContent = document.getElementById('profile');
            
            if(profileTab && profileContent) {
                profileTab.classList.add('text-teal-400', 'border-teal-400');
                profileTab.classList.remove('hover:text-gray-300', 'hover:border-gray-300');
                profileContent.classList.remove('hidden');
            }

            // Configurar clique nas tabs
            const tabs = document.querySelectorAll('[data-tabs-toggle] button');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remover classes ativas de todas as tabs
                    tabs.forEach(t => {
                        t.classList.remove('text-teal-400', 'border-teal-400');
                        t.classList.add('hover:text-gray-300', 'hover:border-gray-300');
                    });
                    
                    // Adicionar classes ativas na tab clicada
                    this.classList.add('text-teal-400', 'border-teal-400');
                    this.classList.remove('hover:text-gray-300', 'hover:border-gray-300');
                });
            });
        });

        // Reset do formulário de perfil
        function resetProfileForm() {
            document.querySelector('#profile form').reset();
        }

        // Máscara para telefone
        $(document).ready(function(){
            $('#phone').mask('(00) 00000-0000');
        });

        // Feedback visual ao enviar formulários
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processando...';
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    if (!this.checkValidity()) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                }, 3000);
            });
        });

        // Função para navegar entre abas
        function showTab(tabName) {
            const tabButton = document.getElementById(tabName + '-tab');
            if (tabButton) {
                tabButton.click();
            }
        }
    </script>

    <style>
        /* Estilos customizados para as tabs do Flowbite */
        [data-tabs-toggle] button {
            @apply text-gray-400 border-transparent;
            transition: all 0.3s ease;
        }

        [data-tabs-toggle] button:hover {
            @apply text-gray-300 border-gray-300;
        }

        [data-tabs-toggle] button.text-teal-400 {
            @apply border-teal-400;
        }

        .teal-glow-hover:hover {
            box-shadow: 0 0 20px rgba(0, 230, 216, 0.3);
        }

        input:focus, select:focus {
            box-shadow: 0 0 0 3px rgba(0, 230, 216, 0.1);
        }

        /* Animações suaves */
        .tab-content {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Barras de progresso */
        .progress-bar {
            transition: width 0.5s ease-in-out;
        }
    </style>
@endsection