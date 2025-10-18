@extends('template.template-site')

@section('title', __('errors.page_not_found'))
@section('description', __('errors.page_not_found_description'))

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
                <div class="w-24 h-24 mx-auto bg-red-500/10 rounded-full flex items-center justify-center border border-red-500/20">
                    <i class="fas fa-exclamation-triangle text-red-400 text-3xl"></i>
                </div>
            </div>

            <!-- Mensagem -->
            <h1 class="text-6xl font-bold text-red-400 mb-4">404</h1>
            <h2 class="text-2xl font-semibold mb-4">{{ __('errors.page_not_found') }}</h2>
            <p class="text-gray-400 mb-8 max-w-md mx-auto">
                {{ __('errors.404_message') }}
            </p>

            <!-- Botões de Ação -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/') }}" class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                    <i class="fas fa-home mr-2"></i>{{ __('errors.back_to_home') }}
                </a>
                
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
                <p>{{ __('errors.support_contact') }}</p>
            </div>
        </div>
    </div>
    @include('components.footer.footer')
@endsection