import React, { useState, useEffect } from 'react';
import { Head, usePage, useForm } from '@inertiajs/react';

const CheckoutRedirect = () => {
    const { props } = usePage();
    const { priceId, planName } = props;
    
    const [countdown, setCountdown] = useState(3);
    const [progress, setProgress] = useState(0);
    const [isRedirecting, setIsRedirecting] = useState(false);
    const [isCancelled, setIsCancelled] = useState(false);

    const { post, processing } = useForm({
        price_id: priceId,
    });

    // Efeito para a contagem regressiva
    useEffect(() => {
        if (isCancelled || isRedirecting) return;

        const progressInterval = setInterval(() => {
            setProgress(prev => {
                const newProgress = prev + (100 / (3 * 10)); // 10 updates per second
                return Math.min(newProgress, 100);
            });
        }, 100);

        const countdownInterval = setInterval(() => {
            setCountdown(prev => {
                if (prev <= 1) {
                    clearInterval(countdownInterval);
                    clearInterval(progressInterval);
                    handleAutoRedirect();
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);

        return () => {
            clearInterval(countdownInterval);
            clearInterval(progressInterval);
        };
    }, [isCancelled, isRedirecting]);

    const handleAutoRedirect = () => {
        setIsRedirecting(true);
        setProgress(100);
        
        // Pequeno delay antes do submit para ver o 100%
        setTimeout(() => {
            post(route('subscription.checkout'));
        }, 300);
    };

    const handleManualRedirect = () => {
        if (!processing && !isRedirecting) {
            setIsRedirecting(true);
            post(route('subscription.checkout'));
        }
    };

    const cancelRedirect = () => {
        setIsCancelled(true);
        setProgress(100);
    };

    const getCountdownText = () => {
        if (isRedirecting) return 'Redirecionando...';
        if (isCancelled) return 'Redirecionamento cancelado';
        return `${countdown} segundo${countdown !== 1 ? 's' : ''}`;
    };

    const getCountdownColor = () => {
        if (isRedirecting) return 'text-teal-400 animate-pulse';
        if (isCancelled) return 'text-green-400';
        return 'text-teal-400';
    };

    return (
        <>
            <Head>
                <title>Finalizar Assinatura - HandGeev</title>
                <style>{`
                    .countdown-circle {
                        width: 60px;
                        height: 60px;
                        border: 3px solid #0d9488;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: bold;
                        font-size: 1.25rem;
                        position: relative;
                    }
                    
                    .countdown-circle::before {
                        content: '';
                        position: absolute;
                        width: 100%;
                        height: 100%;
                        border: 3px solid transparent;
                        border-top: 3px solid #14b8a6;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                    }
                    
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                    
                    .pulse-glow {
                        animation: pulse-glow 2s infinite;
                    }
                    
                    @keyframes pulse-glow {
                        0%, 100% { box-shadow: 0 0 20px rgba(20, 184, 166, 0.3); }
                        50% { box-shadow: 0 0 30px rgba(20, 184, 166, 0.6); }
                    }
                    
                    .fade-in-up {
                        animation: fadeInUp 0.6s ease-out;
                    }
                    
                    @keyframes fadeInUp {
                        from {
                            opacity: 0;
                            transform: translateY(20px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                    
                    .progress-bar {
                        height: 4px;
                        background: #334155;
                        border-radius: 2px;
                        overflow: hidden;
                        margin: 20px 0;
                    }
                    
                    .progress-fill {
                        height: 100%;
                        background: linear-gradient(90deg, #0d9488, #14b8a6);
                        border-radius: 2px;
                        transition: width 0.3s ease;
                    }

                    .teal-glow-hover:hover {
                        box-shadow: 0 0 25px rgba(20, 184, 166, 0.4);
                    }
                `}</style>
            </Head>

            <div className="min-h-screen bg-slate-900 flex items-center justify-center p-4 fade-in-up">
                <div className="max-w-md w-full">
                    <div className="bg-slate-800 rounded-2xl p-8 border border-slate-700 pulse-glow">
                        {/* Cabeçalho com ícone animado */}
                        <div className="text-center mb-6">
                            <div className="w-20 h-20 bg-gradient-to-br from-teal-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4 relative">
                                <i className="fas fa-credit-card text-white text-2xl"></i>
                                <div className="absolute -top-1 -right-1 w-6 h-6 bg-green-400 rounded-full flex items-center justify-center">
                                    <i className="fas fa-check text-white text-xs"></i>
                                </div>
                            </div>
                            <h1 className="text-2xl font-bold text-white mb-2">Quase lá!</h1>
                            <p className="text-slate-400">
                                Prepare-se para finalizar sua assinatura <span className="text-teal-400 font-semibold">{planName}</span>
                            </p>
                        </div>

                        {/* Card de informações */}
                        <div className="bg-slate-700/50 rounded-lg p-4 mb-6 border border-slate-600">
                            <div className="flex items-center space-x-3 mb-3">
                                <div className="w-8 h-8 bg-teal-500/20 rounded-full flex items-center justify-center">
                                    <i className="fas fa-gem text-teal-400 text-sm"></i>
                                </div>
                                <div>
                                    <div className="text-slate-300 text-sm">Plano selecionado</div>
                                    <div className="text-teal-400 font-semibold">{planName}</div>
                                </div>
                            </div>
                            
                            <div className="flex items-center space-x-3">
                                <div className="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center">
                                    <i className="fas fa-shield-alt text-green-400 text-sm"></i>
                                </div>
                                <div>
                                    <div className="text-slate-300 text-sm">Próxima etapa</div>
                                    <div className="text-green-400 font-semibold">Pagamento seguro Stripe</div>
                                </div>
                            </div>
                        </div>

                        {/* Contagem regressiva visual */}
                        <div className="text-center mb-6">
                            <div className="flex flex-col items-center space-y-4">
                                <div className={`countdown-circle ${getCountdownColor()}`}>
                                    {isRedirecting ? '🚀' : isCancelled ? '✓' : countdown}
                                </div>
                                <div className="text-slate-400 text-sm">
                                    {isCancelled ? (
                                        <span className="text-green-400 font-semibold">
                                            {countdown === 0 ? 'Clique no botão acima quando estiver pronto' : 'Redirecionamento cancelado'}
                                        </span>
                                    ) : (
                                        <>
                                            Redirecionando automaticamente em{' '}
                                            <span className={getCountdownColor() + ' font-semibold'}>
                                                {getCountdownText()}
                                            </span>
                                        </>
                                    )}
                                </div>
                            </div>
                            
                            {/* Barra de progresso */}
                            <div className="progress-bar mt-4">
                                <div 
                                    className="progress-fill" 
                                    style={{ 
                                        width: `${progress}%`,
                                        background: isCancelled ? '#10b981' : 'linear-gradient(90deg, #0d9488, #14b8a6)'
                                    }}
                                ></div>
                            </div>
                        </div>

                        {/* Botão de ação principal */}
                        <button
                            onClick={handleManualRedirect}
                            disabled={processing || isRedirecting}
                            className="w-full bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 teal-glow-hover flex items-center justify-center space-x-2 group disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <i className={`fas fa-lock ${processing ? 'animate-spin' : 'group-hover:scale-110'} transition-transform`}></i>
                            <span>
                                {processing || isRedirecting ? 'Processando...' : 'Ir para Pagamento Seguro'}
                            </span>
                            <i className="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </button>

                        {/* Botão secundário */}
                        {!isCancelled && !isRedirecting && (
                            <div className="mt-4 text-center">
                                <button 
                                    onClick={cancelRedirect}
                                    className="text-slate-400 hover:text-slate-300 text-sm transition-colors flex items-center justify-center space-x-1 mx-auto"
                                >
                                    <i className="fas fa-times mr-1"></i>
                                    <span>Cancelar redirecionamento automático</span>
                                </button>
                            </div>
                        )}
                    </div>

                    {/* Informações de segurança */}
                    <div className="mt-6 text-center">
                        <div className="flex items-center justify-center space-x-4 text-slate-500 text-sm">
                            <div className="flex items-center space-x-1">
                                <i className="fas fa-shield-alt text-green-400"></i>
                                <span>Pagamento seguro</span>
                            </div>
                            <div className="flex items-center space-x-1">
                                <i className="fas fa-lock text-teal-400"></i>
                                <span>Criptografado</span>
                            </div>
                            <div className="flex items-center space-x-1">
                                <i className="fas fa-bolt text-yellow-400"></i>
                                <span>Processamento rápido</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default CheckoutRedirect;