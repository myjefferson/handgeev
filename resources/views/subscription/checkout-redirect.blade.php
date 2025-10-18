@extends('template.template-site')

@section('title', 'Finalizar Assinatura - HandGeev')

@push('style')
<style>
    .countdown-circle {
        width: 60px;
        height: 60px;
        border: 3px solid #0d9488;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.25rem;
        position: relative;
    }
    
    .countdown-circle::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        border: 3px solid transparent;
        border-top: 3px solid #14b8a6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .pulse-glow {
        animation: pulse-glow 2s infinite;
    }
    
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 20px rgba(20, 184, 166, 0.3); }
        50% { box-shadow: 0 0 30px rgba(20, 184, 166, 0.6); }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .progress-bar {
        height: 4px;
        background: #334155;
        border-radius: 2px;
        overflow: hidden;
        margin: 20px 0;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #0d9488, #14b8a6);
        border-radius: 2px;
        transition: width 0.3s ease;
    }
</style>
@endpush

@section('content_site')
<div class="min-h-screen bg-slate-900 flex items-center justify-center p-4 fade-in-up">
    <div class="max-w-md w-full">
        <div class="bg-slate-800 rounded-2xl p-8 border border-slate-700 teal-glow pulse-glow">
            <!-- Cabeçalho com ícone animado -->
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 relative">
                    <i class="fas fa-credit-card text-white text-2xl"></i>
                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-green-400 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-xs"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">Quase lá!</h1>
                <p class="text-slate-400">
                    Prepare-se para finalizar sua assinatura <span class="text-teal-400 font-semibold">{{ $planName }}</span>
                </p>
            </div>

            <!-- Card de informações -->
            <div class="bg-slate-700/50 rounded-lg p-4 mb-6 border border-slate-600">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-8 h-8 bg-teal-500/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-gem text-teal-400 text-sm"></i>
                    </div>
                    <div>
                        <div class="text-slate-300 text-sm">Plano selecionado</div>
                        <div class="text-teal-400 font-semibold">{{ $planName }}</div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-shield-alt text-green-400 text-sm"></i>
                    </div>
                    <div>
                        <div class="text-slate-300 text-sm">Próxima etapa</div>
                        <div class="text-green-400 font-semibold">Pagamento seguro Stripe</div>
                    </div>
                </div>
            </div>

            <!-- Contagem regressiva visual -->
            <div class="text-center mb-6">
                <div class="flex flex-col items-center space-y-4">
                    <div class="countdown-circle text-teal-400" id="countdown-number">
                        3
                    </div>
                    <div class="text-slate-400 text-sm">
                        Redirecionando automaticamente em <span id="countdown-text" class="text-teal-400 font-semibold">3 segundos</span>
                    </div>
                </div>
                
                <!-- Barra de progresso -->
                <div class="progress-bar mt-4">
                    <div class="progress-fill" id="progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <!-- Botão de ação principal -->
            <form action="{{ route('subscription.checkout') }}" method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="price_id" value="{{ $priceId }}">
                
                <button type="submit" class="w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 teal-glow-hover flex items-center justify-center space-x-2 group">
                    <i class="fas fa-lock group-hover:scale-110 transition-transform"></i>
                    <span>Ir para Pagamento Seguro</span>
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <!-- Botão secundário -->
            <div class="mt-4 text-center">
                <button onclick="cancelRedirect()" class="text-slate-400 hover:text-slate-300 text-sm transition-colors flex items-center justify-center space-x-1 mx-auto">
                    <i class="fas fa-times mr-1"></i>
                    <span>Cancelar redirecionamento automático</span>
                </button>
            </div>
        </div>

        <!-- Informações de segurança -->
        <div class="mt-6 text-center">
            <div class="flex items-center justify-center space-x-4 text-slate-500 text-sm">
                <div class="flex items-center space-x-1">
                    <i class="fas fa-shield-alt text-green-400"></i>
                    <span>Pagamento seguro</span>
                </div>
                <div class="flex items-center space-x-1">
                    <i class="fas fa-lock text-teal-400"></i>
                    <span>Criptografado</span>
                </div>
                <div class="flex items-center space-x-1">
                    <i class="fas fa-bolt text-yellow-400"></i>
                    <span>Processamento rápido</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let countdown = 3;
    let countdownInterval;
    let progressInterval;
    const progressBar = document.getElementById('progress-bar');
    const countdownNumber = document.getElementById('countdown-number');
    const countdownText = document.getElementById('countdown-text');
    const form = document.getElementById('checkout-form');

    function startCountdown() {
        // Iniciar barra de progresso
        let progress = 0;
        const progressStep = 100 / (countdown * 10); // 10 updates per second
        
        progressInterval = setInterval(() => {
            progress += progressStep;
            progressBar.style.width = Math.min(progress, 100) + '%';
        }, 100);

        // Iniciar contagem regressiva
        countdownInterval = setInterval(() => {
            countdown--;
            
            // Atualizar número e texto
            countdownNumber.textContent = countdown;
            countdownText.textContent = countdown + ' segundo' + (countdown !== 1 ? 's' : '');
            
            // Efeito visual no número
            countdownNumber.style.transform = 'scale(1.2)';
            setTimeout(() => {
                countdownNumber.style.transform = 'scale(1)';
            }, 200);

            // Quando chegar a zero, submeter formulário
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                
                // Pequeno delay antes do submit para ver o 100%
                setTimeout(() => {
                    showLoadingState();
                    form.submit();
                }, 300);
            }
        }, 1000);
    }

    function cancelRedirect() {
        clearInterval(countdownInterval);
        clearInterval(progressInterval);
        
        // Reset visual
        countdownNumber.textContent = '✓';
        countdownNumber.style.color = '#10b981';
        countdownNumber.style.borderColor = '#10b981';
        countdownText.textContent = 'Redirecionamento cancelado';
        countdownText.className = 'text-green-400 font-semibold';
        progressBar.style.width = '100%';
        progressBar.style.background = '#10b981';
        
        // Mostrar mensagem
        setTimeout(() => {
            countdownText.textContent = 'Clique no botão acima quando estiver pronto';
        }, 1000);
    }

    function showLoadingState() {
        countdownNumber.textContent = '🚀';
        countdownText.textContent = 'Redirecionando...';
        countdownText.className = 'text-teal-400 font-semibold animate-pulse';
    }

    // Iniciar quando a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🎯 Página de redirecionamento carregada - Iniciando contagem');
        startCountdown();
        
        // Adicionar efeito de digitação no título (opcional)
        const title = document.querySelector('h1');
        if (title) {
            title.style.animation = 'fadeInUp 0.8s ease-out';
        }
    });

    // Prevenir múltiplos submits
    let formSubmitted = false;
    form.addEventListener('submit', function(e) {
        if (formSubmitted) {
            e.preventDefault();
            return;
        }
        formSubmitted = true;
        showLoadingState();
    });
</script>
@endsection