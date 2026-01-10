// RecoveryAccount.jsx
import React from 'react';
import { Head, useForm, Link } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function RecoveryAccount({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('recovery.password.email'));
    };

    return (
        <SiteLayout>
            <Head title="Recuperar Conta" />

            <div className="flex-grow flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
                <div className="recovery-container w-full max-w-md bg-slate-800/90 backdrop-blur-sm rounded-xl border border-slate-700 p-8 shadow-2xl">
                    <div className="text-center mb-8">
                        <div className="flex justify-center mb-4">
                            <img 
                                className="w-52" 
                                src="/assets/images/logo.png" 
                                alt="Handgeev Logo"
                            />
                        </div>
                        <h2 className="text-2xl font-bold text-slate-100 mb-2">
                            Recuperar Conta
                        </h2>
                        <p className="text-slate-300">
                            Insira seu e-mail para recuperar sua senha
                        </p>
                    </div>

                    {/* Mensagens de status/feedback */}
                    {status && (
                        <div className="mb-4 font-medium text-sm text-green-400 bg-green-900/20 p-3 rounded-lg">
                            {status}
                        </div>
                    )}

                    <form onSubmit={submit} className="space-y-6">
                        <div>
                            <label 
                                htmlFor="email" 
                                className="block text-sm font-medium text-slate-300 mb-2"
                            >
                                E-mail
                            </label>
                            <div className="relative">
                                <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i className="fas fa-envelope text-slate-500"></i>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    required
                                    className="input-field appearance-none relative block bg-slate-700/50 w-full pl-10 pr-10 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm"
                                    placeholder="seu@email.com"
                                />
                            </div>
                            {errors.email && (
                                <p className="mt-1 text-sm text-red-400">{errors.email}</p>
                            )}
                        </div>

                        <div>
                            <button
                                type="submit"
                                disabled={processing}
                                className="btn-primary group relative cursor-pointer w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-md disabled:opacity-50"
                            >
                                <span className="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <i className="fas fa-paper-plane"></i>
                                </span>
                                Enviar Link de Recuperação
                            </button>
                        </div>
                    </form>

                    <div className="mt-6 text-center">
                        <Link
                            href={route('login.show')}
                            className="back-link inline-flex items-center text-sm font-medium text-primary-500 hover:text-primary-400 transition-colors"
                        >
                            <i className="fas fa-arrow-left mr-2"></i>
                            Voltar para o Login
                        </Link>
                    </div>
                </div>
            </div>

            <style jsx>{`
                .recovery-container {
                    backdrop-filter: blur(10px);
                    border-radius: 16px;
                    overflow: hidden;
                }
                .gradient-bg {
                    background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
                }
                .input-field {
                    transition: all 0.3s ease;
                    border: 1px solid rgba(255, 255, 255, 0.1);
                }
                .input-field:focus {
                    border-color: #08fff0;
                    box-shadow: 0 0 0 3px rgba(8, 255, 240, 0.2);
                }
                .btn-primary {
                    background: linear-gradient(135deg, #08fff0 0%, #00b3a8 100%);
                    transition: all 0.3s ease;
                }
                .btn-primary:hover:not(:disabled) {
                    background: linear-gradient(135deg, #00e6d8 0%, #008078 100%);
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(8, 255, 240, 0.3);
                }
                .back-link {
                    transition: all 0.3s ease;
                }
                .back-link:hover {
                    color: #08fff0;
                    transform: translateX(-3px);
                }
            `}</style>
        </SiteLayout>
    );
}