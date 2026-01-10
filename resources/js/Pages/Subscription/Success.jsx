// resources/js/Pages/Subscription/Success.jsx
import React, { useEffect } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function SubscriptionSuccess() {
    const { flash, auth } = usePage().props;

    useEffect(() => {
        // Adiciona estilos de animação
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fill {
                100% {
                    box-shadow: inset 0px 0px 0px 30px #10b981;
                }
            }
            
            @keyframes scale {
                0%, 100% {
                    transform: none;
                }
                50% {
                    transform: scale3d(1.1, 1.1, 1);
                }
            }
        `;
        document.head.appendChild(style);

        return () => {
            document.head.removeChild(style);
        };
    }, []);

    return (
        <SiteLayout>
            <Head>
                <title>Pagamento Confirmado</title>
                <meta name="description" content="Sua assinatura foi ativada com sucesso" />
            </Head>

            <div 
                className="min-h-screen flex items-center justify-center px-4"
                style={{
                    background: 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)',
                    minHeight: '100vh'
                }}
            >
                <div className="max-w-md w-full text-center">
                    {/* Success Icon with Animation */}
                    <div className="mb-8">
                        <div 
                            className="success-checkmark"
                            style={{
                                width: '80px',
                                height: '80px',
                                margin: '0 auto',
                                borderRadius: '50%',
                                display: 'block',
                                strokeWidth: '2',
                                stroke: '#10b981',
                                strokeMiterlimit: '10',
                                boxShadow: 'inset 0px 0px 0px #10b981',
                                animation: 'fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both'
                            }}
                        >
                            <svg 
                                className="check-icon" 
                                viewBox="0 0 24 24"
                                style={{
                                    width: '80px',
                                    height: '80px',
                                    borderRadius: '50%',
                                    display: 'block',
                                    strokeWidth: '2',
                                    stroke: '#fff',
                                    strokeMiterlimit: '10',
                                    margin: '10% auto',
                                    boxShadow: 'inset 0px 0px 0px #10b981',
                                    animation: 'fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both'
                                }}
                            >
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                    </div>

                    {/* Success Message */}
                    <h1 className="text-3xl font-bold text-white mb-4">
                        Pagamento Confirmado!
                    </h1>
                    <p className="text-gray-400 mb-8">
                        Sua conta Pro foi ativada com sucesso. Agora você tem acesso a todos os recursos premium.
                    </p>

                    {/* Features List */}
                    <div className="bg-slate-800/50 rounded-2xl p-6 mb-8">
                        <h2 className="text-xl font-semibold text-white mb-4">
                            O que você ganhou
                        </h2>
                        <ul className="text-left space-y-3">
                            <li className="flex items-center text-gray-300">
                                <svg className="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                </svg>
                                <span>Workspaces ilimitados</span>
                            </li>
                            <li className="flex items-center text-gray-300">
                                <svg className="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                </svg>
                                <span>Tópicos ilimitados</span>
                            </li>
                            <li className="flex items-center text-gray-300">
                                <svg className="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                </svg>
                                <span>API completa</span>
                            </li>
                            <li className="flex items-center text-gray-300">
                                <svg className="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                </svg>
                                <span>Domínio personalizado</span>
                            </li>
                            <li className="flex items-center text-gray-300">
                                <svg className="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                </svg>
                                <span>Exportação de dados</span>
                            </li>
                        </ul>
                    </div>

                    {/* Action Buttons */}
                    <div className="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link 
                            href={route('dashboard.home')}
                            className="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                        >
                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Começar a Usar
                        </Link>
                        
                        <Link 
                            href={route('billing.show')}
                            className="border border-gray-600 hover:border-gray-500 text-gray-300 hover:text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                        >
                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Ver Faturas
                        </Link>
                    </div>

                    {/* Support Message */}
                    <p className="text-sm text-gray-500 mt-8">
                        Problemas com sua assinatura?{' '}
                        <a 
                            href="mailto:support@example.com" 
                            className="text-teal-400 hover:text-teal-300 underline"
                        >
                            Entre em contato com o suporte
                        </a>
                    </p>
                </div>
            </div>
        </SiteLayout>
    );
}