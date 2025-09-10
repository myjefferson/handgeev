<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planos - Handgeev</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --teal-primary: #08fff0;
            --teal-secondary: rgba(8, 255, 240, 0.1);
            --teal-hover: rgba(8, 255, 240, 0.2);
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
        
        .plan-highlight {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, rgba(8, 255, 240, 0.1) 100%);
            border: 2px solid var(--teal-primary);
            position: relative;
            overflow: hidden;
        }
        
        .plan-highlight::before {
            content: 'RECOMENDADO';
            position: absolute;
            top: 15px;
            right: -30px;
            background: var(--teal-primary);
            color: #0f172a;
            font-size: 10px;
            font-weight: bold;
            padding: 5px 30px;
            transform: rotate(45deg);
        }
        
        .feature-list li {
            transition: all 0.2s ease;
        }
        
        .feature-list li:hover {
            color: var(--teal-primary);
            transform: translateX(5px);
        }
        
        .teal-badge {
            background: rgba(8, 255, 240, 0.1);
            color: var(--teal-primary);
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
        
        .outline-button {
            border: 1px solid var(--teal-primary);
            color: var(--teal-primary);
            transition: all 0.3s ease;
        }
        
        .outline-button:hover {
            background: rgba(8, 255, 240, 0.1);
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
</head>
<body class="font-sans antialiased text-white">
    <!-- Header -->
    <header class="user-menu fixed top-0 right-0 left-0 z-30 flex items-center justify-between px-4 md:px-6 py-3">
        <div class="flex items-center">
            <img class="w-44" src="assets/images/logo.png" alt="Handgeev Logo">
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('dashboard.home') }}" class="text-sm text-gray-300 hover:text-teal-400 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Voltar ao Dashboard
            </a>
            <div class="relative">
                <button id="userDropdownButton" data-dropdown-toggle="userDropdown" class="flex items-center space-x-2 text-sm rounded-full focus:ring-2 focus:ring-teal-400">
                    <div class="user-avatar w-8 h-8 rounded-full bg-teal-400/10 flex items-center justify-center border border-teal-400/20">
                        <i class="fas fa-user text-teal-400 text-sm"></i>
                    </div>
                </button>
            </div>
        </div>
    </header>

    <div class="pt-20 pb-10 px-4 max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold mb-4">Escolha seu Plano</h1>
            <p class="text-gray-400 max-w-2xl mx-auto">Selecione o plano ideal para suas necessidades. Todos os planos incluem recursos essenciais, com benefícios adicionais conforme sua assinatura.</p>
        </div>

        <!-- Planos de Assinatura -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <!-- Plano Free -->
            <div class="plan-card rounded-2xl p-6 flex flex-col">
                <div class="mb-6">
                    <span class="teal-badge text-xs font-semibold px-3 py-1 rounded-full">FREE</span>
                    <h3 class="text-2xl font-bold mt-4">Plano Gratuito</h3>
                    <div class="mt-2">
                        <span class="text-3xl font-bold">R$ 0</span>
                        <span class="text-gray-400">/para sempre</span>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">Ideal para quem está começando</p>
                </div>
                
                <ul class="feature-list space-y-3 mb-8 flex-grow">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>3 Workspace</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Até 3 tópicos</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Máximo de 10 campos</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Acesso básico</span>
                    </li>
                    <li class="flex items-center text-gray-500">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span>Domínio personalizado</span>
                    </li>
                    <li class="flex items-center text-gray-500">
                        <i class="fas fa-times-circle mr-2"></i>
                        <span>Exportação de dados</span>
                    </li>
                </ul>
                
                <button class="outline-button w-full py-3 rounded-lg font-semibold">
                    Plano Atual
                </button>
            </div>
            
            <!-- Plano Pro -->
            <div class="plan-card plan-highlight rounded-2xl p-6 flex flex-col">
                <div class="mb-6">
                    <span class="teal-badge text-xs font-semibold px-3 py-1 rounded-full">PRO</span>
                    <h3 class="text-2xl font-bold mt-4">Plano Pro</h3>
                    <div class="mt-2">
                        <span class="text-3xl font-bold">R$ 29</span>
                        <span class="text-gray-400">/mês</span>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">Ideal para profissionais</p>
                </div>
                
                <ul class="feature-list space-y-3 mb-8 flex-grow">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>5 Workspaces</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Tópicos ilimitados</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Campos ilimitados</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Acesso prioritário</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Domínio personalizado</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Exportação de dados</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Suporte premium</span>
                    </li>
                </ul>
                
                <button class="teal-button w-full py-3 rounded-lg font-semibold pulse">
                    Assinar Agora
                </button>
            </div>
            
            <!-- Plano Premium -->
            <div class="plan-card rounded-2xl p-6 flex flex-col">
                <div class="mb-6">
                    <span class="teal-badge text-xs font-semibold px-3 py-1 rounded-full">PREMIUM</span>
                    <h3 class="text-2xl font-bold mt-4">Premium</h3>
                    <div class="mt-2">
                        <span class="text-3xl font-bold">Sob Consulta</span>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">Para empresas e equipes</p>
                </div>
                
                <ul class="feature-list space-y-3 mb-8 flex-grow">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Workspaces ilimitados</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Tópicos ilimitados</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Campos ilimitados</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Gerenciamento de usuários</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Domínios personalizados</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>API completa</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Suporte 24/7</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-teal-400 mr-2"></i>
                        <span>Relatórios avançados</span>
                    </li>
                </ul>
                
                <button class="outline-button w-full py-3 rounded-lg font-semibold">
                    Contatar Vendas
                </button>
            </div>
        </div>

        <!-- Comparação de Planos -->
        <div class="bg-slate-800/50 rounded-2xl p-6 mb-12">
            <h2 class="text-2xl font-bold mb-6 text-center">Comparação Detalhada de Planos</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700">
                            <th class="pb-4 text-left">Recurso</th>
                            <th class="pb-4 text-center">Free</th>
                            <th class="pb-4 text-center">Pro</th>
                            <th class="pb-4 text-center">Premium</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-700">
                            <td class="py-4">Workspaces</td>
                            <td class="py-4 text-center">1</td>
                            <td class="py-4 text-center">5</td>
                            <td class="py-4 text-center">Ilimitados</td>
                        </tr>
                        <tr class="border-b border-slate-700">
                            <td class="py-4">Tópicos por Workspace</td>
                            <td class="py-4 text-center">3</td>
                            <td class="py-4 text-center">Ilimitados</td>
                            <td class="py-4 text-center">Ilimitados</td>
                        </tr>
                        <tr class="border-b border-slate-700">
                            <td class="py-4">Campos por Tópico</td>
                            <td class="py-4 text-center">10</td>
                            <td class="py-4 text-center">Ilimitados</td>
                            <td class="py-4 text-center">Ilimitados</td>
                        </tr>
                        <tr class="border-b border-slate-700">
                            <td class="py-4">Domínio Personalizado</td>
                            <td class="py-4 text-center"><i class="fas fa-times text-red-400"></i></td>
                            <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                            <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                        </tr>
                        <tr class="border-b border-slate-700">
                            <td class="py-4">Exportação de Dados</td>
                            <td class="py-4 text-center"><i class="fas fa-times text-red-400"></i></td>
                            <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                            <td class="py-4 text-center"><i class="fas fa-check text-teal-400"></i></td>
                        </tr>
                        <tr class="border-b border-slate-700">
                            <td class="py-4">Suporte Prioritário</td>
                            <td class="py-4 text-center">Básico</td>
                            <td class="py-4 text-center">Pro</td>
                            <td class="py-4 text-center">24/7</td>
                        </tr>
                        <tr class="border-b border-slate-700">
                            <td class="py-4">Usuários Convidados</td>
                            <td class="py-4 text-center">-</td>
                            <td class="py-4 text-center">3</td>
                            <td class="py-4 text-center">Ilimitados</td>
                        </tr>
                        <tr>
                            <td class="py-4">API Access</td>
                            <td class="py-4 text-center"><i class="fas fa-times text-red-400"></i></td>
                            <td class="py-4 text-center">Leitura</td>
                            <td class="py-4 text-center">Completo</td>
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
                    <p class="text-gray-400">Aceitamos cartão de crédito, débito, PIX e boleto bancário. Para o plano Premium, também aceitamos transferência bancária.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-2">Há garantia de reembolso?</h3>
                    <p class="text-gray-400">Oferecemos garantia de 7 dias para todos os planos. Se não ficar satisfeito, reembolsaremos integralmente seu pagamento.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simulação de seleção de plano
            const planButtons = document.querySelectorAll('.teal-button, .outline-button');
            
            planButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const plan = this.closest('.plan-card').querySelector('h3').textContent;
                    alert(`Você selecionou o ${plan}. Em uma implementação real, você seria redirecionado para o processo de pagamento.`);
                });
            });
            
            // Tooltip para recursos
            const features = document.querySelectorAll('.feature-list li');
            features.forEach(feature => {
                feature.addEventListener('mouseenter', function() {
                    this.style.cursor = 'pointer';
                });
            });
        });
    </script>
</body>
</html>