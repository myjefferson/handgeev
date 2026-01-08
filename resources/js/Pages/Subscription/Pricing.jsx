import React from 'react';
import { Head, usePage, useForm, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function Pricing({ auth, currentPlan, stripePrices }){
    const { props } = usePage();
    const { flash } = props;

    // Form para checkout
    const checkoutForm = useForm({
        price_id: '',
    });

    const handleCheckout = (priceId) => {
        checkoutForm.setData('price_id', priceId);
        checkoutForm.post(route('subscription.checkout'));
    };

    // Helper para verificar plano atual
    const isCurrentPlan = (planType) => {
        return currentPlan === planType;
    };

    // Configurações dos planos
    const plans = [
        {
            id: 'free',
            name: 'Gratuito',
            price: '$0',
            period: '/para sempre',
            badge: 'teal-badge',
            badgeText: 'FREE',
            description: 'Ideal para teste e pequenos projetos',
            features: [
                { text: '1 Workspace', included: true },
                { text: 'Até 3 tópicos', included: true },
                { text: 'Máximo de 10 campos', included: true },
                { text: 'API: 30 req/min', included: true },
                { text: 'Exportação de dados', included: false },
                { text: 'Acesso à API', included: false },
            ],
            button: {
                type: isCurrentPlan('free') ? 'current' : 'outline',
                text: isCurrentPlan('free') ? 'Plano Atual' : 'Plano Atual',
                icon: isCurrentPlan('free') ? 'fa-check' : 'fa-arrow-down',
            }
        },
        {
            id: 'start',
            name: 'Start',
            price: '$10',
            period: '/mês',
            badge: 'teal-badge',
            badgeText: 'START',
            description: 'Para pequenos negócios',
            features: [
                { text: '3 Workspaces', included: true },
                { text: 'Até 10 tópicos', included: true },
                { text: 'Máximo de 50 campos', included: true },
                { text: 'Exportação de dados', included: true },
                { text: 'API: 60 req/min', included: true },
                { text: 'Acesso à API', included: true },
            ],
            button: {
                type: isCurrentPlan('start') ? 'current' : 'teal',
                text: isCurrentPlan('start') ? 'Plano Atual' : (auth.user ? 'Assinar Agora' : 'Login para Assinar'),
                icon: isCurrentPlan('start') ? 'fa-check' : (auth.user ? 'fa-bolt' : 'fa-sign-in-alt'),
                action: isCurrentPlan('start') ? null : () => handleCheckout(stripePrices.start),
            }
        },
        {
            id: 'pro',
            name: 'Profissional',
            price: '$35',
            period: '/mês',
            badge: 'purple-badge',
            badgeText: 'PRO',
            description: 'Para negócios estabelecidos',
            features: [
                { text: '10 Workspaces', included: true },
                { text: 'Até 30 tópicos', included: true },
                { text: 'Máximo de 200 campos', included: true },
                { text: 'Exportação de dados', included: true },
                { text: 'API: 120 req/min', included: true },
                { text: 'Burst: 25 req', included: true },
                { text: 'Suporte prioritário', included: true },
            ],
            button: {
                type: isCurrentPlan('pro') ? 'current' : 'purple',
                text: isCurrentPlan('pro') ? 'Plano Atual' : (auth.user ? 'Assinar Agora' : 'Login para Assinar'),
                icon: isCurrentPlan('pro') ? 'fa-check' : (auth.user ? 'fa-crown' : 'fa-sign-in-alt'),
                action: isCurrentPlan('pro') ? null : () => handleCheckout(stripePrices.pro),
                pulse: !isCurrentPlan('pro') && auth.user
            }
        },
        {
            id: 'premium',
            name: 'Premium',
            price: '$70',
            period: '/mês',
            badge: 'blue-badge',
            badgeText: 'PREMIUM',
            description: 'Para empresas',
            features: [
                { text: 'Workspaces Ilimitados', included: true },
                { text: 'Tópicos Ilimitados', included: true },
                { text: 'Campos Ilimitados', included: true },
                { text: 'Exportação de dados', included: true },
                { text: 'API: 300 req/min', included: true },
                { text: 'Burst: 100 req', included: true },
                { text: 'Suporte 24/7', included: true },
            ],
            button: {
                type: isCurrentPlan('premium') ? 'current' : 'blue',
                text: isCurrentPlan('premium') ? 'Plano Atual' : (auth.user ? 'Assinar Agora' : 'Login para Assinar'),
                icon: isCurrentPlan('premium') ? 'fa-check' : (auth.user ? 'fa-rocket' : 'fa-sign-in-alt'),
                action: isCurrentPlan('premium') ? null : () => handleCheckout(handleCheckout(stripePrices.premium)),
            }
        }
    ];

    // Tabela de comparação
    const comparisonData = [
        {
            feature: 'Workspaces',
            free: '1',
            start: '3',
            pro: '10',
            premium: 'Ilimitados'
        },
        {
            feature: 'Tópicos',
            free: '3',
            start: '10',
            pro: '30',
            premium: 'Ilimitados'
        },
        {
            feature: 'Campos',
            free: '10',
            start: '50',
            pro: '200',
            premium: 'Ilimitados'
        },
        {
            feature: 'Exportação de Dados',
            free: false,
            start: true,
            pro: true,
            premium: true
        },
        {
            feature: 'Acesso à API',
            free: false,
            start: true,
            pro: true,
            premium: true
        },
        {
            feature: 'API Requests/Min',
            free: '30',
            start: '60',
            pro: '120',
            premium: '300'
        },
        {
            feature: 'Burst Requests',
            free: '5',
            start: '15',
            pro: '25',
            premium: '100'
        },
        {
            feature: 'Suporte',
            free: 'Básico',
            start: 'Padrão',
            pro: 'Prioritário',
            premium: '24/7'
        }
    ];

    // FAQ
    const faqs = [
        {
            question: 'Posso mudar de plano depois?',
            answer: 'Sim, você pode atualizar ou downgradar seu plano a qualquer momento. As alterações serão refletidas no próximo ciclo de faturamento.'
        },
        {
            question: 'Há cobrança por setup?',
            answer: 'Não, não há cobrança de setup para nenhum de nossos planos. Você paga apenas a taxa mensal ou anual conforme selecionado.'
        },
        {
            question: 'Quais métodos de pagamento são aceitos?',
            answer: 'Aceitamos cartão de crédito, débito, PIX e boleto bancário. Para o plano Enterprise, também aceitamos transferência bancária.'
        },
        {
            question: 'Há garantia de reembolso?',
            answer: 'Oferecemos garantia de 7 dias para todos os planos. Se não ficar satisfeito, reembolsaremos integralmente seu pagamento.'
        }
    ];

    const renderButton = (plan, buttonConfig) => {
        if (buttonConfig.type === 'current') {
            return (
                <button className="current-plan-button w-full py-3 rounded-lg font-semibold cursor-default">
                    <i className={`fas ${buttonConfig.icon} mr-2`}></i>
                    {buttonConfig.text}
                </button>
            );
        }

        if (!auth.user) {
            return (
                <Link href={route('login.show')} className={`${buttonConfig.type}-button w-full py-3 rounded-lg font-semibold text-center block`}>
                    <i className={`fas ${buttonConfig.icon} mr-2`}></i>
                    {buttonConfig.text}
                </Link>
            );
        }

        if (buttonConfig.href) {
            return (
                <form action={buttonConfig.href} method="POST">
                    <input type="hidden" name="_token" value={props.csrf_token} />
                    <button type="submit" className={`${buttonConfig.type}-button w-full py-3 rounded-lg font-semibold`}>
                        <i className={`fas ${buttonConfig.icon} mr-2`}></i>
                        {buttonConfig.text}
                    </button>
                </form>
            );
        }

        return (
            <button
                type="button"
                onClick={buttonConfig.action}
                disabled={checkoutForm.processing}
                className={`${buttonConfig.type}-button w-full py-3 rounded-lg font-semibold ${buttonConfig.pulse ? 'pulse' : ''} ${checkoutForm.processing ? 'opacity-50 cursor-not-allowed' : ''}`}
            >
                <i className={`fas ${buttonConfig.icon} mr-2`}></i>
                {checkoutForm.processing ? 'Processando...' : buttonConfig.text}
            </button>
        );
    };

    return (
        <SiteLayout>
            <Head title="Planos e Assinatura" description="Handgeev" />

            {/* Header */}
            <header className="user-menu fixed top-0 right-0 left-0 z-30 flex items-center justify-between px-4 md:px-6 py-3 bg-slate-900/80 backdrop-blur-lg">
                <div className="flex items-center">
                    <img className="w-44" src="/assets/images/logo.png" alt="Handgeev Logo" />
                </div>
                <div className="flex items-center space-x-4">
                    {auth.user ? (
                        <Link href={route('dashboard.home')} className="text-sm text-gray-300 hover:text-teal-400 transition-colors">
                            <i className="fas fa-arrow-left mr-1"></i> Voltar ao Dashboard
                        </Link>
                    ) : (
                        <Link href={route('home')} className="text-sm text-gray-300 hover:text-teal-400 transition-colors">
                            <i className="fas fa-arrow-left mr-1"></i> Voltar ao Início
                        </Link>
                    )}
                    
                    {auth.user ? (
                        <div className="relative">
                            <button className="flex items-center space-x-2 text-sm rounded-full focus:ring-2 focus:ring-teal-400">
                                <div className="user-avatar w-8 h-8 rounded-full bg-teal-400/10 flex items-center justify-center border border-teal-400/20">
                                    <i className="fas fa-user text-teal-400 text-sm"></i>
                                </div>
                            </button>
                        </div>
                    ) : (
                        <Link href={route('login.show')} className="text-sm text-gray-300 hover:text-teal-400 transition-colors">
                            <i className="fas fa-sign-in-alt mr-1"></i> Login
                        </Link>
                    )}
                </div>
            </header>

            <div className="pt-20 pb-10 px-4 max-w-7xl mx-auto">
                {/* Alertas */}
                {flash.success && (
                    <div className="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400">
                        <i className="fas fa-check-circle mr-2"></i>
                        {Array.isArray(flash.success) ? flash.success.filter(item => typeof item === 'string').join(', ') : flash.success}
                    </div>
                )}

                {flash.error && (
                    <div className="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400">
                        <i className="fas fa-exclamation-circle mr-2"></i>
                        {Array.isArray(flash.error) ? flash.error.filter(item => typeof item === 'string').join(', ') : flash.error}
                    </div>
                )}

                <div className="text-center mb-12">
                    <h1 className="text-4xl font-bold mb-4">Escolha seu Plano</h1>
                    <p className="text-gray-400 max-w-2xl mx-auto">
                        Selecione o plano ideal para suas necessidades. Todos os planos incluem recursos essenciais, com benefícios adicionais conforme sua assinatura.
                    </p>
                </div>

                {/* Planos de Assinatura */}
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                    {plans.map((plan, index) => (
                        <div 
                            key={plan.id}
                            className={`plan-card rounded-2xl p-6 flex flex-col ${
                                plan.id === 'pro' ? 'relative overflow-hidden border-purple-800 hover:border-purple-500 plan-pro' : 
                                plan.id === 'premium' ? 'relative overflow-hidden border-blue-800 hover:border-blue-500' : ''
                            }`}
                        >
                            <div className="mb-6">
                                <span className={`${plan.badge} text-xs font-semibold px-3 py-1 rounded-full`}>
                                    {plan.badgeText}
                                </span>
                                <h3 className="text-2xl font-bold mt-4">{plan.name}</h3>
                                <div className="mt-2">
                                    <span className="text-3xl font-bold">{plan.price}</span>
                                    <span className="text-gray-400">{plan.period}</span>
                                </div>
                                <p className="text-gray-400 text-sm mt-2">{plan.description}</p>
                            </div>
                            
                            <ul className="feature-list space-y-3 mb-8 flex-grow">
                                {plan.features.map((feature, featureIndex) => (
                                    <li 
                                        key={featureIndex} 
                                        className={`flex items-center ${
                                            feature.included ? 'hover:text-teal-400' : 'text-gray-500'
                                        }`}
                                    >
                                        <i className={`fas ${
                                            feature.included ? 
                                            (plan.id === 'pro' ? 'fa-check-circle text-purple-400' : 
                                             plan.id === 'premium' ? 'fa-check-circle text-blue-500' : 
                                             'fa-check-circle text-teal-400') : 
                                            'fa-times-circle'
                                        } mr-2`}></i>
                                        <span>{feature.text}</span>
                                    </li>
                                ))}
                            </ul>
                            
                            {renderButton(plan, plan.button)}
                        </div>
                    ))}
                </div>

                {/* Comparação de Planos */}
                <div className="bg-slate-800/50 rounded-2xl p-6 mb-12">
                    <h2 className="text-2xl font-bold mb-6 text-center">Comparação Detalhada de Planos</h2>
                    
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-slate-700">
                                    <th className="pb-4 text-left">Recurso</th>
                                    <th className="pb-4 text-center">Free</th>
                                    <th className="pb-4 text-center">Start</th>
                                    <th className="pb-4 text-center">Pro</th>
                                    <th className="pb-4 text-center">Premium</th>
                                </tr>
                            </thead>
                            <tbody>
                                {comparisonData.map((row, index) => (
                                    <tr key={index} className="border-b border-slate-700">
                                        <td className="py-4">{row.feature}</td>
                                        <td className="py-4 text-center">
                                            {typeof row.free === 'boolean' ? (
                                                row.free ? (
                                                    <i className="fas fa-check text-teal-400"></i>
                                                ) : (
                                                    <i className="fas fa-times text-red-400"></i>
                                                )
                                            ) : (
                                                row.free
                                            )}
                                        </td>
                                        <td className="py-4 text-center">
                                            {typeof row.start === 'boolean' ? (
                                                row.start ? (
                                                    <i className="fas fa-check text-teal-400"></i>
                                                ) : (
                                                    <i className="fas fa-times text-red-400"></i>
                                                )
                                            ) : (
                                                row.start
                                            )}
                                        </td>
                                        <td className="py-4 text-center">
                                            {typeof row.pro === 'boolean' ? (
                                                row.pro ? (
                                                    <i className="fas fa-check text-teal-400"></i>
                                                ) : (
                                                    <i className="fas fa-times text-red-400"></i>
                                                )
                                            ) : (
                                                row.pro
                                            )}
                                        </td>
                                        <td className="py-4 text-center">
                                            {typeof row.premium === 'boolean' ? (
                                                row.premium ? (
                                                    <i className="fas fa-check text-teal-400"></i>
                                                ) : (
                                                    <i className="fas fa-times text-red-400"></i>
                                                )
                                            ) : (
                                                row.premium
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* Perguntas Frequentes */}
                <div className="bg-slate-800/50 rounded-2xl p-6">
                    <h2 className="text-2xl font-bold mb-6 text-center">Perguntas Frequentes</h2>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {faqs.map((faq, index) => (
                            <div key={index}>
                                <h3 className="text-lg font-semibold mb-2">{faq.question}</h3>
                                <p className="text-gray-400">{faq.answer}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            <style jsx>{`
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
            `}</style>
        </SiteLayout>
    );
};