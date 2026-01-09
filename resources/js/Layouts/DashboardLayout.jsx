import React, { useState, useEffect } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import '@/Layouts/css/dashboard.css'

export default function DashboardLayout({ children, title = '', description = '' }) {
    const { auth, app } = usePage().props;
    
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [userDropdownOpen, setUserDropdownOpen] = useState(false);

    // Fechar sidebar em mobile quando a tela for redimensionada
    useEffect(() => {
        const handleResize = () => {
            if (window.innerWidth >= 768) {
                setSidebarOpen(false);
            }
        };

        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    // Fechar dropdown ao clicar fora
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (userDropdownOpen && !event.target.closest('#userDropdown') && !event.target.closest('#userDropdownButton')) {
                setUserDropdownOpen(false);
            }
        };

        document.addEventListener('click', handleClickOutside);
        return () => document.removeEventListener('click', handleClickOutside);
    }, [userDropdownOpen]);

    // Fechar sidebar ao clicar fora (mobile)
    useEffect(() => {
        const handleClickOutsideSidebar = (event) => {
            const sidebar = document.getElementById('cta-button-sidebar');
            const sidebarToggle = document.querySelector('[data-drawer-toggle="cta-button-sidebar"]');
            
            if (window.innerWidth < 768 && 
                sidebar && 
                !sidebar.contains(event.target) && 
                event.target !== sidebarToggle && 
                !sidebarToggle?.contains(event.target)) {
                setSidebarOpen(false);
            }
        };

        document.addEventListener('click', handleClickOutsideSidebar);
        return () => document.removeEventListener('click', handleClickOutsideSidebar);
    }, []);

    const getLogo = () => {
        if (!auth.user) return '/assets/images/logo.png';
        
        switch (auth.user.plan?.name) {
            case 'premium':
                return '/assets/images/logo-premium.png';
            case 'pro':
                return '/assets/images/logo-pro.png';
            case 'admin':
                return '/assets/images/logo-admin.png';
            case 'start':
                return '/assets/images/logo-start.png';
            default:
                return '/assets/images/logo.png';
        }
    };

    // Se não está autenticado, mostrar tela de login necessário
    if (!auth.user) {
        return (
            <>
                <Head>
                    <title>{title ? `${title} - Handgeev` : 'Handgeev'}</title>
                    <meta name="description" content={description} />
                    <meta name="keywords" content="api, workspace, json, handgeev, desenvolvimento" />
                    <link rel="icon" type="image/x-icon" href="/assets/images/icon.png" />
                    <link rel="preconnect" href="https://fonts.bunny.net" />
                    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
                </Head>

                <div className="min-h-screen bg-gray-900 flex items-center justify-center">
                    <div className="text-center">
                        <i className="fas fa-exclamation-triangle text-yellow-400 text-4xl mb-4"></i>
                        <p className="text-gray-400">Login necessário</p>
                        <Link 
                            href={route('login.show')}
                            className="inline-block mt-4 px-4 py-2 bg-teal-400 text-slate-900 rounded-lg font-medium hover:bg-teal-300 transition-colors"
                        >
                            Fazer Login
                        </Link>
                    </div>
                </div>
            </>
        );
    }

    return (
        <>
            <Head>
                <title>{title ? `${title} - Handgeev` : 'Handgeev'}</title>
                <meta name="description" content={description} />
                <meta name="keywords" content="api, workspace, json, handgeev, desenvolvimento" />
                
                {/* Favicon */}
                <link rel="icon" type="image/x-icon" href="/assets/images/icon.png" />
                
                {/* Fonts */}
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
                
                {/* Icons */}
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
            </Head>

            <div className="font-sans antialiased text-white">
                {/* Top User Bar */}
                <div className="user-menu fixed top-0 right-0 left-0 z-30 flex items-center justify-end px-4 md:px-6 bg-slate-800/80 backdrop-blur-sm border-b border-slate-700 h-16">
                    <div className="flex items-center space-x-4">                
                        {/* User profile dropdown */}
                        <div className="relative">
                            <div className="flex space-x-3">
                                {/* Notifications Dropdown - Pode ser implementado depois */}
                                {/* <NotificationsDropdown /> */}
                                
                                <button 
                                    id="userDropdownButton"
                                    onClick={() => setUserDropdownOpen(!userDropdownOpen)}
                                    className="flex items-center space-x-2 pl-4 text-sm rounded-full focus:ring-2 bg-slate-700 focus:ring-teal-400"
                                >
                                    <span className="md:block text-gray-300">
                                        {auth.user?.name || 'Bem-vindo'}
                                    </span>
                                    <div className="user-avatar w-10 h-10 rounded-full bg-teal-400/10 flex items-center justify-center border border-teal-400/20">
                                        <i className="fas fa-user text-teal-400"></i>
                                    </div>
                                </button>
                            </div>
                            
                            {/* Dropdown menu */}
                            {userDropdownOpen && (
                                <div 
                                    id="userDropdown" 
                                    className="absolute right-0 mt-2 z-40 text-sm bg-slate-700 divide-y divide-slate-600 rounded-lg shadow w-44 border border-slate-600"
                                >
                                    <Link 
                                        href={route('user.profile')} 
                                        className="block user-dropdown-option rounded-t-md hover:text-teal-400 py-3 px-4"
                                    >
                                        <div className="font-medium">{auth.user?.name || 'Bem-vindo'}</div>
                                        <div className="truncate text-gray-400">{auth.user?.email || 'email@exemplo.com'}</div>
                                    </Link>
                                    
                                    <div className="px-4 pb-3 text-gray-300">
                                        {!auth.user.plan?.name !== 'admin' && (
                                            <>
                                                {!auth.user.plan?.name || auth.user.plan?.name === 'free' ? (
                                                    <div className="mt-2 border-slate-700">
                                                        <Link 
                                                            href={route('subscription.pricing')}
                                                            className="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center"
                                                        >
                                                            <i className="fas fa-rocket mr-2"></i> Fazer Upgrade
                                                        </Link>
                                                    </div>
                                                ) :  (
                                                    <div className={`flex items-center bg-purple-500 w-max text-white rounded-md px-2 py-1 mt-2`}>
                                                        <i className="fas fa-crown text-white w-3 h-3 mr-2 p-0"></i>
                                                        <p className="text-sm">{auth.user.plan?.name.charAt(0).toUpperCase() + auth.user.plan?.name.slice(1)}</p>
                                                    </div>
                                                )}
                                            </>
                                        )}
                                        
                                        {!auth.user.plan?.name === 'admin' && (
                                            <div className="flex items-center bg-slate-900 w-max text-white rounded-md px-2 py-1 mt-2">
                                                <p className="text-sm">Admin</p>
                                            </div>
                                        )}
                                    </div>
                                    
                                    <ul className="py-2 text-gray-300">
                                        <li>
                                            <Link 
                                                href={route('dashboard.settings')} 
                                                className="user-dropdown-option block px-4 py-2 hover:text-teal-400"
                                            >
                                                <i className="fas fa-cog mr-2"></i> Configurações
                                            </Link>
                                        </li>
                                        {!auth.user?.is_admin && (
                                            <li>
                                                <Link 
                                                    href={route('billing.show')} 
                                                    className="user-dropdown-option block px-4 py-2 hover:text-teal-400"
                                                >
                                                    <i className="fas fa-crown mr-2 p-0"></i> Meu Plano
                                                </Link>
                                            </li>
                                        )}
                                        <li>
                                            <Link 
                                                href={route('logout')}
                                                className="user-dropdown-option block w-full text-left px-4 py-2 hover:text-teal-400"
                                            >
                                                <i className="fas fa-sign-out-alt mr-2"></i> Sair
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Mobile sidebar toggle */}
                <button 
                    onClick={() => setSidebarOpen(!sidebarOpen)}
                    data-drawer-toggle="cta-button-sidebar"
                    aria-controls="cta-button-sidebar"
                    className="fixed top-2 left-4 z-50 inline-flex items-center py-1 px-2 mt-2 text-sm text-gray-400 rounded-lg md:hidden hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-600 bg-slate-700"
                >
                    <span className="sr-only">Abrir menu</span>
                    <i className="fas fa-bars text-lg"></i>
                </button>

                {/* Sidebar */}
                <aside 
                    id="cta-button-sidebar"
                    className={`fixed top-0 left-0 z-40 h-screen transition-transform ${
                        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                    } sm:translate-x-0 sidebar-gradient w-64`}
                    aria-label="Sidebar"
                >
                    <div className="h-full px-4 py-6 overflow-y-auto">
                        {/* Logo */}
                        <div className="flex justify-center mb-8">
                            <img 
                                className="w-48" 
                                src={getLogo()} 
                                alt={`Handgeev ${auth.user.plan?.name === 'admin' ? 'ADMIN' : (auth.user.plan?.name?.toUpperCase() || 'FREE')}`} 
                            />
                        </div>
                        
                        {/* Navigation */}
                        <ul className="space-y-1 font-medium">
                            <li>
                                <NavItem 
                                    href={route('dashboard.home')}
                                    icon="home"
                                    label="Início"
                                    active={route().current('dashboard.home')}
                                />
                            </li>
                            <li>
                                <NavItem 
                                    href={route('structures')}
                                    icon="cubes"
                                    label="Estruturas"
                                    active={route().current('structures')}
                                />
                            </li>
                            <li>
                                <NavItem 
                                    href={route('workspaces.show')}
                                    icon="layer-group"
                                    label="Workspaces"
                                    active={route().current('workspaces.show')}
                                    // badge={auth.user?.workspaces_count} // Descomente quando tiver essa informação
                                />
                            </li>
                            <li>
                                <NavItem 
                                    href={route('management.apis.show')}
                                    icon="code"
                                    label="Minhas APIs"
                                    active={route().current('management.apis.show')}
                                />
                            </li>

                            {/* Admin Section */}
                            {['admin'].includes(auth.user.plan?.name) && (
                                <li className="pt-4">
                                    <div className="border-t border-gray-700 pb-4">
                                        <div className="flex items-center mb-2 px-3 pt-4">
                                            <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                                Administração
                                            </p>
                                        </div>
                                        <NavItem 
                                            href={route('admin.users')}
                                            icon="users"
                                            label="Usuários"
                                            active={route().current('admin.users')}
                                            small
                                        />
                                        {/* <NavItem 
                                            href={route('admin.plans')}
                                            icon="crown"
                                            label="Planos"
                                            active={route().current('admin.plans')}
                                            small
                                        /> */}
                                    </div>
                                </li>
                            )}
                        </ul>
                        
                        {/* Version Info */}
                        <div className="p-4 mt-8 rounded-lg bg-teal-400/10 border border-teal-400/20">
                            <div className="flex items-center justify-between text-xs">
                                <span className="text-gray-400">{app.version}</span>
                                <Link 
                                    href={route('dashboard.about')}
                                    className="text-teal-400 hover:text-teal-300 transition-colors"
                                >
                                    Sobre
                                </Link>
                            </div>
                        </div>
                    </div>
                </aside>

                {/* Main content */}
                <div className="main-content bg-gray-900 min-h-screen p-5 ml-0 sm:ml-64 pt-16">
                    <div className="backdrop-blur-sm rounded-2xl p-1 sm:p-3 md:p-5 lg:p-8 xl:p-8 animate-fade-in mt-5">
                        {children}
                    </div>
                </div>
            </div>

            {/* CSS Styles */}
            <style>{`
                .sidebar-gradient {
                    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                }

                .user-dropdown-option {
                    transition: all 0.2s ease;
                }

                .user-dropdown-option:hover {
                    background: rgba(45, 212, 191, 0.1);
                }

                .nav-item.active {
                    background: rgba(45, 212, 191, 0.1);
                    border-left: 3px solid #2dd4bf;
                    color: #2dd4bf;
                }

                .animate-fade-in {
                    animation: fadeIn 0.5s ease-in-out;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
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

// Componente de Item de Navegação
const NavItem = ({ href, icon, label, active, badge, small = false }) => {
    const baseClasses = "nav-item button-item flex items-center text-gray-300 rounded-lg group hover:bg-slate-700/50 transition-all";
    const activeClasses = active ? "active bg-slate-700/30" : "";
    const sizeClasses = small ? "p-2 text-sm" : "px-5 py-3";
    
    return (
        <Link 
            href={href}
            className={`${baseClasses} ${activeClasses} ${sizeClasses}`}
        >
            <div className="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center mr-3">
                <i className={`fas fa-${icon} ${active ? 'text-teal-400' : 'text-gray-400'} group-hover:text-teal-400`}></i>
            </div>
            <span className={`font-medium ${active ? 'text-teal-400' : ''} group-hover:text-teal-400`}>
                {label}
            </span>
            
            {badge && (
                <div className="ml-auto bg-teal-800 h-5 w-5 flex justify-center items-center rounded-full">
                    <span className="text-sm font-semibold text-white">{badge}</span>
                </div>
            )}
        </Link>
    );
};