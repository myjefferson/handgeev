import useLang from '@/Hooks/useLang';
import React from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';

const About = () => {
    const { auth, app } = usePage().props;
    const { __ } = useLang();

    // Função para obter o badge do plano do usuário
    const getPlanBadge = () => {
        const plan = auth.user?.plan?.name || 'free';
        
        const badges = {
            admin: {
                icon: 'fa-crown',
                label: __("account.badges.admin"),
                aria: __("icons.crown")
            },
            start: {
                icon: 'fa-star',
                label: __("account.badges.start"),
                aria: __("icons.star")
            },
            pro: {
                icon: 'fa-star',
                label: __("account.badges.pro"),
                aria: __("icons.pro")
            },
            premium: {
                icon: 'fa-star',
                label: __("account.badges.premium"),
                aria: __("icons.premium")
            },
            free: {
                icon: 'fa-user',
                label: __("account.badges.free"),
                aria: __("icons.user")
            }
        };

        return badges[plan] || badges.free;
    };

    const planBadge = getPlanBadge();

    return (
        <DashboardLayout>
            <Head title={__('title')} />

            <div className="max-w-4xl mx-auto px-4 py-8 pt-20">
                {/* Cabeçalho */}
                <div className="text-center mb-10">
                    <h1 className="text-4xl font-bold gradient-text mb-3">
                        {__('title')}
                    </h1>
                    <p className="text-slate-400 text-lg">
                        {__('description')}
                    </p>
                </div>

                {/* Card de informações da conta */}
                <div className="bg-slate-800 rounded-xl p-6 mb-10 border-l-4 border-cyan-400 card-hover">
                    <h2 className="text-xl font-semibold text-slate-100 mb-4 flex items-center">
                        <i 
                            className="fas fa-user-circle mr-2 text-cyan-400" 
                            aria-label={__('icons.user')}
                        ></i> 
                        {__('account.title')}
                    </h2>
                    
                    <div className="flex flex-wrap items-center gap-6">
                        <div className="flex items-center">
                            <div className="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                                <i 
                                    className="fas fa-user text-cyan-400" 
                                    aria-label={__('icons.user')}
                                ></i>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm text-slate-400">
                                    {__('account.account_type')}
                                </p>
                                <span className="account-badge px-3 py-1 rounded-full text-sm font-medium">
                                    <i 
                                        className={`fas ${planBadge.icon} mr-1`}
                                        aria-label={planBadge.aria}
                                    ></i> 
                                    {planBadge.label}
                                </span>
                            </div>
                        </div>
                        
                        {/* API Count - Comentado por enquanto */}
                        {/* <div className="flex items-center">
                            <div className="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                                <i 
                                    className="fas fa-code text-cyan-400" 
                                    aria-label={translations.about.icons.code}
                                ></i>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm text-slate-400">
                                    {translations.about.account.apis_created}
                                </p>
                                <p className="font-semibold text-slate-100">
                                    {api_count || '0'}
                                </p>
                            </div>
                        </div> */}
                        
                        {/* Member Since - Comentado por enquanto */}
                        {/* <div className="flex items-center">
                            <div className="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                                <i 
                                    className="fas fa-calendar-alt text-cyan-400" 
                                    aria-label={translations.about.icons.calendar}
                                ></i>
                            </div>
                            <div className="ml-3">
                                <p className="text-sm text-slate-400">
                                    {translations.about.account.member_since}
                                </p>
                                <p className="font-semibold text-slate-100">
                                    {auth.user?.created_at ? new Date(auth.user.created_at).toLocaleDateString('pt-BR', { 
                                        month: 'short', 
                                        year: 'numeric' 
                                    }) : 'N/A'}
                                </p>
                            </div>
                        </div> */}
                    </div>
                </div>

                {/* Informações sobre o Handgeev */}
                <div className="grid md:grid-cols-2 gap-6 mb-10">
                    <div className="bg-slate-800 rounded-xl p-6 card-hover border border-slate-700">
                        <div className="w-14 h-14 rounded-full bg-slate-700 flex items-center justify-center mb-4">
                            <i 
                                className="fas fa-rocket text-2xl text-cyan-400" 
                                aria-label={__('icons.rocket')}
                            ></i>
                        </div>
                        <h3 className="text-xl font-semibold text-slate-100 mb-2">
                            {__('what_is_handgeev.title')}
                        </h3>
                        <p className="text-slate-400">
                            {__('what_is_handgeev.description')}
                        </p>
                    </div>
                    
                    <div className="bg-slate-800 rounded-xl p-6 card-hover border border-slate-700">
                        <div className="w-14 h-14 rounded-full bg-slate-700 flex items-center justify-center mb-4">
                            <i 
                                className="fas fa-cogs text-2xl text-cyan-400" 
                                aria-label={__('icons.cogs')}
                            ></i>
                        </div>
                        <h3 className="text-xl font-semibold text-slate-100 mb-2">
                            {__('how_it_works.title')}
                        </h3>
                        <p className="text-slate-400">
                            {__('how_it_works.description')}
                        </p>
                    </div>
                </div>

                {/* Informações de versão e desenvolvedor */}
                <div className="bg-slate-800 rounded-xl p-6 mb-10 card-hover border border-slate-700">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div className="text-center md:text-left">
                            <p className="text-slate-400 mb-1">
                                {__('version.current_version')}
                            </p>
                            <p className="text-2xl font-bold text-cyan-400">
                                {app.version || '1.0.0'}
                            </p>
                        </div>
                        
                        <div className="text-center text-left md:text-center">
                            <p className="text-slate-400 mb-1">
                                {__('version.developed_by')}
                            </p>
                            <p className="text-xl font-semibold text-slate-100">
                                Handgeev Group
                            </p>
                        </div>
                        
                        <div className="text-center md:text-left">
                            <p className="text-slate-400 mb-1">
                                {__('version.contact')}
                            </p>
                            <div className="flex justify-center md:justify-start space-x-3">
                                <a 
                                    href="https://www.linkedin.com/company/handgeev" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    className="text-cyan-400 hover:text-cyan-300 transition-colors"
                                    aria-label="LinkedIn"
                                >
                                    <i className="fab fa-linkedin text-xl"></i>
                                </a>
                                <a 
                                    href="mailto:handgeev@gmail.com"
                                    className="text-cyan-400 hover:text-cyan-300 transition-colors"
                                    aria-label="Email"
                                >
                                    <i className="fas fa-envelope text-xl"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Chamada para contribuição - Comentado por enquanto */}
                {/* <div className="bg-slate-800 rounded-xl p-6 text-center card-hover border border-slate-700">
                    <h3 className="text-2xl font-semibold text-slate-100 mb-3">
                        {translations.about.contribution.title}
                    </h3>
                    <p className="text-slate-400 mb-5">
                        {translations.about.contribution.description}
                    </p>
                    <div className="flex justify-center space-x-4">
                        <button className="bg-cyan-500 hover:bg-cyan-600 text-slate-900 px-5 py-2 rounded-lg font-medium transition-colors">
                            <i 
                                className="fas fa-bug mr-2" 
                                aria-label={translations.about.icons.bug}
                            ></i> 
                            {translations.about.contribution.report_issue}
                        </button>
                    </div>
                </div> */}
            </div>

            <style>{`
                .gradient-text {
                    background: linear-gradient(90deg, #08fff0, #00b3a8);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                }
                .card-hover {
                    transition: all 0.3s ease;
                }
                .card-hover:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 20px 25px -5px rgba(8, 255, 240, 0.15), 0 10px 10px -5px rgba(8, 255, 240, 0.1);
                }
                .account-badge {
                    background-color: rgba(8, 255, 240, 0.15);
                    color: #08fff0;
                    border: 1px solid #08fff0;
                }
            `}</style>
        </DashboardLayout>
    );
};

export default About;