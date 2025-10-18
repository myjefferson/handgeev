@extends('template.template-site')

@section('title', __('errors.server_error'))
@section('description', __('errors.server_error_description'))

@push('style')    
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
        }
    </style>
@endpush

@section('content_site')
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="text-center">
            <!-- Logo -->
            <div class="mb-8">
                <img class="w-48 mx-auto" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev Logo">
            </div>

            <!-- Ícone de Erro -->
            <div class="mb-6">
                <div class="w-24 h-24 mx-auto bg-orange-500/10 rounded-full flex items-center justify-center border border-orange-500/20">
                    <i class="fas fa-server text-orange-400 text-3xl"></i>
                </div>
            </div>

            <!-- Mensagem -->
            <h1 class="text-6xl font-bold text-orange-400 mb-4">500</h1>
            <h2 class="text-2xl font-semibold mb-4">{{ __('errors.server_error') }}</h2>
            <p class="text-gray-400 mb-8 max-w-md mx-auto">
                {{ __('errors.500_message') }}
            </p>

            <!-- Botões de Ação -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/') }}" class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    <i class="fas fa-home mr-2"></i>{{ __('errors.back_to_home') }}
                </a>
                
                <button onclick="window.location.reload()" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    <i class="fas fa-redo-alt mr-2"></i>{{ __('errors.try_again') }}
                </button>
                
                @auth
                    <a href="{{ route('dashboard.home') }}" class="border border-teal-500 text-teal-400 hover:bg-teal-500/10 font-semibold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-tachometer-alt mr-2"></i>{{ __('errors.go_to_dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login.show') }}" class="border border-teal-500 text-teal-400 hover:bg-teal-500/10 font-semibold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>{{ __('errors.login') }}
                    </a>
                @endauth
            </div>

            <!-- Informações Adicionais -->
            <div class="mt-8 text-sm text-gray-500">
                <p>{{ __('errors.persistent_issue') }}</p>
                <p class="mt-2">{{ __('errors.error_code') }}: {{ __('errors.internal_server_error') }}</p>
            </div>

            <!-- Contador para recarregar automaticamente -->
            <div class="mt-6">
                <p class="text-gray-500 text-sm">{{ __('errors.auto_reload') }} <span id="countdown">10</span> {{ __('errors.seconds') }}...</p>
            </div>
        </div>
    </div>
    @include('components.footer.footer')
@endsection

@push('scripts')
    <script>
        // Contador para recarregar automaticamente
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');
        
        const countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                window.location.reload();
            }
        }, 1000);
    </script>
@endpush