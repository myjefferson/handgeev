import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import Footer from '@/Components/Footer/Footer';
import useLang from '@/Hooks/useLang';
import Alert from '@/Components/Alerts/Alert';

export default function Register() {
    const { __ } = useLang();
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        surname: '',
        email: '',
        password: '',
        terms: false,
    });

    const { flash, translations } = usePage().props;
    const [planInfo, setPlanInfo] = useState(null);

    // Verificar parâmetro de plano na URL
    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const planParam = urlParams.get('plan');
        
        if (planParam && planParam !== 'free') {
            showPlanSelection(planParam);
        }
    }, []);

    const showPlanSelection = (planName) => {
        const formattedPlanName = planName.charAt(0).toUpperCase() + planName.slice(1);
        
        setPlanInfo({
            name: formattedPlanName,
            message: `${__('plan.selected').replace(':plan', formattedPlanName)}`,
            paymentMessage: __('plan.payment_redirect')
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        
        // Validação de senha
        if (data.password.length < 8) {
            alert(__('javascript.password_validation'));
            return;
        }
        
        post(route('register.store'));
    };

    return (
        <>
            <Head>
                <title>{__('title')}</title>
                <meta name="description" content="Boas-vindas ao HandGeeV" />
            </Head>

            <div className="font-sans antialiased gradient-bg text-white">
                <section>
                    <div className="w-full grid grid-cols-1 md:grid-cols-[auto_400px] items-start mx-auto h-min lg:py-0 text-white">
                        {/* Lado esquerdo - Apresentação visual */}
                        <div className="p-8 hidden md:flex h-full flex-col justify-between gradient-bg relative overflow-hidden">
                            {/* Elementos decorativos de fundo */}
                            <div className="absolute top-0 left-0 w-full h-full opacity-10">
                                <div className="absolute top-20 left-20 w-72 h-72 rounded-full bg-teal-400 filter blur-3xl"></div>
                                <div className="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-purple-500 filter blur-3xl"></div>
                            </div>
                            
                            <div className="relative z-10">
                                <img className="mb-5 w-48" src="/assets/images/logo.png" alt="Handgeev" />
                                <p className="text-teal-400 font-semibold text-lg">
                                    {__('hero.platform_description')}
                                </p>
                            </div>
                            
                            <div className="relative z-10">
                                <div className="text-4xl font-bold mb-4 leading-tight">
                                    {__('hero.title_line1').split(':highlight')[0]}
                                    <span className="text-teal-400">
                                        {__('hero.highlight')}
                                    </span>
                                    {__('hero.title_line1').split(':highlight')[1]}
                                </div>
                                <div className="text-xl text-gray-300 mb-8">
                                    {__('hero.title_line2')}
                                </div>
                                
                                {/* Benefícios do HandGeev */}
                                <div className="space-y-4 mt-6">
                                    <div className="flex items-center bg-slate-800/50 backdrop-blur-sm p-3 rounded-lg border border-slate-700">
                                        <div className="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center mr-4">
                                            <i className="fas fa-bolt text-green-400"></i>
                                        </div>
                                        <div>
                                            <div className="text-white font-semibold">
                                                {__("features.instant_setup")}
                                            </div>
                                            <div className="text-gray-400 text-sm">
                                                {__("features.instant_setup_desc")}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div className="flex items-center bg-slate-800/50 backdrop-blur-sm p-3 rounded-lg border border-slate-700">
                                        <div className="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center mr-4">
                                            <i className="fas fa-sliders-h text-blue-400"></i>
                                        </div>
                                        <div>
                                            <div className="text-white font-semibold">
                                                {__("features.total_flexibility")}
                                            </div>
                                            <div className="text-gray-400 text-sm">
                                                {__("features.total_flexibility_desc")}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div className="flex items-center bg-slate-800/50 backdrop-blur-sm p-3 rounded-lg border border-slate-700">
                                        <div className="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center mr-4">
                                            <i className="fas fa-shield-alt text-purple-400"></i>
                                        </div>
                                        <div>
                                            <div className="text-white font-semibold">
                                                {__("features.robust_security")}
                                            </div>
                                            <div className="text-gray-400 text-sm">
                                                {__("features.robust_security_desc")}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div className="relative z-10">
                                <div className="flex items-start space-x-3">
                                    <div className="w-12 h-12 rounded-full bg-teal-400 flex items-center justify-center teal-glow flex-shrink-0">
                                        <i className="fas fa-quote-left text-slate-900"></i>
                                    </div>
                                    <div className="flex-1 bg-slate-800/50 backdrop-blur-sm p-4 rounded-lg border border-slate-700">
                                        <p className="text-sm italic text-gray-300">
                                            {__('hero.testimonial')}
                                        </p>
                                        <p className="text-xs mt-2 text-teal-400">
                                            {__('hero.testimonial_author')}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {/* Lado direito - Formulário de cadastro */}
                        <div className="flex items-center w-full min-h-svh relative bg-slate-800 md:border-l-2 border-teal-400 md:mt-0 sm:max-w-md xl:p-0">
                            <div className="px-6 py-8 h-full w-full flex items-center space-y-4 md:space-y-6">
                                <div className="w-full">
                                    <div className="flex md:hidden w-full justify-center mb-8">
                                        <img className="mt-4 mb-3 w-48" src="/assets/images/logo.png" alt="Handgeev" />
                                    </div>
                                    
                                    <div className="mb-2 flex items-center justify-center md:justify-start">
                                        <div className="w-10 h-10 rounded-full bg-teal-400/20 flex items-center justify-center mr-3">
                                            <i className="fas fa-user-plus text-teal-400"></i>
                                        </div>
                                        <h1 className="text-2xl font-bold">
                                            {planInfo ? (
                                                <>
                                                    {__('title')} -
                                                    {planInfo && <span className="text-teal-400"> {planInfo.name}</span>}
                                                </>
                                            ) : (
                                                __('title')
                                            )}
                                        </h1>
                                    </div>
                                    
                                    <p className="text-sm text-gray-400 mb-6 text-center md:text-left">
                                        {__('subtitle')}
                                    </p>
                                    
                                    <p className="text-sm mt-2 mb-6 text-center md:text-left">
                                        {__('already_account')}{' '}
                                        <Link 
                                            href={route('login.show')} 
                                            className="underline text-teal-400 hover:text-teal-300 transition-colors"
                                        >
                                            {__('login_link')}
                                        </Link>.
                                    </p>

                                    {/*Alert Messagens*/}
                                    <Alert/>

                                    {/* Mensagens de Erro */}
                                    {errors && Object.keys(errors).length > 0 && (
                                        <div className="mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm">
                                            <i className="fas fa-exclamation-circle mr-2"></i>
                                            {Object.values(errors)[0]}
                                        </div>
                                    )}

                                    {/* Informações do Plano Selecionado */}
                                    {planInfo && (
                                        <div className="mb-6 p-4 bg-gradient-to-r from-teal-500/10 to-purple-500/10 border border-teal-400/30 rounded-xl slide-down-animation">
                                            <div className="flex items-center space-x-3">
                                                <div>
                                                    <h3 className="text-teal-400 font-semibold">{planInfo.message}</h3>
                                                    <p className="text-teal-300 text-sm mt-1">
                                                        {planInfo.paymentMessage}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                    
                                    <form className="space-y-4" onSubmit={handleSubmit}>
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label htmlFor="name" className="block mb-2 text-sm font-medium text-gray-300">
                                                    {__('form.name')}
                                                </label>
                                                <div className="relative">
                                                    <div className="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <i className="fas fa-user text-gray-500"></i>
                                                    </div>
                                                    <input 
                                                        type="text" 
                                                        name="name" 
                                                        id="name" 
                                                        value={data.name}
                                                        onChange={(e) => setData('name', e.target.value)}
                                                        className="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" 
                                                        placeholder={__('form.name_placeholder')} 
                                                        required 
                                                    />
                                                </div>
                                                {errors.name && (
                                                    <p className="mt-1 text-sm text-red-400">{errors.name}</p>
                                                )}
                                            </div>
                                            <div>
                                                <label htmlFor="surname" className="block mb-2 text-sm font-medium text-gray-300">
                                                    {__('form.surname')}
                                                </label>
                                                <div className="relative">
                                                    <div className="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                        <i className="fas fa-users text-gray-500"></i>
                                                    </div>
                                                    <input 
                                                        type="text" 
                                                        name="surname" 
                                                        id="surname" 
                                                        value={data.surname}
                                                        onChange={(e) => setData('surname', e.target.value)}
                                                        className="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" 
                                                        placeholder={__('form.surname_placeholder')} 
                                                        required 
                                                    />
                                                </div>
                                                {errors.surname && (
                                                    <p className="mt-1 text-sm text-red-400">{errors.surname}</p>
                                                )}
                                            </div>
                                        </div>
                                        <div>
                                            <label htmlFor="email" className="block mb-2 text-sm font-medium text-gray-300">
                                                {__('form.email')}
                                            </label>
                                            <div className="relative">
                                                <div className="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <i className="fas fa-envelope text-gray-500"></i>
                                                </div>
                                                <input 
                                                    type="email" 
                                                    name="email" 
                                                    id="email" 
                                                    value={data.email}
                                                    onChange={(e) => setData('email', e.target.value)}
                                                    className="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" 
                                                    placeholder={__('form.email_placeholder')} 
                                                    required 
                                                />
                                            </div>
                                            {errors.email && (
                                                <p className="mt-1 text-sm text-red-400">{errors.email}</p>
                                            )}
                                        </div>
                                        <div>
                                            <label htmlFor="password" className="block mb-2 text-sm font-medium text-gray-300">
                                                {__('form.password')}
                                            </label>
                                            <div className="relative">
                                                <div className="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <i className="fas fa-lock text-gray-500"></i>
                                                </div>
                                                <input 
                                                    type="password" 
                                                    name="password" 
                                                    id="password" 
                                                    value={data.password}
                                                    onChange={(e) => setData('password', e.target.value)}
                                                    placeholder={__('form.password_placeholder')} 
                                                    className="input-focus bg-slate-700 border border-slate-600 text-white sm:text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full pl-10 p-3" 
                                                    required 
                                                />
                                            </div>
                                            <p className="mt-1 text-xs text-gray-400">
                                                {__('form.password_hint')}
                                            </p>
                                            {errors.password && (
                                                <p className="mt-1 text-sm text-red-400">{errors.password}</p>
                                            )}
                                        </div>
                                        
                                        <div className="flex items-center">
                                            <div className="flex items-center h-5">
                                                <input 
                                                    id="terms" 
                                                    name="terms" 
                                                    type="checkbox" 
                                                    checked={data.terms}
                                                    onChange={(e) => setData('terms', e.target.checked)}
                                                    className="input-focus w-4 h-4 border border-slate-600 rounded bg-slate-700 focus:ring-3 focus:ring-teal-500 focus:ring-offset-slate-800" 
                                                    required 
                                                />
                                            </div>
                                            <div className="ml-3 text-sm">
                                                <label htmlFor="terms" className="text-gray-300">
                                                    {__('form.terms').split(':terms')[0]}
                                                    <Link 
                                                        href={route('legal.terms')} 
                                                        className="text-teal-400 hover:underline"
                                                    >
                                                        {__('form.terms_link')}
                                                    </Link>
                                                    {__('form.terms').split(':terms')[1].split(':privacy')[0]}
                                                    <Link 
                                                        href={route('legal.privacy')} 
                                                        className="text-teal-400 hover:underline"
                                                    >
                                                        {__('form.privacy_link')}
                                                    </Link>
                                                    {__('form.terms').split(':privacy')[1]}
                                                </label>
                                            </div>
                                        </div>
                                        {errors.terms && (
                                            <p className="mt-1 text-sm text-red-400">{errors.terms}</p>
                                        )}
                                        
                                        <div className="pt-2">
                                            <button 
                                                type="submit" 
                                                disabled={processing}
                                                className={`teal-glow w-full text-slate-900 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-md px-5 py-3.5 text-center transition-colors ${
                                                    planInfo 
                                                        ? 'bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700' 
                                                        : 'bg-teal-400 hover:bg-teal-500'
                                                } disabled:opacity-50 disabled:cursor-not-allowed`}
                                            >
                                                <i className="fas fa-rocket mr-2"></i>
                                                {planInfo 
                                                    ? __('form.submit_button_payment')
                                                    : __('form.submit_button')
                                                }
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <Footer />
            </div>

            <style>{`
                @keyframes float {
                    0% { transform: translateY(0px); }
                    50% { transform: translateY(-15px); }
                    100% { transform: translateY(0px); }
                }

                .floating {
                    animation: float 6s ease-in-out infinite;
                }

                .gradient-bg {
                    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                }

                .teal-glow {
                    box-shadow: 0 0 15px rgba(8, 255, 240, 0.3);
                }

                .teal-glow:hover {
                    box-shadow: 0 0 20px rgba(8, 255, 240, 0.5);
                }

                .input-focus:focus {
                    border-color: #08fff0;
                    box-shadow: 0 0 0 3px rgba(8, 255, 240, 0.2);
                }

                /* Animação suave para a div do plano */
                .slide-down-animation {
                    animation: slideDown 0.5s ease-out;
                }

                @keyframes slideDown {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            `}</style>
        </>
    );
}