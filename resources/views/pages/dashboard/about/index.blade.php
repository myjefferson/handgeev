@extends('template.dashboard')

@section('content_dashboard')
    <div class="max-w-4xl mx-auto px-4 py-8 pt-24">
        <!-- Cabeçalho -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold gradient-text mb-3">Sobre o Handgeev</h1>
            <p class="text-slate-400 text-lg">Conheça mais sobre nossa plataforma e sua conta</p>
        </div>

        <!-- Card de informações da conta -->
        <div class="bg-slate-800 rounded-xl p-6 mb-10 border-l-4 border-cyan-400 card-hover">
            <h2 class="text-xl font-semibold text-slate-100 mb-4 flex items-center">
                <i class="fas fa-user-circle mr-2 text-cyan-400"></i> Sua Conta
            </h2>
            
            <div class="flex flex-wrap items-center gap-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                        <i class="fas fa-user text-cyan-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-slate-400">Tipo de conta</p>
                        <span class="account-badge px-3 py-1 rounded-full text-sm font-medium">
                            @if(auth()->user()->account_type == 'admin')
                                <i class="fas fa-crown mr-1"></i> Administrador
                            @elseif(auth()->user()->account_type == 'pro')
                                <i class="fas fa-star mr-1"></i> Pro
                            @else
                                <i class="fas fa-user mr-1"></i> Gratuita
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                        <i class="fas fa-code text-cyan-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-slate-400">APIs criadas</p>
                        <p class="font-semibold text-slate-100">{{ $api_count ?? '0' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-cyan-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-slate-400">Membro desde</p>
                        <p class="font-semibold text-slate-100">{{ auth()->user()->created_at->format('M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações sobre o Handgeev -->
        <div class="grid md:grid-cols-2 gap-6 mb-10">
            <div class="bg-slate-800 rounded-xl p-6 card-hover border border-slate-700">
                <div class="w-14 h-14 rounded-full bg-slate-700 flex items-center justify-center mb-4">
                    <i class="fas fa-rocket text-2xl text-cyan-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-100 mb-2">O que é o Handgeev?</h3>
                <p class="text-slate-400">
                    Handgeev é uma solução inovadora que simplifica a criação de sua API própria, 
                    facilitando o acesso e implementação em seus projetos. Crie, teste e implemente 
                    APIs de forma intuitiva e eficiente.
                </p>
            </div>
            
            <div class="bg-slate-800 rounded-xl p-6 card-hover border border-slate-700">
                <div class="w-14 h-14 rounded-full bg-slate-700 flex items-center justify-center mb-4">
                    <i class="fas fa-cogs text-2xl text-cyan-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-100 mb-2">Como funciona?</h3>
                <p class="text-slate-400">
                    Nossa plataforma oferece ferramentas para criar endpoints, gerenciar dados e 
                    integrar com seus projetos. Tudo com documentação automática e exemplos de código 
                    para acelerar seu desenvolvimento.
                </p>
            </div>
        </div>

        <!-- Informações de versão e desenvolvedor -->
        <div class="bg-slate-800 rounded-xl p-6 mb-10 card-hover border border-slate-700">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="text-center md:text-left">
                    <p class="text-slate-400 mb-1">Versão atual</p>
                    <p class="text-2xl font-bold text-cyan-400">{{ env('APP_VERSION') }}</p>
                </div>
                
                <div class="text-center text-left md:text-center">
                    <p class="text-slate-400 mb-1">Desenvolvido por</p>
                    <p class="text-xl font-semibold text-slate-100">Jefferson Carvalho</p>
                </div>
                
                <div class="text-center md:text-left">
                    <p class="text-slate-400 mb-1">Contato</p>
                    <div class="flex justify-center md:justify-start space-x-3">
                        {{-- <a href="#" class="text-cyan-400 hover:text-cyan-300">
                            <i class="fab fa-github text-xl"></i>
                        </a> --}}
                        <a href="#" class="text-cyan-400 hover:text-cyan-300">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                        <a href="#" class="text-cyan-400 hover:text-cyan-300">
                            <i class="fas fa-envelope text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chamada para contribuição -->
        <div class="bg-slate-800 rounded-xl p-6 text-center card-hover border border-slate-700">
            <h3 class="text-2xl font-semibold text-slate-100 mb-3">Quer ajudar a melhorar o Handgeev?</h3>
            <p class="text-slate-400 mb-5">Sua contribuição é bem-vinda! Envie sugestões, reporte bugs ou contribua com código.</p>
            <div class="flex justify-center space-x-4">
                <button class="bg-cyan-500 hover:bg-cyan-600 text-slate-900 px-5 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-bug mr-2"></i> Reportar Problema
                </button>
                {{-- <button class="bg-slate-700 hover:bg-slate-600 text-slate-200 px-5 py-2 rounded-lg font-medium transition-colors">
                    <i class="fab fa-github mr-2"></i> Contribuir
                </button> --}}
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .gradient-text {
            background: linear-gradient(90deg, #08fff0, #00b3a8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(8, 255, 240, 0.15), 0 10px 10px -5px rgba(8, 255, 240, 0.1);
        }
        .account-badge {
            background-color: rgba(8, 255, 240, 0.15);
            color: #08fff0;
            border: 1px solid #08fff0;
        }
    </style>
@endpush