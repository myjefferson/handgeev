import React, { useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import SiteLayout from "@/Layouts/SiteLayout";

export default function GoogleUpdateEmail() {
    const { google_link_data } = usePage().props;
    const [selectedOption, setSelectedOption] = useState('update');
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

    const { current_email, google_email } = google_link_data;

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('google.update-email'), {
            action: selectedOption === 'update' ? 'update' : 'keep'
        });
    };

    return (
        <SiteLayout>
            <Head>
                <title>Atualizar Email</title>
            </Head>

            <div className="flex flex-col min-h-screen">
                <div className="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
                    <div className="w-full max-w-md bg-slate-800/90 backdrop-blur-sm rounded-xl border border-slate-700 p-8 shadow-2xl">
                        
                        {/* Logo e título */}
                        <div className="text-center mb-8">
                            <div className="flex justify-center mb-4">
                                <img 
                                    className="w-52" 
                                    src="/assets/images/logo.png" 
                                    alt="Logo" 
                                />
                            </div>
                            <h2 className="text-2xl font-bold text-slate-200">
                                Atualizar Email
                            </h2>
                            <p className="mt-2 text-slate-300">
                                Seu email atual: <strong className="text-teal-300">{current_email}</strong><br />
                                Email da conta Google: <strong className="text-teal-300">{google_email}</strong>
                            </p>
                        </div>

                        {/* Aviso */}
                        <div className="bg-yellow-900/30 border-l-4 border-yellow-500 p-4 mb-6">
                            <div className="flex">
                                <div className="flex-shrink-0">
                                    <svg className="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd"/>
                                    </svg>
                                </div>
                                <div className="ml-3">
                                    <p className="text-sm text-yellow-200">
                                        O email da sua conta Google é diferente do email atual da sua conta.
                                        Deseja atualizar seu email para o email do Google?
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Opções */}
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div 
                                className={`p-4 border rounded-lg cursor-pointer transition-all ${selectedOption === 'update' ? 'border-teal-500 bg-teal-900/20' : 'border-slate-600 hover:bg-slate-700/50'}`}
                                onClick={() => setSelectedOption('update')}
                            >
                                <div className="flex items-center">
                                    <div className={`flex-shrink-0 h-5 w-5 rounded-full border flex items-center justify-center ${selectedOption === 'update' ? 'border-teal-500 bg-teal-500' : 'border-slate-500'}`}>
                                        {selectedOption === 'update' && (
                                            <svg className="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                            </svg>
                                        )}
                                    </div>
                                    <div className="ml-3">
                                        <p className="text-sm font-medium text-slate-200">
                                            Sim, atualizar para <strong className="text-teal-300">{google_email}</strong>
                                        </p>
                                        <p className="text-xs text-slate-400 mt-1">
                                            Todas as comunicações futuras serão enviadas para este email
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div 
                                className={`p-4 border rounded-lg cursor-pointer transition-all ${selectedOption === 'keep' ? 'border-teal-500 bg-teal-900/20' : 'border-slate-600 hover:bg-slate-700/50'}`}
                                onClick={() => setSelectedOption('keep')}
                            >
                                <div className="flex items-center">
                                    <div className={`flex-shrink-0 h-5 w-5 rounded-full border flex items-center justify-center ${selectedOption === 'keep' ? 'border-teal-500 bg-teal-500' : 'border-slate-500'}`}>
                                        {selectedOption === 'keep' && (
                                            <svg className="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7" />
                                            </svg>
                                        )}
                                    </div>
                                    <div className="ml-3">
                                        <p className="text-sm font-medium text-slate-200">
                                            Não, manter <strong className="text-teal-300">{current_email}</strong> como email principal
                                        </p>
                                        <p className="text-xs text-slate-400 mt-1">
                                            A conta Google será vinculada, mas o email atual será mantido
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="mt-6 pt-6 border-t border-slate-700">
                                <p className="text-xs text-slate-400 mb-4">
                                    {selectedOption === 'update' 
                                        ? 'Se atualizar, você receberá um email de confirmação e todas as comunicações futuras serão enviadas para o novo email.'
                                        : 'Se manter o email atual, você poderá fazer login com Google, mas as comunicações continuarão sendo enviadas para o email atual.'
                                    }
                                </p>
                                
                                <div className="flex space-x-3">
                                    <button 
                                        type="submit" 
                                        disabled={processing}
                                        className="flex-1 inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-slate-900 bg-gradient-to-r from-teal-400 to-teal-600 hover:from-teal-300 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-300 disabled:opacity-50"
                                    >
                                        {processing ? 'Processando...' : 'Confirmar'}
                                    </button>
                                    <Link 
                                        href={route('login')}
                                        className="flex-1 inline-flex justify-center py-3 px-4 border border-slate-600 shadow-sm text-sm font-medium rounded-lg text-slate-200 bg-slate-700 hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-all duration-300 text-center"
                                    >
                                        Cancelar
                                    </Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </SiteLayout>
    );
}