<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfoline - Sua Plataforma de Portfólios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#e6fffe',
                            100: '#b3fffc',
                            200: '#80fffa',
                            300: '#4dfff8',
                            400: '#1afff6',
                            500: '#08fff0',
                            600: '#00e6d8',
                            700: '#00b3a8',
                            800: '#008078',
                            900: '#004d48',
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
            background-color: #0f172a;
            color: #f1f5f9;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(8, 255, 240, 0.15), 0 10px 10px -5px rgba(8, 255, 240, 0.1);
        }
        .pricing-card.popular {
            border: 2px solid #08fff0;
            position: relative;
            overflow: hidden;
        }
        .popular-tag {
            position: absolute;
            top: 20px;
            right: -34px;
            background: #08fff0;
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
            background-color: #08fff0;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
    </style>
</head>
    <body class="antialiased">
        <!-- Header/Navigation -->
        <header class="fixed w-full bg-slate-900 bg-opacity-90 backdrop-blur-sm z-50 border-b border-slate-700">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <img class="w-40" src="assets/images/logo.png" alt="Portfoline">
                    </div>
                    
                    <nav class="hidden md:flex space-x-10">
                        <a href="#features" class="nav-link text-slate-300 hover:text-primary-400 transition-colors">Recursos</a>
                        <a href="#pricing" class="nav-link text-slate-300 hover:text-primary-400 transition-colors">Preços</a>
                        <a href="#testimonials" class="nav-link text-slate-300 hover:text-primary-400 transition-colors">Depoimentos</a>
                        <a href="#faq" class="nav-link text-slate-300 hover:text-primary-400 transition-colors">FAQ</a>
                    </nav>
                    
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login.index') }}" class="hidden md:inline-block text-primary-400 hover:text-primary-300 font-medium">Entrar</a>
                        <a href="#pricing" class="bg-primary-500 hover:bg-primary-600 text-slate-900 px-5 py-2 rounded-lg font-medium transition-colors">Começar Grátis</a>
                        
                        <button class="md:hidden text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="pt-32 pb-20 px-4">
            <div class="container mx-auto max-w-6xl">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="md:w-1/2 mb-12 md:mb-0">
                        <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">Destaque seu trabalho com <span class="text-primary-400">portfólios incríveis</span></h1>
                        <p class="text-lg text-slate-400 mb-8">A plataforma intuitiva que ajuda profissionais criativos a exibir seu trabalho e conquistar oportunidades extraordinárias.</p>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="#pricing" class="bg-primary-500 hover:bg-primary-600 text-slate-900 px-6 py-3 rounded-lg font-medium text-center transition-colors">Comece Agora - É Grátis</a>
                            <a href="#" class="border border-primary-500 text-primary-500 hover:bg-slate-800 px-6 py-3 rounded-lg font-medium text-center transition-colors">Ver Demonstração</a>
                        </div>
                        <div class="mt-8 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm">Não é necessário cartão de crédito</span>
                        </div>
                    </div>
                    <div class="md:w-1/2">
                        <div class="bg-slate-800 rounded-2xl p-6 shadow-xl border border-slate-700">
                            <div class="bg-slate-900 rounded-lg shadow-md p-4 border border-slate-700">
                                <div class="flex space-x-2 mb-4">
                                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                </div>
                                <div class="p-4">
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div class="bg-slate-800 rounded-lg h-20 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 极速赛车开奖直播1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                            </svg>
                                        </div>
                                        <div class="bg-slate-800 rounded-lg h-20 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z" />
                                            </svg>
                                        </div>
                                        <div class="bg-slate-800 rounded-lg h-20 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 极速赛车开奖直播0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="bg-slate-800 rounded-lg h-20 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="bg-primary-500 text-slate-900 rounded-lg p-4">
                                        <h3 class="font-semibold mb-2">Dashboard Portfoline</h3>
                                        <p class="text-sm">Controle total sobre seu portfólio em um único lugar</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-slate-800 px-4">
            <div class="container mx-auto max-w-6xl">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Recursos Poderosos</h2>
                    <p class="text-lg text-slate-400 max-w-2xl mx-auto">Tudo o que você precisa para criar um portfólio impressionante e gerenciar seus projetos</p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-slate-900 rounded-xl p-6 border border-slate-700 card-hover">
                        <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center text-slate-900 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1极速赛车开奖直播V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2 text-slate-100">Templates Modernos</h3>
                        <p class="text-slate-400">Escolha entre diversos templates modernos e personalize conforme sua identidade visual.</p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="bg-slate-900 rounded-xl p-6 border border-slate-700 card-hover">
                        <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center text-slate-900 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2 text-slate-100">Domínio Personalizado</h3>
                        <p class="text-slate-400">Use seu próprio domínio para um portfólio verdadeiramente profissional e personalizado.</p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="bg-slate-900 rounded-xl p-6 border border-slate-700 card-hover">
                        <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center text-slate-900 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 极速赛车开奖直播24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2 text-slate-100">Otimização SEO</h3>
                        <p class="text-slate-400">Seu portfólio otimizado para mecanismos de busca para ser encontrado por mais clientes.</p>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="bg-slate-900 rounded-xl p-6 border border-slate-700 card-hover">
                        <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center text-slate-900 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2 text-slate-100">Segurança Avançada</h3>
                        <p class="text-slate-400">Proteção de dados e backups automáticos para garantir que seu trabalho esteja sempre seguro.</p>
                    </div>
                    
                    <!-- Feature 5 -->
                    <div class="bg-slate-900 rounded-xl p-6 border border-slate-700 card-hover">
                        <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center text-slate-900 mb-4">
                            <svg xmlns="http://www.w3.org/2000/s极速赛车开奖直播vg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2 text-slate-100">Compartilhamento Fácil</h3>
                        <p class="text-slate-400">Compartilhe seu portfólio com clientes através de um link único e personalizado.</p>
                    </div>
                    
                    <!-- Feature 6 -->
                    <div class="bg-slate-900 rounded-xl p-6 border border-slate-700 card-hover">
                        <div class="w-12 h-12 gradient-bg rounded-lg flex items-center justify-center text-slate-900 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-lg mb-2 text-slate-100">Análises de Performance</h3>
                        <p class="text-slate-400">Acompanhe visualizações, engagement e outros metrics para otimizar seu portfólio.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-20 bg-slate-900 px-4">
            <div class="container mx-auto max-w-6xl">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Planos e Preços</h2>
                    <p class="text-lg text-slate-400 max-w-2xl mx-auto">Escolha o plano ideal para o seu negócio. Todos os planos incluem suporte 24/7.</p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Plano Básico -->
                    <div class="bg-slate-800 rounded-xl p-6 shadow-md border border-slate-700 card-hover">
                        <h3 class="font-semibold text-lg mb-2 text-slate-100">Básico</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-slate-100">R$ 29</span>
                            <span class="text-slate-400">/mês</span>
                        </div>
                        <p class="text-slate-400 mb-6">Ideal para freelancers e pequenos projetos.</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">1 portfólio</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">5GB de armazenamento</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0极速赛车开奖直播z" clip-rule极速赛车开奖直播="evenodd" />
                                </svg>
                                <span class="text-slate-300">Suporte por email</span>
                            </li>
                        </ul>
                        <a href="#" class="block w-full bg-slate-700 hover:bg-slate-600 text-slate-200 text-center py-3 rounded-lg font-medium transition-colors">Começar Agora</a>
                    </div>
                    
                    <!-- Plano Profissional -->
                    <div class="bg-slate-800 rounded-xl p-6 shadow-md border border-slate-700 card-hover pricing-card popular">
                        <div class="popular-tag">Popular</div>
                        <h3 class="font-semibold text-lg mb-2 text-slate-100">Profissional</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-slate-100">R$ 59</span>
                            <span class="text-slate-400">/mês</span>
                        </div>
                        <p class="text-slate-400 mb-6">Perfeito para profissionais estabelecidos.</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">3 portfólios</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns极速赛车开奖直播="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">20GB de armazenamento</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0极速赛车开奖直播l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">Domínio personalizado</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 极速赛车开奖直播0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">Suporte prioritário</span>
                            </li>
                        </ul>
                        <a href="#" class="block w-full gradient-bg hover:opacity-90 text-slate-900 text-center py-3 rounded-lg font-medium transition-all">Começar Agora</a>
                    </div>
                    
                    <!-- Plano Empresarial -->
                    <div class="bg-slate-800 rounded-xl p-6 shadow-md border border-slate-700 card-hover">
                        <h3 class="font-semibold text-lg mb-2 text-slate-100">Estúdio</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-slate-100">R$ 99</span>
                            <span class="text-slate-400">/mês</span>
                        </div>
                        <p class="text-slate-400 mb-6">Para agências e estúdios criativos.</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">10 portfólios</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">100GB de armazenamento</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">Domínios personalizados</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">Analytics avançado</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">Suporte 24/7</span>
                            </li>
                        </ul>
                        <a href="#" class="block w-full bg-slate-700 hover:bg-slate-600 text-slate-200 text-center py-3 rounded-lg font-medium transition-colors">Começar Agora</a>
                    </div>
                    
                    <!-- Plano Personalizado -->
                    <div class="bg-slate-800 rounded-xl p-6 shadow-md border border-slate-700 card-hover">
                        <h3 class="font-semibold text-lg mb-2 text-slate-100">Enterprise</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-slate-100">SobConsulta</span>
                        </div>
                        <p class="text-slate-400 mb-6">Soluções sob medida para grandes empresas.</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">Portfólios ilimitados</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">Armazenamento personalizado</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">SSO e segurança empresarial</span>
                            </li>
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-极速赛车开奖直播4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-slate-300">Gerente de conta dedicado</span>
                            </li>
                        </ul>
                        <a href="#" class="block w-full bg-slate-700 hover:bg-slate-600 text-slate-200 text-center py-3 rounded-lg font-medium transition-colors">Fale Conosco</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="py-16 gradient-bg">
            <div class="container mx-auto max-w-4xl text-center px-4">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">Pronto para criar seu portfólio profissional?</h2>
                <p class="text-slate-800 text-lg mb-8">Junte-se a milhares de profissionais que já transformaram suas carreiras com o Portfoline</p>
                <a href="#pricing" class="bg-slate-900 text-primary-400 hover:bg-slate-800 px-8 py-3 rounded-lg font-medium text-lg inline-block transition-colors">Começar Agora</a>
                <p class="text-slate-800 text-sm mt-4">Teste grátis por 14 dias - Não é necessário cartão de crédito</p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 text-slate-400 py-12 px-4 border-t border-slate-800">
            <div class="container mx-auto max-w-6xl">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center mb-6">
                            <img class="w-32" src="assets/images/logo.png" alt="Portfoline">
                        </div>
                        <p class="mb-6">Soluções digitais para profissionais criativos exibirem seu trabalho.</p>
                        <div class="flex space-x-4">
                            <!-- Social media icons would go here -->
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-lg mb-6 text-slate-200">Produto</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Recursos</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Planos</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors">FAQ</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Blog</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-lg mb-6 text-slate-200">Empresa</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Sobre nós</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Carreiras</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Contato</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Imprensa</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-lg mb-6 text-slate-200">Legal</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Termos de uso</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Política de privacidade</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Cookies</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors">Segurança</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-slate-800 mt-12 pt-8 text-center">
                    <p>&copy; 2025 Portfoline. Todos os direitos reservados.</p>
                </div>
            </div>
        </footer>
    </body>
</html>