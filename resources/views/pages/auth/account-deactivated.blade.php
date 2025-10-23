@extends('template.template-site')

@section('title', 'Até Breve - Sua Conta foi Desativada')
@section('description', 'Sua Conta foi Desativada')

@push('style')
        <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
        }
        
        .floating-heart {
            animation: float 3s ease-in-out infinite;
        }
        
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(6, 182, 212, 0.3); }
            50% { box-shadow: 0 0 30px rgba(6, 182, 212, 0.6); }
        }
        
        .fade-in {
            animation: fadeIn 1.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .countdown-number {
            background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .urgent-pulse {
            animation: urgent-pulse 1s ease-in-out infinite;
        }
        
        @keyframes urgent-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
@endpush

@section('content_site')
    <div class="max-w-2xl mx-auto py-20 w-full text-center fade-in">
        <!-- Ícone emocional -->
        <div class="floating-heart mb-8">
            <div class="w-32 h-32 mx-auto bg-cyan-500/10 rounded-full flex items-center justify-center pulse-glow border border-cyan-500/30">
                <i class="fas fa-heart text-cyan-400 text-5xl"></i>
            </div>
        </div>

        <!-- Mensagem principal -->
        <div class="mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                Até <span class="text-cyan-400">Breve</span> 💫
            </h1>
            
            <p class="text-xl text-gray-300 mb-6 leading-relaxed">
                Sua jornada conosco fez parte de uma história incrível...
            </p>
        </div>

        @include('components.alerts.alert')

        <!-- Card informativo -->
        <div class="bg-slate-800/50 backdrop-blur-sm rounded-2xl p-8 border border-slate-700 mb-8">
            <div class="flex items-center justify-center mb-4">
                <div class="w-12 h-12 bg-amber-500/20 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-clock text-amber-400 text-xl"></i>
                </div>
                <h2 class="text-2xl font-semibold text-white">Tempo para Reflexão</h2>
            </div>
            
            <p class="text-gray-300 text-lg mb-6">
                Sua conta foi <span class="text-amber-300 font-medium">desativada</span> e será 
                <span class="text-red-400 font-medium">permanentemente removida</span> em:
            </p>
            
            <!-- Contador visual dinâmico -->
            @if(session('deactivated_account_access'))
                @php
                    $deactivatedData = session('deactivated_account_access');
                    $days_remaining = $deactivatedData['days_remaining'];
                    $user_name = $deactivatedData['name'];
                @endphp
            @endif
            
            <!-- Alerta urgente se faltam poucos dias -->
            @if($days_remaining <= 7)
            <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                    <span class="text-red-300 font-medium">Tempo Limitado!</span>
                </div>
                <p class="text-red-200 text-sm mt-1">
                    Restam apenas {{ $days_remaining }} {{ $days_remaining == 1 ? 'dia' : 'dias' }} para recuperar sua conta!
                </p>
            </div>
            @endif
            
            <div class="bg-slate-700/50 rounded-xl p-4 border border-slate-600">
                <p class="text-cyan-300 text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    Você tem {{ $days_remaining }} {{ $days_remaining == 1 ? 'dia' : 'dias' }} para recuperar sua conta com todos os dados intactos.
                </p>
            </div>
        </div>

        <!-- Mensagem emocional -->
        <div class="bg-gradient-to-r from-purple-500/10 to-cyan-500/10 rounded-2xl p-6 border border-purple-500/20 mb-8">
            <div class="flex items-start">
                <i class="fas fa-quote-left text-purple-400 text-2xl mr-4 mt-1"></i>
                <div class="text-left">
                    <p class="text-gray-200 text-xl font-semibold mb-4">
                        "Seu legado ainda está aqui, esperando para ser retomado. A sua melhor versão merece um final diferente."
                    </p>
                    <p class="text-gray-400">
                        Todas as suas conquistas, workspaces e ideias foram guardadas com carinho. Sua história ainda não acabou.
                    </p>
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="space-y-4">
            <!-- Botão de recuperação -->
            <div class="bg-green-500/10 rounded-xl p-4 border border-green-500/20">
                <p class="text-green-300 text-sm mb-3">
                    <i class="fas fa-heart-circle-check mr-2"></i>
                    Você pode recuperar sua conta a qualquer momento durante os próximos {{ $days_remaining }} {{ $days_remaining == 1 ? 'dia' : 'dias' }}.
                </p>
                
                <!-- Formulário de recuperação -->
                <form action="{{ route('account.restore') }}" method="POST">
                    @csrf
                    <button type="submit" 
                           class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-500 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-rotate-left mr-2"></i>
                        Recuperar Minha Conta
                    </button>
                </form>
            </div>

            <!-- Logout -->
            <div>
                <form action="{{ route('logout') }}" method="GET">
                    @csrf
                    <button type="submit" 
                           class="inline-flex items-center text-gray-400 hover:text-gray-300 transition-colors duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Sair
                    </button>
                </form>
            </div>
        </div>

        <!-- Rodapé emocional -->
        <div class="mt-12 pt-6 border-t border-slate-700">
            <p class="text-gray-500 text-sm">
                Com gratidão por fazer parte da nossa história 
                <i class="fas fa-heart text-red-400 ml-1"></i>
            </p>
        </div>
    </div>

    <!-- Efeitos de partículas simples -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-[-1]">
        <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-cyan-400 rounded-full opacity-20 animate-pulse"></div>
        <div class="absolute top-1/3 right-1/4 w-1 h-1 bg-purple-400 rounded-full opacity-30 animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-1/4 left-1/3 w-3 h-3 bg-amber-400 rounded-full opacity-10 animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-1/3 right-1/3 w-1 h-1 bg-green-400 rounded-full opacity-25 animate-pulse" style="animation-delay: 1.5s;"></div>
    </div>

    @include('components.footer.footer')
@endsection