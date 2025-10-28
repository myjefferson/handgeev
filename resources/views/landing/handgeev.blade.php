@extends('template.template-site')

@section('title', 'Handgeev - Crie e Gerencie APIs de Forma Intuitiva')
@section('description', 'Boas-vindas ao HandGeev')

@push('style')
    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #00e6d8 0%, #00b3a8 100%);
        }
        .teal-glow {
            box-shadow: 0 0 25px rgba(0, 230, 216, 0.3);
        }
        .teal-glow-hover:hover {
            box-shadow: 0 0 35px rgba(0, 230, 216, 0.4);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 230, 216, 0.15), 0 10px 10px -5px rgba(0, 230, 216, 0.1);
        }
        .pricing-card.popular {
            position: relative;
            overflow: hidden;
        }

        .popular-tag {
            color: #0f172a;
            padding: 5px 40px;
        }
        .recommended-tag{
            padding: 5px 40px;
        }
        .recommended-tag, .popular-tag {
            position: absolute;
            top: 32px;
            right: -40px;
            font-size: 12px;
            font-weight: 600;
            transform: rotate(45deg);
        }
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #00e6d8;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .code-block {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            overflow: hidden;
        }
        .feature-icon {
            background: rgba(0, 230, 216, 0.1);
            border: 1px solid rgba(0, 230, 216, 0.2);
        }
        .teal-badge {
            background: rgba(8, 255, 240, 0.1);
            color: #08fff0;
        }
        .purple-badge {
            background: #a855f7;
            color: white;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }
        .blue-badge {
            background: #2B68FF;
            color: white;
        }
        .popular-tag {
            position: absolute;
            top: 20px;
            right: -34px;
            background: #8b5cf6;
            color: white;
            padding: 5px 40px;
            font-size: 12px;
            font-weight: 600;
            transform: rotate(45deg);
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .pulse {
            animation: pulse 2s infinite;
        }
    </style>
@endpush

@section('content_site')
    <!-- Header/Navigation -->
    <header class="fixed w-full bg-slate-900 bg-opacity-95 z-50 border-b border-slate-700">
        <div class="container mx-auto px-2 sm:px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img class="w-40" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev">
                </div>
                
                <!-- Menu Desktop -->
                <nav class="hidden md:flex space-x-10">
                    <a href="#features" class="nav-link text-slate-300 hover:text-teal-400 transition-colors">
                        {{ __('site.navigation.features') }}
                    </a>
                    <a href="#how-it-works" class="nav-link text-slate-300 hover:text-teal-400 transition-colors">
                        {{ __('site.navigation.how_it_works') }}
                    </a>
                    <a href="#pricing" class="nav-link text-slate-300 hover:text-teal-400 transition-colors">
                        {{ __('site.navigation.pricing') }}
                    </a>
                    <a href="#use-cases" class="nav-link text-slate-300 hover:text-teal-400 transition-colors">
                        {{ __('site.navigation.use_cases') }}
                    </a>
                </nav>
                
                <div class="flex items-center space-x-4">                    
                    <a href="{{ route('login.show') }}" class="hidden md:inline-block text-teal-400 hover:text-teal-300 font-medium">
                        {{ __('site.navigation.login') }}
                    </a>
                    <a href="{{ route('register.show') }}" class="text-[13px] sm:text-[16px] bg-teal-500 hover:bg-teal-400 text-slate-900 px-2 sm:px-5 py-1 sm:py-2 rounded-lg font-medium transition-colors teal-glow-hover">
                        {{ __('site.navigation.get_started') }}
                    </a>
                    
                    <!-- Botão do menu mobile com data attributes do Flowbite -->
                    <button data-collapse-toggle="mobile-menu" type="button" class="md:hidden text-slate-300 p-2" aria-controls="mobile-menu" aria-expanded="false">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Menu Mobile com data attribute do Flowbite -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 px-3 py-4 border-t border-slate-700">
                <div class="flex flex-col space-y-4">
                    <a href="#features" class="nav-link text-slate-300 hover:text-teal-400 transition-colors py-2">
                        {{ __('site.navigation.features') }}
                    </a>
                    <a href="#how-it-works" class="nav-link text-slate-300 hover:text-teal-400 transition-colors py-2">
                        {{ __('site.navigation.how_it_works') }}
                    </a>
                    <a href="#pricing" class="nav-link text-slate-300 hover:text-teal-400 transition-colors py-2">
                        {{ __('site.navigation.pricing') }}
                    </a>
                    <a href="#use-cases" class="nav-link text-slate-300 hover:text-teal-400 transition-colors py-2">
                        {{ __('site.navigation.use_cases') }}
                    </a>
                    <a href="{{ route('login.show') }}" class="text-teal-400 hover:text-teal-300 font-medium py-2">
                        {{ __('site.navigation.login') }}
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2">
                    <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">
                        {!! __('site.hero.title', ['highlight' => '<span class="text-teal-400">'.__('site.hero.highlight').'</span>']) !!}
                    </h1>
                    <p class="text-lg text-slate-400 mb-8">
                        {{ __('site.hero.description') }}
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="#pricing" class="bg-teal-500 hover:bg-teal-400 text-slate-900 px-6 py-3 rounded-lg font-medium text-center transition-colors teal-glow-hover">
                            <i class="fas fa-bolt mr-2"></i>{{ __('site.hero.start_free') }}
                        </a>
                        <a href="#demo" class="border border-teal-500 text-teal-500 hover:bg-slate-800 px-6 py-3 rounded-lg font-medium text-center transition-colors">
                            <i class="fas fa-play-circle mr-2"></i>{{ __('site.hero.see_demo') }}
                        </a>
                    </div>
                    <div class="mt-8 flex items-center text-slate-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span class="text-sm">{{ __('site.hero.no_credit_card') }}</span>
                    </div>
                </div>
                
                <div class="lg:w-1/2">
                    <div class="bg-slate-800 rounded-2xl p-6 shadow-xl border border-slate-700 teal-glow">
                        <div class="code-block">
                            <div class="flex space-x-2 p-4 border-b border-slate-700">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="p-6">
                                <div class="text-sm font-mono text-slate-300">
                                    <div class="text-teal-400">{{ __('site.code_example.api_endpoint') }}</div>
                                    <div class="text-purple-400">GET</div> 
                                    <span class="text-green-400">https://www.handgeev.com/workspace/</span>
                                    <span class="text-yellow-400">:id</span>
                                    <span class="text-blue-400">/data</span>
                                    
                                    <div class="mt-4 text-teal-400">{{ __('site.code_example.response_json') }}</div>
                                    <div>{</div>
                                    <div class="ml-4">"status": <span class="text-green-400">"success"</span>,</div>
                                    <div class="ml-4">"data": [</div>
                                    <div class="ml-8">{</div>
                                    <div class="ml-12">"id": <span class="text-blue-400">1</span>,</div>
                                    <div class="ml-12">"name": <span class="text-green-400">"Example"</span>,</div>
                                    <div class="ml-12">"value": <span class="text-green-400">"Dynamic data"</span></div>
                                    <div class="ml-8">}</div>
                                    <div class="ml-4">]</div>
                                    <div>}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-slate-800">
        <div class="container mx-auto max-w-6xl px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-teal-400 mb-2">8K+</div>
                    <div class="text-slate-400">{{ __('site.stats.apis_created') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-teal-400 mb-2">99.9%</div>
                    <div class="text-slate-400">{{ __('site.stats.uptime') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-teal-400 mb-2">1.5M</div>
                    <div class="text-slate-400">{{ __('site.stats.requests_day') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-teal-400 mb-2">1.2s</div>
                    <div class="text-slate-400">{{ __('site.stats.response_time') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-slate-900 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ __('site.features.title') }}</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                    {{ __('site.features.subtitle') }}
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-layer-group text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.features.organized_workspaces.title') }}</h3>
                    <p class="text-slate-400">{{ __('site.features.organized_workspaces.description') }}</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-folder-tree text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.features.hierarchical_topics.title') }}</h3>
                    <p class="text-slate-400">{{ __('site.features.hierarchical_topics.description') }}</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-table text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.features.dynamic_fields.title') }}</h3>
                    <p class="text-slate-400">{{ __('site.features.dynamic_fields.description') }}</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-code text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.features.automatic_api.title') }}</h3>
                    <p class="text-slate-400">{{ __('site.features.automatic_api.description') }}</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.features.robust_security.title') }}</h3>
                    <p class="text-slate-400">{{ __('site.features.robust_security.description') }}</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-bolt text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.features.low_latency.title') }}</h3>
                    <p class="text-slate-400">{{ __('site.features.low_latency.description') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-slate-800 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ __('site.how_it_works.title') }}</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                    {{ __('site.how_it_works.subtitle') }}
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-slate-900 font-bold text-xl mx-auto mb-4">
                        1
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.how_it_works.step1.title') }}</h3>
                    <p class="text-slate-400">{{ __('site.how_it_works.step1.description') }}</p>
                </div>
                
                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-slate-900 font-bold text-xl mx-auto mb-4">
                        2
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.how_it_works.step2.title') }}</h3>
                    <p class="text-slate-400">{{ __('site.how_it_works.step2.description') }}</p>
                </div>
                
                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-slate-900 font-bold text-xl mx-auto mb-4">
                        3
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.how_it_works.step3.title') }}</h3>
                    <p class="text-slate-400">{{ __('site.how_it_works.step3.description') }}</p>
                </div>
                
                <!-- Step 4 -->
                <div class="text-center">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-slate-900 font-bold text-xl mx-auto mb-4">
                        4
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.how_it_works.step4.title') }}</h3>
                    <p class="text-slate-400">{{ __('site.how_it_works.step4.description') }}</p>
                </div>
            </div>
            
            <div class="mt-16 bg-slate-900 rounded-2xl p-8 border border-slate-700">
                <div class="flex flex-col lg:flex-row items-center gap-8">
                    <div class="lg:w-1/2">
                        <h3 class="text-2xl font-bold mb-4 text-white">{{ __('site.how_it_works.endpoint_ready.title') }}</h3>
                        <p class="text-slate-400 mb-6">
                            {{ __('site.how_it_works.endpoint_ready.description') }}
                        </p>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-check text-teal-400 mr-3"></i>
                                <span class="text-slate-300">{{ __('site.how_it_works.endpoint_ready.auto_crud') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-teal-400 mr-3"></i>
                                <span class="text-slate-300">{{ __('site.how_it_works.endpoint_ready.filters') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-teal-400 mr-3"></i>
                                <span class="text-slate-300">{{ __('site.how_it_works.endpoint_ready.pagination') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-teal-400 mr-3"></i>
                                <span class="text-slate-300">{{ __('site.how_it_works.endpoint_ready.json_responses') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="code-block">
                            <div class="p-4 bg-slate-800 border-b border-slate-700">
                                <span class="text-teal-400 text-sm">// {{ __('site.code_example.api_endpoint') }}</span>
                            </div>
                            <div class="p-4">
                                <div class="text-sm font-mono text-slate-300">
                                    <div class="text-purple-400">fetch</div>(<span class="text-green-400">'https://wwww.handgeev.com/workspace/123/data'</span>)
                                    <div>  .then(<span class="text-blue-400">response</span> => response.<span class="text-purple-400">json</span>())</div>
                                    <div>  .then(<span class="text-blue-400">data</span> => {</div>
                                    <div>    <span class="text-gray-500">// {{ __('site.how_it_works.endpoint_ready.json_responses') }}</span></div>
                                    <div>    console.<span class="text-purple-400">log</span>(data);</div>
                                    <div>  });</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Cases Section -->
    <section id="use-cases" class="py-20 bg-slate-900 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ __('site.use_cases.title') }}</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                    {{ __('site.use_cases.subtitle') }}
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Use Case 1 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-mobile-alt text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.use_cases.apis_for_apps.title') }}</h3>
                    <p class="text-slate-400 mb-4">{{ __('site.use_cases.apis_for_apps.description') }}</p>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            {{ __('site.use_cases.apis_for_apps.rapid_prototyping') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            {{ __('site.use_cases.apis_for_apps.mvp_development') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            {{ __('site.use_cases.apis_for_apps.testing_environments') }}
                        </li>
                    </ul>
                </div>
                
                <!-- Use Case 2 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-desktop text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.use_cases.microservices.title') }}</h3>
                    <p class="text-slate-400 mb-4">{{ __('site.use_cases.microservices.description') }}</p>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            {{ __('site.use_cases.microservices.service_prototyping') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            {{ __('site.use_cases.microservices.data_aggregation') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            {{ __('site.use_cases.microservices.api_gateways') }}
                        </li>
                    </ul>
                </div>
                
                <!-- Use Case 3 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-database text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">{{ __('site.use_cases.mock_apis.title') }}</h3>
                    <p class="text-slate-400 mb-4">{{ __('site.use_cases.mock_apis.description') }}</p>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            {{ __('site.use_cases.mock_apis.frontend_development') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            {{ __('site.use_cases.mock_apis.integration_testing') }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            {{ __('site.use_cases.mock_apis.demo_environments') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-slate-800 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ __('site.pricing.title') }}</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                    {{ __('site.pricing.subtitle') }}
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Plano Free -->
                <div class="bg-slate-900 rounded-xl p-6 shadow-md border border-slate-700 card-hover">
                    <div class="mb-4">
                        <span class="teal-badge text-xs font-semibold px-3 py-1 rounded-full">FREE</span>
                        <h3 class="font-semibold text-lg mb-2 text-white mt-3">{{ __('site.pricing.free.name') }}</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-white">$0</span>
                            <span class="text-slate-400">{{ __('site.pricing.free.period') }}</span>
                        </div>
                    </div>
                    <p class="text-slate-400 mb-6 text-sm">{{ __('site.pricing.free.description') }}</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">1 Workspace</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">Até 3 tópicos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">Máximo 10 campos por tópico</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">API: 30 req/min</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">Exportação de dados</span>
                        </li>
                        <li class="flex items-center text-gray-500">
                            <i class="fas fa-times mr-2"></i>
                            <span class="text-sm">Importação de dados</span>
                        </li>
                        <li class="flex items-center text-gray-500">
                            <i class="fas fa-times mr-2"></i>
                            <span class="text-sm">Acesso à Interface API REST</span>
                        </li>
                    </ul>
                    <a href="{{ route('register.show') }}" 
                        class="block w-full bg-slate-700 hover:bg-slate-600 text-white text-center py-3 rounded-lg font-medium transition-colors">
                        {{ __('site.pricing.free.button') }}
                    </a>
                </div>
                
                <!-- Plano Start -->
                <div class="bg-slate-900 rounded-xl p-6 shadow-md border border-slate-700 card-hover">
                    <div class="mb-4">
                        <span class="teal-badge text-xs font-semibold px-3 py-1 rounded-full">START</span>
                        <h3 class="font-semibold text-lg mb-2 text-white mt-3">{{ __('site.pricing.start.name') }}</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-white">$10</span>
                            <span class="text-slate-400">{{ __('site.pricing.start.period') }}</span>
                        </div>
                    </div>
                    <p class="text-slate-400 mb-6 text-sm">{{ __('site.pricing.start.description') }}</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">3 Workspaces</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">Até 10 tópicos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">Máximo 50 campos por tópico</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">Exportação de dados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">API: 60 req/min</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span class="text-sm">Acesso à API</span>
                        </li>
                    </ul>
                    <a href="{{ route('register.show', ['plan' => 'start']) }}" 
                        class="block w-full bg-teal-500 hover:bg-teal-400 text-slate-900 text-center py-3 rounded-lg font-medium transition-colors">
                        {{ __('site.pricing.start.button') }}
                    </a>
                </div>
                
                <!-- Plano Pro -->
                <div class="overflow-hidden bg-slate-900 rounded-xl p-6 shadow-md border-2 border-purple-500 card-hover pricing-card popular relative">
                    <div class="recommended-tag bg-purple-500">RECOMENDADO</div>
                    <div class="mb-4">
                        <span class="purple-badge text-xs font-semibold px-3 py-1 rounded-full">PRO</span>
                        <h3 class="font-semibold text-lg mb-2 text-white mt-3">{{ __('site.pricing.pro.name') }}</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-white">$35</span>
                            <span class="text-slate-400">{{ __('site.pricing.pro.period') }}</span>
                        </div>
                    </div>
                    <p class="text-slate-400 mb-6 text-sm">{{ __('site.pricing.pro.description') }}</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-purple-400 mr-2"></i>
                            <span class="text-sm">10 Workspaces</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-purple-400 mr-2"></i>
                            <span class="text-sm">Até 30 tópicos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-purple-400 mr-2"></i>
                            <span class="text-sm">Máximo 200 campos por tópico</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-purple-400 mr-2"></i>
                            <span class="text-sm">Exportação de dados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-purple-400 mr-2"></i>
                            <span class="text-sm">API: 120 req/min</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-purple-400 mr-2"></i>
                            <span class="text-sm">Burst: 25 req</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-purple-400 mr-2"></i>
                            <span class="text-sm">Suporte prioritário</span>
                        </li>
                    </ul>
                    <a href="{{ route('register.show', ['plan' => 'pro']) }}" 
                        class="block w-full bg-purple-600 hover:bg-purple-500 text-white text-center py-3 rounded-lg font-medium transition-colors pulse">
                        {{ __('site.pricing.pro.button') }}
                    </a>
                </div>
                
                <!-- Plano Premium -->
                <div class="relative overflow-hidden bg-slate-900 rounded-xl p-6 shadow-md border border-blue-600 card-hover popular">
                    {{-- <div class="popular-tag">POPULAR</div> --}}
                    <div class="mb-4">
                        <span class="blue-badge text-xs font-semibold px-3 py-1 rounded-full">PREMIUM</span>
                        <h3 class="font-semibold text-lg mb-2 text-white mt-3">{{ __('site.pricing.premium.name') }}</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-white">$70</span>
                            <span class="text-slate-400">{{ __('site.pricing.premium.period') }}</span>
                        </div>
                    </div>
                    <p class="text-slate-400 mb-6 text-sm">{{ __('site.pricing.premium.description') }}</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            <span class="text-sm">Workspaces Ilimitados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            <span class="text-sm">Tópicos Ilimitados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            <span class="text-sm">Campos Ilimitados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            <span class="text-sm">Exportação de dados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            <span class="text-sm">API: 300 req/min</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            <span class="text-sm">Burst: 100 req</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-blue-500 mr-2"></i>
                            <span class="text-sm">Suporte 24/7</span>
                        </li>
                    </ul>
                    <a href="{{ route('register.show', ['plan' => 'premium']) }}" 
                        class="block w-full bg-blue-600 hover:bg-blue-500 text-white text-center py-3 rounded-lg font-medium transition-colors">
                        {{ __('site.pricing.premium.button') }}
                    </a>
                </div>
            </div>

            <!-- Tabela de Comparação -->
            <div class="mt-16 bg-slate-900 rounded-2xl p-6 border border-slate-700">
                <h3 class="text-2xl font-bold mb-6 text-center text-white">Comparação Detalhada de Planos</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-700">
                                <th class="pb-4 text-left">Recurso</th>
                                <th class="pb-4 text-center">Free</th>
                                <th class="pb-4 text-center">Start</th>
                                <th class="pb-4 text-center">Pro</th>
                                <th class="pb-4 text-center">Premium</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-slate-700">
                                <td class="py-4">Workspaces</td>
                                <td class="py-4 text-center">1</td>
                                <td class="py-4 text-center">3</td>
                                <td class="py-4 text-center">10</td>
                                <td class="py-4 text-center">Ilimitados</td>
                            </tr>
                            <tr class="border-b border-slate-700">
                                <td class="py-4">Tópicos</td>
                                <td class="py-4 text-center">3</td>
                                <td class="py-4 text-center">10</td>
                                <td class="py-4 text-center">30</td>
                                <td class="py-4 text-center">Ilimitados</td>
                            </tr>
                            <tr class="border-b border-slate-700">
                                <td class="py-4">Campos por tópico</td>
                                <td class="py-4 text-center">10</td>
                                <td class="py-4 text-center">50</td>
                                <td class="py-4 text-center">200</td>
                                <td class="py-4 text-center">Ilimitados</td>
                            </tr>
                            <tr class="border-b border-slate-700">
                                <td class="py-4">Exportação de Dados</td>
                                <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                                <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                                <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                                <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                            </tr>
                            <tr class="border-b border-slate-700">
                                <td class="py-4">Importação de Dados</td>
                                <td class="py-4 text-center"><i class="fas fa-times text-red-400"></i></td>
                                <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                                <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                                <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                            </tr>
                            <tr class="border-b border-slate-700">
                                <td class="py-4">Acesso à Interface API REST</td>
                                <td class="py-4 text-center"><i class="fas fa-times text-red-400"></i></td>
                                <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                                <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                                <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                            </tr>
                            <tr class="border-b border-slate-700">
                                <td class="py-4">API Requests/Min</td>
                                <td class="py-4 text-center">30</td>
                                <td class="py-4 text-center">60</td>
                                <td class="py-4 text-center">120</td>
                                <td class="py-4 text-center">300</td>
                            </tr>
                            <tr>
                                <td class="py-4">Suporte</td>
                                <td class="py-4 text-center">Básico</td>
                                <td class="py-4 text-center">Padrão</td>
                                <td class="py-4 text-center">Prioritário</td>
                                <td class="py-4 text-center">24/7</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    

    <!-- Final CTA Section -->
    <section class="py-16 gradient-bg">
        <div class="container mx-auto max-w-4xl text-center px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">
                {{ __('site.cta.title') }}
            </h2>
            <p class="text-slate-800 text-lg mb-8">
                {{ __('site.cta.description') }}
            </p>
            <a href="{{ route('register.show') }}" class="bg-slate-900 text-teal-400 hover:bg-slate-800 px-8 py-3 rounded-lg font-medium text-lg inline-block transition-colors teal-glow-hover">
                <i class="fas fa-rocket mr-2"></i>{{ __('site.cta.button') }}
            </a>
            <p class="text-slate-800 text-sm mt-4">
                {{ __('site.cta.notice') }}
            </p>
        </div>
    </section>

    @include('components.footer.footer')
@endsection