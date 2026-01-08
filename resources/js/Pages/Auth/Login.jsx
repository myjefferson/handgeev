import React, { useState } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import Alert from '@/Components/Alerts/Alert';

import SiteLayout from "@/Layouts/SiteLayout";
import useLang from '@/Hooks/useLang';

export default function Login() {
    const { __ } = useLang();
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const [showPassword, setShowPassword] = useState(false);
    const { translations } = usePage().props;

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('login.auth'));
    };

    const togglePassword = () => {
        setShowPassword(!showPassword);
    };

    return (
        <SiteLayout>
            <Head>
                <title>Login</title>
                <meta name="description" content="Entre na sua conta" />
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
                                    alt="Handgeev Logo" 
                                />
                            </div>
                            <p className="text-slate-300">{__('title')}</p>
                        </div>

                        <Alert />

                        {/* Formulário */}
                        <form className="space-y-6" onSubmit={handleSubmit}>
                            <div>
                                <label htmlFor="email" className="block text-sm font-medium text-slate-300 mb-2">
                                    {__('email')}
                                </label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i className="fas fa-envelope text-slate-500"></i>
                                    </div>
                                    <input 
                                        id="email" 
                                        name="email" 
                                        type="email" 
                                        autoComplete="email" 
                                        required 
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        className="appearance-none relative block bg-slate-700/50 w-full pl-10 pr-3 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm border border-slate-600 focus:border-teal-400 focus:ring-2 focus:ring-teal-400/20 transition-all duration-300" 
                                        placeholder={__('email_placeholder')}
                                    />
                                </div>
                                {errors.email && (
                                    <p className="mt-1 text-sm text-red-400">
                                        {errors.email}
                                    </p>
                                )}
                            </div>

                            <div>
                                <label htmlFor="password" className="block text-sm font-medium text-slate-300 mb-2">
                                    {__('password')}
                                </label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i className="fas fa-lock text-slate-500"></i>
                                    </div>
                                    <input 
                                        id="password" 
                                        name="password" 
                                        type={showPassword ? 'text' : 'password'} 
                                        autoComplete="current-password" 
                                        required 
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        className="appearance-none relative block bg-slate-700/50 w-full pl-10 pr-10 py-3 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-0 sm:text-sm border border-slate-600 focus:border-teal-400 focus:ring-2 focus:ring-teal-400/20 transition-all duration-300" 
                                        placeholder={__('password_placeholder')}
                                    />
                                    <button 
                                        type="button" 
                                        onClick={togglePassword} 
                                        className="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-200 transition-colors"
                                    >
                                        <i className={`fas ${showPassword ? 'fa-eye-slash' : 'fa-eye'}`}></i>
                                    </button>
                                </div>
                                {errors.password && (
                                    <p className="mt-1 text-sm text-red-400">
                                        {errors.password}
                                    </p>
                                )}
                            </div>

                            <div className="flex items-center justify-between">
                                {/* <div className="flex items-center">
                                    <input 
                                        id="remember-me" 
                                        name="remember" 
                                        type="checkbox" 
                                        checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                        className="h-4 w-4 text-teal-500 focus:ring-teal-500 border-slate-600 rounded bg-slate-700" 
                                    />
                                    <label htmlFor="remember-me" className="ml-2 block text-sm text-slate-400">
                                        {__('remember_me')}
                                    </label>
                                </div> */}

                                <div className="text-sm">
                                    <Link 
                                        href={route('recovery.account.show')} 
                                        className="font-medium text-teal-500 hover:text-teal-400 transition-colors"
                                    >
                                        {__('forgot_password')}
                                    </Link>
                                </div>
                            </div>

                            <div>
                                <button 
                                    type="submit" 
                                    disabled={processing}
                                    className="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-slate-900 bg-gradient-to-r from-teal-400 to-teal-600 hover:from-teal-300 hover:to-teal-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 shadow-md transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span className="absolute left-0 inset-y-0 flex items-center pl-3">
                                        <i className="fas fa-sign-in-alt"></i>
                                    </span>
                                    {processing ? 'Entrando...' : __('submit_button')}
                                </button>
                            </div>
                        </form>

                        <div className="mt-6 text-center">
                            <p className="text-sm text-slate-400">
                                {__('no_account')}
                                <Link
                                    href={route('register.show')} 
                                    className="font-medium text-teal-500 hover:text-teal-400 transition-colors ml-1"
                                >
                                    {__('signup_link')}
                                </Link>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </SiteLayout>
    );
}