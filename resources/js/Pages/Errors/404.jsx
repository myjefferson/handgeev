import React from 'react';
import { Head } from '@inertiajs/react';
import Footer from '@/Components/Footer/Footer'; // Ajuste o caminho conforme sua estrutura

export default function Error404() {
    // Verifica se o usuário está autenticado (via props do Inertia)
    const { auth } = usePage().props;

    return (
        <>
            <Head>
                <title>{__('errors.page_not_found')}</title>
                <meta name="description" content={__('errors.page_not_found_description')} />
            </Head>

            <div className="min-h-screen flex items-center justify-center px-4">
                <div className="text-center">
                    {/* Logo */}
                    <div className="mb-8">
                        <img 
                            className="w-48 mx-auto" 
                            src="/assets/images/logo.png" 
                            alt="Handgeev Logo" 
                        />
                    </div>

                    {/* Ícone de Erro */}
                    <div className="mb-6">
                        <div className="w-24 h-24 mx-auto bg-red-500/10 rounded-full flex items-center justify-center border border-red-500/20">
                            <i className="fas fa-exclamation-triangle text-red-400 text-3xl"></i>
                        </div>
                    </div>

                    {/* Mensagem */}
                    <h1 className="text-6xl font-bold text-red-400 mb-4">404</h1>
                    <h2 className="text-2xl font-semibold mb-4 text-white">
                        {__('errors.page_not_found')}
                    </h2>
                    <p className="text-gray-400 mb-8 max-w-md mx-auto">
                        {__('errors.404_message')}
                    </p>

                    {/* Botões de Ação */}
                    <div className="flex flex-col sm:flex-row gap-4 justify-center">
                        <a 
                            href="/" 
                            className="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
                        >
                            <i className="fas fa-home mr-2"></i>
                            {__('errors.back_to_home')}
                        </a>
                        
                        {auth.user ? (
                            <a 
                                href={route('dashboard.home')} 
                                className="border border-teal-500 text-teal-400 hover:bg-teal-500/10 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                <i className="fas fa-tachometer-alt mr-2"></i>
                                {__('errors.go_to_dashboard')}
                            </a>
                        ) : (
                            <a 
                                href={route('login.show')} 
                                className="border border-teal-500 text-teal-400 hover:bg-teal-500/10 font-semibold py-3 px-6 rounded-lg transition-colors"
                            >
                                <i className="fas fa-sign-in-alt mr-2"></i>
                                {__('errors.login')}
                            </a>
                        )}
                    </div>

                    {/* Informações Adicionais */}
                    <div className="mt-8 text-sm text-gray-500">
                        <p>{__('errors.support_contact')}</p>
                    </div>
                </div>
            </div>

            <Footer />
        </>
    );
}

// Estilos inline equivalentes (ou você pode mover para CSS modules/tailwind)
const styles = `
    body {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        min-height: 100vh;
    }
`;

// Adiciona os estilos ao head
const styleSheet = document.createElement('style');
styleSheet.innerText = styles;
document.head.appendChild(styleSheet);