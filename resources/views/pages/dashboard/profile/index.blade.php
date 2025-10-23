@extends('template.template-dashboard')

@section('title', __('profile.title'))
@section('description', __('profile.description'))

@push('style')
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
@endpush

@section('content_dashboard')
    <div class="min-h-screen bg-slate-900 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">{{ __('profile.header.title') }}</h1>
                <p class="text-gray-400">{{ __('profile.header.subtitle') }}</p>
                <div class="w-20 h-1 bg-teal-400 rounded-full mt-3"></div>
            </div>

            <!-- Alertas -->
            @include('components.alerts.alert')

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
                                        <i class="fas fa-shield-alt mr-1"></i> {{ __('profile.sidebar.badges.admin') }}
                                    </span>
                                @elseif(Auth::user()->isPremium())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-500/20 text-orange-400 border border-orange-400/30">
                                        <i class="fas fa-crown mr-1"></i> {{ __('profile.sidebar.badges.premium') }}
                                    </span>
                                @elseif(Auth::user()->isPro())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-400 border border-purple-400/30">
                                        <i class="fas fa-crown mr-1"></i> {{ __('profile.sidebar.badges.pro') }}
                                    </span>
                                @elseif(Auth::user()->isStart())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-teal-500/20 text-teal-400 border border-teal-400/30">
                                        <i class="fas fa-crown mr-1"></i> {{ __('profile.sidebar.badges.start') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-teal-500/20 text-teal-400 border border-teal-400/30">
                                        <i class="fas fa-user mr-1"></i> {{ __('profile.sidebar.badges.free') }}
                                    </span>
                                @endif
                            </div>

                            <!-- Botão de Upgrade -->
                            @if(Auth::user()->isFree())
                                <div class="mt-4 border-slate-700">
                                    <a href="{{ route('subscription.pricing') }}" 
                                    class="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-sm font-medium py-2 px-4 rounded-xl transition-all duration-300 flex items-center justify-center">
                                        <i class="fas {{ __('profile.sidebar.upgrade.icon') }} mr-2"></i> 
                                        {{ __('profile.sidebar.upgrade.button') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Estatísticas Rápidas -->
                        <div class="mt-6 pt-6 border-t border-slate-700">
                            <h3 class="text-sm font-medium text-gray-400 mb-3">{{ __('profile.sidebar.stats.title') }}</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">{{ __('profile.sidebar.stats.workspaces') }}</span>
                                    <span class="text-white font-medium">{{ auth()->user()->workspaces()->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">{{ __('profile.sidebar.stats.topics') }}</span>
                                    <span class="text-white font-medium">{{ auth()->user()->topics()->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">{{ __('profile.sidebar.stats.fields') }}</span>
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
                                        <i class="fas {{ __('profile.tabs.personal_info.icon') }} mr-2"></i>
                                        {{ __('profile.tabs.personal_info.label') }}
                                    </button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-300 hover:border-gray-300" id="password-tab" data-tabs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                                        <i class="fas {{ __('profile.tabs.password.icon') }} mr-2"></i>
                                        {{ __('profile.tabs.password.label') }}
                                    </button>
                                </li>
                                <li class="me-2" role="presentation">
                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-300 hover:border-gray-300" id="account-tab" data-tabs-target="#account" type="button" role="tab" aria-controls="account" aria-selected="false">
                                        <i class="fas fa-cog mr-2"></i>
                                        Configurações da Conta
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Conteúdo das Abas -->
                        <div id="default-tab-content">
                            <!-- Aba Informações Pessoais -->
                            <div class="p-6 rounded-b-2xl flex justify-center" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="max-w-3xl">
                                    <h2 class="text-xl font-semibold text-white mb-6">{{ __('profile.tabs.personal_info.title') }}</h2>

                                    <form action="{{ route('user.profile.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                            <!-- Nome -->
                                            <div>
                                                <label for="name" class="block text-sm font-medium text-gray-400 mb-2">
                                                    {{ __('profile.forms.personal_info.name.label') }}
                                                </label>
                                                <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                                                    class="w-full bg-slate-700 border {{ $errors->has('name') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                                                    placeholder="{{ __('profile.forms.personal_info.name.placeholder') }}">
                                                @error('name')
                                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Sobrenome -->
                                            <div>
                                                <label for="surname" class="block text-sm font-medium text-gray-400 mb-2">
                                                    {{ __('profile.forms.personal_info.surname.label') }}
                                                </label>
                                                <input type="text" id="surname" name="surname" value="{{ old('surname', Auth::user()->surname) }}"
                                                    class="w-full bg-slate-700 border {{ $errors->has('surname') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                                                    placeholder="{{ __('profile.forms.personal_info.surname.placeholder') }}">
                                                @error('surname')
                                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="mb-6">
                                            <label for="email" class="block text-sm font-medium text-gray-400 mb-2">
                                                {{ __('profile.forms.personal_info.email.label') }}
                                            </label>
                                            
                                            <div class="flex space-x-3">
                                                <div class="flex-1">
                                                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                                        class="w-full bg-slate-700 border {{ $errors->has('email') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-slate-400 placeholder-gray-500 outline-transparent focus:border-transparent transition-colors"
                                                        placeholder="{{ __('profile.forms.personal_info.email.placeholder') }}"
                                                        readonly>
                                                </div>
                                                
                                                <button type="button" 
                                                        data-modal-target="email-change-modal" 
                                                        data-modal-toggle="email-change-modal"
                                                        class="flex-shrink-0 px-4 py-3 bg-teal-600 hover:bg-teal-500 text-white rounded-xl transition-colors duration-200 flex items-center space-x-2">
                                                    <i class="fas fa-edit"></i>
                                                    <span>Alterar</span>
                                                </button>
                                            </div>
                                            
                                            <!-- Status de confirmação do email -->
                                            <div class="mt-3 flex items-center justify-between">
                                                <div class="flex items-center space-x-2">
                                                    @if(Auth::user()->email_verified_at)
                                                        <div class="flex items-center text-green-400 text-sm">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            <span>Email confirmado</span>
                                                        </div>
                                                    @else
                                                        <div class="flex items-center text-amber-400 text-sm">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                                            <span>Email não confirmado</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                @unless(Auth::user()->email_verified_at)
                                                    <form action="{{ route('verification.send') }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="text-cyan-400 hover:text-cyan-300 text-sm flex items-center space-x-1 transition-colors duration-200">
                                                            <i class="fas fa-paper-plane mr-1"></i>
                                                            <span>Reenviar confirmação</span>
                                                        </button>
                                                    </form>
                                                @endunless
                                            </div>
                                            
                                            @error('email')
                                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>


                                        <!-- Telefone -->
                                        <div class="mb-6">
                                            <label for="phone" class="block text-sm font-medium text-gray-400 mb-2">
                                                {{ __('profile.forms.personal_info.phone.label') }}
                                            </label>
                                            <input type="tel" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                                class="w-full bg-slate-700 border {{ $errors->has('phone') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                                                placeholder="{{ __('profile.forms.personal_info.phone.placeholder') }}">
                                            @error('phone')
                                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Botões -->
                                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                                            <button type="submit"
                                                class="flex-1 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium py-3 px-6 rounded-xl transition-colors duration-300 teal-glow-hover flex items-center justify-center">
                                                <i class="fas {{ __('profile.forms.personal_info.buttons.icons.save') }} mr-2"></i> 
                                                {{ __('profile.forms.personal_info.buttons.save') }}
                                            </button>

                                            <button type="button" onclick="resetProfileForm()"
                                                class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-6 rounded-xl transition-colors duration-300 border border-slate-600 flex items-center justify-center">
                                                <i class="fas {{ __('profile.forms.personal_info.buttons.icons.cancel') }} mr-2"></i> 
                                                {{ __('profile.forms.personal_info.buttons.cancel') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Aba Alterar Senha -->
                            <div class="hidden p-6 rounded-b-2xl flex justify-center" id="password" role="tabpanel" aria-labelledby="password-tab">
                                <div class="max-w-2xl">
                                    <h2 class="text-xl font-semibold text-white mb-6">{{ __('profile.tabs.password.title') }}</h2>

                                    <form action="{{ route('user.profile.password.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <!-- Senha Atual -->
                                        <div class="mb-6">
                                            <label for="current_password" class="block text-sm font-medium text-gray-400 mb-2">
                                                {{ __('profile.forms.password.current_password.label') }}
                                            </label>
                                            <input type="password" id="current_password" name="current_password"
                                                class="w-full bg-slate-700 border {{ $errors->has('current_password') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                                                placeholder="{{ __('profile.forms.password.current_password.placeholder') }}">
                                            @error('current_password')
                                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                            <!-- Nova Senha -->
                                            <div>
                                                <label for="new_password" class="block text-sm font-medium text-gray-400 mb-2">
                                                    {{ __('profile.forms.password.new_password.label') }}
                                                </label>
                                                <input type="password" id="new_password" name="new_password"
                                                    class="w-full bg-slate-700 border {{ $errors->has('new_password') ? 'border-red-500' : 'border-slate-600' }} rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                                                    placeholder="{{ __('profile.forms.password.new_password.placeholder') }}">
                                                @error('new_password')
                                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Confirmar Nova Senha -->
                                            <div>
                                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-400 mb-2">
                                                    {{ __('profile.forms.password.confirm_password.label') }}
                                                </label>
                                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                                    class="w-full bg-slate-700 border border-slate-600 rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                                                    placeholder="{{ __('profile.forms.password.confirm_password.placeholder') }}">
                                            </div>
                                        </div>

                                        <!-- Dicas de Segurança -->
                                        <div class="bg-slate-700/50 rounded-xl p-4 mb-6">
                                            <h4 class="text-sm font-medium text-teal-400 mb-2">
                                                <i class="fas {{ __('profile.forms.password.tips.icon') }} mr-2"></i>
                                                {{ __('profile.forms.password.tips.title') }}
                                            </h4>
                                            <ul class="text-xs text-gray-400 space-y-1">
                                                @foreach(__('profile.forms.password.tips.items') as $tip)
                                                    <li>• {{ $tip }}</li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        <!-- Botões -->
                                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                                            <button type="submit"
                                                class="flex-1 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium py-3 px-6 rounded-xl transition-colors duration-300 teal-glow-hover flex items-center justify-center">
                                                <i class="fas {{ __('profile.forms.password.buttons.icons.update') }} mr-2"></i> 
                                                {{ __('profile.forms.password.buttons.update') }}
                                            </button>

                                            <button type="button" onclick="showTab('profile')"
                                                class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-6 rounded-xl transition-colors duration-300 border border-slate-600 flex items-center justify-center">
                                                <i class="fas {{ __('profile.forms.password.buttons.icons.back') }} mr-2"></i> 
                                                {{ __('profile.forms.password.buttons.back') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Aba Configurações da Conta -->
                            <div class="hidden p-6 rounded-b-2xl flex justify-center" id="account" role="tabpanel" aria-labelledby="account-tab">
                                <div class="max-w-2xl w-full">
                                    <h2 class="text-xl font-semibold text-white mb-6">Configurações da Conta</h2>

                                    <!-- Seção de Deletar Conta -->
                                    <div class="bg-slate-700/50 rounded-xl p-6 border border-red-500/20">
                                        <div class="flex items-start space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-medium text-white mb-2">Deletar Conta</h3>
                                                <p class="text-gray-400 text-sm mb-4">
                                                    Uma vez que você deletar sua conta, não há como voltar atrás. Por favor, tenha certeza.
                                                </p>
                                                
                                                <!-- Botão para abrir o modal -->
                                                <button type="button" 
                                                        data-modal-target="delete-account-modal" 
                                                        data-modal-toggle="delete-account-modal"
                                                        class="bg-red-600 hover:bg-red-500 text-white font-medium py-2 px-4 rounded-xl transition-colors duration-200 flex items-center space-x-2">
                                                    <i class="fas fa-trash mr-2"></i>
                                                    <span>Deletar Minha Conta</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    @include('components.modals.modal-change-email')
    @include('components.modals.modal-delete-account')
@endpush

@push('scripts')
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
                
                submitBtn.innerHTML = '<i class="fas {{ __('profile.processing.icon') }} mr-2"></i> {{ __('profile.processing.text') }}';
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
@endpush