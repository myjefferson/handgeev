@extends('template.template-dashboard')

@section('content_dashboard')
<div class="min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Cabeçalho -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Meu Perfil</h1>
            <p class="text-gray-400">Gerencie suas informações pessoais e preferências</p>
            <div class="w-20 h-1 bg-teal-400 rounded-full mt-3"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Lateral - Avatar e Status -->
            <div class="lg:col-span-1">
                <div class="bg-slate-800 rounded-2xl shadow-lg p-6 border border-slate-700">
                    <!-- Avatar -->
                    <div class="text-center mb-6">
                        <div class="w-32 h-32 bg-teal-400/10 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-teal-400/30">
                            <i class="fas fa-user text-teal-400 text-5xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-white">{{ Auth::user()->name }} {{ Auth::user()->surname }}</h2>
                        <p class="text-gray-400">{{ Auth::user()->email }}</p>
                        
                        {{-- <!-- Status da Conta -->
                        <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm
                            @if(Auth::user()->status === 'active') bg-green-500/20 text-green-400
                            @elseif(Auth::user()->status === 'inactive') bg-orange-500/20 text-orange-400
                            @else bg-red-500/20 text-red-400 @endif">
                            <i class="fas fa-circle text-xs mr-2"></i>
                            {{ ucfirst(Auth::user()->status) }}
                        </div> --}}
                    </div>

                    <!-- Plano do Usuário -->
                    <div class="bg-slate-700/50 rounded-xl p-4 mb-6">
                        <h3 class="text-sm font-medium text-gray-400 mb-2">Plano Atual</h3>
                        <div class="flex items-center">
                            @if(Auth::user()->isPro())
                                <i class="fas fa-crown text-purple-400 mr-2"></i>
                                <span class="text-white font-medium">Pro</span>
                            @elseif(Auth::user()->isAdmin())
                                <i class="fas fa-shield-alt text-blue-400 mr-2"></i>
                                <span class="text-white font-medium">Admin</span>
                            @else
                                <i class="fas fa-user text-teal-400 mr-2"></i>
                                <span class="text-white font-medium">Free</span>
                            @endif
                        </div>
                    </div>

                    <!-- Estatísticas Rápidas -->
                    <div class="space-y-3">
                        <h3 class="text-sm font-medium text-gray-400 mb-2">Estatísticas</h3>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Workspaces</span>
                            <span class="text-white font-medium">{{ count($workspaces ?? []) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Tópicos</span>
                            <span class="text-white font-medium">{{ count(auth()->user()->topics ?? []) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Campos</span>
                            {{-- <span class="text-white font-medium">{{ 3 ?? []) }}</span> --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna Principal - Formulário de Edição -->
            <div class="lg:col-span-2">
                <div class="bg-slate-800 rounded-2xl shadow-lg p-6 border border-slate-700">
                    <h2 class="text-xl font-semibold text-white mb-6">Informações Pessoais</h2>

                    <form id="profileForm" action="{{ route('user.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Nome -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-400 mb-2">Nome</label>
                                <input type="text" id="name" name="name" value="{{ Auth::user()->name }}"
                                    class="w-full bg-slate-700 border border-slate-600 rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                            </div>

                            <!-- Sobrenome -->
                            <div>
                                <label for="surname" class="block text-sm font-medium text-gray-400 mb-2">Sobrenome</label>
                                <input type="text" id="surname" name="surname" value="{{ Auth::user()->surname }}"
                                    class="w-full bg-slate-700 border border-slate-600 rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-400 mb-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}"
                                class="w-full bg-slate-700 border border-slate-600 rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors">
                        </div>

                        <!-- Telefone -->
                        <div class="mb-6">
                            <label for="phone" class="block text-sm font-medium text-gray-400 mb-2">Telefone</label>
                            <input type="tel" id="phone" name="phone" value="{{ Auth::user()->phone ?? '' }}"
                                class="w-full bg-slate-700 border border-slate-600 rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                                placeholder="(00) 00000-0000">
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit"
                                class="flex-1 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium py-3 px-6 rounded-xl transition-colors duration-300 teal-glow-hover flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i> Salvar Alterações
                            </button>

                            <button type="button" onclick="resetForm()"
                                class="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-6 rounded-xl transition-colors duration-300 border border-slate-600 flex items-center justify-center">
                                <i class="fas fa-undo mr-2"></i> Cancelar
                            </button>
                        </div>
                    </form>

                    <!-- Seção de Segurança -->
                    <div class="mt-8 pt-6 border-t border-slate-700">
                        <h3 class="text-lg font-semibold text-white mb-4">Segurança</h3>
                        
                        <div class="space-y-4">
                            <a href="#"
                                class="flex items-center justify-between p-4 bg-slate-700/50 rounded-xl hover:bg-slate-700 transition-colors duration-300">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-teal-400/10 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-lock text-teal-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-white">Alterar Senha</h4>
                                        <p class="text-sm text-gray-400">Atualize sua senha regularmente</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </a>

                            {{-- <a href="{{ route('2fa.settings') }}"
                                class="flex items-center justify-between p-4 bg-slate-700/50 rounded-xl hover:bg-slate-700 transition-colors duration-300">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-teal-400/10 rounded-full flex items-center justify-center mr-4">
                                        <i class="fas fa-shield-alt text-teal-400"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-white">Autenticação Two-Factor</h4>
                                        <p class="text-sm text-gray-400">Proteja sua conta com 2FA</p>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById('profileForm').reset();
    }

    // Máscara para telefone
    $(document).ready(function(){
        $('#phone').mask('(00) 00000-0000');
    });

    // Validação do formulário
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Simulação de salvamento (substituir por AJAX real)
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
        submitBtn.disabled = true;
        
        setTimeout(() => {
            // Simular sucesso
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Salvo!';
            submitBtn.classList.remove('bg-teal-500', 'hover:bg-teal-400');
            submitBtn.classList.add('bg-green-500', 'hover:bg-green-400');
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-green-500', 'hover:bg-green-400');
                submitBtn.classList.add('bg-teal-500', 'hover:bg-teal-400');
            }, 2000);
        }, 1500);
    });
</script>

<style>
    .teal-glow {
        box-shadow: 0 0 25px rgba(0, 230, 216, 0.15);
    }
    
    .teal-glow-hover:hover {
        box-shadow: 0 0 20px rgba(0, 230, 216, 0.3);
    }

    input:focus, select:focus {
        box-shadow: 0 0 0 3px rgba(0, 230, 216, 0.1);
    }
</style>
@endsection