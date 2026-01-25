import React from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import SiteLayout from "@/Layouts/SiteLayout";

export default function GoogleConfirmLink() {
    const { google_link_data } = usePage().props;
    const { post, processing } = useForm();
    
    if (!google_link_data) {
        return (
            <SiteLayout>
                <div className="flex flex-col min-h-screen">
                    <div className="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
                        <div className="w-full max-w-md bg-slate-800/90 backdrop-blur-sm rounded-xl border border-slate-700 p-8 shadow-2xl text-center">
                            <p className="text-slate-300">Sessão inválida ou expirada.</p>
                            <Link 
                                href={route('login')}
                                className="mt-4 inline-flex items-center px-4 py-2 bg-teal-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-400 active:bg-teal-600 focus:outline-none focus:border-teal-600 focus:ring focus:ring-teal-200 disabled:opacity-25 transition"
                            >
                                Voltar para o login
                            </Link>
                        </div>
                    </div>
                </div>
            </SiteLayout>
        );
    }

    const { user_email, google_email, google_name, action } = google_link_data;

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('google.confirm-link'));
    };

    return (
        <SiteLayout>
            <Head>
                <title>Vincular Conta Google</title>
            </Head>

            <div className="flex flex-col min-h-screen">
                <div className="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
                    <div className="w-full max-w-md bg-slate-800/90 backdrop-blur-sm rounded-xl border border-slate-700 p-8 shadow-2xl">
                        
                        {/* Logo */}
                        <div className="text-center mb-8">
                            <div className="flex justify-center mb-4">
                                <img 
                                    className="w-52" 
                                    src="/assets/images/logo.png" 
                                    alt="Logo" 
                                />
                            </div>
                            <h2 className="text-2xl font-bold text-slate-200">
                                Vincular Conta Google
                            </h2>
                            <p className="mt-2 text-slate-300">
                                {action === 'link_existing_account' 
                                    ? `Já existe uma conta com o email ${user_email}. Deseja vincular esta conta Google?`
                                    : `Você está logado como ${user_email}. Deseja vincular esta conta Google?`
                                }
                            </p>
                        </div>

                        {/* Dados da conta Google */}
                        <div className="bg-slate-700/50 rounded-lg p-6 mb-6 border border-slate-600">
                            <div className="flex items-center space-x-4">
                                <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg className="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 className="font-medium text-slate-200">{google_name}</h3>
                                    <p className="text-sm text-slate-400">{google_email}</p>
                                </div>
                            </div>
                        </div>

                        {/* Formulário de confirmação */}
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label className="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="confirm" 
                                        required 
                                        className="rounded border-slate-600 text-teal-500 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50 bg-slate-700"
                                    />
                                    <span className="ml-2 text-sm text-slate-300">
                                        Sim, quero vincular minha conta Google
                                    </span>
                                </label>
                            </div>
                            
                            <div className="flex space-x-3">
                                <button 
                                    type="submit" 
                                    disabled={processing}
                                    className="flex-1 inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-slate-900 bg-gradient-to-r from-teal-400 to-teal-600 hover:from-teal-300 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-300 disabled:opacity-50"
                                >
                                    {processing ? 'Processando...' : 'Vincular Conta'}
                                </button>
                                <Link 
                                    href={route('login')}
                                    className="flex-1 inline-flex justify-center py-3 px-4 border border-slate-600 shadow-sm text-sm font-medium rounded-lg text-slate-200 bg-slate-700 hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-all duration-300 text-center"
                                >
                                    Cancelar
                                </Link>
                            </div>
                        </form>

                        <div className="mt-6 pt-6 border-t border-slate-700">
                            <p className="text-xs text-slate-400">
                                Ao vincular sua conta, você poderá fazer login usando Google ou email/senha.
                                Suas configurações e dados serão mantidos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </SiteLayout>
    );
}