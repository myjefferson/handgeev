<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handgeev - Crie e Gerencie APIs de Forma Intuitiva</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
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
            border: 2px solid #00e6d8;
            position: relative;
            overflow: hidden;
        }
        .popular-tag {
            position: absolute;
            top: 20px;
            right: -34px;
            background: #00e6d8;
            color: #0f172a;
            padding: 5px 40px;
            font-size: 14px;
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
    </style>
</head>
<body class="antialiased">
    <!-- Header/Navigation -->
    <header class="fixed w-full bg-slate-900 bg-opacity-90 backdrop-blur-sm z-50 border-b border-slate-700">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img class="w-40" src="{{ asset('assets/images/logo.png') }}" alt="Handgeev">
                </div>
                
                <nav class="hidden md:flex space-x-10">
                    <a href="#features" class="nav-link text-slate-300 hover:text-teal-400 transition-colors">Recursos</a>
                    <a href="#how-it-works" class="nav-link text-slate-300 hover:text-teal-400 transition-colors">Como Funciona</a>
                    <a href="#pricing" class="nav-link text-slate-300 hover:text-teal-400 transition-colors">Planos</a>
                    <a href="#use-cases" class="nav-link text-slate-300 hover:text-teal-400 transition-colors">Casos de Uso</a>
                </nav>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login.show') }}" class="hidden md:inline-block text-teal-400 hover:text-teal-300 font-medium">Entrar</a>
                    <a href="#pricing" class="bg-teal-500 hover:bg-teal-400 text-slate-900 px-5 py-2 rounded-lg font-medium transition-colors teal-glow-hover">Começar Grátis</a>
                    
                    <button class="md:hidden text-slate-300">
                        <i class="fas fa-bars"></i>
                    </button>
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
                        Crie APIs poderosas 
                        <span class="text-teal-400">sem escrever código</span>
                    </h1>
                    <p class="text-lg text-slate-400 mb-8">
                        Handgeev é a plataforma intuitiva que permite criar, gerenciar e consumir APIs 
                        através de uma interface visual. Perfect para desenvolvedores, product managers 
                        e equipes que precisam de APIs rápidas e organizadas.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="#pricing" class="bg-teal-500 hover:bg-teal-400 text-slate-900 px-6 py-3 rounded-lg font-medium text-center transition-colors teal-glow-hover">
                            <i class="fas fa-bolt mr-2"></i>Comece Agora - É Grátis
                        </a>
                        <a href="#demo" class="border border-teal-500 text-teal-500 hover:bg-slate-800 px-6 py-3 rounded-lg font-medium text-center transition-colors">
                            <i class="fas fa-play-circle mr-2"></i>Ver Demonstração
                        </a>
                    </div>
                    <div class="mt-8 flex items-center text-slate-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span class="text-sm">Não é necessário cartão de crédito • Setup em 2 minutos</span>
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
                                    <div class="text-teal-400">// API Endpoint criado automaticamente</div>
                                    <div class="text-purple-400">GET</div> 
                                    <span class="text-green-400">https://api.handgeev.com/workspace/</span>
                                    <span class="text-yellow-400">:id</span>
                                    <span class="text-blue-400">/data</span>
                                    
                                    <div class="mt-4 text-teal-400">// Response JSON estruturado</div>
                                    <div>{</div>
                                    <div class="ml-4">"status": <span class="text-green-400">"success"</span>,</div>
                                    <div class="ml-4">"data": [</div>
                                    <div class="ml-8">{</div>
                                    <div class="ml-12">"id": <span class="text-blue-400">1</span>,</div>
                                    <div class="ml-12">"name": <span class="text-green-400">"Exemplo"</span>,</div>
                                    <div class="ml-12">"value": <span class="text-green-400">"Dado dinâmico"</span></div>
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
                    <div class="text-3xl font-bold text-teal-400 mb-2">10K+</div>
                    <div class="text-slate-400">APIs criadas</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-teal-400 mb-2">99.9%</div>
                    <div class="text-slate-400">Uptime</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-teal-400 mb-2">2.5M</div>
                    <div class="text-slate-400">Requests/dia</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-teal-400 mb-2">1.2s</div>
                    <div class="text-slate-400">Response time</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-slate-900 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Recursos Poderosos</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                    Tudo que você precisa para criar e gerenciar APIs de forma eficiente
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-layer-group text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Workspaces Organizados</h3>
                    <p class="text-slate-400">Crie workspaces para diferentes projetos e mantenha suas APIs perfeitamente organizadas.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-folder-tree text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Tópicos Hierárquicos</h3>
                    <p class="text-slate-400">Organize seus dados em tópicos e subtópicos para estruturação lógica da informação.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-table text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Campos Dinâmicos</h3>
                    <p class="text-slate-400">Adicione campos personalizados com tipos variados e validações específicas.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-code text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">API Automática</h3>
                    <p class="text-slate-400">Endpoints RESTful gerados automaticamente para todos os seus dados.</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Segurança Robusta</h3>
                    <p class="text-slate-400">Autenticação por API keys, rate limiting e controle de permissões granular.</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-bolt text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Baixa Latência</h3>
                    <p class="text-slate-400">Infraestrutura otimizada para responses rápidas e alta disponibilidade.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-slate-800 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Como Funciona</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                    4 passos simples para criar sua primeira API
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-slate-900 font-bold text-xl mx-auto mb-4">
                        1
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Crie um Workspace</h3>
                    <p class="text-slate-400">Comece criando um workspace para seu projeto</p>
                </div>
                
                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-slate-900 font-bold text-xl mx-auto mb-4">
                        2
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Adicione Tópicos</h3>
                    <p class="text-slate-400">Organize seus dados em tópicos lógicos</p>
                </div>
                
                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-slate-900 font-bold text-xl mx-auto mb-4">
                        3
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Defina Campos</h3>
                    <p class="text-slate-400">Adicione campos com seus tipos e valores</p>
                </div>
                
                <!-- Step 4 -->
                <div class="text-center">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-slate-900 font-bold text-xl mx-auto mb-4">
                        4
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Consuma a API</h3>
                    <p class="text-slate-400">Use os endpoints gerados automaticamente</p>
                </div>
            </div>
            
            <div class="mt-16 bg-slate-900 rounded-2xl p-8 border border-slate-700">
                <div class="flex flex-col lg:flex-row items-center gap-8">
                    <div class="lg:w-1/2">
                        <h3 class="text-2xl font-bold mb-4 text-white">Endpoint Pronto para Uso</h3>
                        <p class="text-slate-400 mb-6">
                            Cada workspace automaticamente gera endpoints RESTful completos 
                            com suporte para CRUD, filtros e paginação.
                        </p>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-check text-teal-400 mr-3"></i>
                                <span class="text-slate-300">GET, POST, PUT, DELETE automáticos</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-teal-400 mr-3"></i>
                                <span class="text-slate-300">Suporte a filtros e queries</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-teal-400 mr-3"></i>
                                <span class="text-slate-300">Paginação integrada</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-teal-400 mr-3"></i>
                                <span class="text-slate-300">Respostas em JSON formatado</span>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-1/2">
                        <div class="code-block">
                            <div class="p-4 bg-slate-800 border-b border-slate-700">
                                <span class="text-teal-400 text-sm">// Exemplo de consumo</span>
                            </div>
                            <div class="p-4">
                                <div class="text-sm font-mono text-slate-300">
                                    <div class="text-purple-400">fetch</div>(<span class="text-green-400">'https://api.handgeev.com/workspace/123/data'</span>)
                                    <div>  .then(<span class="text-blue-400">response</span> => response.<span class="text-purple-400">json</span>())</div>
                                    <div>  .then(<span class="text-blue-400">data</span> => {</div>
                                    <div>    <span class="text-gray-500">// Usar seus dados aqui</span></div>
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
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Casos de Uso</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                    Ideal para diversos cenários de desenvolvimento
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Use Case 1 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-mobile-alt text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">APIs para Apps</h3>
                    <p class="text-slate-400 mb-4">Backend rápido para aplicativos mobile e web apps</p>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            Prototipagem rápida
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            MVP development
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            Testing environments
                        </li>
                    </ul>
                </div>
                
                <!-- Use Case 2 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-desktop text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Microserviços</h3>
                    <p class="text-slate-400 mb-4">Serviços especializados para arquiteturas modernas</p>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            Service prototyping
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            Data aggregation
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            API gateways
                        </li>
                    </ul>
                </div>
                
                <!-- Use Case 3 -->
                <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
                        <i class="fas fa-database text-xl"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Mock APIs</h3>
                    <p class="text-slate-400 mb-4">Dados de teste para desenvolvimento frontend</p>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            Frontend development
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            Integration testing
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2 text-xs"></i>
                            Demo environments
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
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Planos Acessíveis</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                    Escolha o plano ideal para suas necessidades de API
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Plano Free -->
                <div class="bg-slate-900 rounded-xl p-6 shadow-md border border-slate-700 card-hover">
                    <h3 class="font-semibold text-lg mb-2 text-white">Free</h3>
                    <div class="mb-4">
                        <span class="text-4xl font-bold text-white">R$ 0</span>
                        <span class="text-slate-400">/sempre</span>
                    </div>
                    <p class="text-slate-400 mb-6">Perfect para testes e projetos pequenos</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>1 Workspace</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>3 Tópicos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>50 Campos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>1K requests/mês</span>
                        </li>
                    </ul>
                    <a href="{{ route('register.index') }}" class="block w-full bg-slate-700 hover:bg-slate-600 text-white text-center py-3 rounded-lg font-medium transition-colors">
                        Começar Grátis
                    </a>
                </div>
                
                <!-- Plano Pro -->
                <div class="bg-slate-900 rounded-xl p-6 shadow-md border border-slate-700 card-hover pricing-card popular">
                    <div class="popular-tag">Popular</div>
                    <h3 class="font-semibold text-lg mb-2 text-white">Pro</h3>
                    <div class="mb-4">
                        <span class="text-4xl font-bold text-white">R$ 29</span>
                        <span class="text-slate-400">/mês</span>
                    </div>
                    <p class="text-slate-400 mb-6">Ideal para desenvolvedores e startups</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>5 Workspaces</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>Tópicos Ilimitados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>500 Campos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>50K requests/mês</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>Suporte Prioritário</span>
                        </li>
                    </ul>
                    <a href="{{ route('register.index') }}" class="block w-full gradient-bg hover:opacity-90 text-slate-900 text-center py-3 rounded-lg font-medium transition-all">
                        Assinar Agora
                    </a>
                </div>
                
                <!-- Plano Team -->
                <div class="bg-slate-900 rounded-xl p-6 shadow-md border border-slate-700 card-hover">
                    <h3 class="font-semibold text-lg mb-2 text-white">Team</h3>
                    <div class="mb-4">
                        <span class="text-4xl font-bold text-white">R$ 99</span>
                        <span class="text-slate-400">/mês</span>
                    </div>
                    <p class="text-slate-400 mb-6">Para equipes e projetos em escala</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>20 Workspaces</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>Tópicos Ilimitados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>2K Campos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>200K requests/mês</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>Múltiplos Usuários</span>
                        </li>
                    </ul>
                    <a href="{{ route('register.index') }}" class="block w-full bg-slate-700 hover:bg-slate-600 text-white text-center py-3 rounded-lg font-medium transition-colors">
                        Assinar Agora
                    </a>
                </div>
                
                <!-- Plano Enterprise -->
                <div class="bg-slate-900 rounded-xl p-6 shadow-md border border-slate-700 card-hover">
                    <h3 class="font-semibold text-lg mb-2 text-white">Enterprise</h3>
                    <div class="mb-4">
                        <span class="text-4xl font-bold text-white">Custom</span>
                    </div>
                    <p class="text-slate-400 mb-6">Soluções personalizadas para empresas</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>Workspaces Ilimitados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>Campos Ilimitados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>Requests Ilimitados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>SSO & Security</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-teal-400 mr-2"></i>
                            <span>Suporte Dedicado</span>
                        </li>
                    </ul>
                    <a href="#contact" class="block w-full bg-slate-700 hover:bg-slate-600 text-white text-center py-3 rounded-lg font-medium transition-colors">
                        Contate-nos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="py-16 gradient-bg">
        <div class="container mx-auto max-w-4xl text-center px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">
                Pronto para criar APIs incríveis?
            </h2>
            <p class="text-slate-800 text-lg mb-8">
                Junte-se a milhares de desenvolvedores que já usam Handgeev para prototipagem rápida, 
                desenvolvimento frontend e produção de APIs.
            </p>
            <a href="{{ route('register.index') }}" class="bg-slate-900 text-teal-400 hover:bg-slate-800 px-8 py-3 rounded-lg font-medium text-lg inline-block transition-colors teal-glow-hover">
                <i class="fas fa-rocket mr-2"></i>Criar Minha Primeira API
            </a>
            <p class="text-slate-800 text-sm mt-4">
                Comece gratuitamente • Sem compromisso • Sem cartão de crédito
            </p>
        </div>
    </section>

    @include('components.footer.footer_login')
</body>
</html>