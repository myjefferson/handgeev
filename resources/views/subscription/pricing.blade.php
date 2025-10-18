@extends('template.template-site')

@section('title', 'Planos e Assinatura')
@section('description', 'Handgeev')

@push('style')
    <style>
        :root {
            --teal-primary: #08fff0;
            --teal-secondary: rgba(8, 255, 240, 0.1);
            --teal-hover: rgba(8, 255, 240, 0.2);
            --purple-primary: #8b5cf6;
            --blue-primary: #2B68FF;
        }
        
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
        }
        
        .plan-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(8, 255, 240, 0.1);
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }
        
        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(8, 255, 240, 0.15);
            border-color: rgba(8, 255, 240, 0.3);
        }
        
        .plan-pro::before {
            content: 'RECOMENDADO';
            position: absolute;
            top: 35px;
            right: -35px;
            background: var(--purple-primary);
            color: white;
            font-size: 12px;
            font-weight: 500;
            padding: 5px 40px;
            transform: rotate(45deg);
        }
        
        .feature-list li {
            transition: all 0.2s ease;
        }
        
        .feature-list li:hover {
            transform: translateX(5px);
        }
        
        .teal-badge {
            background: rgba(8, 255, 240, 0.1);
            color: var(--teal-primary);
        }
        
        .purple-badge {
            background: rgba(139, 92, 246, 0.1);
            color: #c084fc;
        }
        
        .blue-badge {
            background: #2B68FF;
            color: white;
        }
        
        .teal-button {
            background: var(--teal-primary);
            color: #0f172a;
            transition: all 0.3s ease;
        }
        
        .teal-button:hover {
            background: #06e6d8;
            box-shadow: 0 0 15px rgba(8, 255, 240, 0.4);
        }
        
        .purple-button {
            background: var(--purple-primary);
            color: white;
            transition: all 0.3s ease;
        }
        
        .purple-button:hover {
            background: #7c3aed;
            box-shadow: 0 0 15px rgba(139, 92, 246, 0.4);
        }
        
        .blue-button {
            background: var(--blue-primary);
            color: white;
            transition: all 0.3s ease;
        }
        
        .blue-button:hover {
            background: #003EDA;
        }
        
        .outline-button {
            border: 1px solid var(--teal-primary);
            color: var(--teal-primary);
            transition: all 0.3s ease;
        }
        
        .outline-button:hover {
            background: rgba(8, 255, 240, 0.1);
        }
        
        .current-plan-button {
            background: rgba(8, 255, 240, 0.1);
            border: 1px solid var(--teal-primary);
            color: var(--teal-primary);
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
    <!-- Header -->
    <header class="user-menu fixed top-0 right-0 left-0 z-30 flex items-center justify-between px-4 md:px-6 py-3 bg-slate-900/80 backdrop-blur-lg">
        <div class="flex items-center">
            <img class="w-44" src="assets/images/logo.png" alt="Handgeev Logo">
        </div>
        <div class="flex items-center space-x-4">
            @auth
                <a href="{{ route('dashboard.home') }}" class="text-sm text-gray-300 hover:text-teal-400 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar ao Dashboard
                </a>
            @else
                <a href="{{ route('home') }}" class="text-sm text-gray-300 hover:text-teal-400 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar ao Início
                </a>
            @endauth
            
            @auth
                <div class="relative">
                    <button id="userDropdownButton" data-dropdown-toggle="userDropdown" class="flex items-center space-x-2 text-sm rounded-full focus:ring-2 focus:ring-teal-400">
                        <div class="user-avatar w-8 h-8 rounded-full bg-teal-400/10 flex items-center justify-center border border-teal-400/20">
                            <i class="fas fa-user text-teal-400 text-sm"></i>
                        </div>
                    </button>
                </div>
            @else
                <a href="{{ route('login.show') }}" class="text-sm text-gray-300 hover:text-teal-400 transition-colors">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                </a>
            @endauth
        </div>
    </header>

    <div class="pt-20 pb-10 px-4 max-w-7xl mx-auto">
        <!-- Alertas -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400">
            <i class="fas fa-check-circle mr-2"></i>
            @if(is_array(session('success')))
                {{ implode(', ', array_filter(session('success'), 'is_string')) }}
            @else
                {{ session('success') }}
            @endif
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400">
            <i class="fas fa-exclamation-circle mr-2"></i>
            @if(is_array(session('error')))
                {{ implode(', ', array_filter(session('error'), 'is_string')) }}
            @else
                {{ session('error') }}
            @endif
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif          

        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">Escolha seu Plano</h1>
            <p class="text-gray-400 max-w-2xl mx-auto">Selecione o plano ideal para suas necessidades. Todos os planos incluem recursos essenciais, com benefícios adicionais conforme sua assinatura.</p>
        </div>

        <!-- Planos de Assinatura -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            <!-- Plano Free -->
            <div class="plan-card rounded-2xl p-6 flex flex-col">
                <div class="mb-6">
                    <span class="teal-badge text-xs font-semibold px-3 py-1 rounded-full">FREE</span>
                    <h3 class="text-2xl font-bold mt-4">Gratuito</h3>
                    <div class="mt-2">
                        <span class="text-3xl font-bold">$0</span>
                        <span class="text-gray-400">/para sempre</span>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">Ideal para teste e pequenos projetos</p>
                </div>
                
                <ul class="feature-list space-y-3 mb-8 flex-grow">
                    <li class="flex items-center hover:text-teal-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>1 Workspace</span>
                    </li>
                    <li class="flex items-center hover:text-teal-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Até 3 tópicos</span>
                    </li>
                    <li class="flex items-center hover:text-teal-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Máximo de 10 campos</span>
                    </li>
                    <li class="flex items-center hover:text-teal-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>API: 30 req/min</span>
                    </li>
                    <li class="flex items-center text-gray-500">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span>Exportação de dados</span>
                    </li>
                    <li class="flex items-center text-gray-500">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span>Acesso à API</span>
                    </li>
                </ul>
                
                @auth
                    @if(auth()->user()->isFree())
                        <button class="current-plan-button w-full py-3 rounded-lg font-semibold cursor-default">
                            <i class="fas fa-check mr-2"></i>Plano Atual
                        </button>
                    @else
                        {{-- <form action="{{ route('subscription.downgrade') }}" method="POST">
                            @csrf
                            <button type="submit" class="outline-button w-full py-3 rounded-lg font-semibold">
                                <i class="fas fa-arrow-down mr-2"></i>Mudar para Free
                            </button>
                        </form> --}}
                    @endif
                @else
                    <a href="{{ route('login.show') }}" class="outline-button w-full py-3 rounded-lg font-semibold text-center block">
                        <i class="fas fa-sign-in-alt mr-2"></i>Fazer Login
                    </a>
                @endauth
            </div>
            
            <!-- Plano Start -->
            <div class="plan-card rounded-2xl p-6 flex flex-col">
                <div class="mb-6">
                    <span class="teal-badge text-xs font-semibold px-3 py-1 rounded-full">START</span>
                    <h3 class="text-2xl font-bold mt-4">Start</h3>
                    <div class="mt-2">
                        <span class="text-3xl font-bold">$10</span>
                        <span class="text-gray-400">/mês</span>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">Para pequenos negócios</p>
                </div>
                
                <ul class="feature-list space-y-3 mb-8 flex-grow">
                    <li class="flex items-center hover:text-teal-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>3 Workspaces</span>
                    </li>
                    <li class="flex items-center hover:text-teal-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Até 10 tópicos</span>
                    </li>
                    <li class="flex items-center hover:text-teal-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Máximo de 50 campos</span>
                    </li>
                    <li class="flex items-center hover:text-teal-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Exportação de dados</span>
                    </li>
                    <li class="flex items-center hover:text-teal-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>API: 60 req/min</span>
                    </li>
                    <li class="flex items-center hover:text-teal-400">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Acesso à API</span>
                    </li>
                </ul>
                
                @auth
                    @if(auth()->user()->isStart())
                        <button class="current-plan-button w-full py-3 rounded-lg font-semibold cursor-default">
                            <i class="fas fa-check mr-2"></i>Plano Atual
                        </button>
                    @else
                        <form action="{{ route('subscription.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="price_id" value="{{ config('services.stripe.prices.start', 'price_start_default') }}">
                            <button type="submit" class="teal-button w-full py-3 rounded-lg font-semibold">
                                @if(auth()->user()->isFree())
                                    <i class="fas fa-bolt mr-2"></i>Assinar Agora
                                @else
                                    <i class="fas fa-sync-alt mr-2"></i>Mudar para Start
                                @endif
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login.show') }}" class="teal-button w-full py-3 rounded-lg font-semibold text-center block">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login para Assinar
                    </a>
                @endauth
            </div>
            
            <!-- Plano Pro -->
            <div class="plan-card relative overflow-hidden border-purple-800 hover:border-purple-500 rounded-2xl p-6 flex flex-col">
                <div class="mb-6 plan-pro">
                    <span class="purple-badge text-xs font-semibold px-3 py-1 rounded-full">PRO</span>
                    <h3 class="text-2xl font-bold mt-4">Profissional</h3>
                    <div class="mt-2">
                        <span class="text-3xl font-bold">$32</span>
                        <span class="text-gray-400">/mês</span>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">Para negócios estabelecidos</p>
                </div>
                
                <ul class="feature-list space-y-3 mb-8 flex-grow">
                    <li class="flex items-center hover:text-purple-400">
                        <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                        <span>10 Workspaces</span>
                    </li>
                    <li class="flex items-center hover:text-purple-400">
                        <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                        <span>Até 30 tópicos</span>
                    </li>
                    <li class="flex items-center hover:text-purple-400">
                        <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                        <span>Máximo de 200 campos</span>
                    </li>
                    <li class="flex items-center hover:text-purple-400">
                        <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                        <span>Exportação de dados</span>
                    </li>
                    <li class="flex items-center hover:text-purple-400">
                        <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                        <span>API: 120 req/min</span>
                    </li>
                    <li class="flex items-center hover:text-purple-400">
                        <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                        <span>Burst: 25 req</span>
                    </li>
                    <li class="flex items-center hover:text-purple-400">
                        <i class="fas fa-check-circle text-purple-400 mr-2"></i>
                        <span>Suporte prioritário</span>
                    </li>
                </ul>
                
                @auth
                    @if(auth()->user()->isPro())
                        <button class="current-plan-button w-full py-3 rounded-lg font-semibold cursor-default">
                            <i class="fas fa-check mr-2"></i>Plano Atual
                        </button>
                    @else
                        <form action="{{ route('subscription.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="price_id" value="{{ config('services.stripe.prices.pro', 'price_pro_default') }}">
                            <button type="submit" class="purple-button w-full py-3 rounded-lg font-semibold pulse">
                                @if(auth()->user()->isFree() || auth()->user()->isStart())
                                    <i class="fas fa-crown mr-2"></i>Assinar Agora
                                @else
                                    <i class="fas fa-sync-alt mr-2"></i>Mudar para Pro
                                @endif
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login.show') }}" class="purple-button w-full py-3 rounded-lg font-semibold text-center block">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login para Assinar
                    </a>
                @endauth
            </div>
            
            <!-- Plano Premium -->
            <div class="plan-card relative overflow-hidden border-blue-800 hover:border-blue-500 rounded-2xl p-6 flex flex-col">
                <div class="mb-6 plan-premium">
                    <span class="blue-badge text-xs font-semibold px-3 py-1 rounded-full">PREMIUM</span>
                    <h3 class="text-2xl font-bold mt-4">Premium</h3>
                    <div class="mt-2">
                        <span class="text-3xl font-bold">$70</span>
                        <span class="text-gray-400">/mês</span>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">Para empresas</p>
                </div>
                
                <ul class="feature-list space-y-3 mb-8 flex-grow">
                    <li class="flex items-center hover:text-blue-300">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        <span>Workspaces Ilimitados</span>
                    </li>
                    <li class="flex items-center hover:text-blue-300">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        <span>Tópicos Ilimitados</span>
                    </li>
                    <li class="flex items-center hover:text-blue-300">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        <span>Campos Ilimitados</span>
                    </li>
                    <li class="flex items-center hover:text-blue-300">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        <span>Exportação de dados</span>
                    </li>
                    <li class="flex items-center hover:text-blue-300">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        <span>API: 300 req/min</span>
                    </li>
                    <li class="flex items-center hover:text-blue-300">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        <span>Burst: 100 req</span>
                    </li>
                    <li class="flex items-center hover:text-blue-300">
                        <i class="fas fa-check-circle text-blue-500 mr-2"></i>
                        <span>Suporte 24/7</span>
                    </li>
                </ul>
                
                @auth
                    @if(auth()->user()->isPremium())
                        <button class="current-plan-button w-full py-3 rounded-lg font-semibold cursor-default">
                            <i class="fas fa-check mr-2"></i>Plano Atual
                        </button>
                    @else
                        <form action="{{ route('subscription.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="price_id" value="{{ config('services.stripe.prices.premium', 'price_premium_default') }}">
                            <button type="submit" class="blue-button w-full py-3 rounded-lg font-semibold">
                                @if(!auth()->user()->isPremium())
                                    <i class="fas fa-rocket mr-2"></i>Assinar Agora
                                @else
                                    <i class="fas fa-sync-alt mr-2"></i>Mudar para Premium
                                @endif
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login.show') }}" class="blue-button w-full py-3 rounded-lg font-semibold text-center block">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login para Assinar
                    </a>
                @endauth
            </div>
        </div>

        <!-- Resto do código permanece igual -->
        <!-- Comparação de Planos -->
        <div class="bg-slate-800/50 rounded-2xl p-6 mb-12">
            <h2 class="text-2xl font-bold mb-6 text-center">Comparação Detalhada de Planos</h2>
            
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
                            <td class="py-4">Campos</td>
                            <td class="py-4 text-center">10</td>
                            <td class="py-4 text-center">50</td>
                            <td class="py-4 text-center">200</td>
                            <td class="py-4 text-center">Ilimitados</td>
                        </tr>
                        <tr class="border-b border-slate-700">
                            <td class="py-4">Exportação de Dados</td>
                            <td class="py-4 text-center"><i class="fas fa-times text-red-400"></i></td>
                            <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                            <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                            <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                        </tr>
                        <tr class="border-b border-slate-700">
                            <td class="py-4">Acesso à API</td>
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
                        <tr class="border-b border-slate-700">
                            <td class="py-4">Burst Requests</td>
                            <td class="py-4 text-center">5</td>
                            <td class="py-4 text-center">15</td>
                            <td class="py-4 text-center">25</td>
                            <td class="py-4 text-center">100</td>
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

        <!-- Perguntas Frequentes -->
        <div class="bg-slate-800/50 rounded-2xl p-6">
            <h2 class="text-2xl font-bold mb-6 text-center">Perguntas Frequentes</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-2">Posso mudar de plano depois?</h3>
                    <p class="text-gray-400">Sim, você pode atualizar ou downgradar seu plano a qualquer momento. As alterações serão refletidas no próximo ciclo de faturamento.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-2">Há cobrança por setup?</h3>
                    <p class="text-gray-400">Não, não há cobrança de setup para nenhum de nossos planos. Você paga apenas a taxa mensal ou anual conforme selecionado.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-2">Quais métodos de pagamento são aceitos?</h3>
                    <p class="text-gray-400">Aceitamos cartão de crédito, débito, PIX e boleto bancário. Para o plano Enterprise, também aceitamos transferência bancária.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-2">Há garantia de reembolso?</h3>
                    <p class="text-gray-400">Oferecemos garantia de 7 dias para todos os planos. Se não ficar satisfeito, reembolsaremos integralmente seu pagamento.</p>
                </div>
            </div>
        </div>
    </div>

    @include('components.footer.footer')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const planButtons = document.querySelectorAll('.teal-button, .outline-button, .purple-button, .-button');
            
            planButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const plan = this.closest('.plan-card').querySelector('h3').textContent;
                });
            });
            
            const features = document.querySelectorAll('.feature-list li');
            features.forEach(feature => {
                feature.addEventListener('mouseenter', function() {
                    this.style.cursor = 'pointer';
                });
            });
        });
    </script>
@endpush