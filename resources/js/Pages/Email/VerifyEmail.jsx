import React, { useState, useEffect } from 'react';
import { Head, usePage, router, useForm } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

const VerifyEmail = () => {
    const { email, translations, flash } = usePage().props;
    const [showEmailModal, setShowEmailModal] = useState(false);
    const [code, setCode] = useState('');

    // Formulário de verificação
    const { data, setData, post, processing, errors } = useForm({
        code: ''
    });

    // Formulário de atualização de email
    const { data: emailData, setData: setEmailData, post: updateEmail, processing: updatingEmail } = useForm({
        email: email
    });

    // Auto-submit quando o código tiver 6 dígitos
    useEffect(() => {
        if (code.length === 6) {
            post(route('verification.verify'), {
                preserveScroll: true,
                onSuccess: () => {
                    setCode('');
                }
            });
        }
    }, [code]);

    // Atualizar código no estado local e form
    const handleCodeChange = (e) => {
        const value = e.target.value.replace(/\D/g, '').slice(0, 6);
        setCode(value);
        setData('code', value);
    };

    // Submeter formulário manualmente
    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('verification.verify'), {
            preserveScroll: true,
            onSuccess: () => {
                setCode('');
            }
        });
    };

    // Reenviar código
    const handleResendCode = () => {
        router.post(route('verification.resend'), {}, {
            preserveScroll: true
        });
    };

    // Atualizar email
    const handleUpdateEmail = (e) => {
        e.preventDefault();
        updateEmail(route('verification.update-email'), {
            preserveScroll: true,
            onSuccess: () => {
                setShowEmailModal(false);
            }
        });
    };

    // Logout
    const handleLogout = () => {
        router.post(route('logout'));
    };

    // Apenas números no input
    const handleKeyPress = (e) => {
        if (!/[0-9]/.test(e.key)) {
            e.preventDefault();
        }
    };

    return (
        <SiteLayout>
            <Head title={translations.title} />
            
            <div className="min-h-screen flex flex-col bg-gradient-to-br from-slate-900 to-slate-800 text-slate-100">
                <div className="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
                    <div className="w-full max-w-md bg-slate-900/90 backdrop-blur-xl rounded-2xl p-8 border border-teal-500 shadow-2xl">
                        {/* Logo */}
                        <div className="text-center mb-8">
                            <div className="flex justify-center mb-6">
                                <img 
                                    className="w-48 h-auto" 
                                    src="/assets/images/logo.png" 
                                    alt="Handgeev Logo"
                                    onError={(e) => {
                                        e.target.style.display = 'none';
                                        e.target.parentElement.innerHTML = 
                                            '<div class="text-4xl text-teal-400 font-bold">Handgeev</div>';
                                    }}
                                />
                            </div>
                            
                            {/* Ícone de email */}
                            <div className="text-6xl mb-4">
                                {translations.icons.email || '✉️'}
                            </div>
                            
                            {/* Títulos */}
                            <h2 className="text-2xl font-bold text-slate-100 mb-2">
                                {translations.header.title}
                            </h2>
                            <p className="text-slate-300">
                                {translations.header.sent_to}
                            </p>
                            <p className="text-teal-400 font-semibold text-lg mt-1">
                                {email}
                            </p>
                        </div>

                        {/* Mensagens de flash */}
                        {flash.success && (
                            <div className="mb-6 p-4 bg-green-900/30 border border-green-500/30 rounded-lg">
                                <p className="text-green-400 text-center">
                                    {flash.success}
                                </p>
                            </div>
                        )}

                        {flash.error && (
                            <div className="mb-6 p-4 bg-red-900/30 border border-red-500/30 rounded-lg">
                                <p className="text-red-400 text-center">
                                    {flash.error}
                                </p>
                            </div>
                        )}

                        {/* Formulário de código */}
                        <form onSubmit={handleSubmit} className="space-y-6">
                            <div>
                                <label className="block text-sm font-medium text-slate-300 mb-3 text-center">
                                    {translations.form.code_label}
                                </label>
                                <input
                                    type="text"
                                    name="code"
                                    value={code}
                                    onChange={handleCodeChange}
                                    onKeyPress={handleKeyPress}
                                    maxLength={6}
                                    autoComplete="off"
                                    className="w-full py-4 px-3 bg-slate-800/60 border-2 border-slate-700 rounded-xl text-white text-2xl font-bold text-center tracking-[0.5em] placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/20 transition-all duration-200"
                                    placeholder={translations.form.code_placeholder}
                                    required
                                    autoFocus
                                />
                                {errors.code && (
                                    <p className="mt-2 text-sm text-red-400 text-center animate-pulse">
                                        {errors.code}
                                    </p>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full py-3 px-4 bg-gradient-to-r from-teal-500 to-emerald-500 text-slate-900 font-semibold text-lg rounded-xl hover:from-teal-600 hover:to-emerald-600 transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-teal-500/20"
                            >
                                <i className={`${translations.icons.check} mr-2`}></i>
                                {processing ? 'Verificando...' : translations.form.submit_button}
                            </button>
                        </form>

                        {/* Links adicionais */}
                        <div className="mt-8 space-y-4">
                            <div className="text-center">
                                <button
                                    type="button"
                                    onClick={handleResendCode}
                                    className="text-teal-400 hover:text-teal-300 text-sm transition-colors duration-200"
                                >
                                    <i className={`${translations.icons.redo} mr-1`}></i>
                                    {translations.form.resend_code}
                                </button>
                                <span className="text-slate-600 mx-3">•</span>
                                <button
                                    type="button"
                                    onClick={() => setShowEmailModal(true)}
                                    className="text-slate-400 hover:text-slate-300 text-sm transition-colors duration-200"
                                >
                                    <i className={`${translations.icons.edit} mr-1`}></i>
                                    {translations.form.change_email}
                                </button>
                            </div>

                            <div className="pt-4 border-t border-slate-800">
                                <div className="text-center">
                                    <button
                                        type="button"
                                        onClick={handleLogout}
                                        className="text-slate-500 hover:text-slate-400 text-sm transition-colors duration-200"
                                    >
                                        <i className={`${translations.icons.logout} mr-1`}></i>
                                        {translations.form.logout}
                                    </button>
                                </div>
                            </div>
                        </div>

                        {/* Informação adicional */}
                        <div className="mt-6 text-center">
                            <p className="text-xs text-slate-500">
                                {translations.messages.code_expires}
                            </p>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <footer className="py-6 px-4 border-t border-slate-800">
                    <div className="max-w-7xl mx-auto text-center text-slate-500 text-sm">
                        <p>© {new Date().getFullYear()} Handgeev. Todos os direitos reservados.</p>
                    </div>
                </footer>
            </div>

            {/* Modal para alterar email */}
            {showEmailModal && (
                <div className="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fadeIn">
                    <div className="w-full max-w-md bg-slate-900/95 backdrop-blur-xl rounded-2xl p-6 border border-teal-500/30 shadow-2xl">
                        <h3 className="text-lg font-semibold text-slate-100 mb-4">
                            {translations.modal.title}
                        </h3>
                        <form onSubmit={handleUpdateEmail}>
                            <div className="mb-6">
                                <label className="block text-sm text-slate-300 mb-2">
                                    {translations.modal.email_label}
                                </label>
                                <input
                                    type="email"
                                    value={emailData.email}
                                    onChange={(e) => setEmailData('email', e.target.value)}
                                    className="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all duration-200"
                                    required
                                    autoFocus
                                />
                            </div>
                            <div className="flex gap-3">
                                <button
                                    type="button"
                                    onClick={() => setShowEmailModal(false)}
                                    className="flex-1 bg-slate-800 hover:bg-slate-700 text-white py-3 rounded-xl transition-all duration-200"
                                >
                                    {translations.modal.cancel_button}
                                </button>
                                <button
                                    type="submit"
                                    disabled={updatingEmail}
                                    className="flex-1 bg-gradient-to-r from-teal-500 to-emerald-500 text-slate-900 font-semibold py-3 rounded-xl hover:from-teal-600 hover:to-emerald-600 transition-all duration-200 disabled:opacity-50"
                                >
                                    {updatingEmail ? (
                                        <>
                                            <i className="fas fa-spinner animate-spin mr-2"></i>
                                            Alterando...
                                        </>
                                    ) : (
                                        translations.modal.change_button
                                    )}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </SiteLayout>
    );
};

// Estilos CSS
const styles = `
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
    
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    input[type="number"] {
        -moz-appearance: textfield;
    }
    
    .tracking-\[0\.5em\] {
        letter-spacing: 0.5em;
    }
`;

// Adicionar estilos ao documento
const styleSheet = document.createElement("style");
styleSheet.innerText = styles;
document.head.appendChild(styleSheet);

export default VerifyEmail;