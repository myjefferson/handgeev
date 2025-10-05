@extends('template.template-dashboard')

@section('title', __('about.title'))

@section('content_dashboard')
    <div class="max-w-4xl mx-auto px-4 py-8 pt-24">
        <!-- Cabeçalho -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold gradient-text mb-3">{{ __('about.title') }}</h1>
            <p class="text-slate-400 text-lg">{{ __('about.description') }}</p>
        </div>

        <!-- Card de informações da conta -->
        <div class="bg-slate-800 rounded-xl p-6 mb-10 border-l-4 border-cyan-400 card-hover">
            <h2 class="text-xl font-semibold text-slate-100 mb-4 flex items-center">
                <i class="fas fa-user-circle mr-2 text-cyan-400" aria-label="{{ __('about.icons.user') }}"></i> 
                {{ __('about.account.title') }}
            </h2>
            
            <div class="flex flex-wrap items-center gap-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                        <i class="fas fa-user text-cyan-400" aria-label="{{ __('about.icons.user') }}"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-slate-400">{{ __('about.account.account_type') }}</p>
                        <span class="account-badge px-3 py-1 rounded-full text-sm font-medium">
                            @if(auth()->user()->account_type == 'admin')
                                <i class="fas fa-crown mr-1" aria-label="{{ __('about.icons.crown') }}"></i> 
                                {{ __('about.account.badges.admin') }}
                            @elseif(auth()->user()->account_type == 'pro')
                                <i class="fas fa-star mr-1" aria-label="{{ __('about.icons.star') }}"></i> 
                                {{ __('about.account.badges.pro') }}
                            @else
                                <i class="fas fa-user mr-1" aria-label="{{ __('about.icons.user') }}"></i> 
                                {{ __('about.account.badges.free') }}
                            @endif
                        </span>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                        <i class="fas fa-code text-cyan-400" aria-label="{{ __('about.icons.code') }}"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-slate-400">{{ __('about.account.apis_created') }}</p>
                        <p class="font-semibold text-slate-100">{{ $api_count ?? '0' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-cyan-400" aria-label="{{ __('about.icons.calendar') }}"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-slate-400">{{ __('about.account.member_since') }}</p>
                        <p class="font-semibold text-slate-100">{{ auth()->user()->created_at->format('M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações sobre o Handgeev -->
        <div class="grid md:grid-cols-2 gap-6 mb-10">
            <div class="bg-slate-800 rounded-xl p-6 card-hover border border-slate-700">
                <div class="w-14 h-14 rounded-full bg-slate-700 flex items-center justify-center mb-4">
                    <i class="fas fa-rocket text-2xl text-cyan-400" aria-label="{{ __('about.icons.rocket') }}"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-100 mb-2">{{ __('about.what_is_handgeev.title') }}</h3>
                <p class="text-slate-400">
                    {{ __('about.what_is_handgeev.description') }}
                </p>
            </div>
            
            <div class="bg-slate-800 rounded-xl p-6 card-hover border border-slate-700">
                <div class="w-14 h-14 rounded-full bg-slate-700 flex items-center justify-center mb-4">
                    <i class="fas fa-cogs text-2xl text-cyan-400" aria-label="{{ __('about.icons.cogs') }}"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-100 mb-2">{{ __('about.how_it_works.title') }}</h3>
                <p class="text-slate-400">
                    {{ __('about.how_it_works.description') }}
                </p>
            </div>
        </div>

        <!-- Informações de versão e desenvolvedor -->
        <div class="bg-slate-800 rounded-xl p-6 mb-10 card-hover border border-slate-700">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="text-center md:text-left">
                    <p class="text-slate-400 mb-1">{{ __('about.version.current_version') }}</p>
                    <p class="text-2xl font-bold text-cyan-400">{{ env('APP_VERSION') }}</p>
                </div>
                
                <div class="text-center text-left md:text-center">
                    <p class="text-slate-400 mb-1">{{ __('about.version.developed_by') }}</p>
                    <p class="text-xl font-semibold text-slate-100">{{ __('about.version.developer_name') }}</p>
                </div>
                
                <div class="text-center md:text-left">
                    <p class="text-slate-400 mb-1">{{ __('about.version.contact') }}</p>
                    <div class="flex justify-center md:justify-start space-x-3">
                        {{-- <a href="#" class="text-cyan-400 hover:text-cyan-300" aria-label="GitHub">
                            <i class="fab fa-github text-xl"></i>
                        </a> --}}
                        <a href="#" class="text-cyan-400 hover:text-cyan-300" aria-label="LinkedIn">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                        <a href="#" class="text-cyan-400 hover:text-cyan-300" aria-label="Email">
                            <i class="fas fa-envelope text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chamada para contribuição -->
        <div class="bg-slate-800 rounded-xl p-6 text-center card-hover border border-slate-700">
            <h3 class="text-2xl font-semibold text-slate-100 mb-3">{{ __('about.contribution.title') }}</h3>
            <p class="text-slate-400 mb-5">{{ __('about.contribution.description') }}</p>
            <div class="flex justify-center space-x-4">
                <button class="bg-cyan-500 hover:bg-cyan-600 text-slate-900 px-5 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-bug mr-2" aria-label="{{ __('about.icons.bug') }}"></i> 
                    {{ __('about.contribution.report_issue') }}
                </button>
                {{-- <button class="bg-slate-700 hover:bg-slate-600 text-slate-200 px-5 py-2 rounded-lg font-medium transition-colors">
                    <i class="fab fa-github mr-2"></i> 
                    {{ __('about.contribution.contribute') }}
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