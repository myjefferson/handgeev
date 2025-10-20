@extends('template.template-site')

@section('title', 'Conta Suspensa')
@section('description', 'Conta suspensa devido a violação dos termos de uso da plataforma - uso excessivo de recursos do sistema.')

@section('content_site')
<div class="min-h-screen bg-slate-900 flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-slate-800 rounded-2xl shadow-xl p-8 border border-red-500/30">
        <!-- Ícone de Alerta -->
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Conta Suspensa</h1>
            <p class="text-gray-400">Sua conta foi temporariamente suspensa</p>
        </div>

        <!-- Mensagem de Suspensão -->
        <div class="bg-slate-700/50 rounded-xl p-4 mb-6 border border-red-500/20">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-red-400 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-medium text-white mb-1">Motivo da Suspensão</h3>
                    <p class="text-gray-400 text-sm">Violação dos termos de uso da plataforma - uso excessivo de recursos do sistema.</p>
                </div>
            </div>
        </div>

        <!-- Detalhes da Suspensão -->
        <div class="space-y-4 mb-8">
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Data da Suspensão</span>
                <span class="text-white font-medium">25/11/2025</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Previsão de Liberação</span>
                <span class="text-white font-medium">02/12/2025</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Status</span>
                <span class="px-3 py-1 bg-red-500/20 text-red-400 rounded-full text-sm">Suspenso</span>
            </div>
        </div>

        <!-- Ações -->
        <div class="space-y-3">
            <button class="w-full bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium py-3 px-4 rounded-xl transition-colors duration-300 teal-glow-hover">
                <i class="fas fa-envelope mr-2"></i> Entrar em Contato com Suporte
            </button>
            
            <button class="w-full bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-4 rounded-xl transition-colors duration-300 border border-slate-600">
                <i class="fas fa-file-alt mr-2"></i> Ver Termos de Uso
            </button>
            
            <a href="{{ route('login.show') }}" class="block w-full text-center text-gray-400 hover:text-teal-400 transition-colors duration-300 mt-4">
                <i class="fas fa-sign-out-alt mr-2"></i> Fazer Logout
            </a>
        </div>

        <!-- Informações de Contato -->
        <div class="mt-8 pt-6 border-t border-slate-700">
            <h3 class="text-sm font-medium text-gray-400 mb-3">Precisa de ajuda?</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center">
                    <i class="fas fa-envelope text-teal-400 mr-3 w-5"></i>
                    <span class="text-gray-400">suporte@handgeev.com</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-phone text-teal-400 mr-3 w-5"></i>
                    <span class="text-gray-400">+55 (11) 99999-9999</span>
                </div>
            </div>
        </div>
    </div>
</div>

@include('components.footer.footer')

<style>
    .teal-glow {
        box-shadow: 0 0 25px rgba(0, 230, 216, 0.15);
    }
    
    .teal-glow-hover:hover {
        box-shadow: 0 0 20px rgba(0, 230, 216, 0.3);
    }
</style>
@endsection