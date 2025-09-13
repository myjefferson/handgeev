@extends('template.template-site')

@section('content_site')
<div class="min-h-screen bg-slate-900 flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-slate-800 rounded-2xl shadow-xl p-8 border border-orange-500/30">
        <!-- Ícone de Conta Inativa -->
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-orange-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-clock text-orange-500 text-4xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white mb-2">Conta Inativa</h1>
            <p class="text-gray-400">Sua conta está temporariamente inativa</p>
        </div>

        <!-- Mensagem de Status -->
        <div class="bg-slate-700/50 rounded-xl p-4 mb-6 border border-orange-500/20">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-orange-400 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-medium text-white mb-1">Status da Conta</h3>
                    <p class="text-gray-400 text-sm">Sua conta foi marcada como inativa devido à inatividade prolongada ou solicitação do administrador.</p>
                </div>
            </div>
        </div>

        <!-- Detalhes da Inatividade -->
        <div class="space-y-4 mb-8">
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Data da Inatividade</span>
                <span class="text-white font-medium">15/11/2023</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Tipo de Inatividade</span>
                <span class="text-white font-medium">Automática por não uso</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Status</span>
                <span class="px-3 py-1 bg-orange-500/20 text-orange-400 rounded-full text-sm">Inativa</span>
            </div>
        </div>

        <!-- Processo de Reativação -->
        <div class="bg-teal-400/10 rounded-xl p-4 mb-6 border border-teal-400/30">
            <h3 class="font-medium text-teal-400 mb-3 flex items-center">
                <i class="fas fa-sync-alt mr-2"></i> Reative sua conta
            </h3>
            <p class="text-gray-400 text-sm mb-3">Para reativar sua conta, solicite a reativação ao administrador do sistema ou clique no botão abaixo para enviar uma solicitação automática.</p>
            
            <button class="w-full bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium py-3 px-4 rounded-xl transition-colors duration-300 teal-glow-hover flex items-center justify-center">
                <i class="fas fa-paper-plane mr-2"></i> Solicitar Reativação
            </button>
        </div>

        <!-- Ações Secundárias -->
        <div class="space-y-3">
            <button class="w-full bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-4 rounded-xl transition-colors duration-300 border border-slate-600 flex items-center justify-center">
                <i class="fas fa-user-cog mr-2"></i> Ver Perfil
            </button>
            
            <a href="{{ route('logout') }}" class="block w-full text-center text-gray-400 hover:text-teal-400 transition-colors duration-300 mt-4">
                <i class="fas fa-sign-out-alt mr-2"></i> Fazer Logout
            </a>
        </div>

        <!-- Informações de Contato -->
        <div class="mt-8 pt-6 border-t border-slate-700">
            <h3 class="text-sm font-medium text-gray-400 mb-3">Dúvidas sobre reativação?</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center">
                    <i class="fas fa-envelope text-teal-400 mr-3 w-5"></i>
                    <span class="text-gray-400">admin@handgeev.com</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-user-shield text-teal-400 mr-3 w-5"></i>
                    <span class="text-gray-400">Administrador do Sistema</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .teal-glow {
        box-shadow: 0 0 25px rgba(0, 230, 216, 0.15);
    }
    
    .teal-glow-hover:hover {
        box-shadow: 0 0 20px rgba(0, 230, 216, 0.3);
    }
</style>
@endsection