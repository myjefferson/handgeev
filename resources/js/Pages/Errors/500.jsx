// resources/js/Pages/Errors/500.jsx
import React, { useEffect, useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function Error500({ status = 500, message = 'Erro interno do servidor' }) {
    const [countdown, setCountdown] = useState(30);
    const { auth } = usePage().props;

    // useEffect(() => {
    //     const interval = setInterval(() => {
    //         setCountdown(prev => {
    //             if (prev <= 1) {
    //                 clearInterval(interval);
    //                 window.location.reload();
    //                 return 0;
    //             }
    //             return prev - 1;
    //         });
    //     }, 1000);

    //     return () => clearInterval(interval);
    // }, []);

    return (
        <SiteLayout>
            <Head>
                <title>Erro do Servidor</title>
                <meta name="description" content="Ocorreu um erro interno no servidor" />
            </Head>

            <div className="min-h-screen flex items-center justify-center px-4 bg-gradient-to-br from-gray-900 to-gray-800">
                <div className="text-center">
                    {/* Logo */}
                    <div className="mb-8">
                        <img 
                            className="w-48 mx-auto" 
                            src="/assets/images/logo.png" 
                            alt="Logo" 
                        />
                    </div>

                    {/* Ícone de Erro */}
                    <div className="mb-6">
                        <div className="w-24 h-24 mx-auto bg-orange-500/10 rounded-full flex items-center justify-center border border-orange-500/20">
                            <svg 
                                className="w-12 h-12 text-orange-400" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path 
                                    strokeLinecap="round" 
                                    strokeLinejoin="round" 
                                    strokeWidth={2} 
                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" 
                                />
                            </svg>
                        </div>
                    </div>

                    {/* Mensagem */}
                    <h1 className="text-6xl font-bold text-orange-400 mb-4">500</h1>
                    <h2 className="text-2xl font-semibold text-white mb-4">
                        Erro do Servidor
                    </h2>
                    <p className="text-gray-400 mb-8 max-w-md mx-auto">
                        Ocorreu um erro interno no servidor. Nossa equipe já foi notificada e está trabalhando para resolver o problema.
                    </p>

                    {/* Botões de Ação */}
                    <div className="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link 
                            href="/" 
                            className="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                        >
                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Voltar para Home
                        </Link>
                        
                        <button 
                            onClick={() => window.location.reload()} 
                            className="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                        >
                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Tentar Novamente
                        </button>
                        
                        {auth.user ? (
                            <Link 
                                href={route('dashboard.home')}
                                className="border border-teal-500 text-teal-400 hover:bg-teal-500/10 font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                            >
                                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                Ir para Dashboard
                            </Link>
                        ) : (
                            <Link 
                                href={route('login.show')}
                                className="border border-teal-500 text-teal-400 hover:bg-teal-500/10 font-semibold py-3 px-6 rounded-lg transition-colors flex items-center justify-center"
                            >
                                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                                Fazer Login
                            </Link>
                        )}
                    </div>

                    {/* Informações Adicionais */}
                    <div className="mt-8 text-sm text-gray-500">
                        <p>Se o problema persistir, entre em contato com nosso suporte.</p>
                        <p className="mt-2">Código do erro: Erro Interno do Servidor</p>
                    </div>

                    {/* Contador para recarregar automaticamente */}
                    {/* <div className="mt-6">
                        <p className="text-gray-500 text-sm">
                            Recarregando automaticamente em <span className="font-semibold">{countdown}</span> segundos...
                        </p>
                    </div> */}
                </div>
            </div>
        </SiteLayout>
    );
}