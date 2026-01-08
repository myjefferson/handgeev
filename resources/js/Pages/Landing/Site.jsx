// resources/js/Pages/Home.jsx
import React, { useState, useEffect } from 'react';
import { Head, Link } from '@inertiajs/react';

import './css/site.css'
import SiteLayout from '@/Layouts/SiteLayout';

export default function Home() {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    // Fechar menu mobile ao redimensionar para desktop
    useEffect(() => {
        const handleResize = () => {
            if (window.innerWidth >= 768) {
                setMobileMenuOpen(false);
            }
        };

        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    const scrollToSection = (sectionId) => {
        const element = document.getElementById(sectionId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
        setMobileMenuOpen(false);
    };

    return (
        <SiteLayout>
            <Head>
                <title>Handgeev - Crie e Gerencie APIs de Forma Intuitiva</title>
            </Head>

            <div className="font-sans antialiased text-white" style={{
                background: 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)'
            }}>
                {/* Header/Navigation */}
                <header className="fixed w-full bg-slate-900 bg-opacity-95 z-50 border-b border-slate-700">
                    <div className="container mx-auto px-2 sm:px-4 py-4">
                        <div className="flex justify-between items-center">
                            <div className="flex items-center">
                                <img className="w-40" src="/assets/images/logo.png" alt="Handgeev" />
                            </div>
                            
                            {/* Menu Desktop */}
                            <nav className="hidden lg:flex space-x-10">
                                <button 
                                    onClick={() => scrollToSection('features')}
                                    className="nav-link text-slate-300 hover:text-teal-400 transition-colors"
                                >
                                    Funcionalidades
                                </button>
                                <button 
                                    onClick={() => scrollToSection('how-it-works')}
                                    className="nav-link text-slate-300 hover:text-teal-400 transition-colors"
                                >
                                    Como Funciona
                                </button>
                                <button 
                                    onClick={() => scrollToSection('pricing')}
                                    className="nav-link text-slate-300 hover:text-teal-400 transition-colors"
                                >
                                    Preços
                                </button>
                                <button 
                                    onClick={() => scrollToSection('use-cases')}
                                    className="nav-link text-slate-300 hover:text-teal-400 transition-colors"
                                >
                                    Casos de Uso
                                </button>
                            </nav>
                            
                            <div className="flex items-center space-x-4">                    
                                <Link 
                                    href={route('login.show')} 
                                    className="hidden md:inline-block text-teal-400 hover:text-teal-300 font-medium"
                                >
                                    Login
                                </Link>
                                <Link 
                                    href={route('register.show')}
                                    className="text-[13px] sm:text-[16px] bg-teal-500 hover:bg-teal-400 text-slate-900 px-2 sm:px-5 py-1 sm:py-2 rounded-lg font-medium transition-colors teal-glow-hover"
                                >
                                    Começar Agora
                                </Link>
                                
                                {/* Botão do menu mobile */}
                                <button 
                                    onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                                    className="lg:hidden text-slate-300 p-2"
                                    aria-expanded={mobileMenuOpen}
                                >
                                    <i className="fas fa-bars text-xl"></i>
                                </button>
                            </div>
                        </div>

                        {/* Menu Mobile */}
                        <div className={`${mobileMenuOpen ? 'block' : 'hidden'} lg:hidden mt-4 px-3 py-4 border-t border-slate-700`}>
                            <div className="flex flex-col space-y-4">
                                <button 
                                    onClick={() => scrollToSection('features')}
                                    className="nav-link text-slate-300 hover:text-teal-400 transition-colors py-2 text-left"
                                >
                                    Funcionalidades
                                </button>
                                <button 
                                    onClick={() => scrollToSection('how-it-works')}
                                    className="nav-link text-slate-300 hover:text-teal-400 transition-colors py-2 text-left"
                                >
                                    Como Funciona
                                </button>
                                <button 
                                    onClick={() => scrollToSection('pricing')}
                                    className="nav-link text-slate-300 hover:text-teal-400 transition-colors py-2 text-left"
                                >
                                    Preços
                                </button>
                                <button 
                                    onClick={() => scrollToSection('use-cases')}
                                    className="nav-link text-slate-300 hover:text-teal-400 transition-colors py-2 text-left"
                                >
                                    Casos de Uso
                                </button>
                                <Link 
                                    href={route('login.show')}
                                    className="text-teal-400 hover:text-teal-300 font-medium py-2 text-left"
                                >
                                    Login
                                </Link>
                            </div>
                        </div>
                    </div>
                </header>

                {/* Hero Section */}
                <section className="pt-32 pb-20 px-4">
                    <div className="container mx-auto max-w-6xl">
                        <div className="flex flex-col lg:flex-row items-center gap-12">
                            <div className="lg:w-1/2">
                                <h1 className="text-4xl md:text-5xl font-bold leading-tight mb-6">
                                    Crie e Gerencie APIs de Forma{' '}
                                    <span className="text-teal-400">Intuitiva</span>
                                </h1>
                                <p className="text-lg text-slate-400 mb-8">
                                    Transforme suas ideias em APIs RESTful completas em minutos, sem escrever código. 
                                    Gerencie dados estruturados e acesse endpoints automaticamente gerados.
                                </p>
                                <div className="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                                    <button 
                                        onClick={() => scrollToSection('pricing')}
                                        className="bg-teal-500 hover:bg-teal-400 text-slate-900 px-6 py-3 rounded-lg font-medium text-center transition-colors teal-glow-hover"
                                    >
                                        <i className="fas fa-bolt mr-2"></i>Começar de Graça
                                    </button>
                                    <button 
                                        onClick={() => scrollToSection('how-it-works')}
                                        className="border border-teal-500 text-teal-500 hover:bg-slate-800 px-6 py-3 rounded-lg font-medium text-center transition-colors"
                                    >
                                        <i className="fas fa-play-circle mr-2"></i>Ver Demonstração
                                    </button>
                                </div>
                                <div className="mt-8 flex items-center text-slate-400">
                                    <i className="fas fa-check-circle text-teal-400 mr-2"></i>
                                    <span className="text-sm">Sem necessidade de cartão de crédito</span>
                                </div>
                            </div>
                            
                            <div className="w-full lg:w-1/2">
                                <div className="bg-slate-800 rounded-2xl p-6 shadow-xl border border-slate-700 teal-glow">
                                    <div className="code-block">
                                        <div className="flex space-x-2 p-4 border-b border-slate-700">
                                            <div className="w-3 h-3 rounded-full bg-red-400"></div>
                                            <div className="w-3 h-3 rounded-full bg-yellow-400"></div>
                                            <div className="w-3 h-3 rounded-full bg-green-400"></div>
                                        </div>
                                        <div className="overflow-auto p-6">
                                            <div className="text-sm font-mono text-slate-300">
                                                <div className="text-teal-400">// Endpoint da API</div>
                                                <div className="text-purple-400">GET</div> 
                                                <span className="text-green-400">https://www.handgeev.com/api/workspace/</span>
                                                <span className="text-yellow-400">:id</span>
                                                <span className="text-blue-400">/data</span>
                                                
                                                <div className="mt-4 text-teal-400">// Resposta JSON</div>
                                                <div>{'{'}</div>
                                                <div className="ml-4">"status": <span className="text-green-400">"success"</span>,</div>
                                                <div className="ml-4">"data": [</div>
                                                <div className="ml-8">{'{'}</div>
                                                <div className="ml-12">"id": <span className="text-blue-400">1</span>,</div>
                                                <div className="ml-12">"name": <span className="text-green-400">"Example"</span>,</div>
                                                <div className="ml-12">"value": <span className="text-green-400">"Dynamic data"</span></div>
                                                <div className="ml-8">{'}'}</div>
                                                <div className="ml-4">]</div>
                                                <div>{'}'}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Stats Section */}
                <section className="py-16 bg-slate-800">
                    <div className="container mx-auto max-w-6xl px-4">
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                            <div>
                                <div className="text-3xl font-bold text-teal-400 mb-2">8K+</div>
                                <div className="text-slate-400">APIs Criadas</div>
                            </div>
                            <div>
                                <div className="text-3xl font-bold text-teal-400 mb-2">99.9%</div>
                                <div className="text-slate-400">Tempo de Atividade</div>
                            </div>
                            <div>
                                <div className="text-3xl font-bold text-teal-400 mb-2">1.5M</div>
                                <div className="text-slate-400">Requisições/Dia</div>
                            </div>
                            <div>
                                <div className="text-3xl font-bold text-teal-400 mb-2">1.2s</div>
                                <div className="text-slate-400">Tempo de Resposta</div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Features Section */}
                <section id="features" className="py-20 bg-slate-900 px-4">
                    <div className="container mx-auto max-w-6xl">
                        <div className="text-center mb-16">
                            <h2 className="text-3xl md:text-4xl font-bold mb-4">Funcionalidades Poderosas</h2>
                            <p className="text-lg text-slate-400 max-w-2xl mx-auto">
                                Tudo que você precisa para criar e gerenciar APIs de forma eficiente
                            </p>
                        </div>
                        
                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {features.map((feature, index) => (
                                <FeatureCard key={index} {...feature} />
                            ))}
                        </div>
                    </div>
                </section>

                {/* How It Works Section */}
                <HowItWorksSection />

                {/* Use Cases Section */}
                <UseCasesSection />

                {/* Pricing Section */}
                <PricingSection />

                {/* Final CTA Section */}
                <section className="py-16 gradient-bg">
                    <div className="container mx-auto max-w-4xl text-center px-4">
                        <h2 className="text-3xl md:text-4xl font-bold text-slate-900 mb-6">
                            Pronto para Transformar suas Ideias em APIs?
                        </h2>
                        <p className="text-slate-800 text-lg mb-8">
                            Junte-se a milhares de desenvolvedores que já estão criando APIs incríveis com o Handgeev
                        </p>
                        <Link 
                            href={route('register.show')}
                            className="bg-slate-900 text-teal-400 hover:bg-slate-800 px-8 py-3 rounded-lg font-medium text-lg inline-block transition-colors teal-glow-hover"
                        >
                            <i className="fas fa-rocket mr-2"></i>Começar Agora
                        </Link>
                        <p className="text-slate-800 text-sm mt-4">
                            Teste grátis por 14 dias - Sem compromisso
                        </p>
                    </div>
                </section>
            </div>

           
        </SiteLayout>
    );
}

// Componente de Card de Funcionalidade
const FeatureCard = ({ icon, title, description }) => (
    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
        <div className="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
            <i className={`fas fa-${icon} text-xl`}></i>
        </div>
        <h3 className="font-semibold text-lg mb-2 text-white">{title}</h3>
        <p className="text-slate-400">{description}</p>
    </div>
);

// Dados das funcionalidades
const features = [
    {
        icon: 'layer-group',
        title: 'Workspaces Organizados',
        description: 'Crie workspaces separados para diferentes projetos e mantenha tudo organizado'
    },
    {
        icon: 'folder-tree',
        title: 'Tópicos Hierárquicos',
        description: 'Estruture seus dados em tópicos e subtópicos de forma intuitiva'
    },
    {
        icon: 'table',
        title: 'Campos Dinâmicos',
        description: 'Defina campos personalizados para cada tópico com tipos de dados variados'
    },
    {
        icon: 'code',
        title: 'API Automática',
        description: 'Endpoints RESTful gerados automaticamente para seus dados estruturados'
    },
    {
        icon: 'shield-alt',
        title: 'Segurança Robusta',
        description: 'Autenticação e autorização integradas para proteger seus dados'
    },
    {
        icon: 'bolt',
        title: 'Baixa Latência',
        description: 'Infraestrutura otimizada para respostas rápidas e confiáveis'
    }
];

// Componente da Seção Como Funciona
const HowItWorksSection = () => (
    <section id="how-it-works" className="py-20 bg-slate-800 px-4">
        <div className="container mx-auto max-w-6xl">
            <div className="text-center mb-16">
                <h2 className="text-3xl md:text-4xl font-bold mb-4">Como Funciona</h2>
                <p className="text-lg text-slate-400 max-w-2xl mx-auto">
                    Crie sua primeira API em apenas 4 passos simples
                </p>
            </div>
            
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                {steps.map((step, index) => (
                    <div key={index} className="text-center">
                        <div className="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-slate-900 font-bold text-xl mx-auto mb-4">
                            {index + 1}
                        </div>
                        <h3 className="font-semibold text-lg mb-2 text-white">{step.title}</h3>
                        <p className="text-slate-400">{step.description}</p>
                    </div>
                ))}
            </div>
            
            <div className="mt-16 bg-slate-900 rounded-2xl p-8 border border-slate-700">
                <div className="flex flex-col lg:flex-row items-center gap-8">
                    <div className="lg:w-1/2">
                        <h3 className="text-2xl font-bold mb-4 text-white">Seu Endpoint Está Pronto!</h3>
                        <p className="text-slate-400 mb-6">
                            Assim que você estrutura seus dados, o Handgeev automaticamente gera endpoints RESTful completos com todas as operações CRUD.
                        </p>
                        <div className="space-y-2">
                            {endpointFeatures.map((feature, index) => (
                                <div key={index} className="flex items-center">
                                    <i className="fas fa-check text-teal-400 mr-3"></i>
                                    <span className="text-slate-300">{feature}</span>
                                </div>
                            ))}
                        </div>
                    </div>
                    <div className="w-full lg:w-1/2">
                        <div className="code-block">
                            <div className="p-4 bg-slate-800 border-b border-slate-700">
                                <span className="text-teal-400 text-sm">// Endpoint da API</span>
                            </div>
                            <div className="overflow-auto p-4">
                                <div className="text-sm font-mono text-slate-300">
                                    <div className="text-purple-400">fetch</div>(<span className="text-green-400">'https://www.handgeev.com/api/workspace/123/data'</span>)
                                    <div>  .then(<span className="text-blue-400">response</span> {'=> response.'}<span className="text-purple-400">json</span>())</div>
                                    <div>  .then(<span className="text-blue-400">data</span> {' => {'}</div>
                                    <div>    <span className="text-gray-500">// Trabalhe com os dados JSON</span></div>
                                    <div>    console.<span className="text-purple-400">log</span>(data);</div>
                                    <div>  {'}'});</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
);

const steps = [
    {
        title: 'Crie um Workspace',
        description: 'Inicie um novo projeto criando um workspace organizado'
    },
    {
        title: 'Estruture os Tópicos',
        description: 'Defina tópicos e campos para organizar seus dados'
    },
    {
        title: 'Adicione os Dados',
        description: 'Insira seus dados nos campos estruturados criados'
    },
    {
        title: 'Acesse a API',
        description: 'Use os endpoints automaticamente gerados'
    }
];

const endpointFeatures = [
    'Operações CRUD automáticas',
    'Filtros e consultas avançadas',
    'Paginação integrada',
    'Respostas JSON padronizadas'
];

// Componente da Seção de Casos de Uso
const UseCasesSection = () => (
    <section id="use-cases" className="py-20 bg-slate-900 px-4">
        <div className="container mx-auto max-w-6xl">
            <div className="text-center mb-16">
                <h2 className="text-3xl md:text-4xl font-bold mb-4">Casos de Uso</h2>
                <p className="text-lg text-slate-400 max-w-2xl mx-auto">
                    Ideal para diversos cenários de desenvolvimento
                </p>
            </div>
            
            <div className="grid md:grid-cols-3 gap-8">
                {useCases.map((useCase, index) => (
                    <UseCaseCard key={index} {...useCase} />
                ))}
            </div>
        </div>
    </section>
);

const UseCaseCard = ({ icon, title, description, features }) => (
    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700 card-hover">
        <div className="feature-icon w-12 h-12 rounded-lg flex items-center justify-center text-teal-400 mb-4">
            <i className={`fas fa-${icon} text-xl`}></i>
        </div>
        <h3 className="font-semibold text-lg mb-2 text-white">{title}</h3>
        <p className="text-slate-400 mb-4">{description}</p>
        <ul className="space-y-2 text-slate-400 text-sm">
            {features.map((feature, index) => (
                <li key={index} className="flex items-center">
                    <i className="fas fa-check text-teal-400 mr-2 text-xs"></i>
                    {feature}
                </li>
            ))}
        </ul>
    </div>
);

const useCases = [
    {
        icon: 'mobile-alt',
        title: 'APIs para Apps',
        description: 'Desenvolva aplicativos móveis e web com backends rápidos e escaláveis',
        features: [
            'Prototipagem rápida',
            'Desenvolvimento de MVP',
            'Ambientes de teste'
        ]
    },
    {
        icon: 'desktop',
        title: 'Microsserviços',
        description: 'Crie serviços especializados para arquiteturas distribuídas',
        features: [
            'Prototipagem de serviços',
            'Agregação de dados',
            'Gateways de API'
        ]
    },
    {
        icon: 'database',
        title: 'APIs de Mock',
        description: 'Simule APIs reais para desenvolvimento e testes',
        features: [
            'Desenvolvimento frontend',
            'Testes de integração',
            'Ambientes de demonstração'
        ]
    }
];

// Componente da Seção de Preços
const PricingSection = () => (
    <section id="pricing" className="py-20 bg-slate-800 px-4">
        <div className="container mx-auto max-w-6xl">
            <div className="text-center mb-16">
                <h2 className="text-3xl md:text-4xl font-bold mb-4">Planos e Preços</h2>
                <p className="text-lg text-slate-400 max-w-2xl mx-auto">
                    Escolha o plano perfeito para suas necessidades
                </p>
            </div>
            
            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                {pricingPlans.map((plan, index) => (
                    <PricingCard key={index} {...plan} />
                ))}
            </div>

            {/* Tabela de Comparação */}
            <ComparisonTable />
        </div>
    </section>
);

const PricingCard = ({ name, price, period, description, features, buttonText, popular, recommended, badgeColor, buttonColor, popularTag }) => (
    <div className={`bg-slate-900 rounded-xl p-6 shadow-md border ${
        popular ? 'border-purple-500 border-2' : 'border-slate-700'
    } card-hover relative overflow-hidden ${popular ? 'pricing-card popular' : ''}`}>
        {popular && <div className="recommended-tag bg-purple-500">RECOMENDADO</div>}
        {popularTag && <div className="popular-tag">{popularTag}</div>}
        
        <div className="mb-4">
            <span className={`${badgeColor} text-xs font-semibold px-3 py-1 rounded-full`}>
                {name}
            </span>
            <h3 className="font-semibold text-lg mb-2 text-white mt-3">{name}</h3>
            <div className="mb-4">
                <span className="text-4xl font-bold text-white">${price}</span>
                <span className="text-slate-400">/{period}</span>
            </div>
        </div>
        <p className="text-slate-400 mb-6 text-sm">{description}</p>
        <ul className="space-y-3 mb-8">
            {features.map((feature, index) => (
                <li key={index} className="flex items-center">
                    <i className={`fas fa-${feature.included ? 'check' : 'times'} ${
                        feature.included ? (popular ? 'text-purple-400' : 'text-teal-400') : 'text-gray-500'
                    } mr-2`}></i>
                    <span className="text-sm">{feature.text}</span>
                </li>
            ))}
        </ul>
        <Link 
            href={route('register.show', { plan: name.toLowerCase() })}
            className={`block w-full ${buttonColor} text-center py-3 rounded-lg font-medium transition-colors ${
                popular ? 'pulse' : ''
            }`}
        >
            {buttonText}
        </Link>
    </div>
);

const pricingPlans = [
    {
        name: 'FREE',
        price: '0',
        period: 'mês',
        description: 'Perfeito para começar e testar a plataforma',
        badgeColor: 'teal-badge',
        buttonColor: 'bg-slate-700 hover:bg-slate-600 text-white',
        buttonText: 'Começar de Graça',
        features: [
            { text: '1 Workspace', included: true },
            { text: 'Até 3 tópicos', included: true },
            { text: 'Máximo 10 campos por tópico', included: true },
            { text: 'API: 30 req/min', included: true },
            { text: 'Exportação de dados', included: true },
            { text: 'Importação de dados', included: false },
            { text: 'Acesso à Geev Studio REST', included: false }
        ]
    },
    {
        name: 'START',
        price: '10',
        period: 'mês',
        description: 'Ideal para projetos pequenos e médios',
        badgeColor: 'teal-badge',
        buttonColor: 'bg-teal-500 hover:bg-teal-400 text-slate-900',
        buttonText: 'Escolher Start',
        features: [
            { text: '3 Workspaces', included: true },
            { text: 'Até 10 tópicos', included: true },
            { text: 'Máximo 50 campos por tópico', included: true },
            { text: 'Exportação de dados', included: true },
            { text: 'API: 60 req/min', included: true },
            { text: 'Acesso à API', included: true }
        ]
    },
    {
        name: 'PRO',
        price: '35',
        period: 'mês',
        description: 'Para desenvolvedores e equipes profissionais',
        badgeColor: 'purple-badge',
        buttonColor: 'bg-purple-600 hover:bg-purple-500 text-white',
        buttonText: 'Escolher Pro',
        popular: true,
        features: [
            { text: '10 Workspaces', included: true },
            { text: 'Até 30 tópicos', included: true },
            { text: 'Máximo 200 campos por tópico', included: true },
            { text: 'Exportação de dados', included: true },
            { text: 'API: 120 req/min', included: true },
            { text: 'Burst: 25 req', included: true },
            { text: 'Suporte prioritário', included: true }
        ]
    },
    {
        name: 'PREMIUM',
        price: '70',
        period: 'mês',
        description: 'Solução completa para empresas',
        badgeColor: 'blue-badge',
        buttonColor: 'bg-blue-600 hover:bg-blue-500 text-white',
        buttonText: 'Escolher Premium',
        features: [
            { text: 'Workspaces Ilimitados', included: true },
            { text: 'Tópicos Ilimitados', included: true },
            { text: 'Campos Ilimitados', included: true },
            { text: 'Exportação de dados', included: true },
            { text: 'API: 300 req/min', included: true },
            { text: 'Burst: 100 req', included: true },
            { text: 'Suporte 24/7', included: true }
        ]
    }
];

const ComparisonTable = () => (
    <div className="mt-16 bg-slate-900 rounded-2xl p-6 border border-slate-700">
        <h3 className="text-2xl font-bold mb-6 text-center text-white">Comparação Detalhada de Planos</h3>
        
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
                        <tr key={index} className={index < comparisonData.length - 1 ? 'border-b border-slate-700' : ''}>
                            <td className="py-4">{row.feature}</td>
                            {row.plans.map((plan, planIndex) => (
                                <td key={planIndex} className="py-4 text-center">
                                    {typeof plan === 'boolean' ? (
                                        <i className={`fas fa-${plan ? 'check' : 'times'} ${
                                            plan ? 'text-teal-400' : 'text-red-400'
                                        }`}></i>
                                    ) : (
                                        plan
                                    )}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    </div>
);

const comparisonData = [
    { feature: 'Workspaces', plans: ['1', '3', '10', 'Ilimitados'] },
    { feature: 'Tópicos', plans: ['3', '10', '30', 'Ilimitados'] },
    { feature: 'Campos por tópico', plans: ['10', '50', '200', 'Ilimitados'] },
    { feature: 'Exportação de Dados', plans: [true, true, true, true] },
    { feature: 'Importação de Dados', plans: [false, true, true, true] },
    { feature: 'Acesso à Geev Studio REST', plans: [false, true, true, true] },
    { feature: 'API Requests/Min', plans: ['30', '60', '120', '300'] },
    { feature: 'Suporte', plans: ['Básico', 'Padrão', 'Prioritário', '24/7'] }
];